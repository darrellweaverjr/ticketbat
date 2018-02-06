<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Http\Models\Purchase;
use App\Http\Models\Venue;
use App\Http\Models\Discount;
use App\Http\Models\User;
use App\Http\Models\Customer;
use App\Http\Models\Show;
use App\Http\Models\Ticket;
use App\Http\Models\ShowTime;
use App\Http\Models\Transaction;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Models\Util;
use App\Mail\EmailSG;

/**
 * Manage Refunds
 *
 * @author ivan
 */
class RefundController extends Controller{
    
    /**
     * List all purchases and return default view.
     *
     * @return view
     */
    public function index()
    {
        try {
            //init
            $refunds = [];
            //if user has permission to view                
            if(in_array('View',Auth::user()->user_type->getACLs()['REFUNDS']['permission_types']))
            {
                if(Auth::user()->user_type->getACLs()['REFUNDS']['permission_scope'] != 'All')
                {
                    $refunds = DB::table('transaction_refunds')
                                ->join('purchases', 'purchases.id', '=' ,'transaction_refunds.purchase_id')
                                ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                                ->join('users', 'users.id', '=' ,'transaction_refunds.user_id')
                                ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                                ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                                ->join('shows', 'shows.id', '=', 'show_times.show_id')
                                ->join('venues', 'venues.id', '=', 'shows.venue_id')
                                ->join('tickets', 'tickets.id', '=', 'purchases.ticket_id')
                                ->join('packages', 'packages.id', '=', 'tickets.package_id')
                                ->join('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                                ->select(DB::raw('transaction_refunds.*, purchases.id AS order_id, transactions.card_holder, transactions.authcode, transactions.refnum, transactions.last_4,
                                                  transactions.amount, purchases.note,
                                                  discounts.code, tickets.ticket_type AS ticket_type_type,venues.name AS venue_name,
                                                  users.first_name AS u_first_name, users.last_name AS u_last_name, users.email AS u_email, 
                                                  customers.first_name, customers.last_name, customers.email, customers.phone,
                                                  show_times.show_time, shows.name AS show_name, packages.title'))
                                >whereIn('shows.venue_id',[Auth::user()->venues_edit])
                                ->orderBy('purchases.created','transaction_refunds.created')
                                ->groupBy('transaction_refunds.id')
                                ->get();
                }//all
                else
                {
                    $refunds = DB::table('transaction_refunds')
                                ->join('purchases', 'purchases.id', '=' ,'transaction_refunds.purchase_id')
                                ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                                ->join('users', 'users.id', '=' ,'transaction_refunds.user_id')
                                ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                                ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                                ->join('shows', 'shows.id', '=', 'show_times.show_id')
                                ->join('venues', 'venues.id', '=', 'shows.venue_id')
                                ->join('tickets', 'tickets.id', '=', 'purchases.ticket_id')
                                ->join('packages', 'packages.id', '=', 'tickets.package_id')
                                ->join('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                                ->select(DB::raw('transaction_refunds.*, purchases.id AS order_id, transactions.card_holder, transactions.authcode, transactions.refnum, transactions.last_4,
                                                  transactions.amount, purchases.note,
                                                  discounts.code, tickets.ticket_type AS ticket_type_type,venues.name AS venue_name,
                                                  users.first_name AS u_first_name, users.last_name AS u_last_name, users.email AS u_email, 
                                                  customers.first_name, customers.last_name, customers.email, customers.phone,
                                                  show_times.show_time, shows.name AS show_name, packages.title'))
                                ->orderBy('purchases.created','transaction_refunds.created')
                                ->groupBy('transaction_refunds.id')
                                ->get();
                }   
            }
            return view('admin.refunds.index',compact('refunds'));
        } catch (Exception $ex) {
            throw new Exception('Error Refunds Index: '.$ex->getMessage());
        }
    }
    
    /**
     * List all purchases and return default view.
     *
     * @return view
     */
    public function pendings()
    {
        try {
            //init
            $purchases = [];
            //if user has permission to view                
            if(in_array('View',Auth::user()->user_type->getACLs()['REFUNDS']['permission_types']))
            {
                if(Auth::user()->user_type->getACLs()['REFUNDS']['permission_scope'] != 'All')
                {
                    $purchases = DB::table('purchases')
                                ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                                ->join('users', 'users.id', '=' ,'purchases.user_id')
                                ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                                ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                                ->join('shows', 'shows.id', '=', 'show_times.show_id')
                                ->join('venues', 'venues.id', '=', 'shows.venue_id')
                                ->join('tickets', 'tickets.id', '=', 'purchases.ticket_id')
                                ->join('packages', 'packages.id', '=', 'tickets.package_id')
                                ->join('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                                ->select(DB::raw('purchases.*, transactions.card_holder, transactions.authcode, transactions.refnum, transactions.last_4,
                                                  transactions.amount AS amount, 
                                                  ( CASE WHEN (purchases.ticket_type = "Consignment") THEN purchases.ticket_type 
                                                    WHEN (purchases.ticket_type != "Consignment") AND (tickets.retail_price<0.01) THEN "Free" 
                                                    ELSE purchases.payment_type END ) AS method,
                                                  transactions.id AS color,
                                                  discounts.code, tickets.ticket_type AS ticket_type_type,venues.name AS venue_name,
                                                  users.first_name AS u_first_name, users.last_name AS u_last_name, users.email AS u_email, users.phone AS u_phone,
                                                  customers.first_name, customers.last_name, customers.email, customers.phone,
                                                  show_times.show_time, shows.name AS show_name, packages.title'))
                                ->where('purchases.status','like','Pending%')
                                >whereIn('shows.venue_id',[Auth::user()->venues_edit])
                                ->orderBy('purchases.created','purchases.transaction_id','purchases.user_id','purchases.price_paid')
                                ->groupBy('purchases.id')
                                ->get();
                }//all
                else
                {
                    $purchases = DB::table('purchases')
                                ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                                ->join('users', 'users.id', '=' ,'purchases.user_id')
                                ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                                ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                                ->join('shows', 'shows.id', '=', 'show_times.show_id')
                                ->join('venues', 'venues.id', '=', 'shows.venue_id')
                                ->join('tickets', 'tickets.id', '=', 'purchases.ticket_id')
                                ->join('packages', 'packages.id', '=', 'tickets.package_id')
                                ->join('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                                ->select(DB::raw('purchases.*, transactions.card_holder, transactions.authcode, transactions.refnum, transactions.last_4,
                                                  transactions.amount AS amount, 
                                                  ( CASE WHEN (purchases.ticket_type = "Consignment") THEN purchases.ticket_type 
                                                    WHEN (purchases.ticket_type != "Consignment") AND (tickets.retail_price<0.01) THEN "Free" 
                                                    ELSE purchases.payment_type END ) AS method,
                                                  transactions.id AS color,
                                                  discounts.code, tickets.ticket_type AS ticket_type_type,venues.name AS venue_name,
                                                  users.first_name AS u_first_name, users.last_name AS u_last_name, users.email AS u_email, users.phone AS u_phone,
                                                  customers.first_name, customers.last_name, customers.email, customers.phone,
                                                  show_times.show_time, shows.name AS show_name, packages.title'))
                                ->where('purchases.status','like','Pending%')
                                ->orderBy('purchases.created','purchases.transaction_id','purchases.user_id','purchases.price_paid')
                                ->groupBy('purchases.id')
                                ->get();
                }   
            }
            return view('admin.refunds.pendings',compact('purchases'));
        } catch (Exception $ex) {
            throw new Exception('Error Refunds Pendings: '.$ex->getMessage());
        }
    }
    
    /**
     * Refund purchase.
     *
     * @void
     */
    public function refund()
    {
        try {
            //init
            $input = Input::all();
            $current = date('Y-m-d H:i:s');
            //save all record      
            if($input && isset($input['id']))
            {
                $purchase = Purchase::find($input['id']);
                if(isset($input['status']))
                {
                    //update status
                    $old_status = $purchase->status;
                    $purchase->status = $input['status'];
                    $note = '&nbsp;<br><b>'.Auth::user()->first_name.' '.Auth::user()->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Change ';
                    $note.= ' status from '.$old_status.' to '.$input['status'];
                    $purchase->note = ($purchase->note)? $purchase->note.$note : $note;  
                    $purchase->updated = $current;
                    $purchase->save();
                    //send emails for pending status
                    if(preg_match('/^Pending/',$input['status']))
                    {
                        $sent = $purchase->set_pending();
                        if(!$sent)
                            return ['success'=>false,'msg'=>'The system updated the status.<br>But the email could not be sent to the admin.'];
                    }
                    //re-send email if change form active to any inactive and viceversa
                    else if($input['status']=='Active' || $old_status=='Active' || preg_match('/^Pending/',$old_status))
                    {
                        $receipt = $purchase->get_receipt();
                        $status = ($input['status']=='Active')? 'ACTIVATED' : ( ($input['status']=='Chargeback')? 'CHARGEBACK' :'CANCELED' );
                        $sent = Purchase::email_receipts($status.': TicketBat Purchase',[$receipt],'receipt',$status,true);
                        if(!$sent)
                            return ['success'=>false,'msg'=>'The purchase changed the status.<br>But the email could not be sent to the customer and the venue.'];
                    }
                    return ['success'=>true,'msg'=>'Purchase saved successfully!','note'=>$purchase->note];
                }                    
                else if(isset($input['note']))
                {                    
                    $note = '&nbsp;<br><b>'.Auth::user()->first_name.' '.Auth::user()->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b>'.strip_tags($input['note']).'&nbsp;';
                    $purchase->note = $purchase->note.$note;
                    $purchase->updated = $current;
                    $purchase->save();
                    return ['success'=>true,'msg'=>'Purchase saved successfully!','note'=>$purchase->note];
                }               
                else return ['success'=>false,'msg'=>'There was an error saving the purchase.<br>Invalid data.'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the purchase.<br>Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error Purchases Save: '.$ex->getMessage());
        }
    }
}                    