<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Models\User;
use App\Http\Models\Util;
use App\Http\Models\ShowTime;
use App\Http\Models\Seat;

/**
 * Manage options for Loggued users in the app
 *
 * @author ivan
 */
class UserController extends Controller{
    
    private $check_tickets_hours_after = 6;
    private $check_tickets_hours_before = 24;
    
    /*
     * List My purchases
     */
    public function purchases()
    {
        try {   
            $info = Input::all();   
            if(!empty($info['user_id']) && is_numeric($info['user_id']))
            {
                $purchases = DB::table('purchases')
                            ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                            ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                            ->join('shows', 'shows.id', '=', 'show_times.show_id')
                            ->join('venues', 'venues.id', '=', 'shows.venue_id')
                            ->join('tickets', 'tickets.id', '=', 'purchases.ticket_id')
                            ->join('packages', 'packages.id', '=', 'tickets.package_id')
                            ->leftJoin('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                            ->select(DB::raw('purchases.id, purchases.quantity, tickets.ticket_type AS ticket_type_type, purchases.user_id, DATE_FORMAT(purchases.created,"%m/%d/%Y %T") AS created,
                                              IF(transactions.amount IS NOT NULL,transactions.amount,purchases.price_paid) AS amount, NULL AS tickets,
                                              venues.name AS venue_name, DATE_FORMAT(show_times.show_time,"%m/%d/%Y %T") AS show_time, shows.name AS show_name, packages.title'))
                            ->where('purchases.status','=','Active')->where('purchases.user_id','=',$info['user_id'])
                            ->orderBy('purchases.created','DESC')
                            ->get();
                foreach ($purchases as $p)
                {
                    //available only after check hours later
                    if(strtotime('+ '.$this->check_tickets_hours_after.' hours') <= strtotime($p->show_time))
                    {
                        $p->tickets = [];
                        $tickets = range(1,$p->quantity);
                        foreach ($tickets as $t)
                            $p->tickets[] = ['number'=>$t,'code'=>Util::getQRcode($p->id,$p->user_id,$t,400)];
                    }
                }
                return Util::json(['success'=>true, 'purchases'=>$purchases]);
            }
            return Util::json(['success'=>false, 'msg'=>'You must be logged to this option!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }   
    
    /*
     * List all purchases that the user is allow to check
     */
    public function purchases_to_check()
    {
        try {
            $info = Input::all();   
            $purchases = [];
            if(!empty($info['show_time_id']) && is_numeric($info['show_time_id']))
            {
                $purchases = DB::table('purchases')
                            ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                            ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                            ->join('shows', 'shows.id', '=', 'show_times.show_id')
                            ->join('venues', 'venues.id', '=', 'shows.venue_id')
                            ->join('tickets', 'tickets.id', '=', 'purchases.ticket_id')
                            ->join('packages', 'packages.id', '=', 'tickets.package_id')
                            ->leftJoin('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                            ->select(DB::raw('purchases.id, purchases.quantity, tickets.ticket_type AS ticket_type_type,
                                              IF(transactions.amount IS NOT NULL,transactions.amount,purchases.price_paid) AS amount, 
                                              customers.first_name, customers.last_name'))
                            ->where('purchases.status','=','Active')->where('purchases.show_time_id','=',$info['show_time_id'])
                            ->orderBy('purchases.created','DESC')
                            ->get();
                foreach ($purchases as $p)
                {
                    //init variables
                    $p->tickets = $p->checked = 0; 
                    $p->to_check = [];
                    $tickets = DB::table('ticket_number')->select('id','tickets','checked')->where('purchases_id','=',$p->id)->get();
                    foreach ($tickets as $t)
                    {
                        $qty_tck = (!empty($t->tickets))? explode(',',$t->tickets) : [];
                        $checked = (!empty($t->checked))? explode(',',$t->checked) : [];
                        $p->tickets += count($qty_tck);
                        $p->checked += count($checked);
                        $p->to_check = array_merge($p->to_check,array_diff($qty_tck, $checked));
                    }
                }
            }
            return Util::json(['success'=>true, 'purchases'=>$purchases]);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }   
    /*
     * List all venues that the user is allow to check
     */
    public function venues_to_check()
    {
        try {
            $info = Input::all();   
            $venues = [];
            if(!empty($info['user_id']) && is_numeric($info['user_id']))
            {
                $venues_check_ticket = User::where('id','=',$info['user_id'])->first(['venues_check_ticket']);
                if($venues_check_ticket && $venues_check_ticket->venues_check_ticket)
                    $venues = explode(',',$venues_check_ticket->venues_check_ticket);
            }
            return Util::json(['success'=>true, 'venues'=>$venues]);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }   
    
    /*
     * List all events that the user is allow to check
     */
    public function events_to_check()
    {
        try {
            $info = Input::all();   
            $events = [];
            if(!empty($info['show_id']) && is_numeric($info['show_id']))
            {
                $events = DB::table('show_times')
                                    ->select(DB::raw('show_times.id, show_times.show_id, DATE_FORMAT(show_times.show_time,"%m/%d/%Y %H:%i:%s") AS show_time'))
                                    ->where('show_id',$info['show_id'])
                                    ->where('show_time','<=',date('Y-m-d H:i:s',strtotime(' + '.$this->check_tickets_hours_before.' hours')))
                                    ->where('show_time','>=',date('Y-m-d H:i:s',strtotime(' - '.$this->check_tickets_hours_after.' hours')))
                                    ->get();
            }
            return Util::json(['success'=>true, 'events'=>$events]);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }   
    
    /*
     * Check Tickets
     */
    public function check_tickets()
    {
        try {
            $info = Input::all();   
            if(!empty($info['purchase_id']) && is_numeric($info['purchase_id']) && !empty($info['tickets'])
            && !empty($info['show_time_id']) && is_numeric($info['show_time_id']))
                return Util::json($this->update_tickets($info['purchase_id'],null,$info['tickets'],$info['show_time_id']));
            return Util::json(['success'=>false, 'msg'=>'You must send a valid request!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }   
    
    /*
     * Check Tickets
     */
    public function scan_tickets()
    {
        try {
            $info = Input::all();
            $regex = '/^TB[0-9]{12,17}$/';			
            if (preg_match($regex,$info['code']) && !empty($info['show_time_id']) && is_numeric($info['show_time_id']))
            {
                $purchase_id = ltrim(substr($info['code'],2,6),'0');
                $user_id = ltrim(substr($info['code'],8,5),'0');
                $ticket = substr($info['code'],13);
                return Util::json($this->update_tickets($purchase_id,$user_id,$ticket,$info['show_time_id']));
            }
            return Util::json(['success'=>false, 'msg'=>'You must scan a valid code for a valid event date!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }   
    
    /*
     * Update checked/scanned Tickets
     */
    private function update_tickets($purchase_id, $user_id, $ticket,$show_time_id)
    {
        try {
            $to_check = explode(',',$ticket);
            //get purchase info mix  
            $purchase = DB::table('purchases')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                            ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                            ->join('shows', 'shows.id', '=', 'show_times.show_id')
                            ->join('venues', 'venues.id', '=', 'shows.venue_id')
                            ->join('customers', 'customers.id', '=', 'purchases.customer_id')
                            ->leftJoin('ticket_number', 'ticket_number.purchases_id', '=', 'purchases.id')
                            ->select(DB::raw('purchases.id, purchases.quantity, tickets.ticket_type AS ticket_type_type, DATE_FORMAT(show_times.show_time,"%m/%d/%Y %H:%i:%s") AS show_time, 
                                              packages.title, shows.name AS show_name, shows.restrictions, venues.name AS venue_name, tickets.avail_hours,
                                              customers.first_name, customers.last_name, IF(ticket_number.id IS NULL, 0, 1) as section'))
                            ->where('purchases.id','=',$purchase_id)->where('show_times.id','=',$show_time_id)->groupBy('purchases.id')->first();
            if($purchase)
            {
                //purchase checking
                if(strtotime('+ '.$this->check_tickets_hours_before.' hours') < strtotime($purchase->show_time))
                    return ['success'=>false, 'msg'=>'You can only check tickets '.$this->check_tickets_hours_before.' hours before the event starts!'];
                if(strtotime('- '.$this->check_tickets_hours_after.' hours') > strtotime($purchase->show_time))
                    return ['success'=>false, 'msg'=>'You can only check tickets '.$this->check_tickets_hours_after.' hours after the event starts!'];
                //check when purchase section 
                if($purchase->section)
                {
                    $tickets = DB::table('ticket_number')
                                    ->select(DB::raw('SUM( IF(tickets, (LENGTH(tickets)-LENGTH(REPLACE(tickets,",",""))+1), 0) ) AS qty, 
                                                      SUM( IF(checked, (LENGTH(checked)-LENGTH(REPLACE(checked,",",""))+1), 0) ) AS checked'))
                                    ->where('purchases_id',$purchase->id)->groupBy('purchases_id')->first();
                    $purchase->tickets = $tickets->qty;
                    $purchase->checked = $tickets->checked;
                    $tickets = DB::table('ticket_number')
                                    ->select(DB::raw('id, tickets, checked, updated, 
                                                      (LENGTH(tickets)-LENGTH(REPLACE(tickets,",",""))+1) AS qty'))
                                    ->where('purchases_id','=',$purchase->id)->get();
                    foreach ($tickets as $t)
                    {
                        //init variables
                        $checked = (!empty($t->checked))? explode(',',$t->checked) : [];
                        $qty_tck = (!empty($t->tickets))? explode(',',$t->tickets) : [];
                        //find tickets already checked
                        $already = array_intersect($checked,$to_check);
                        if(count($already))
                        {
                            //check if reentry
                            if(!empty($purchase->avail_hours) && $t->updated && date('Y-m-d H:i')<= date('Y-m-d H:i', strtotime($t->updated. ' + '.$purchase->avail_hours.' hours'))  )
                                return ['success'=>true, 'purchase'=>$purchase];
                            return ['success'=>false, 'msg'=>'These tickets are already checked: '.implode(',',$already)];
                        } 
                        //find tickets to update
                        $to_update = array_intersect($qty_tck,$to_check);
                        if(count($to_update))
                        {
                            $t_updated = implode(',',array_merge($checked,$to_update));
                            //continue to update
                            $updated = DB::table('ticket_number')->where('id',$t->id)->update(['checked' => $t_updated]);
                            if($updated>=0)
                            {
                                if(!empty($purchase->avail_hours))
                                    DB::table('ticket_number')->where('id',$t->id)->update(['updated' => date('Y-m-d H:i')]);
                                $purchase->checked += count($to_update);
                                $to_check = array_diff($to_check, $to_update);
                            } 
                            else    
                                return ['success'=>false, 'msg'=>'There was an error updating these tickets: '.$t_updated];
                        }
                    }
                    //after check all valid tickets there is something missing 
                    if(count($to_check))
                        return ['success'=>false, 'msg'=>'These tickets are not valid: '.implode(',',$to_check)];
                }
                //when buy a seat instead of section
                else
                {
                    $seats = DB::table('seats')->select(DB::raw('SUM( IF(status="Checked",1,0) ) AS checked, SUM( IF(status="Sold",1,0) ) AS sold'))
                                               ->where('purchase_id',$purchase->id)->groupBy('purchase_id')->first();
                    if(!$seats)
                        return ['success'=>false, 'msg'=>'This purchase has not seats asigned!'];
                    //update qty checked for purchase
                    $purchase->tickets = $seats->checked + $seats->sold;
                    $purchase->checked = $seats->checked;
                    foreach ($to_check as $t)
                    {
                        $seat = Seat::find($t);
                        //if seat does not exists
                        if(!$seat)
                            return ['success'=>false, 'msg'=>'This ticket is not valid: '.$t];
                        if($seat->status == 'Checked')
                        {
                            //check if reentry
                            if(!empty($purchase->avail_hours) && $seat->updated && date('Y-m-d H:i')<= date('Y-m-d H:i', strtotime($seat->updated. ' + '.$purchase->avail_hours.' hours'))  )
                                return ['success'=>true, 'purchase'=>$purchase];
                            return ['success'=>false, 'msg'=>'This ticket has been checked already: '.$t];
                        }  
                        if($seat->status != 'Sold')
                            return ['success'=>false, 'msg'=>'This ticket does not have a valid status: '.$t];
                        $seat->status = 'Checked';
                        $seat->updated = date('Y-m-d H:i:s');
                        if(!$seat->save())
                            return ['success'=>false, 'msg'=>'There was an error checking this ticket: '.$t];
                        else
                            $purchase->checked++;
                    }
                }
                //prepare message for successful text
                if($user_id)
                    return ['success'=>true, 'purchase'=>$purchase];
                return ['success'=>true];
            }
            return ['success'=>false, 'msg'=>'The system could not load the tickets for that purchase!'];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }   
    
}
