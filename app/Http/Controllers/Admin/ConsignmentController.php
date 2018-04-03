<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
use App\Http\Models\Consignment;
use App\Http\Models\Purchase;
use App\Http\Models\Customer;
use App\Http\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;


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
                                ->leftJoin('seats', 'seats.consignment_id', '=' ,'consignments.id')
                                ->leftJoin('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->select(DB::raw('consignments.*,shows.name AS show_name,users.first_name,users.last_name,show_times.show_time,users.email,
                                        COUNT(seats.id) AS qty,
                                        ROUND(SUM(COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0))+COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0))),2) AS total'))
                                ->where(function ($query) {
                                    return $query->whereNull('seats.status')
                                                 ->orWhere('seats.status','<>','Voided');
                                })
                                ->where('consignments.id','=',$input['id'])
                                ->groupBy('consignments.id')
                                ->first();
                if(!$consignment)
                    return ['success'=>false,'msg'=>'There was an error getting the consignment.<br>Maybe it is not longer in the system.'];
                $seats = DB::table('seats')
                                ->join('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->select(DB::raw('seats.*, tickets.ticket_type,
                                                  COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0)) AS retail_price,
                                                  COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0)) AS processing_fee,
                                                  COALESCE(seats.fixed_commission,COALESCE(tickets.fixed_commission,0)) AS fixed_commission,
                                                  COALESCE(seats.percent_commission,COALESCE(tickets.percent_commission,0)) AS percent_commission'))
                                ->where('seats.consignment_id','=',$input['id'])
                                ->orderBy('tickets.ticket_type')->orderByRaw('CAST(seats.seat AS UNSIGNED)')
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
            else if(isset($input) && isset($input['ticket_type']) && isset($input['show_id']))
            {
                $ticket = Ticket::whereRaw('md5(ticket_type) = "'.$input['ticket_type'].'"')->where('show_id','=',$input['show_id'])->first();
                if($ticket)
                    return ['success'=>true,'retail_price'=>$ticket->retail_price,'processing_fee'=>$ticket->processing_fee,'percent_commission'=>$ticket->percent_commission,'fixed_commission'=>$ticket->fixed_commission];
                return ['success'=>true,'retail_price'=>0,'processing_fee'=>0,'percent_commission'=>0,'fixed_commission'=>0];
            }
            else if(isset($input) && isset($input['show_id']))
            {
                $show_times = ShowTime::where('is_active','=',1)->where('show_id','=',$input['show_id'])->where('show_time','>',$current)
                                ->select('id', 'show_time')->orderBy('show_time','ASC')->distinct()->get();
                $tickets = Ticket::where('show_id','=',$input['show_id'])
                                ->select('id', 'ticket_type')->orderBy('ticket_type')->distinct()->get();
                return ['success'=>true,'show_times'=>$show_times,'tickets'=>$tickets];
            }
            else
            {
                //conditions to search
                $search = [];
                $where = [['consignments.id','>',0]];
                //search venue
                if(isset($input) && isset($input['venue']))
                {
                    $search['venue'] = $input['venue'];
                    if($search['venue'] != '')
                        $where[] = ['shows.venue_id','=',$search['venue']];
                }
                else
                    $search['venue'] = '';
                //search show
                if(isset($input) && isset($input['show']))
                {
                    $search['show'] = $input['show'];
                    if($search['show'] != '')
                        $where[] = ['shows.id','=',$search['show']];
                }
                else
                    $search['show'] = '';
                //search showtime
                if(isset($input) && isset($input['showtime_start_date']) && isset($input['showtime_end_date']))
                {
                    $search['showtime_start_date'] = $input['showtime_start_date'];
                    $search['showtime_end_date'] = $input['showtime_end_date'];
                }
                else
                {
                    $search['showtime_start_date'] = '';
                    $search['showtime_end_date'] = '';
                }
                if($search['showtime_start_date'] != '' && $search['showtime_end_date'] != '')
                {
                    $where[] = [DB::raw('DATE(show_times.show_time)'),'>=',$search['showtime_start_date']];
                    $where[] = [DB::raw('DATE(show_times.show_time)'),'<=',$search['showtime_end_date']];
                }
                //search created
                if(isset($input) && isset($input['created_start_date']) && isset($input['created_end_date']))
                {
                    $search['created_start_date'] = $input['created_start_date'];
                    $search['created_end_date'] = $input['created_end_date'];
                }
                else
                {
                    $search['created_start_date'] = date('Y-m-d', strtotime('-30 DAY'));
                    $search['created_end_date'] = date('Y-m-d');
                }
                if($search['created_start_date'] != '' && $search['created_end_date'] != '')
                {
                    $where[] = [DB::raw('DATE(consignments.created)'),'>=',$search['created_start_date']];
                    $where[] = [DB::raw('DATE(consignments.created)'),'<=',$search['created_end_date']];
                }

                //if user has permission to view
                $sellers = [];
                $stages = [];
                $status = [];
                $status_seat = [];
                $sections = [];
                $consignments = [];
                $search['venues'] = [];
                $search['shows'] = [];
                if(in_array('View',Auth::user()->user_type->getACLs()['CONSIGNMENTS']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['CONSIGNMENTS']['permission_scope'] != 'All')
                    {
                        $consignments = DB::table('consignments')
                                ->join('users', 'users.id', '=' ,'consignments.seller_id')
                                ->join('show_times', 'show_times.id', '=' ,'consignments.show_time_id')
                                ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                ->leftJoin('seats', 'seats.consignment_id', '=' ,'consignments.id')
                                ->leftJoin('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->leftJoin('purchases', 'purchases.id', '=' ,'seats.purchase_id')
                                ->select(DB::raw('consignments.*,shows.name AS show_name,users.first_name,users.last_name,show_times.show_time,users.email,
                                        COUNT(seats.id) AS qty, (CASE WHEN (consignments.created = purchases.created) THEN 1 ELSE 0 END) as purchase,
                                        ROUND(SUM(COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0))+COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0))),2) AS total'))
                                ->where($where)
                                ->where(function($query)
                                {
                                    $query->whereIn('shows.venue_id',[Auth::user()->venues_edit])
                                          ->orWhere('shows.audit_user_id','=',Auth::user()->id);
                                })
                                ->where(function ($query) {
                                    return $query->whereNull('seats.status')
                                                 ->orWhere('seats.status','<>','Voided');
                                })
                                ->groupBy('consignments.id')
                                ->orderBy('shows.name','show_times.show_time')
                                ->get();
                        $search['venues'] = Venue::whereIn('id',explode(',',Auth::user()->venues_edit))->orderBy('name')->get(['id','name']);
                        $search['shows'] = Show::whereIn('venue_id',explode(',',Auth::user()->venues_edit))->orWhere('audit_user_id',Auth::user()->id)->orderBy('name')->get(['id','name','venue_id']);
                    }//all
                    else
                    {
                        $consignments = DB::table('consignments')
                                ->join('users', 'users.id', '=' ,'consignments.seller_id')
                                ->join('show_times', 'show_times.id', '=' ,'consignments.show_time_id')
                                ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                ->leftJoin('seats', 'seats.consignment_id', '=' ,'consignments.id')
                                ->leftJoin('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->leftJoin('purchases', 'purchases.id', '=' ,'seats.purchase_id')
                                ->select(DB::raw('consignments.*,shows.name AS show_name,users.first_name,users.last_name,show_times.show_time,users.email,
                                        COUNT(seats.id) AS qty, (CASE WHEN (consignments.created = purchases.created) THEN 1 ELSE 0 END) as purchase,
                                        ROUND(SUM(COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0))+COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0))),2) AS total'))
                                ->where($where)
                                ->where(function ($query) {
                                    return $query->whereNull('seats.status')
                                                 ->orWhere('seats.status','<>','Voided');
                                })
                                ->groupBy('consignments.id')
                                ->orderBy('shows.name','show_times.show_time')
                                ->get();
                        $search['venues'] = Venue::orderBy('name')->get(['id','name']);
                        $search['shows'] = Show::orderBy('name')->get(['id','name','venue_id']);
                    }
                    $sellers = DB::table('users')
                                ->join('user_types', 'user_types.id', '=' ,'users.user_type_id')
                                ->select('users.*')
                                ->where('user_types.user_type','=','Seller')
                                ->orderBy('users.email')
                                ->get();
                    $status = Util::getEnumValues('consignments','status');
                    $status_seat = Util::getEnumValues('seats','status');
                    $sections = Util::getEnumValues('tickets','ticket_type');
                }
                //return view
                return view('admin.consignments.index',compact('consignments','sellers','stages','status','status_seat','sections','search'));
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
                    $consignment->seller_id = $input['seller_id'];
                    if($file)
                        $consignment->set_agreement($file);
                    else
                        $consignment->agreement = null;
                    $consignment->save();
                    if(isset($input['action']) && isset($input['seat']) && count($input['seat']))
                    {
                        $seats = array_unique($input['seat']);
                        if($input['action'] == 'status' && isset($input['status']))
                        {
                           if($consignment->seats()->count() == count($seats) && $input['status']=='Voided')
                           {
                               $consignment->status = $input['status'];
                               $consignment->save();
                           }
                           else
                           {
                                foreach ($seats as $s)
                                {
                                     $purchase_seat = Seat::find($s);
                                     if($purchase_seat)
                                     {
                                         $oldStatus = $purchase_seat->status;
                                         $purchase_seat->status = $input['status'];
                                         $purchase_seat->save();
                                         //if it has purchase change values
                                         if($purchase_seat->purchase_id)
                                         {
                                             $t = DB::table('tickets')
                                                     ->join('seats', 'tickets.id', '=' ,'seats.ticket_id')
                                                     ->select(DB::raw('tickets.id,
                                                                       COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0)) AS retail_price,
                                                                       COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0)) AS processing_fee,
                                                                       COALESCE(seats.fixed_commission,COALESCE(tickets.fixed_commission,0)) AS fixed_commission,
                                                                       COALESCE(seats.percent_commission,COALESCE(tickets.percent_commission,0)) AS percent_commission'))
                                                     ->where('tickets.id','=',$purchase_seat->ticket_id)->first();
                                             $comm = ($t->fixed_commission>0)? $t->fixed_commission : $t->percent_commission*$t->retail_price/100 ;
                                             if($oldStatus == 'Voided' && $purchase_seat->status != $oldStatus)
                                             {
                                                 $purchase = Purchase::find($purchase_seat->purchase_id);
                                                 if($purchase)
                                                 {
                                                     $purchase->increment('quantity',1);
                                                     $purchase->increment('retail_price',$t->retail_price);
                                                     $purchase->increment('processing_fee',$t->processing_fee);
                                                     $purchase->increment('price_paid',$t->retail_price+$t->processing_fee);
                                                     $purchase->increment('commission_percent',$comm);
                                                 }
                                             }
                                             else if($oldStatus != 'Voided' && $purchase_seat->status == 'Voided')
                                             {
                                                 $purchase = Purchase::find($purchase_seat->purchase_id);
                                                 if($purchase)
                                                 {
                                                     $purchase->decrement('quantity',1);
                                                     $purchase->decrement('retail_price',$t->retail_price);
                                                     $purchase->decrement('processing_fee',$t->processing_fee);
                                                     $purchase->decrement('price_paid',$t->retail_price+$t->processing_fee);
                                                     $purchase->decrement('commission_percent',$comm);
                                                 }
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
                                                ->join('seats', 'seats.purchase_id', '=' ,'purchases.id')
                                                ->join('consignments', 'consignments.id', '=' ,'seats.consignment_id')
                                                ->select('purchases.*')
                                                ->where('consignments.id','=',$consignment->id)
                                                ->where('consignments.created','=','purchases.created')
                                                ->first();
                            //if it has purchase on origin(our shows)
                            if($purchaseFrom)
                            {
                                //purchase of consignment destiny
                                $purchaseTo = DB::table('purchases')
                                                ->join('seats', 'seats.purchase_id', '=' ,'purchases.id')
                                                ->join('consignments', 'consignments.id', '=' ,'seats.consignment_id')
                                                ->select('purchases.id')
                                                ->where('consignments.id','=',$input['moveto'])
                                                ->where('consignments.created','=','purchases.created')
                                                ->first();
                                //if no purchase, check if it has tickets at begining(not our shows, alert) - or - create empty
                                if(!$purchaseTo)
                                {
                                    $purchase_seatsHas = DB::table('seats')->where('consignment_id','=',$input['moveto'])->count();
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
                                        $purchaseTo->channel = 'Consignment';
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
                                    $purchase_seat = DB::table('seats')
                                            ->join('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                            ->select(DB::raw('seats.id,
                                                              COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0)) AS retail_price,
                                                              COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0)) AS processing_fee,
                                                              COALESCE(seats.fixed_commission,COALESCE(tickets.fixed_commission,0)) AS fixed_commission,
                                                              COALESCE(seats.percent_commission,COALESCE(tickets.percent_commission,0)) AS percent_commission'))
                                            ->where('seats.id','=',$s)->first();
                                    $comm = ($purchase_seat->fixed_commission>0)? $purchase_seat->fixed_commission : $purchase_seat->percent_commission*$purchase_seat->retail_price/100 ;
                                    //decrement old
                                    $purchaseFrom->decrement('quantity',1);
                                    $purchaseFrom->decrement('retail_price',$purchase_seat->retail_price);
                                    $purchaseFrom->decrement('processing_fee',$purchase_seat->processing_fee);
                                    $purchaseFrom->decrement('price_paid',$purchase_seat->retail_price+$purchase_seat->processing_fee);
                                    $purchaseFrom->decrement('commission_percent',$comm);
                                    //increment new
                                    $purchaseTo->increment('quantity',1);
                                    $purchaseTo->increment('retail_price',$purchase_seat->retail_price);
                                    $purchaseTo->increment('processing_fee',$purchase_seat->processing_fee);
                                    $purchaseTo->increment('price_paid',$purchase_seat->retail_price+$purchase_seat->processing_fee);
                                    $purchaseTo->increment('commission_percent',$comm);
                                }
                                DB::table('seats')->where('id',$s)->update($updates);
                            }
                        }
                        else if($input['action'] == 'showseats' && isset($input['showseats']))
                        {
                           foreach ($seats as $s)
                               DB::table('seats')->where('id',$s)->update(['show_seat'=>$input['showseats']]);
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
                                    ->join('seats', 'seats.purchase_id', '=' ,'purchases.id')
                                    ->select('purchases.id','purchases.status')
                                    ->where('seats.consignment_id','=',$consignment->id)->where('purchases.created','=',$consignment->created)
                                    ->orderBy('purchases.id')
                                    ->groupBy('purchases.id')
                                    ->first();
                            if($purchase)
                                Purchase::where('id',$purchase->id)->update(['status'=>'Active']);
                        }
                        if($oldStatus != 'Voided' && $consignment->status == 'Voided')
                        {
                            $purchase = DB::table('purchases')
                                    ->join('seats', 'seats.purchase_id', '=' ,'purchases.id')
                                    ->select('purchases.id','purchases.status')
                                    ->where('seats.consignment_id','=',$consignment->id)->where('purchases.created','=',$consignment->created)
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
                    $consignment->create_user_id = Auth::user()->id;
                    $consignment->agreement = ($file)? Util::upload_file($file,'consignments') : null;
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
                        $purchase->customer_id = $customer->id;
                        $purchase->session_id = Session::getId();
                        $purchase->referrer_url = Config::get('app.url');
                        $purchase->ticket_type = 'Consignment';
                        $purchase->retail_price = 0;
                        $purchase->processing_fee = 0;
                        $purchase->savings = 0;
                        $purchase->commission_percent = 0;
                        $purchase->price_paid = 0;
                        $purchase->payment_type = 'None';
                        $purchase->merchandise = 0;
                        $purchase->updated = $current;
                        $purchase->created = $current;
                        $purchase->channel = 'Consignment';
                        $purchase->save();
                    }
                    //create consignments seats
                    if((isset($input['seats'])&& $input['seats']))
                    {
                        $purchase_seat = [];
                        $seats = array_unique($input['seats']);
                        foreach ($seats as $s)
                        {
                            $seat = json_decode($s);
                            //create ticket
                            $ticket = Ticket::where('ticket_type','=',$seat->ticket_type)->where('show_id','=',$input['show_id'])->first();
                            if(!$ticket)
                            {
                                $ticket = new Ticket;
                                $ticket->show_id = $input['show_id'];
                                $ticket->ticket_type = $seat->ticket_type;
                                $ticket->retail_price = $seat->retail_price;
                                $ticket->processing_fee = $seat->processing_fee;
                                if(isset($seat->fixed_commission) && !empty($seat->fixed_commission))
                                {
                                    $ticket->percent_commission = 0.00;
                                    $ticket->fixed_commission = $seat->fixed_commission;
                                }
                                else
                                {
                                    $ticket->percent_commission = $seat->percent_commission;
                                    $ticket->fixed_commission = null;
                                }
                                $ticket->audit_user_id = Auth::user()->id;
                                $ticket->updated = $current;
                                $ticket->save();
                            }
                            //insert seat
                            if($ticket && isset($ticket->id))
                            {
                                $new_seat = new Seat;
                                $new_seat->ticket_id = $ticket->id;
                                $new_seat->consignment_id = $consignment->id;
                                $new_seat->seat = $seat->seat;
                                if($seat->retail_price != $ticket->retail_price)
                                    $new_seat->retail_price = $seat->retail_price;
                                if($seat->processing_fee != $ticket->processing_fee)
                                    $new_seat->processing_fee = $seat->processing_fee;
                                if($seat->percent_commission != $ticket->percent_commission)
                                    $new_seat->percent_commission = $seat->percent_commission;
                                if(!empty($seat->fixed_commission) && $seat->fixed_commission != $ticket->fixed_commission)
                                    $new_seat->fixed_commission = $seat->fixed_commission;
                                if(!empty($seat->collect_price))
                                    $new_seat->collect_price = $seat->collect_price;
                                $new_seat->show_seat = $seat->show_seat;
                                $new_seat->status = 'Created';
                                $new_seat->updated = $current;
                                if($input['purchase'] && $purchase)
                                {
                                    if(!empty($new_seat->fixed_commission))
                                        $commission = $new_seat->fixed_commission;
                                    else if(!empty($ticket->fixed_commission))
                                        $commission = $ticket->fixed_commission;
                                    else if(!empty($new_seat->percent_commission))
                                        $commission = $new_seat->percent_commission;
                                    else
                                        $commission = $ticket->percent_commission;
                                    //new values
                                    $ret_pric = ($new_seat->retail_price)? $new_seat->retail_price : $ticket->retail_price;
                                    $pro_fees = ($new_seat->processing_fee)? $new_seat->processing_fee : $ticket->processing_fee;
                                    //fill out purchase
                                    $new_seat->purchase_id = $purchase->id;
                                    $purchase->retail_price += $ret_pric;
                                    $purchase->processing_fee += $pro_fees;
                                    $purchase->commission_percent += $commission;
                                    $purchase->price_paid += $ret_pric + $pro_fees;
                                    $purchase->ticket_id = $ticket->id;
                                    $purchase->save();
                                }
                                $new_seat->save();
                            }
                            else
                                return ['success'=>false,'msg'=>'There was an error saving the ticket: '.$seat->ticket_type.' Seat:'.$seat->seat.'.<br>The server could not retrieve the data.'];
                        }
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
     * View tickets of consignment.
     *
     * @void
     */
    public function tickets($type,$ids,$start=null,$end=null)
    {
        try {
            //check input values
            if(in_array($type,['C','S']))
            {
                $tickets = $purchases_id = [];
                $consignments_ids = explode('-',$ids);
                //loop all consignments to get all purchases id
                foreach ($consignments_ids as $id)
                {
                    $purchase = DB::table('purchases')
                                ->join('seats', 'seats.purchase_id', '=' ,'purchases.id')
                                ->join('consignments', 'consignments.id', '=' ,'seats.consignment_id')
                                ->select('purchases.id')
                                ->where('seats.consignment_id','=',$id)->where('purchases.created','=',Consignment::find($id)->created)
                                ->first();
                    if($purchase)
                        $purchases_id[] = $purchase->id;
                }

                //if it has no purchase throw error
                if(!count($purchases_id))
                {
                    $format='plain'; $type='X';
                    $tickets = '<script>alert("The system could not load the information from the DB. This consignment has not a purchase.");window.close();</script>';
                    return View::make('command.report_sales_receipt_tickets', compact('tickets','type','format'))->render();
                }
                //loop all purchases
                foreach ($purchases_id as $id)
                {
                    $t = Purchase::find($id)->get_receipt()['tickets'];
                    //check for range to show
                    if(!empty($start) && !empty($end) && is_numeric($start) && is_numeric($end))
                        $t = array_slice($t, $start-1, $end-$start+1, true);
                    $tickets = array_merge($tickets,$t);
                }
                //create pdf tickets
                $format = 'pdf';
                $pdf_receipt = View::make('command.report_sales_receipt_tickets', compact('tickets','type','format'));
                if($type == 'S')
                    return PDF::loadHTML($pdf_receipt->render())->setPaper([0,0,396,144],'portrait')->setWarnings(false)->download('TicketBat Admin - tickets - '.$ids.'.pdf');
                return PDF::loadHTML($pdf_receipt->render())->setPaper('a4', 'portrait')->setWarnings(false)->download('TicketBat Admin - tickets - '.$ids.'.pdf');
            }
            else
            {
                $format='plain';
                $tickets = '<script>alert("The system could not load the information from the DB. These are not valid purchases.");window.close();</script>';
                return View::make('command.report_sales_receipt_tickets', compact('tickets','type','format'))->render();
            }

        } catch (Exception $ex) {
            throw new Exception('Error Consignment tickets: '.$ex->getMessage());
        }
    }
    /**
     * Generate contract of consignment.
     *
     * @void
     */
    public function contract($id)
    {
        try {
            $contract = Consignment::generate_contract($id);
            if($contract)
                return PDF::loadHTML($contract->render())->setPaper('a4', 'portrait')->setWarnings(false)->download('TicketBat Admin - Consignment Contract - '.$id.'.pdf');
            else {
                $format='plain';
                $consignment = '<script>alert("There is an error with the server. Please, contact us.");window.close();</script>';
                return View::make('command.consignment_contract', compact('consignment','format'))->render();
            }
        } catch (Exception $ex) {
            throw new Exception('Error Consignment tickets: '.$ex->getMessage());
        }
    }
    /**
     * View consignment agreement.
     *
     * @void
     */
    public function view($format,$id)
    {
        try {
            $consignment = Consignment::find($id);
            //check agreement data sent
            if($consignment && isset($consignment->agreement))
            {
                //check format
                if($format==='file')
                {
                    $file = str_replace('/s3/','',$consignment->agreement);
                    $exists = Storage::disk('s3')->exists($file);
                    if($exists)
                    {
                        $file = Storage::disk('s3')->get($file);
                        return Response::make($file, 201, [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'inline; filename="Consignment_Agreement_'.$id.'" filename*="Consignment_Agreement_'.$id.'"'
                        ]);
                    }
                    else
                        return '<script>alert("The system could not load the information from the DB. It does not exists.");window.close();</script>';
                }
                else
                    return '<script>alert("The system could not load the information from the DB. It has not a valid format.");window.close();</script>';
            }
            else
                return '<script>alert("The system could not load the information from the DB. There is not that file.");window.close();</script>';
        } catch (Exception $ex) {
            throw new Exception('Error Consignments View: '.$ex->getMessage());
        }
    }
}
