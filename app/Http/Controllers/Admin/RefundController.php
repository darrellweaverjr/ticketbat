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
            $input = Input::all();
            if(isset($input) && isset($input['start_date']) && isset($input['end_date']))
            {
                //input dates
                $start_date = date('Y-m-d H:i:s',strtotime($input['start_date']));
                $end_date = date('Y-m-d H:i:s',strtotime($input['end_date']));
            }
            else
            {
                //default dates
                $start_date = date('Y-m-d H:i:s', strtotime('-30 DAY'));
                $end_date = date('Y-m-d H:i:s');
            }
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
                                ->leftJoin('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                                ->select(DB::raw('transaction_refunds.*, purchases.id AS order_id, transactions.card_holder, transactions.authcode, transactions.refnum, transactions.last_4,
                                                  transactions.amount, purchases.note, purchases.quantity, purchases.retail_price, purchases.processing_fee, purchases.commission_percent,
                                                  discounts.code, tickets.ticket_type AS ticket_type_type,venues.name AS venue_name, purchases.savings, purchases.status,
                                                  users.first_name AS u_first_name, users.last_name AS u_last_name, users.email AS u_email,
                                                  purchases.payment_type AS method, purchases.printed_fee,
                                                  customers.first_name, customers.last_name, customers.email, customers.phone,
                                                  show_times.show_time, shows.name AS show_name, packages.title'))
                                ->whereIn('shows.venue_id',[Auth::user()->venues_edit])
                                ->whereBetween('transaction_refunds.created', [$start_date,$end_date])
                                ->orderBy('transaction_refunds.created','DESC')
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
                                ->leftJoin('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                                ->select(DB::raw('transaction_refunds.*, purchases.id AS order_id, transactions.card_holder, transactions.authcode, transactions.refnum, transactions.last_4,
                                                  transactions.amount, purchases.note, purchases.quantity, purchases.retail_price, purchases.processing_fee, purchases.commission_percent,
                                                  discounts.code, tickets.ticket_type AS ticket_type_type,venues.name AS venue_name, purchases.savings, purchases.status,
                                                  users.first_name AS u_first_name, users.last_name AS u_last_name, users.email AS u_email,
                                                  purchases.payment_type AS method, purchases.printed_fee,
                                                  customers.first_name, customers.last_name, customers.email, customers.phone,
                                                  show_times.show_time, shows.name AS show_name, packages.title'))
                                ->whereBetween('transaction_refunds.created', [$start_date,$end_date])
                                ->orderBy('transaction_refunds.created','DESC')
                                ->groupBy('transaction_refunds.id')
                                ->get();
                }
            }
            return view('admin.refunds.index',compact('refunds','start_date','end_date'));
        } catch (Exception $ex) {
            throw new Exception('Error Refunds Index: '.$ex->getMessage());
        }
    }
    
    /**
     * check purchases before refund.
     *
     * @return view
     */
    public function check()
    {
        try {
            //init
            $input = Input::all();
            $purchases = $refunds = [];
            if(!empty($input['ids']))
            {
                $id = explode('-', $input['ids']);
                if(!empty($id))
                {
                    //multiples transactions
                    if(count($id)>1)
                    {
                        foreach ($id as $i)
                        {
                            $data = DB::table('purchases')
                                    ->leftJoin('transaction_refunds', function($join){
                                        $join->on('transaction_refunds.purchase_id', '=', 'purchases.id')
                                             ->where('transaction_refunds.result','=','Approved');
                                    })
                                    ->select(DB::raw('purchases.id, purchases.price_paid, SUM( COALESCE(transaction_refunds.amount,0) ) AS refunded '))
                                    ->where('purchases.id','=',$i)->groupBy('purchases.id')->orderBy('purchases.id')->first();
                            $purchases[$i] = (!empty($data))? ['paid'=>$data->price_paid,'refunded'=>$data->refunded,'available'=>$data->price_paid-$data->refunded] : ['paid'=>0,'refunded'=>0,'available'=>0];
                        }
                    }
                    else
                    {
                        $purchase = Purchase::find($id)->first();  
                        if($purchase)
                        {
                            $refundx = TransactionRefund::where('purchase_id',$id)->where('result','=','Approved')
                                                    ->get(['id','quantity','retail_price','processing_fee','savings','commission_percent','printed_fee','sales_taxes','amount','created'])->toArray();
                            $available = ['quantity'=>$purchase->quantity - array_sum(array_column($refundx,'quantity')),
                                          'retail_price'=>$purchase->retail_price - array_sum(array_column($refundx,'retail_price')),
                                          'processing_fee'=>$purchase->processing_fee - array_sum(array_column($refundx,'processing_fee')),
                                          'savings'=>$purchase->savings - array_sum(array_column($refundx,'savings')),
                                          'commission_percent'=>$purchase->commission_percent - array_sum(array_column($refundx,'commission_percent')),
                                          'printed_fee'=>$purchase->printed_fee - array_sum(array_column($refundx,'printed_fee')),
                                          'sales_taxes'=>$purchase->sales_taxes - array_sum(array_column($refundx,'sales_taxes')),
                                          'amount'=>$purchase->price_paid - array_sum(array_column($refundx,'amount')),
                                        'ticket_price'=>$purchase->retail_price/$purchase->quantity,
                                        'ticket_fee'=>($purchase->inclusive_fee<1)? $purchase->processing_fee/$purchase->quantity : 0,
                                        'sales_percent'=>$purchase->sales_taxes/($purchase->price_paid-$purchase->sales_taxes)];
                        }
                        else
                            $available = [ 'quantity'=>0,'retail_price'=>0,'processing_fee'=>0,'savings'=>0,'commission_percent'=>0,'printed_fee'=>0,'sales_taxes'=>0,'amount'=>0 ];
                        $refunds = ['purchase'=>$purchase,'refunds'=>$refundx,'available'=>$available];
                    }
                    return ['success'=>true, 'qty'=>count($id), 'purchases'=>$purchases, 'refunds'=>$refunds];
                }
                return ['success'=>false, 'msg'=>'You must select at least one valid purchase to process.'];
            }
            return ['success'=>false, 'msg'=>'You must select at least one purchase to process.'];
        } catch (Exception $ex) {
            throw new Exception('Error Refunds Check: '.$ex->getMessage());
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
            $response = [];
            $user = Auth::user();
            function create_refund($purchase,$user,$amount,$description,$current,$status,$input)
            {
                $transaction = new TransactionRefund;
                $transaction->purchase_id = $purchase->id;
                $transaction->user_id = $user->id;
                $transaction->amount = $amount;
                $transaction->description = (!empty($description))? $description : null;
                $transaction->result = 'Approved';
                $transaction->error = 'Manually changed purchase to '.$status;
                $transaction->created = $current;
                $transaction->payment_type = $purchase->payment_type;
                if($transaction->result == 'Approved')
                {
                    $transaction->refunded_reason = $purchase->refunded_reason;
                    $transaction->quantity = (isset($input['quantity']))? $input['quantity'] : $purchase->quantity;
                    $transaction->retail_price = (isset($input['retail_price']))? $input['retail_price'] : $purchase->retail_price;
                    $transaction->savings = (isset($input['savings']))? $input['savings'] : $purchase->savings;
                    $transaction->processing_fee = (isset($input['processing_fee']))? $input['processing_fee'] : $purchase->processing_fee;
                    $transaction->printed_fee = (isset($input['printed_fee']))? $input['printed_fee'] : $purchase->printed_fee;
                    $transaction->sales_taxes = (isset($input['sales_taxes']))? $input['sales_taxes'] : $purchase->sales_taxes;
                    $transaction->commission_percent = (isset($input['commission_percent']))? $input['commission_percent'] : $purchase->commission_percent;
                }
                return $transaction->save();
            }   
            
            if($input && !empty($input['id']) && isset($input['type']))
            {
                $id = explode('-', $input['id']);
                $description = (!empty(trim($input['description'])))? trim($input['description']) : null;
                if(isset($input['amount']) && empty($input['amount']))
                    return ['success'=>false, 'msg'=>'You must select a valid amount to process.'];
                if(!empty($id))
                {
                    $msg = '';
                    //try to refund each purchase 
                    foreach ($id as $i)
                    {
                        $purchase = Purchase::find($i);
                        if($purchase)
                        {
                            //check amount to refun
                            $data = DB::table('purchases')
                                    ->leftJoin('transaction_refunds', 'purchases.id', '=', 'transaction_refunds.purchase_id')
                                    ->select(DB::raw('SUM( COALESCE(transaction_refunds.amount,0) ) AS refunded '))
                                    ->where('purchases.id',$purchase->id)
                                    ->where(function($query) {
                                        $query->where('transaction_refunds.result','=','Approved')
                                              ->orWhereNull('transaction_refunds.id');
                                    })
                                    ->orderBy('purchases.id')->groupBy('purchases.id')->first();
                            $available = $purchase->price_paid - $data->refunded;
                            $amount = (!empty($input['amount']) && $input['amount']>0 && $input['amount']<$available)? $input['amount'] : $available;
                            //return ['success'=>false, 'msg'=>$amount];
                            if($amount>0)
                            {
                                //check action to process
                                if($input['type']=='current_purchase')
                                {
                                    //refund credit card thru USAePay
                                    if($purchase->payment_type == 'Credit')
                                    {
                                        if($purchase->transaction && $purchase->transaction->trans_result=='Approved')
                                        {
                                            $refunded = TransactionRefund::usaepay($purchase, $user, $amount, $description, $current, $input);
                                            if($refunded['success'])
                                            {
                                                $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Refunded $'.$amount;
                                                $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                                $purchase->refunded_reason = null;
                                                $purchase->save();
                                                $response[$i] = ['success'=>true, 'msg'=>'Done successfully!<br>'.$refunded['msg']];
                                            }
                                            else
                                            {
                                                $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Intented to refund $'.$amount;
                                                $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                                $purchase->save();
                                                $response[$i] = ['success'=>false, 'msg'=>'Intent to refund failed ('.$purchase->payment_type.').<br>'.$refunded['msg']] ;
                                            }
                                        }
                                        else
                                            $response[$i] = ['success'=>false, 'msg'=>'That purchase has no a valid transaction.'];
                                    }
                                    //refund cash
                                    else
                                    {
                                        $refunded = create_refund($purchase,$user,$amount,$description,$current,'Refunded',$input);
                                        if($refunded)
                                        {
                                            $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Refunded $'.$amount;
                                            $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                            $purchase->status = 'Refunded';
                                            $purchase->refunded_reason = null;
                                            $purchase->save();
                                            $response[$i] = ['success'=>true, 'msg'=>'Done successfully!'];  
                                        }
                                        else
                                        {
                                            $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Intented to refund $'.$amount;
                                            $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                            $purchase->save();
                                            $response[$i] = ['success'=>false, 'msg'=>'Intent to refund failed ('.$purchase->payment_type.').'];
                                        }
                                    } 
                                }
                                //only update status refunded
                                else if($input['type']=='update_purchase')
                                {
                                    $refunded = create_refund($purchase,$user,$amount,$description,$current,'Refunded',$input);
                                    if($refunded)
                                    {
                                        $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Manually refunded $'.$amount;
                                        $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                        $purchase->status = 'Refunded';
                                        $purchase->refunded_reason = null;
                                        $purchase->save();
                                        $response[$i] = ['success'=>true, 'msg'=>'Done successfully!'];  
                                    }
                                    else
                                    {
                                        $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Intented to manually refund $'.$amount;
                                        $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                        $purchase->save();
                                        $response[$i] = ['success'=>false, 'msg'=>'Intent to manually refund failed ('.$purchase->payment_type.').'];
                                    }
                                }
                                //update status chargeback
                                else if($input['type']=='charge_purchase')
                                {
                                    $refunded = create_refund($purchase,$user,$amount,$description,$current,'Chargeback',$input);
                                    if($refunded)
                                    {
                                        $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Manually Chargeback $'.$amount;
                                        $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                        $purchase->status = 'Refunded';
                                        $purchase->refunded_reason = null;
                                        $purchase->save();
                                        $response[$i] = ['success'=>true, 'msg'=>'Done successfully!'];  
                                    }
                                    else
                                    {
                                        $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Intented to manually Chargeback $'.$amount;
                                        $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                                        $purchase->save();
                                        $response[$i] = ['success'=>false, 'msg'=>'Intent to manually chargeback failed ('.$purchase->payment_type.').'];
                                    }
                                }
                                else
                                    $response[$i] = ['success'=>false, 'msg'=>'Invalid action.']; 
                            }
                            else
                                $response[$i] = ['success'=>false, 'msg'=>'No amount to be refunded.']; 
                        }
                        else
                            $response[$i] = ['success'=>false, 'msg'=>'Not found in the system.']; 
                        
                        //make response when no condition
                        if(!isset($response[$i]))
                            $response[$i] = ['success'=>false, 'msg'=>'No action made!']; 
                        
                        //if refunded, send an email to the customer of the refunded ones and re-do tickets   
                        $emailSent = '<i class="icon-envelope"></i><i class="icon-close"></i>';
                        if($response[$i]['success'])
                        {
                            //qty tickets availables
                            $data = DB::table('purchases')
                                    ->leftJoin('transaction_refunds', 'purchases.id', '=', 'transaction_refunds.purchase_id')
                                    ->select(DB::raw('SUM( COALESCE(transaction_refunds.quantity,0) ) AS refunded '))
                                    ->where('purchases.id',$purchase->id)
                                    ->where(function($query) {
                                        $query->where('transaction_refunds.result','=','Approved')
                                              ->orWhereNull('transaction_refunds.id');
                                    })
                                    ->orderBy('purchases.id')->groupBy('purchases.id')->first();
                            //remove old tickets
                            DB::table('ticket_number')->where('purchases_id',$purchase->id)->delete();
                            //create new tickets
                            $tickets = implode(',',range(1,$purchase->quantity - $data->refunded));
                            DB::table('ticket_number')->insert( ['purchases_id'=>$purchase->id,'customers_id'=>$purchase->customer_id,'tickets'=>$tickets] );
                            //send email
                            if($purchase->set_pending(true))
                                $emailSent = '<i class="icon-envelope"></i><i class="icon-check"></i>';
                        }
                        
                        //creating message
                        $msg .= '<br><b>Order #'.$i.' :</b> '.$emailSent.' '.$response[$i]['msg'];
                    }
                    //return
                    return ['success'=>true, 'msg'=>$msg];
                }
                return ['success'=>false, 'msg'=>'You must select a valid purchase(s) to process.'];
            }
            return ['success'=>false, 'msg'=>'You must select a purchase to process.'];
            
        } catch (Exception $ex) {
            throw new Exception('Error Purchases Refund: '.$ex->getMessage());
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
            if($input && !empty($input['id']) && !empty($input['created']) && strtotime($input['created']))
            {
                $refund = TransactionRefund::find($input['id']);
                if($refund)
                {
                    $refund->created = date('Y-m-d H:i:s', strtotime($input['created']));
                    $refund->save();
                    return ['success'=>true, 'msg'=>'Refund updated successfully.'];
                }
                return ['success'=>false, 'msg'=>'That refund is not longer in the system.'];
            }
            return ['success'=>false, 'msg'=>'You must select a purchase to process and a valid date to change the refund to.'];
            
        } catch (Exception $ex) {
            throw new Exception('Error Purchases Save: '.$ex->getMessage());
        }
    }
}
