<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Http\Models\Ticket;
use App\Http\Models\Util;

/**
 * Manage Tickets
 *
 * @author ivan
 */
class TicketController extends Controller{
    
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
            if(isset($input) && isset($input['id']))
            {
                //get selected record
                if(!isset($ticket_types[$input['id']]))
                    return ['success'=>false,'msg'=>'There was an error getting the ticket type.<br>Maybe it is not longer in the system.'];
                else
                {
                    $t = Ticket::where('ticket_type',$input['id'])->first();
                    $ticket = ['ticket_type'=>$input['id'],'ticket_type_class'=>($t && $t->ticket_type_class)? $t->ticket_type_class : 'btn-primary'];
                    return ['success'=>true,'ticket_type'=>array_merge($ticket)];
                }
            }
            else
            {
                $tickets = [];
                foreach ($ticket_types as $tt)
                {
                    $t = Ticket::where('ticket_type',$tt)->first();
                    $tickets[$tt] = ['ticket_type'=>$tt,'ticket_type_class'=>($t && $t->ticket_type_class)? $t->ticket_type_class : 'btn-primary'];
                }
                $ticket_styles = Util::getEnumValues('tickets','ticket_type_class');
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
            //save all record      
            if($input && (isset($input['ticket_type']) || isset($input['id'])))
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
            return ['success'=>false,'msg'=>'There was an error saving the Ticket Type.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Ticket Type Save: '.$ex->getMessage());
        }
    }
    
}
