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
                $consignment = DB::table('consignments')
                                ->join('users', 'users.id', '=' ,'consignments.seller_id')
                                ->join('show_times', 'show_times.id', '=' ,'consignments.show_time_id')
                                ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                ->leftJoin('purchase_seats', 'purchase_seats.consignment_id', '=' ,'consignments.id')
                                ->leftJoin('seats', 'seats.id', '=' ,'purchase_seats.seat_id')
                                ->leftJoin('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->select(DB::raw('consignments.*,shows.name AS show_name,users.first_name,users.last_name,show_times.show_time,users.email, 
                                        COUNT(purchase_seats.id) AS qty, 
                                        ROUND(SUM(COALESCE(tickets.retail_price,0)+COALESCE(tickets.processing_fee,0)-COALESCE(tickets.retail_price,0)*COALESCE(tickets.percent_commission,0)/100),2) AS total'))
                                ->where(function ($query) {
                                    return $query->whereNull('purchase_seats.status')
                                                 ->orWhere('purchase_seats.status','<>','Voided');
                                })
                                ->where('consignments.id','=',$input['id'])
                                ->groupBy('consignments.id')    
                                ->first();
                if(!$consignment)
                    return ['success'=>false,'msg'=>'There was an error getting the consignment.<br>Maybe it is not longer in the system.'];
                $seats = DB::table('purchase_seats')
                                ->join('seats', 'purchase_seats.seat_id', '=' ,'seats.id')
                                ->join('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->select('purchase_seats.*','seats.seat','tickets.ticket_type','tickets.retail_price','tickets.processing_fee','tickets.percent_commission')
                                ->where('purchase_seats.consignment_id','=',$input['id'])
                                ->orderBy('tickets.ticket_type','seats.seat')
                                ->distinct()->get();
                $moveto = DB::table('consignments')
                                ->join('users', 'users.id', '=' ,'consignments.seller_id')
                                ->join('show_times', 'show_times.id', '=' ,'consignments.show_time_id')
                                ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                ->select(DB::raw('consignments.*,shows.name AS show_name,users.first_name,users.last_name,show_times.show_time,users.email'))
                                ->where('consignments.show_time_id','=',$consignment->show_time_id)
                                ->where('consignments.id','<>',$consignment->id)
                                ->groupBy('consignments.id')    
                                ->get();
                return ['success'=>true,'consignment'=>$consignment, 'seats'=>$seats, 'moveto'=>$moveto];
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
                if(isset($input['available']) && $input['available'])
                    $seats = DB::select(' SELECT DISTINCT s.* FROM seats s WHERE s.id NOT IN ' 
                                            .'(SELECT ps.seat_id FROM purchase_seats ps INNER JOIN seats ss ON ss.id = ps.seat_id WHERE ss.ticket_id = ?) '
                                            .'AND s.ticket_id = ? ORDER BY s.seat',array($input['ticket_id'],$input['ticket_id']));
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
                                ->leftJoin('purchase_seats', 'purchase_seats.consignment_id', '=' ,'consignments.id')
                                ->leftJoin('seats', 'seats.id', '=' ,'purchase_seats.seat_id')
                                ->leftJoin('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->leftJoin('purchases', 'purchases.id', '=' ,'purchase_seats.purchase_id')
                                ->select(DB::raw('consignments.*,shows.name AS show_name,users.first_name,users.last_name,show_times.show_time,users.email, 
                                        COUNT(purchase_seats.id) AS qty, (CASE WHEN (consignments.created = purchases.created) THEN 1 ELSE 0 END) as purchase,
                                        ROUND(SUM(COALESCE(tickets.retail_price,0)+COALESCE(tickets.processing_fee,0)-COALESCE(tickets.retail_price,0)*COALESCE(tickets.percent_commission,0)/100),2) AS total'))
                                ->where(function ($query) {
                                    return $query->whereNull('purchase_seats.status')
                                                 ->orWhere('purchase_seats.status','<>','Voided');
                                })
                                ->groupBy('consignments.id')    
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
                $status = Util::getEnumValues('consignments','status');
                $status_seat = Util::getEnumValues('purchase_seats','status');
                //return view
                return view('admin.consignments.index',compact('consignments','sellers','venues','stages','status','status_seat'));
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
            if(Input::hasFile('agreement_file'))
                $file = Input::file('agreement_file');
            $current = date('Y-m-d H:i:s');
            //save all record      
            if($input)
            {
                if(isset($input['id']) && $input['id'])
                {
                    $consignment = Consignment::find($input['id']);
                    $consignment->due_date = $input['due_date'];
                    if($file)
                        $consignment->set_agreement($file);
                    $consignment->save();
                    if(isset($input['action']) && isset($input['seat']) && count($input['seat']))
                    {
                        $seats = array_unique($input['seat']);
                        if($input['action'] == 'status' && isset($input['status']))
                        {
                           foreach ($seats as $s)
                           {
                                $purchase_seat = DB::table('purchase_seats')
                                            ->join('tickets', 'tickets.id', '=' ,'purchase_seats.ticket_id')
                                            ->select('purchase_seats.*','tickets.retail_price','tickets.processing_fee')
                                            ->where('purchase_seats_id',$s)->first();
                                if($purchase_seat)
                                {
                                    $oldStatus = $purchase_seat->status;
                                    DB::table('purchase_seats')->where('id',$purchase_seat->id)->update(['status'=>$input['status']]);
                                    //if it has purchase change values
                                    if($purchase_seat->purchase_id)
                                    {
                                        if($oldStatus == 'Voided' && $purchase_seat->status != $oldStatus)
                                        {
                                            $purchase = Purchase::find($purchase_seat->purchase_id);
                                            if($purchase)
                                            {
                                                $purchase->increment('quantity',1);
                                                $purchase->increment('retail_price',$purchase_seat->retail_price);
                                                $purchase->increment('processing_fee',$purchase_seat->processing_fee);
                                            }
                                        }
                                        if($oldStatus != 'Voided' && $purchase_seat->status == 'Voided')
                                        {
                                            $purchase = Purchase::find($purchase_seat->purchase_id);
                                            if($purchase)
                                            {
                                                $purchase->decrement('quantity',1);
                                                $purchase->decrement('retail_price',$purchase_seat->retail_price);
                                                $purchase->decrement('processing_fee',$purchase_seat->processing_fee);
                                            }
                                        }
                                    }
                                }
                           }
                        }
                        else if($input['action'] == 'moveto' && isset($input['moveto']))
                        {
                            //purchase of consignment origin
                            $purchaseFrom = DB::table('purchases')
                                                ->join('purchase_seats', 'purchase_seats.purchase_id', '=' ,'purchases.id')
                                                ->join('consignments', 'consignments.id', '=' ,'purchase_seats.consignment_id')
                                                ->select('purchases.*')
                                                ->where('consignments.id','=',$consignment->id)
                                                ->where('consignments.created','=','purchases.created')
                                                ->first();
                            //if it has purchase on origin(our shows)
                            if($purchaseFrom)
                            {
                                //purchase of consignment destiny
                                $purchaseTo = DB::table('purchases')
                                                ->join('purchase_seats', 'purchase_seats.purchase_id', '=' ,'purchases.id')
                                                ->join('consignments', 'consignments.id', '=' ,'purchase_seats.consignment_id')
                                                ->select('purchases.id')
                                                ->where('consignments.id','=',$input['moveto'])
                                                ->where('consignments.created','=','purchases.created')
                                                ->first(); 
                                //if no purchase, check if it has tickets at begining(not our shows, alert) - or - create empty
                                if(!$purchaseTo)
                                {
                                    $purchase_seatsHas = DB::table('purchase_seats')->where('consignment_id','=',$input['moveto'])->count();
                                    //if it has tickets is not our shows, alert error, wrong destity
                                    if($purchaseHas)
                                        return ['success'=>false,'msg'=>'The consignment to move is not from our shows.'];
                                    //if it is empty, create a purchase as $purchaseTo
                                    else
                                    {
                                        $current = $consignment->created;
                                        //get user and create as customer if it doesnt exists
                                        $user = User::find($consignment->seller_id); 
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
                                        //create purchase
                                        $purchaseTo = new Purchase;
                                        $purchaseTo->quantity = 0;
                                        $purchaseTo->user_id = $consignment->seller_id;
                                        $purchaseTo->show_time_id = $purchaseFrom->show_time_id;
                                        $purchaseTo->ticket_id = $purchaseFrom->ticket_id;
                                        $purchaseTo->customer_id = $customer->id;
                                        $purchaseTo->session_id = Session::getId();  
                                        $purchaseTo->referrer_url = Config::get('app.url');
                                        $purchaseTo->ticket_type = 'Consignment';
                                        $purchaseTo->retail_price = 0;
                                        $purchaseTo->processing_fee = 0;
                                        $purchaseTo->savings = 0;
                                        $purchaseTo->commission_percent = $purchaseFrom->commission_percent;
                                        $purchaseTo->price_paid = $purchaseTo->retail_price + $purchaseTo->processing_fee;
                                        $purchaseTo->payment_type = 'None';
                                        $purchaseTo->merchandise = 0;
                                        $purchaseTo->updated = $current;
                                        $purchaseTo->created = $current;
                                        $purchaseTo->save();
                                    }
                                }
                                $purchaseFrom = Purchase::find($purchaseFrom->id);
                            }
                            //asign seats after change on purchases
                            foreach ($seats as $s)
                            {
                                //init
                                $updates = ['consignment_id'=>$input['moveto']];
                                //if it has values, changing each time to asign a new 
                                if($purchaseFrom && $purchaseTo)
                                {
                                    //asign purchase
                                    $updates['purchase_id'] = $purchaseTo->id;
                                    //get values
                                    $purchase_seat = DB::table('purchase_seats')
                                            ->join('tickets', 'tickets.id', '=' ,'purchase_seats.ticket_id')
                                            ->select('purchase_seats.*','tickets.retail_price','tickets.processing_fee')
                                            ->where('purchase_seats_id',$s)->first();
                                    //decrement old
                                    $purchaseFrom->decrement('quantity',1);
                                    $purchaseFrom->decrement('retail_price',$purchase_seat->retail_price);
                                    $purchaseFrom->decrement('processing_fee',$purchase_seat->processing_fee);
                                    //increment new
                                    $purchaseTo->increment('quantity',1);
                                    $purchaseTo->increment('retail_price',$purchase_seat->retail_price);
                                    $purchaseTo->increment('processing_fee',$purchase_seat->processing_fee);
                                }
                                DB::table('purchase_seats')->where('id',$s)->update($updates);
                            }   
                        }
                    }
                    //return
                    return ['success'=>true,'msg'=>'Consignments Tickets saved successfully!'];
                } 
                else if(isset($input['consignment_id']) && isset($input['status']))
                {
                    $consignment = Consignment::find($input['consignment_id']);
                    if($consignment)
                    {
                        $oldStatus = $consignment->status;
                        $consignment->status = $input['status'];
                        $consignment->updated = $current;
                        $consignment->save();
                        if($oldStatus == 'Voided' && $consignment->status != $oldStatus)
                        {
                            $purchase = DB::table('purchases')
                                    ->join('purchase_seats', 'purchase_seats.purchase_id', '=' ,'purchases.id')
                                    ->select('purchases.id','purchases.status')
                                    ->where('purchase_seats.consignment_id','=',$consignment->id)->where('purchases.created','=',$consignment->created)
                                    ->orderBy('purchases.id')
                                    ->groupBy('purchases.id')
                                    ->first();
                            if($purchase)
                                Purchase::where('id',$purchase->id)->update(['status'=>'Active']);
                        }
                        if($oldStatus != 'Voided' && $consignment->status == 'Voided')
                        {
                            $purchase = DB::table('purchases')
                                    ->join('purchase_seats', 'purchase_seats.purchase_id', '=' ,'purchases.id')
                                    ->select('purchases.id','purchases.status')
                                    ->where('purchase_seats.consignment_id','=',$consignment->id)->where('purchases.created','=',$consignment->created)
                                    ->orderBy('purchases.id')
                                    ->groupBy('purchases.id')
                                    ->first();
                            if($purchase)
                                Purchase::where('id',$purchase->id)->update(['status'=>'Void']);
                        }
                    }
                    return ['success'=>true,'msg'=>'Consignment saved successfully!'];
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
                    if($file)
                        $consignment->set_agreement($file);
                    $consignment->save();
                    //create purchase
                    if(isset($input['purchase']) && $input['purchase'] && isset($input['seats'])&& $input['seats'])
                    {
                        //get user and create as customer if it doesnt exists
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
                        //create purchase
                        $purchase = new Purchase;
                        $purchase->quantity = count($input['seats']);
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
                    if((isset($input['seats'])&& $input['seats']))
                    {
                        $purchase_seat = [];
                        $seats = array_unique($input['seats']);
                        foreach ($seats as $s)
                            $consignment->purchase_seats()->save(Seat::find($s), ['purchase_id' => ($input['purchase'] && $purchase)? $purchase->id : null, 'status' => ($input['purchase'] && $purchase)? 'Sold' : 'Created', 'updated' => $current ]);
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
