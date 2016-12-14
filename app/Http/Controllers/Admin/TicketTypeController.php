<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Ticket;
use App\Http\Models\Util;

/**
 * Manage TicketsTypes
 *
 * @author ivan
 */
class TicketTypeController extends Controller{
    
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
                foreach ($ticket_types as $tt)
                {
                    $t = Ticket::where('ticket_type',$tt)->first();
                    $tickets[$tt] = ['ticket_type'=>$tt,'ticket_type_class'=>($t && $t->ticket_type_class)? $t->ticket_type_class : '(btn-primary)','active'=>(in_array($tt,$inactives))? '' : 'checked'];
                }
                $ticket_styles = Util::getEnumValues('tickets','ticket_type_class');
                //dd($tickets);
                //return view
                return view('admin.ticket_types.index',compact('tickets','ticket_types','ticket_styles'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Ticket Index: '.$ex->getMessage());
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
    
}
