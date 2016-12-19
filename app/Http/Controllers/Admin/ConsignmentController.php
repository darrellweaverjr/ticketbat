<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use App\Http\Models\Util;
use App\Http\Models\Show;
use App\Http\Models\Venue;
use App\Http\Models\ShowTime;
use App\Http\Models\Seat;
use App\Http\Models\Ticket;
use App\Http\Models\Stage;
use App\Http\Models\Consignment;
use App\Http\Models\Purchase;
use App\Http\Models\Customer;
use App\Http\Models\User;


/**
 * Manage Consignments
 *
 * @author ivan
 */
class ConsignmentController extends Controller{
    
    /**
     * List all Consignments and return default view.
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
                                ->select('id', 'show_time')->orderBy('show_time','ASC')->distinct()->get();
                $tickets = Ticket::where('is_active','=',1)->where('show_id','=',$input['show_id'])
                                ->select('id', 'ticket_type')->orderBy('ticket_type')->distinct()->get();
                return ['success'=>true,'show_times'=>$show_times,'tickets'=>$tickets];
            }
            else if(isset($input) && isset($input['ticket_id']))
            {
                $ticket = Ticket::where('id','=',$input['ticket_id'])
                                ->first(['retail_price','processing_fee','percent_commission']);
                if(isset($input['available']) && $input['available'] = 1)
                    $seats = DB::table('seats')
                                ->leftJoin('purchase_seats', 'purchase_seats.seat_id', '=' ,'seats.id','outer')
                                ->select('seats.*')
                                ->where('ticket_id','=',$input['ticket_id'])
                                ->orderBy('seats.seat')
                                ->distinct()->get();
                else
                    $seats = Seat::where('ticket_id','=',$input['ticket_id'])
                                ->select('seats.*')->orderBy('seats.seat')->get();
                return ['success'=>true,'ticket'=>$ticket,'seats'=>$seats];
            }
            else if(isset($input) && isset($input['stage_id']))
            {
                $tickets = DB::table('tickets')
                                ->join('shows', 'shows.id', '=' ,'tickets.show_id')
                                ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                                ->select('tickets.id','tickets.ticket_type','packages.title')
                                ->where('shows.stage_id','=',$input['stage_id'])->where('tickets.is_active','=',1)
                                ->orderBy('tickets.ticket_type')
                                ->distinct()->get();
                return ['success'=>true,'tickets'=>$tickets];
            }
            else if(isset($input) && isset($input['ticket_id']))
            {
                $ticket = Ticket::where('id','=',$input['ticket_id'])
                                ->first(['ticket_type','retail_price','processing_fee','percent_commission']);
                $seats = DB::table('seats')
                                ->join('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->join('shows', 'shows.id', '=' ,'tickets.show_id')
                                ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                                ->select('tickets.ticket_type', 'packages.title', 'seats.*')
                                ->orderBy('seats.seat')
                                ->get();
                return ['success'=>true,'ticket'=>$ticket,'seats'=>$seats];
            }
            else
            {
                //get all records        
                $consignments = DB::table('consignments')
                                ->join('users', 'users.id', '=' ,'consignments.seller_id')
                                ->join('show_times', 'show_times.id', '=' ,'consignments.show_time_id')
                                ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                ->select('consignments.*', 'shows.name AS show_name', 'users.first_name', 'users.last_name', 'show_times.show_time')
                                ->orderBy('shows.name','show_times.show_time')
                                ->get();
                $sellers = DB::table('users')
                                ->join('user_types', 'user_types.id', '=' ,'users.user_type_id')
                                ->select('users.*')
                                ->where('user_types.user_type','=','Seller')
                                ->orderBy('users.email')
                                ->get();
                $venues = Venue::orderBy('name')->get();
                $stages = Stage::orderBy('name')->get();
                //return view
                return view('admin.consignments.index',compact('consignments','sellers','venues','stages'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Consignment Index: '.$ex->getMessage());
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
            //init
            $input = Input::all();
            $file = null;
            if(Input::hasFile('agreement'))
                $file = Input::file('agreement');
            $current = date('Y-m-d H:i:s');
            //save all record      
            if($input)
            {
                if(isset($input['id']) && $input['id'])
                {
                    /*$user = User::find($input['id']);
                    $user->updated = $current;
                    $location = $user->location;
                    $location->updated = $current;
                    if(isset($input['password']) && $input['password'])
                        $user->password = md5($input['password']);*/
                }                    
                else
                {                    
                    //create consignment
                    $consignment = new Consignment;
                    $consignment->show_time_id = $input['show_time_id'];
                    $consignment->seller_id = $input['seller_id'];
                    $consignment->due_date = $input['due_date'];
                    $consignment->created = $current;
                    $consignment->updated = $current;
                    $consignment->agreement = ($file)? Util::upload_file($file,'consignments') : '';
                    $consignment->save();
                    //create purchase
                    if($input['purchase'])
                    {
                        $user = User::find($input['seller_id']); 
                        $customer = Customer::where('email','=',$user->email)->first(); 
                        if(!$customer)
                        {
                            $customer = new Customer;
                            $customer->location_id = $user->location_id;
                            $customer->email = $user->email;
                            $customer->first_name = $user->first_name;
                            $customer->last_name = $user->last_name;
                            $customer->phone = $user->phone;
                            $customer->created = $current;
                            $customer->updated = $current;
                            $customer->save();
                        }
                        $purchase = new Purchase;
                        $purchase->quantity = count($input['seat']);
                        $purchase->user_id = $input['seller_id'];
                        $purchase->show_time_id = $input['show_time_id'];
                        $purchase->ticket_id = $input['ticket_id'];
                        $purchase->customer_id = $customer->id;
                        $purchase->session_id = Session::getId();  
                        $purchase->referrer_url = Config::get('app.url');
                        $purchase->ticket_type = 'Consignment';
                        $purchase->retail_price = $input['retail_price'] * $purchase->quantity;
                        $purchase->processing_fee = $input['processing_fee'] * $purchase->quantity;
                        $purchase->savings = 0;
                        $purchase->commission_percent = round($input['percent_commission']/$input['retail_price']*100,2);
                        $purchase->price_paid = $purchase->retail_price + $purchase->processing_fee;
                        $purchase->payment_type = 'None';
                        $purchase->merchandise = 0;
                        $purchase->updated = $current;
                        $purchase->created = $current;
                        $purchase->save();
                    }
                    //create consignments seats
                    foreach ($input['seat'] as $s)
                    {
                        $seat = new Seat;
                        $seat->consignment_id = $consignment->id;
                        if($input['purchase'])
                            $seat->purchase_id = $purchase->id;
                        $seat->seat_id = $s;
                        $seat->updated = $current;
                    }
                    //return
                    return ['success'=>true,'msg'=>'Consignments Tickets saved successfully!'];
                }
            }
            return ['success'=>false,'msg'=>'There was an error saving the consignment.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Consignment Save: '.$ex->getMessage());
        }
    }
    /**
     * Save new or updated seats.
     *
     * @void
     */
    public function save_seats()
    {
        try {   
            //init
            $input = Input::all();
            //save all record      
            if($input && isset($input['ticket_id']) && isset($input['seat']))
            {
                $seats_new = $input['seat'];   
                $seats = Seat::where('ticket_id','=',$input['ticket_id'])->get();
                foreach ($seats as $s)
                    if(!(in_array($s->seat, $seats_new)))
                        Seat::where('id','=',$s->id)->delete();
                foreach ($seats_new as $s)
                    if(!Seat::where('ticket_id','=',$input['ticket_id'])->where('seat','=',$s)->count())
                    {
                        $seat = new Seat;
                        $seat->ticket_id = $input['ticket_id'];
                        $seat->seat = $s;
                        $seat->save();
                    } 
                return ['success'=>true,'msg'=>'Seats saved successfully!'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the seats.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Consignment Save Seats: '.$ex->getMessage());
        }
    }
    
}
