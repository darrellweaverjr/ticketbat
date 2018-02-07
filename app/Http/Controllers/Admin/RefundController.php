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
use App\Http\Models\TransactionRefund;
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
    public function save()
    {
        try {
            //init
            $input = Input::all();
            $current = date('Y-m-d H:i:s');
            //function refund each
            function refund_each($purchase, $user, $amount, $description, $current)
            {
                if($amount>0.01 && $amount<=$purchase->price_paid)
                {
                    $refunded = TransactionRefund::usaepay($purchase, $user, $amount, $description, $current);
                    if($refunded['success'])
                    {
                        $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Refunded $'.$amount.'/ $'.$purchase->price_paid;
                        $purchase->note = ($purchase->note)? $purchase->note.$note : $note;  
                        $purchase->status = 'Chargeback';
                        $purchase->updated = $current;
                        $purchase->save();
                        return ['success'=>true, 'id'=>$purchase->id, 'msg'=>$refunded['msg']];
                    }
                    else
                    {
                        $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Intented to refund $'.$amount.'/ $'.$purchase->price_paid;
                        $purchase->note = ($purchase->note)? $purchase->note.$note : $note; 
                        $purchase->save();
                        return ['success'=>false, 'id'=>$purchase->id, 'msg'=>$refunded['msg']];
                    }
                }
                return ['success'=>false, 'id'=>$purchase->id, 'msg'=>'This is an invalid amount to refund in that purchase'];
            }
            //save all record      
            if($input && isset($input['id']) && isset($input['type']))
            {
                $purchase = Purchase::find($input['id']);
                if($purchase && $purchase->transaction_id && $purchase->price_paid>0)
                {
                    $user = Auth::user();
                    $description = (!empty(trim($input['description'])))? trim($input['description']) : null;
                    if($input['type']=='current_purchase')
                    {
                        $refunded = refund_each($purchase, $user, $purchase->price_paid, $description, $current);
                        if($refunded['success'])
                            return ['success'=>true,'msg'=>'Purchase #'.$purchase->id.' refunded successfully!<br>'.$refunded['msg']];
                        return ['success'=>false, 'msg'=>'There was an error trying to refund the purchase #'.$purchase->id.'<br>'.$refunded['msg']];
                    }  
                    else if($input['type']=='full_transaction')
                    {
                        $msg = '';
                        $success = $errors = [];
                        $purchases = $purchase->transaction->purchases();
                        foreach ($purchases as $p)
                        {
                            $refunded = refund_each($p, $user, $p->price_paid, $description, $current);
                            if($refunded['success'])
                                $success[$p->id] = $refunded['msg'];
                            else
                                $errors[$p->id] = $refunded['msg'];
                        }
                        if(count($success))
                        {
                            $msg .= 'These purchases where successfully refunded:<br>';
                            foreach ($success as $k=>$v)
                                $msg .= ' - #'.$k.' => '.$v.'<br>';
                        }
                        if(count($errors))
                        {
                            $msg .= 'These purchases had errors trying to refund them:<br>';
                            foreach ($errors as $k=>$v)
                                $msg .= ' - #'.$k.' => '.$v.'<br>';
                        }
                        if(count($success))
                            return ['success'=>true,'msg'=>$msg];
                        return ['success'=>false, 'msg'=>$msg];
                    }  
                    else if($input['type']=='custom_amount')
                    {
                        if(!empty($input['amount']) && $input['amount']<=$purchase->price_paid  && $input['amount']>0)
                        {
                            $refunded = refund_each($purchase, $user, $input['amount'], $description, $current);
                            if($refunded['success'])
                                return ['success'=>true,'msg'=>'Purchase #'.$purchase->id.' refunded successfully!<br>'.$refunded['msg']];
                            return ['success'=>false, 'msg'=>'There was an error trying to refund the purchase #'.$purchase->id.'<br>'.$refunded['msg']];
                        }
                        return ['success'=>false, 'msg'=>'The amount to refund must be greater than $0.00 and less or equal to $'.$purchase->price_paid];
                    }  
                    else 
                        return ['success'=>false,'msg'=>'There was an error refunding.<br>Invalid option.'];
                }
                return ['success'=>false,'msg'=>'There was an error refunding.<br>That purchase is not longer available to refund.'];
            }
            return ['success'=>false,'msg'=>'There was an error refund.<br>Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error Purchases Save: '.$ex->getMessage());
        }
    }
}                    