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
            $pendings = $this->pendings();
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
                                                  transactions.amount, purchases.note, purchases.quantity, purchases.retail_price, purchases.processing_fee, purchases.commission_percent,
                                                  discounts.code, tickets.ticket_type AS ticket_type_type,venues.name AS venue_name, purchases.savings, purchases.status,
                                                  users.first_name AS u_first_name, users.last_name AS u_last_name, users.email AS u_email,
                                                  purchases.payment_type AS method,
                                                  customers.first_name, customers.last_name, customers.email, customers.phone,
                                                  show_times.show_time, shows.name AS show_name, packages.title'))
                                ->whereIn('shows.venue_id',[Auth::user()->venues_edit])
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
                                                  transactions.amount, purchases.note, purchases.quantity, purchases.retail_price, purchases.processing_fee, purchases.commission_percent,
                                                  discounts.code, tickets.ticket_type AS ticket_type_type,venues.name AS venue_name, purchases.savings, purchases.status,
                                                  users.first_name AS u_first_name, users.last_name AS u_last_name, users.email AS u_email,
                                                  purchases.payment_type AS method,
                                                  customers.first_name, customers.last_name, customers.email, customers.phone,
                                                  show_times.show_time, shows.name AS show_name, packages.title'))
                                ->orderBy('purchases.created','DESC')->orderBy('transaction_refunds.created','DESC')
                                ->groupBy('transaction_refunds.id')
                                ->get();
                }
            }
            return view('admin.refunds.index',compact('refunds','pendings'));
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
                                ->leftJoin('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                                ->select(DB::raw('purchases.*, transactions.card_holder, 
                                                  transactions.authcode, transactions.refnum, transactions.last_4,
                                                  transactions.amount AS amount,
                                                  purchases.payment_type AS method, IF(transactions.id,0,1) AS skip,
                                                  IF(transactions.id, transactions.id, purchases.created) AS color,
                                                  discounts.code, tickets.ticket_type AS ticket_type_type,venues.name AS venue_name,
                                                  users.first_name AS u_first_name, users.last_name AS u_last_name, users.email AS u_email, users.phone AS u_phone,
                                                  customers.first_name, customers.last_name, customers.email, customers.phone,
                                                  show_times.show_time, shows.name AS show_name, packages.title'))
                                ->where('purchases.status','like','Pending%')
                                ->whereIn('shows.venue_id',[Auth::user()->venues_edit])
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
                                ->leftJoin('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                                ->select(DB::raw('purchases.*, transactions.card_holder, transactions.authcode, transactions.refnum, transactions.last_4,
                                                  transactions.amount AS amount,
                                                  purchases.payment_type AS method, IF(transactions.id,0,1) AS skip,
                                                  IF(transactions.id, transactions.id, purchases.created) AS color,
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
            return $purchases;
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
            function refund_each($purchase, $user, $amount, $description, $current, $partial=false)
            {
                if($purchase->payment_type != 'Credit')
                {
                    $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Change status to Refunded.';
                    $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                    $purchase->status = 'Refunded';
                    $purchase->updated = $current;
                    $purchase->save();
                    return ['success'=>true, 'id'=>$purchase->id, 'msg'=>$note];
                }
                else if($purchase->payment_type == 'Credit' && $purchase->transaction && $purchase->transaction->trans_result=='Approved')
                {
                    if($amount>0.01 && $amount<=$purchase->price_paid)
                    {
                        $refunded = TransactionRefund::usaepay($purchase, $user, $amount, $description, $current);
                        if($refunded['success'])
                        {
                            $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Refunded $'.$amount.'/ $'.$purchase->price_paid;
                            $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                            if($partial)
                            {
                                $purchase->status = 'Active';
                                $purchase->price_paid -= $amount;
                            }
                            else
                            {
                                $purchase->status = 'Refunded';
                            }
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
                else
                    return ['success'=>false, 'id'=>$purchase->id, 'msg'=>'That purchase has not a valid transaction to refund from'];
            }
            //save all record
            if($input && isset($input['id']) && isset($input['type']))
            {
                $purchase = Purchase::find($input['id']);
                if($purchase && $purchase->price_paid>0)
                {
                    $user = Auth::user();
                    $description = (!empty(trim($input['description'])))? trim($input['description']) : null;
                    if($input['type']=='update_purchase')
                    {
                        $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Change status to Refunded.';
                        $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                        $purchase->status = 'Refunded';
                        $purchase->updated = $current;
                        if($purchase->save())
                            return ['success'=>true,'msg'=>'Purchase #'.$purchase->id.' status updated successfully!<br>'.$note];
                        return ['success'=>false, 'msg'=>'There was an error trying to update the status of the purchase #'.$purchase->id.'<br>'.$note];
                    }
                    else if($purchase->transaction_id)
                    {
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
                            $purchases = $success = $errors = [];
                            if(!empty($purchase->transaction_id))
                            {
                                $transaction = Transaction::find($purchase->transaction_id);
                                if($transaction)
                                    $purchases = $transaction->purchases();
                                else
                                    return ['success'=>false, 'msg'=>'There is not a valid Transaction associate to that Purchase'];
                            }
                            else
                            {
                                $purchases = Purchase::where('transaction_id',$purchase->transaction_id)
                                                     ->where('user_id',$purchase->user_id)
                                                     ->where('created',$purchase->created)->get();
                            }
                            //loop by purchases
                            if(!empty($purchases))
                            {
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
                            return ['success'=>false, 'msg'=>'That Transaction has no Purchases associates'];
                        }
                        else if($input['type']=='custom_amount')
                        {
                            if(!empty($input['amount']) && $input['amount']<$purchase->price_paid  && $input['amount']>0)
                            {
                                $refunded = refund_each($purchase, $user, $input['amount'], $description, $current,true);
                                if($refunded['success'])
                                    return ['success'=>true,'msg'=>'Purchase #'.$purchase->id.' refunded successfully!<br>'.$refunded['msg']];
                                return ['success'=>false, 'msg'=>'There was an error trying to refund the purchase #'.$purchase->id.'<br>'.$refunded['msg']];
                            }
                            return ['success'=>false, 'msg'=>'The amount to refund must be greater than $0.00 and less than $'.$purchase->price_paid];
                        }
                        else
                            return ['success'=>false,'msg'=>'There was an error refunding.<br>Invalid option.'];
                    }
                    return ['success'=>false,'msg'=>'There was an error refunding.<br>That purchase cannot be refunded.'];
                }
                return ['success'=>false,'msg'=>'There was an error refunding.<br>That purchase is not longer available to refund.'];
            }
            return ['success'=>false,'msg'=>'There was an error refund.<br>Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error Purchases Save: '.$ex->getMessage());
        }
    }
}
