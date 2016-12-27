<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Show;
use App\Http\Models\Venue;
use App\Http\Models\ShowTime;
use App\Http\Models\Ticket;

/**
 * Manage Tickets codes
 *
 * @author ivan
 */
class TicketCodeController extends Controller{
    
    /**
     * List all ticket codes and return default view.
     *
     * @return view
     */
    public function index()
    {
        try {
            //init
            $input = Input::all(); 
            $current = date('Y-m-d H:i:s');
            if(isset($input) && isset($input['id']))
            {
                //get selected record
                /*$user = User::find($input['id']);  
                if(!$user)
                    return ['success'=>false,'msg'=>'There was an error getting the user.<br>Maybe it is not longer in the system.'];
                $location = Location::find($user->location_id);
                $discounts = [];
                foreach($user->user_discounts as $d)
                    $discounts[] = $d->pivot->discount_id;
                $user->venues_check_ticket = explode(',',$user->venues_check_ticket);
                //dont show these fields
                unset($user->password);
                unset($location->id);
                return ['success'=>true,'user'=>array_merge($user->getAttributes(),$location->getAttributes(),['discounts[]'=>$discounts],['venues_check_ticket[]'=>$user->venues_check_ticket])];
                 */
            }
            else if(isset($input) && isset($input['venue_id']))
            {
                $shows = Show::where('is_active','=',1)->where('venue_id','=',$input['venue_id'])
                                ->select('id', 'name')->distinct()->get();
                return ['success'=>true,'shows'=>$shows];
            }
            else if(isset($input) && isset($input['show_id']))
            {
                $show_times = ShowTime::where('is_active','=',1)->where('show_id','=',$input['show_id'])->where('show_time','>',$current)
                                ->select('id', 'show_time')->orderBy('show_time','DESC')->distinct()->get();
                $tickets = Ticket::where('is_active','=',1)->where('show_id','=',$input['show_id'])
                                ->select('id', 'ticket_type')->orderBy('ticket_type')->distinct()->get();
                return ['success'=>true,'show_times'=>$show_times,'tickets'=>$tickets];
            }
            else if(isset($input) && isset($input['ticket_id']))
            {
                $ticket = Ticket::where('id','=',$input['ticket_id'])
                                ->first(['retail_price','processing_fee','percent_commission']);
                return ['success'=>true,'ticket'=>$ticket];
            }
            else
            {
                //get all records        
                $codes = DB::table('ticket_codes')
                                ->join('tickets', 'tickets.id', '=' ,'ticket_codes.ticket_id')
                                ->join('users', 'users.id', '=' ,'ticket_codes.user_id')
                                ->join('show_times', 'show_times.id', '=' ,'ticket_codes.show_time_id')
                                ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                ->select('ticket_codes.*', 'shows.name AS show_name', 'users.first_name', 'users.last_name', 'show_times.show_time', 
                                         'tickets.ticket_type', 'tickets.retail_price', 'tickets.processing_fee', 'tickets.percent_commission')
                                ->orderBy('shows.name','show_times.show_time')
                                ->get();
                $sellers = DB::table('users')
                                ->join('user_types', 'user_types.id', '=' ,'users.user_type_id')
                                ->select('users.*')
                                ->where('user_types.user_type','=','Seller')
                                ->orderBy('users.email')
                                ->get();
                $venues = Venue::orderBy('name')->get();
                //return view
                return view('admin.ticket_codes.index',compact('codes','sellers','venues'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Ticket Index: '.$ex->getMessage());
        }
    }
    /**
     * Save new or updated ticket codes.
     *
     * @void
     */
    public function save()
    {
        try {   
            
        } catch (Exception $ex) {
            throw new Exception('Error Ticket Type Save: '.$ex->getMessage());
        }
    }
    
}
