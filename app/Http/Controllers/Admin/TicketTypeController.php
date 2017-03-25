<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Ticket;
use App\Http\Models\Util;
use Illuminate\Support\Facades\Storage;

/**
 * Manage TicketsTypes
 *
 * @author ivan
 */
class TicketTypeController extends Controller{
    
    private $style_url = 'styles/ticket_types.css';
    /**
     * List all ticket types and return default view.
     *
     * @return view
     */
    public function index()
    {
        try {
            //init
            $input = Input::all(); 
            //get all records        
            $ticket_types = Util::getEnumValues('tickets','ticket_type');
            $inactives = explode(',',DB::table('ticket_types_inactive')->get()->implode('ticket_type',','));
            if(isset($input) && isset($input['id']))
            {
                //get selected record
                if(!isset($ticket_types[$input['id']]))
                    return ['success'=>false,'msg'=>'There was an error getting the ticket type.<br>Maybe it is not longer in the system.'];
                else
                {
                    $t = Ticket::where('ticket_type',$input['id'])->first();
                    $ticket = ['ticket_type'=>$input['id'],'ticket_type_class'=>($t && $t->ticket_type_class)? $t->ticket_type_class : 'btn-primary','active'=>(in_array($input['id'],$inactives))? '' : 'checked'];
                    return ['success'=>true,'ticket_type'=>array_merge($ticket)];
                }
            }
            else
            {
                $tickets = [];
                $ticket_styles = [];
                //if user has permission to view
                if(in_array('View',Auth::user()->user_type->getACLs()['TYPES']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['TYPES']['permission_scope'] == 'All')
                    {
                        foreach ($ticket_types as $tt)
                        {
                            $t = Ticket::where('ticket_type',$tt)->first();
                            $tickets[$tt] = ['ticket_type'=>$tt,'ticket_type_class'=>($t && $t->ticket_type_class)? $t->ticket_type_class : '(btn-primary)','active'=>(in_array($tt,$inactives))? '' : 'checked'];
                        }
                        $ticket_styles = Util::getEnumValues('tickets','ticket_type_class');
                    }
                }
                //get styles from cloud
                $ticket_types_css = '';
                if(Storage::disk('s3')->exists($this->style_url))
                    $ticket_types_css = Storage::disk('s3')->get($this->style_url);
                else
                    Storage::disk('s3')->put($this->style_url,$ticket_types_css,'public');
                //return view
                return view('admin.ticket_types.index',compact('tickets','ticket_types','ticket_styles','ticket_types_css'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Ticket Type Index: '.$ex->getMessage());
        }
    }
    /**
     * Save new or updated ticket type.
     *
     * @void
     */
    public function save()
    {
        try {   
            //init
            $input = Input::all(); 
            //active/inactive      
            if($input && (isset($input['ticket_type']) && isset($input['active'])))
            {
                if($input['active'] == 'true')
                {
                    $success = true;
                    if(DB::table('ticket_types_inactive')->where('ticket_type','=',$input['ticket_type'])->count())
                        $success = DB::table('ticket_types_inactive')->where('ticket_type','=',$input['ticket_type'])->delete();
                }
                else
                {
                    $tickets = DB::table('tickets')->join('packages', 'tickets.package_id', '=' ,'packages.id')
                                ->join('shows', 'tickets.show_id', '=' ,'shows.id')
                                ->select('shows.name')
                                ->where('tickets.ticket_type','=',$input['ticket_type'])
                                ->where('tickets.is_active','=',1)
                                ->where('shows.is_active','=',1)
                                ->groupBy('shows.name')->orderBy('shows.name')->distinct()->get();
                    if($tickets)
                    {
                        $msg = 'The following shows have active tickets that depend of that ticket type:<br><br><ol style="max-height:200px;overflow:auto;text-align:left;">';
                        foreach ($tickets as $t)
                            $msg .= '<li style="color:red;">'.$t->name.'</li>';
                        $msg .= '</ol><br> Please, inactive them first.';
                        return ['success'=>false,'msg'=>$msg];
                    }
                    $success = true;
                    if(DB::table('ticket_types_inactive')->where('ticket_type','=',$input['ticket_type'])->count() == 0)
                        $success = DB::table('ticket_types_inactive')->insert(['ticket_type' => $input['ticket_type']]);
                } 
                if($success)
                    return ['success'=>true,'msg'=>'Ticket Type updated successfully!'];
                return ['success'=>false,'msg'=>'There was an error updating the ticket_type.'];
            }
            //save all record      
            else if($input && (isset($input['ticket_type']) || isset($input['id'])))
            {
                if(isset($input['id']) && $input['id'])
                {
                    Ticket::where('ticket_type',$input['id'])->update(['ticket_type_class' => $input['ticket_type_class']]);
                    return ['success'=>true,'msg'=>'Ticket Type saved successfully!'];
                }                    
                else
                {                    
                    $ticket_types = Util::getEnumValues('tickets','ticket_type');
                    if(isset($ticket_types[$input['ticket_type']]))
                        return ['success'=>false,'msg'=>'There was an error saving the type.<br>That code is already in the system.','errors'=>'type'];
                    else
                    {
                        $ticket_types[$input['ticket_type']] = $input['ticket_type'];
                        $broker_rates = Util::setEnumValues('broker_rates','ticket_type',$ticket_types);
                        $tickets = Util::setEnumValues('tickets','ticket_type',$ticket_types);
                        //return
                        if($broker_rates && $tickets)
                            return ['success'=>true,'msg'=>'Ticket Type saved successfully!'];
                        return ['success'=>false,'msg'=>'There was an error saving the ticket_type.'];
                    }
                }
            }
            else return ['success'=>false,'msg'=>'There was an error saving the Ticket Type.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Ticket Type Save: '.$ex->getMessage());
        }
    }
    /**
     * Modify all clases of ticket types.
     *
     * @return view
     */
    public function classes()
    {
        try {
            //init
            $input = Input::all(); 
            $ticket_styles = Util::getEnumValues('tickets','ticket_type_class');
            if(isset($input) && isset($input['action']) && isset($input['ticket_type_class']) && $input['action']==1)
            {
                $ticket_styles[$input['ticket_type_class']] = $input['ticket_type_class'];
                $ticket_styles = array_unique(array_values($ticket_styles));
                DB::statement('ALTER TABLE tickets CHANGE COLUMN ticket_type_class ticket_type_class ENUM( "'.implode('","',$ticket_styles).'" ) DEFAULT "btn-primary"');
                $ticket_styles = Util::getEnumValues('tickets','ticket_type_class');
                return ['success'=>true,'classes'=>$ticket_styles];
            }
            elseif(isset($input) && isset($input['action']) && isset($input['ticket_type_class']) && count($input['ticket_type_class']) && $input['action']==-1)
            {
                if(($key = array_search('btn-primary',$input['ticket_type_class'])) !== false) 
                    unset($input['ticket_type_class'][$key]);
                $ticket_styles = array_diff(array_unique(array_values($ticket_styles)),$input['ticket_type_class']);
                DB::statement('ALTER TABLE tickets CHANGE COLUMN ticket_type_class ticket_type_class ENUM( "'.implode('","',$ticket_styles).'" ) DEFAULT "btn-primary"');
                $ticket_styles = Util::getEnumValues('tickets','ticket_type_class');
                return ['success'=>true,'classes'=>$ticket_styles];
            }
            return ['success'=>false,'msg'=>'There was an error updating the clases.<br>Invalid option/data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Ticket Index: '.$ex->getMessage());
        }
    }
    /**
     * Modify the file css in the cloud.
     *
     * @return view
     */
    public function styles()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && isset($input['ticket_type_file']))
            {
                Storage::disk('s3')->put($this->style_url,$input['ticket_type_file'],'public');
                return ['success'=>true];
            }
            return ['success'=>false,'msg'=>'There was an error updating the file.'];
        } catch (Exception $ex) {
            throw new Exception('Error Ticket Type File Index: '.$ex->getMessage());
        }
    }
    
}
