<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Purchase;
use App\Http\Models\Transaction;
use App\Http\Models\TransactionRefund;

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
                                                  purchases.payment_type AS method, purchases.printed_fee,
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
                                                  purchases.payment_type AS method, purchases.printed_fee,
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
            $response = [];
            $user = Auth::user();
            
            function create_refund($purchase,$user,$description,$current,$status)
            {
                $transaction = new TransactionRefund;
                $transaction->purchase_id = $purchase->id;
                $transaction->user_id = $user->id;
                $transaction->amount = $purchase->price_paid;
                $transaction->description = (!empty($description))? $description : null;
                $transaction->result = 'Approved';
                $transaction->error = 'Manually changed purchase to '.$status;
                $transaction->created = $current;
                $transaction->payment_type = $purchase->payment_type;
                return $transaction->save();
            }   
            
            if($input && !empty($input['id']) && isset($input['type']))
            {
                $id = explode('-', $input['id']);
                $description = (!empty(trim($input['description'])))? trim($input['description']) : null;
                if(!empty($id))
                {
                    //try to refund each purchase 
                    foreach ($id as $i)
                    {
                        $purchase = Purchase::find($i);
                        if($purchase)
                        {
                            //check amount to refun
                            if($purchase->price_paid>0)
                            {
                                $amount = $purchase->price_paid;
                                //check action to process
                                if($input['type']=='current_purchase')
                                {
                                    //refund credit card thru USAePay
                                    if($purchase->payment_type == 'Credit')
                                    {
                                        if($purchase->transaction && $purchase->transaction->trans_result=='Approved')
                                        {
                                            $refunded = TransactionRefund::where('purchase_id',$purchase->id)->where('result','=','Approved')->first();
                                            if($refunded)
                                            {
                                                $purchase->status = 'Refunded';
                                                $purchase->refunded = $refunded->created;
                                                $purchase->save();
                                                $response[$i] = 'Already refunded';
                                            }
                                            else
                                            {
                                                $refunded = TransactionRefund::usaepay($purchase, $user, $amount, $description, $current);
                                                if($refunded['success'])
                                                {
                                                    $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Refunded $'.$amount;
                                                    $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                                    $purchase->status = 'Refunded';
                                                    $purchase->refunded = $current;
                                                    $purchase->save();
                                                    $response[$i] = 'Done successfully!';
                                                }
                                                else
                                                {
                                                    $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Intented to refund $'.$amount;
                                                    $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                                    $purchase->save();
                                                    $response[$i] = 'Intent to refund failed ('.$purchase->payment_type.').';
                                                }
                                            }
                                        }
                                        else
                                            $response[$i] = 'That purchase has no a valid transaction.';
                                    }
                                    //refund cash
                                    else
                                    {
                                        $refunded = create_refund($purchase,$user,$description,$current,'Refunded');
                                        if($refunded)
                                        {
                                            $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Refunded $'.$amount;
                                            $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                            $purchase->status = 'Refunded';
                                            $purchase->refunded = $current;
                                            $purchase->save();
                                            $response[$i] = 'Done successfully!';
                                        }
                                        else
                                        {
                                            $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Intented to refund $'.$amount;
                                            $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                            $purchase->save();
                                            $response[$i] = 'Intent to refund failed ('.$purchase->payment_type.').';
                                        }
                                    }
                                        
                                }
                                //only update status refunded
                                else if($input['type']=='update_purchase')
                                {
                                    $refunded = TransactionRefund::where('purchase_id',$purchase->id)->where('result','=','Approved')->first();
                                    if($refunded)
                                    {
                                        $purchase->status = 'Refunded';
                                        $purchase->refunded = $refunded->created;
                                        $purchase->save();
                                        $response[$i] = 'Already refunded';
                                    }
                                    else
                                    {
                                        $refunded = create_refund($purchase,$user,$description,$current,'Refunded');
                                        if($refunded)
                                        {
                                            $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Manually refunded $'.$amount;
                                            $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                            $purchase->status = 'Refunded';
                                            $purchase->refunded = $current;
                                            $purchase->save();
                                            $response[$i] = 'Done successfully!';
                                        }
                                        else
                                        {
                                            $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Intented to manually refund $'.$amount;
                                            $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                            $purchase->save();
                                            $response[$i] = 'Intent to manually refund failed ('.$purchase->payment_type.').';
                                        }
                                    }
                                }
                                //update status chargeback
                                else if($input['type']=='charge_purchase')
                                {
                                    $refunded = TransactionRefund::where('purchase_id',$purchase->id)->where('result','=','Approved')->first();
                                    if($refunded)
                                    {
                                        $purchase->status = 'Chargeback';
                                        $purchase->refunded = $refunded->created;
                                        $purchase->save();
                                        $response[$i] = 'Already refunded';
                                    }
                                    else
                                    {
                                        $refunded = create_refund($purchase,$user,$description,$current,'Chargeback');
                                        if($refunded)
                                        {
                                            $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Manually Chargeback $'.$amount;
                                            $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                            $purchase->status = 'Refunded';
                                            $purchase->refunded = $current;
                                            $purchase->save();
                                            $response[$i] = 'Done successfully!';
                                        }
                                        else
                                        {
                                            $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Intented to manually Chargeback $'.$amount;
                                            $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                            $purchase->save();
                                            $response[$i] = 'Intent to manually Chargeback failed ('.$purchase->payment_type.').';
                                        }
                                    }
                                }
                                else
                                    $response[$i] = 'Invalid action.';
                            }
                            else
                                $response[$i] = 'No amount to be refunded.';
                        }
                        else
                            $response[$i] = 'Not found in the system.';
                        
                        //response true
                        if(!isset($response[$i]))
                            $response[$i] = 'Done successfully!';
                    }
                    
                    $msg = '';
                    foreach ($response as $k=>$v)
                        $msg .= '<br><b>Order #'.$k.' :</b> '.$v;
                    return ['success'=>true, 'msg'=>$msg];
                }
                return ['success'=>false, 'msg'=>'You must select a valid purchase(s) to process.'];
            }
            return ['success'=>false, 'msg'=>'You must select a purchase to process.'];
            
        } catch (Exception $ex) {
            throw new Exception('Error Purchases Save: '.$ex->getMessage());
        }
    }
}
