<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Http\Models\Purchase;
use App\Http\Models\Discount;
use App\Http\Models\User;
use App\Http\Models\Show;
use App\Http\Models\Customer;
use App\Http\Models\Ticket;
use App\Http\Models\ShowTime;
use App\Http\Models\TransactionRefund;
use App\Http\Models\Transaction;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Models\Util;
use App\Mail\EmailSG;

/**
 * Manage Purchases
 *
 * @author ivan
 */
class PurchaseController extends Controller{

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
            if(isset($input) && !empty($input['id']) && isset($input['action']) && $input['action']==0)
            {
                $purchase = DB::table('purchases')
                                    ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                                    ->join('users', 'users.id', '=' ,'purchases.user_id')
                                    ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                                    ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                                    ->join('shows', 'shows.id', '=', 'show_times.show_id')
                                    ->join('venues', 'venues.id', '=', 'shows.venue_id')
                                    ->join('tickets', 'tickets.id', '=', 'purchases.ticket_id')
                                    ->join('packages', 'packages.id', '=', 'tickets.package_id')
                                    ->leftJoin('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                                    ->leftJoin('transaction_refunds', 'transaction_refunds.purchase_id', '=' ,'purchases.id')
                                    ->select(DB::raw('purchases.*, transactions.card_holder, transactions.authcode, transactions.refnum, transactions.last_4,
                                                      IF(transactions.amount IS NOT NULL,transactions.amount,purchases.price_paid) AS amount,
                                                      purchases.payment_type AS method, shows.venue_id, purchases.show_time_id, show_times.show_id,
                                                      IF(transactions.id IS NOT NULL,transactions.id,CONCAT(purchases.session_id,purchases.created)) AS color,
                                                      discounts.code, tickets.ticket_type AS ticket_type_type,venues.name AS venue_name, purchases.printed_fee,
                                                      users.first_name AS u_first_name, users.last_name AS u_last_name, users.email AS u_email, users.phone AS u_phone,
                                                      customers.first_name, customers.last_name, customers.email, customers.phone,
                                                      show_times.show_time, shows.name AS show_name, packages.title'))
                                    ->where('purchases.id',$input['id'])->groupBy('purchases.id')->first();
                if($purchase)
                {
                    $purchase->tickets = DB::table('ticket_number')
                            ->join('customers', 'customers.id', '=', 'ticket_number.customers_id')
                            ->join('purchases', 'purchases.id', '=', 'ticket_number.purchases_id')
                            ->select(DB::raw('ticket_number.id, ticket_number.tickets,
                                              customers.first_name, customers.last_name, customers.email'))
                            ->whereColumn('ticket_number.customers_id','<>','purchases.customer_id')
                            ->where('ticket_number.purchases_id', $purchase->id)
                            ->groupBy('ticket_number.id')->orderBy('ticket_number.id','DESC')->get();
                    return ['success'=>true,'purchase'=>$purchase];
                }
                return ['success'=>false,'msg'=>'There was an error.<br>That purchase is not longer in the system.'];
            }
            else if(isset($input) && isset($input['id']))
            {
                $root_setting = (Auth::check() && in_array(Auth::user()->id,explode(',',env('ROOT_USER_ID'))))? '-2 months' : 'yesterday';
                $current = DB::table('purchases')
                                ->join('show_times', 'purchases.show_time_id', '=', 'show_times.id')
                                ->join('shows', 'shows.id', '=', 'show_times.show_id')
                                ->join('tickets','purchases.ticket_id','=','tickets.id')
                                ->join('packages','packages.id','=','tickets.package_id')
                                ->join('discounts','discounts.id','=','purchases.discount_id')
                                ->select('tickets.ticket_type','tickets.retail_price','tickets.processing_fee','tickets.percent_pf','tickets.fixed_commission', 'tickets.inclusive_fee', 'purchases.refunded',
                                          'tickets.percent_commission','tickets.is_active','purchases.quantity','purchases.retail_price AS p_retail_price', 'tickets.is_active', 'purchases.updated',
                                          'purchases.inclusive_fee AS p_inclusive_fee','purchases.sales_taxes','purchases.cc_fees AS cc_fee','purchases.channel','purchases.payment_type',
                                          'purchases.processing_fee AS p_processing_fee','purchases.savings','purchases.commission_percent','purchases.price_paid','discounts.code', 'purchases.printed_fee',
                                          'show_times.show_time','packages.title','purchases.ticket_id','purchases.id AS purchase_id','shows.id AS show_id','purchases.show_time_id')
                                ->where('purchases.id','=',$input['id'])->first();
                $showtimes = DB::table('show_times')->select('id','show_time')
                                ->where('show_id','=',$current->show_id)->where('is_active','=',1)->where('show_times.show_time','>',date('Y-m-d H:i:s',strtotime($root_setting)))
                                ->orderBy('show_times.show_time')->get();
                $tickets = DB::table('tickets')
                                ->join('packages','packages.id','=','tickets.package_id')
                                ->select('tickets.id','tickets.ticket_type','packages.title')
                                ->where('tickets.show_id','=',$current->show_id)->where('tickets.is_active','=',1)
                                ->get();
                $discounts = DB::table('discounts')
                                ->leftJoin('discount_tickets', 'discounts.id', '=', 'discount_tickets.discount_id')
                                ->leftJoin('purchases','purchases.ticket_id','=','discount_tickets.ticket_id')
                                ->select('discounts.id','discounts.code','discounts.description')
                                ->where('purchases.id','=',$current->purchase_id)
                                ->orWhere('discounts.id','=',1)
                                ->orderBy('discounts.code')->get();
                return ['success'=>true,'current'=>$current,'tickets'=>$tickets,'showtimes'=>$showtimes,'discounts'=>$discounts];
            }
            else if(isset($input) && isset($input['purchase_id']))
            {
                $purchase = Purchase::find($input['purchase_id']);
                if($purchase)
                {
                    $st_id = (!empty($input['to_show_time_id']))? $input['to_show_time_id'] : $purchase->show_time_id;
                    $t_id = (!empty($input['to_ticket_id']))? $input['to_ticket_id'] : $purchase->ticket_id;
                    $d_id = (!empty($input['to_discount_id']))? $input['to_discount_id'] : $purchase->discount_id;
                    $qty = (!empty($input['to_quantity']) && $input['to_quantity']>0)? ceil($input['to_quantity']) : $purchase->quantity;
                    $showtime = ShowTime::find($st_id);
                    $ticket = Ticket::find($t_id);
                    $discount = Discount::find($d_id);
                    if($showtime && $ticket && $qty && $discount)
                    {
                        $ticket_o = null;
                        $contracts = DB::table('show_contracts')->select('data')
                                    ->where('show_id','=',$showtime->show_id)
                                    ->where('effective_date','<=',date('Y-m-d',strtotime($showtime->show_time)))->where('effective_date','>=',date('Y-m-d'))
                                    ->orderBy('effective_date','desc')->get();
                        foreach ($contracts as $c)
                        {
                            if($ticket_o)
                                break;
                            if(!empty($c->data) && Util::isJSON($c->data))
                            {
                                $data = json_decode($c->data);
                                foreach ($data as $d)
                                {
                                    if($d->ticket_id == $ticket->id)
                                    {
                                        $ticket_o = $d;
                                        break;
                                    }
                                }
                            }
                        }
                        //recalculate qty tickets to pay to
                        $free_tickets = $discount->free_tickets($qty);
                        $qty_item_pay = $qty-$free_tickets;
                        //calculate target result
                        $target = ['t_ticket_type'=>$ticket->ticket_type,'t_title'=>$ticket->package->title,'t_retail_price'=>$ticket->retail_price,
                                   't_is_active'=>$ticket->is_active,'t_inclusive_fee'=>$ticket->inclusive_fee, 
                                   't_code'=>$discount->code, 't_printed_fee'=>$purchase->printed_fee, 
                                   't_p_inclusive_fee'=>(isset($input['t_p_inclusive_fee']))? $input['t_p_inclusive_fee'] : $purchase->inclusive_fee,
                                   't_processing_fee'=>$ticket->processing_fee,'t_percent_pf'=>$ticket->t_percent_pf,'t_fixed_commission'=>$ticket->fixed_commission,
                                   't_percent_commission'=>$ticket->percent_commission,'t_quantity'=>$qty,'t_show_time'=>$showtime->show_time,
                                   't_p_retail_price'=> Util::round($ticket->retail_price*$qty),'t_cc_fee'=> $purchase->cc_fees,
                                   't_payment_type'=> (!empty($input['t_payment_type']))? $input['t_payment_type'] : $purchase->payment_type,
                                   't_channel'=> (!empty($input['t_channel']))? $input['t_channel'] : $purchase->channel,
                                   't_updated'=> (!empty($input['t_updated']))? $input['t_updated'] : $purchase->updated,
                                   't_refunded'=> (!empty($input['t_refunded']))? $input['t_refunded'] : $purchase->refunded,
                                   't_p_processing_fee'=>(!empty($ticket->processing_fee))? Util::round($ticket->processing_fee*$qty_item_pay) : Util::round($ticket->t_percent_pf/100*$ticket->retail_price*$qty_item_pay)];
                        //calculate savings result
                        $target['t_savings'] = Util::round( $discount->calculate_savings($qty,$target['t_p_retail_price'] + $target['t_p_processing_fee']) );
                        //calculate commission result
                        $c = DB::table('discount_tickets')->select('fixed_commission')
                                    ->where('discount_id','=',$discount->id)->where('ticket_id','=',$ticket->id)->first();
                        $fixed_commission = (!empty($c->fixed_commission))? $c->fixed_commission : $ticket->fixed_commission;
                        $target['t_commission_percent'] = (!empty($fixed_commission))? Util::round($fixed_commission*$qty_item_pay) : Util::round($ticket->percent_commission/100*$ticket->retail_price*$qty_item_pay);
                        //calculate total result
                        $target['t_price_paid'] = Util::round($target['t_p_retail_price'] - $target['t_savings'] + $target['t_printed_fee']);
                        if(!($target['t_p_inclusive_fee']>0))
                            $target['t_price_paid'] += Util::round($target['t_p_processing_fee']);
                        $target['t_sales_taxes'] = Util::round($target['t_price_paid'] * $showtime->show->venue->default_sales_taxes_percent/100);
                        $target['t_price_paid'] += Util::round($target['t_sales_taxes']);
                        return ['success'=>true,'target'=>$target];
                    }
                    else
                        return ['success'=>false,'msg'=>'There was an error.<br>That event date/ticket/qty is not longer in the system.'];
                }
                else
                    return ['success'=>false,'msg'=>'There was an error.<br>That purchase is not longer in the system.'];
            }
            else
            {
                //conditions to search
                $status = [];
                $purchases = [];
                $data = Purchase::filter_options('PURCHASES', $input, '-7');
                $where = $data['where'];
                $search = $data['search'];                
                //if user has permission to view
                if(in_array('View',Auth::user()->user_type->getACLs()['PURCHASES']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['PURCHASES']['permission_scope'] != 'All')
                    {
                        if(count($input))
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
                                    ->leftJoin('transaction_refunds', 'transaction_refunds.purchase_id', '=' ,'purchases.id')
                                    ->leftJoin('ticket_number',function($join){
                                        $join->on('ticket_number.purchases_id','=','purchases.id')
                                             ->on('ticket_number.customers_id','!=','customers.id');
                                    })
                                    ->select(DB::raw('purchases.*, transactions.card_holder, transactions.authcode, transactions.refnum, transactions.last_4, transactions.amount,
                                                      purchases.payment_type AS method, tickets.inclusive_fee, transactions.invoice_num, transaction_refunds.amount AS r_amount,
                                                      transaction_refunds.authcode AS r_authcode, transaction_refunds.ref_num AS r_refnum, transaction_refunds.description,
                                                      IF(transactions.id IS NOT NULL,transactions.id,CONCAT(purchases.session_id,purchases.created)) AS color,
                                                      discounts.code, tickets.ticket_type AS ticket_type_type,venues.name AS venue_name,
                                                      users.first_name AS u_first_name, users.last_name AS u_last_name, users.email AS u_email, users.phone AS u_phone,
                                                      customers.first_name, customers.last_name, customers.email, customers.phone,
                                                      show_times.show_time, shows.name AS show_name, packages.title, COUNT( ticket_number.id ) AS shared'))
                                    ->where($where)
                                    ->where(function($query)
                                    {
                                        $query->whereIn('shows.venue_id',[Auth::user()->venues_edit])
                                              ->orWhere('shows.audit_user_id','=',Auth::user()->id);
                                    })
                                    ->orderBy('purchases.created','purchases.transaction_id','purchases.user_id','purchases.price_paid')
                                    ->havingRaw('method IN ("'.implode('","',$search['payment_type']).'")')
                                    ->groupBy('purchases.id')
                                    ->get();
                    }//all
                    else
                    {
                        if(count($input))
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
                                    ->leftJoin('transaction_refunds', 'transaction_refunds.purchase_id', '=' ,'purchases.id')
                                    ->leftJoin('ticket_number',function($join){
                                        $join->on('ticket_number.purchases_id','=','purchases.id')
                                             ->on('ticket_number.customers_id','!=','customers.id');
                                    })
                                    ->select(DB::raw('purchases.*, transactions.card_holder, transactions.authcode, transactions.refnum, transactions.last_4, transactions.amount,
                                                      purchases.payment_type AS method, tickets.inclusive_fee, transactions.invoice_num, transaction_refunds.amount AS r_amount,
                                                      transaction_refunds.authcode AS r_authcode, transaction_refunds.ref_num AS r_refnum, transaction_refunds.description,
                                                      IF(transactions.id IS NOT NULL,transactions.id,CONCAT(purchases.session_id,purchases.created)) AS color,
                                                      discounts.code, tickets.ticket_type AS ticket_type_type,venues.name AS venue_name,
                                                      users.first_name AS u_first_name, users.last_name AS u_last_name, users.email AS u_email, users.phone AS u_phone,
                                                      customers.first_name, customers.last_name, customers.email, customers.phone,
                                                      show_times.show_time, shows.name AS show_name, packages.title, COUNT( ticket_number.id ) AS shared'))
                                    ->where($where)
                                    ->orderBy('purchases.created','purchases.transaction_id','purchases.user_id','purchases.price_paid')
                                    ->havingRaw('method IN ("'.implode('","',$search['payment_type']).'")')
                                    ->groupBy('purchases.id')
                                    ->get();
                    }
                }
                $modal = (count($input))? 0 : 1;
                return view('admin.purchases.index',compact('purchases','search','modal'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Purchases Index: '.$ex->getMessage());
        }
    }
    /**
     * Updated purchase.
     *
     * @void
     */
    public function save()
    {
        try {
            //init
            $input = Input::all();
            $current = date('Y-m-d H:i:s');
            $note = '';
            //save all record
            if($input && !empty($input['id']))
            {
                $purchase = Purchase::find($input['id']);
                if($purchase)
                {                    
                    if(isset($input['status']))
                    {
                        if(strpos($input['status'], 'Pending') === 0)
                        {
                            $reason = (!empty(trim($input['reason'])) && strlen(trim($input['reason']))>5)? trim($input['reason']) : null;
                            if(empty($reason))
                                return ['success'=>false,'msg'=>'There was an error updating the purchase.<br>You must write a reason to the refund with more than 5 characters.'];
                            $purchase->refunded_reason = $reason;
                        }                        
                        $old_status = $purchase->status;
                        $purchase->status = $input['status'];
                        $note.= ', status from '.$purchase->status.' to '.$input['status'];
                    }
                    if(isset($input['note']))
                    {
                        $note = '&nbsp;<br><b>'.Auth::user()->first_name.' '.Auth::user()->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b>'.strip_tags($input['note']).'&nbsp;';
                    }
                    if(!empty($input['to_user_email']))
                    {
                        $to_email = trim($input['to_user_email']);
                        if (!filter_var($to_email, FILTER_VALIDATE_EMAIL))
                            return ['success'=>false,'msg'=>'You must enter a valid email for the user.'];
                        $user = User::where('email',$to_email)->first();
                        if(!$user)
                            return ['success'=>false,'msg'=>'That user does not exists in the system.'];
                        $note.= ', user from '.$purchase->user->email.' to '.$to_email;
                        $purchase->user_id = $user->id;
                    }
                    if(!empty($input['to_customer_email']))
                    {
                        $to_email = trim($input['to_customer_email']);
                        if (!filter_var($to_email, FILTER_VALIDATE_EMAIL))
                            return ['success'=>false,'msg'=>'You must enter a valid email for the customer.'];
                        $customer = Customer::where('email',$to_email)->first();
                        if(!$customer)
                            return ['success'=>false,'msg'=>'That customer does not exists in the system.'];
                        $note.= ', customer from '.$purchase->customer->email.' to '.$to_email;
                        $purchase->customer_id = $customer->id;
                    }
                    if(!empty($input['to_show_time_id']) && $purchase->show_time_id != $input['to_show_time_id'])
                    {
                        $from = ShowTime::find($purchase->show_time_id);
                        $date_from = date('m/d/Y g:i a',strtotime($from->show_time));
                        $to = ShowTime::find($input['to_show_time_id']);
                        $note.= ', date from '.$date_from.' to '.date('m/d/Y g:i a',strtotime($to->show_time));
                        $purchase->show_time_id = $input['to_show_time_id'];
                    }
                    if(!empty($input['to_ticket_id']) && $purchase->ticket_id != $input['to_ticket_id'])
                    {
                        $from = Ticket::find($purchase->ticket_id);
                        $to = Ticket::find($input['to_ticket_id']);
                        $note.= ', ticket from'.$from->ticket_type.' to '.$to->ticket_type;
                        $purchase->ticket_id = $input['to_ticket_id'];
                    }
                    if(!empty($input['to_discount_id']) && $purchase->discount_id != $input['to_discount_id'])
                    {
                        $from = Discount::find($purchase->discount_id);
                        $to = Discount::find($input['to_discount_id']);
                        $note.= ', coupon from '.$from->code.' to '.$to->code;
                        $purchase->discount_id = $input['to_discount_id'];
                    }
                    if(!empty($input['to_quantity']) && $purchase->quantity != $input['to_quantity'])
                    {
                        $old_qty = $purchase->quantity;
                        $note.= ', qty from '.$purchase->quantity.' to '.$input['to_quantity'];
                        $purchase->quantity = $input['to_quantity'];
                    }
                    if(isset($input['t_p_retail_price']) &&  $purchase->retail_price != $input['t_p_retail_price'])
                    {
                        $note.= ', retail_price from '.$purchase->retail_price.' to '.$input['t_p_retail_price'];
                        $purchase->retail_price = $input['t_p_retail_price'];
                    }
                    if(isset($input['t_p_processing_fee']) &&  $purchase->processing_fee != $input['t_p_processing_fee'])
                    {
                        $note.= ', processing_fee from '.$purchase->processing_fee.' to '.$input['t_p_processing_fee'];
                        $purchase->processing_fee = $input['t_p_processing_fee'];
                    }
                    if(isset($input['t_savings']) &&  $purchase->savings != $input['t_savings'])
                    {
                        $note.= ', savings from '.$purchase->savings.' to '.$input['t_savings'];
                        $purchase->savings = $input['t_savings'];
                    }
                    if(isset($input['t_commission_percent']) &&  $purchase->commission_percent != $input['t_commission_percent'])
                    {
                        $note.= ', commission from '.$purchase->commission_percent.' to '.$input['t_commission_percent'];
                        $purchase->commission_percent = $input['t_commission_percent'];
                    }
                    if(isset($input['t_printed_fee']) &&  $purchase->printed_fee != $input['t_printed_fee'])
                    {
                        $note.= ', printed_fee from '.$purchase->printed_fee.' to '.$input['t_printed_fee'];
                        $purchase->printed_fee = $input['t_printed_fee'];
                    }
                    if(isset($input['t_price_paid']) &&  $purchase->price_paid != $input['t_price_paid'])
                    {
                        $note.= ', price paid from '.$purchase->price_paid.' to '.$input['t_price_paid'];
                        $purchase->price_paid = $input['t_price_paid'];
                        if($purchase->transaction_id)
                        {
                            $transaction = Transaction::find($purchase->transaction_id);
                            if($transaction)
                            {
                                $transaction->amount = $purchase->price_paid;
                                $transaction->save();
                            }
                        }
                    }
                    if(isset($input['t_sales_taxes']) && $input['t_sales_taxes']!=$purchase->sales_taxes)
                    {
                        $note.= ', sale taxes from '.$purchase->sales_taxes.' to '.$input['t_sales_taxes'];
                        $purchase->sales_taxes = $input['t_sales_taxes'];
                    }
                    if(isset($input['t_cc_fee']) && $input['t_cc_fee']!=$purchase->cc_fees)
                    {
                        $note.= ', CC Fees from '.$purchase->cc_fees.' to '.$input['t_cc_fee'];
                        $purchase->cc_fees = $input['t_cc_fee'];
                    }
                    if(isset($input['t_inclusive_fee']) && $input['t_inclusive_fee']!=$purchase->inclusive_fee)
                    {
                        $note.= ', Incl Fee from '.$purchase->inclusive_fee.' to '.$input['t_inclusive_fee'];
                        $purchase->inclusive_fee = $input['t_inclusive_fee'];
                    }
                    if(!empty($input['t_payment_type']) && $input['t_payment_type']!=$purchase->payment_type)
                    {
                        $note.= ', Payment from '.$purchase->payment_type.' to '.$input['t_payment_type'];
                        $purchase->payment_type = $input['t_payment_type'];
                    }
                    if(!empty($input['t_channel']) && $input['t_channel']!=$purchase->channel)
                    {
                        $note.= ', Channel from '.$purchase->channel.' to '.$input['t_channel'];
                        $purchase->channel = $input['t_channel'];
                    }
                    if(!empty($input['t_updated']) && strtotime($input['t_updated']) )
                    {
                        $updated= date('Y-m-d H:i',strtotime($input['t_updated']));
                        if($updated != date('Y-m-d H:i',strtotime($purchase->updated)) )
                        {
                            $note.= ', Updated from '.date('m/d/Y g:ia',strtotime($purchase->updated)).' to '.$input['t_updated'];
                            $purchase->updated = $updated;
                        }
                    }
                    
                    //note and save
                    if(!empty($note))
                    {
                        $note = '&nbsp;<br><b>'.Auth::user()->first_name.' '.Auth::user()->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Change '.$note;
                        $purchase->note = ($purchase->note)? $purchase->note.$note : $note;
                        $purchase->save();
                        
                        //after save options
                        if(!empty($input['to_quantity']) && isset($old_qty))
                        {
                            DB::table('ticket_number')->where('purchases_id',$purchase->id)->delete();
                            $tickets = implode(',',range(1,$purchase->quantity));
                            DB::table('ticket_number')->insert( ['purchases_id'=>$purchase->id,'customers_id'=>$purchase->customer_id,'tickets'=>$tickets] );
                        }
                        if(!empty($input['to_show_time_id']))
                        {
                            $receipt = $purchase->get_receipt();
                            Purchase::email_receipts('Updated show information: TicketBat Purchase', [$receipt], 'changed', $date_from, true);
                        }
                        if(!empty($input['status']))
                        {
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
                                $status = ($input['status']=='Active')? 'ACTIVATED' : ( ($input['status']=='Refunded')? 'REFUNDED' :'CANCELED' );
                                $sent = Purchase::email_receipts($status.': TicketBat Purchase',[$receipt],'receipt',$status,true);
                                if(!$sent)
                                    return ['success'=>false,'msg'=>'The purchase changed the status.<br>But the email could not be sent to the customer and the venue.'];
                            }
                        } 
                        return ['success'=>true,'msg'=>'Purchase saved successfully!'];
                    }
                    return ['success'=>true,'msg'=>'There were no changes on that purchase!'];
                }
                else return ['success'=>false,'msg'=>'There was an error saving the purchase.<br>That purchase is not longer in the system.'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the purchase.<br>Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error Purchases Save: '.$ex->getMessage());
        }
    }
    /**
     * Updated purchase.
     *
     * @void
     */
    public function email()
    {
        try {
            //init
            $input = Input::all();
            //save all record
            if($input && isset($input['action']))
            {
                if($input['action']=='receipt' && isset($input['id']))
                {
                    $receipt = Purchase::find($input['id'])->get_receipt();
                    $sent = Purchase::email_receipts('Re-sending: TicketBat Purchase',[$receipt],'receipt');
                    if($sent)
                        return ['success'=>true,'msg'=>'Email sent successfully!'];
                    return ['success'=>false,'msg'=>'There was an error sending the email.'];
                }
                else if($input['action']=='custom')
                {
                    $to = [];
                    if(!empty($input['email'][1]['value']) && !empty($input['ids']))
                    {
                        $to = DB::table('purchases')
                                    ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                                    ->select('customers.email')
                                    ->whereIn('purchases.id', $input['ids'])->distinct()->get();
                    }
                    else
                    {
                        $where =[['purchases.id','>',0]];
                        //by venue
                        if($input['search'][1]['value'] != '')
                            $where[] = ['shows.venue_id','=',$input['search'][1]['value']];
                        //by show
                        if($input['search'][2]['value'] != '')
                            $where[] = ['shows.id','=',$input['search'][2]['value']];
                        //by showtime date
                        if($input['search'][3]['value'] != '' && $input['search'][4]['value'] != '')
                        {
                            $where[] = [DB::raw('DATE(show_times.show_time)'),'>=',$input['search'][3]['value']];
                            $where[] = [DB::raw('DATE(show_times.show_time)'),'<=',$input['search'][4]['value']];
                        }
                        //by created date
                        if($input['search'][5]['value'] != '' && $input['search'][6]['value'] != '')
                        {
                            $where[] = [DB::raw('DATE(show_times.show_time)'),'>=',$input['search'][5]['value']];
                            $where[] = [DB::raw('DATE(show_times.show_time)'),'<=',$input['search'][6]['value']];
                        }
                        $to = DB::table('purchases')
                                    ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                                    ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                                    ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                    ->select('customers.email')
                                    ->where($where)->distinct()->get();
                    }
                    //send email
                    if(!empty($to) && !empty($input['email'][2]['value']) && !empty($input['email'][3]['value']))
                    {
                        $msg = '';
                        foreach ($to as $t)
                        {
                            //send email
                            $email = new EmailSG(null, $t->email, strip_tags($input['email'][2]['value']));
                            $email->category('Custom');
                            $email->body('custom',['body'=>$input['email'][3]['value']]);
                            if(!empty($input['email'][4]['value']))
                                $email->template($input['email'][4]['value']);
                            else
                                $email->template('46388c48-5397-440d-8f67-48f82db301f7');
                            $response = $email->send();
                            if(!$response)
                                $msg = (empty($msg))? $t->email : ', '.$t->email;
                        }
                        if(empty($msg))
                            return ['success'=>true,'msg'=>'Email sent successfully!'];
                        return ['success'=>false,'msg'=>'The system could not send email to:<br>'.$msg];
                    }
                    return ['success'=>false,'msg'=>'There was an error sending the email.<br>Needed values missing.'];
                }
                return ['success'=>false,'msg'=>'There was an error sending the email.<br>Invalid option and values.'];
            }
            return ['success'=>false,'msg'=>'There was an error sending the email.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Purchases Email: '.$ex->getMessage());
        }
    }
    /**
     * View tickets of purchase.
     *
     * @void
     */
    public function tickets($type,$ids)
    {
        try {
            //init
            $input = Input::all();
            //save all record
            if($input && isset($input['action']))
            {

            }
            //check input values
            if(in_array($type,['C','S']))
            {
                $tickets = [];
                $purchases_id = explode('-',$ids);
                foreach ($purchases_id as $id)
                {
                    $t = Purchase::find($id)->get_receipt()['tickets'];
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
            throw new Exception('Error Purchases tickets: '.$ex->getMessage());
        }
    }
    /**
     * View filters enum of purchase.
     *
     * @void
     */
    public function filter()
    {
        try {
            //init
            $input = Input::all();
            if(!empty($input['venue_id']))
                $values = Show::where('venue_id',$input['venue_id'])->orderBy('name')->get(['id','name']);
            else if(!empty($input['show_id']))
                $values = DB::table('tickets')
                            ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                            ->select(DB::raw('tickets.id, CONCAT(tickets.ticket_type," - ",packages.title) AS name'))
                            ->where('tickets.show_id',$input['show_id'])->groupBy('tickets.id')->get();
            else
                $values = [];
            return ['success'=>true,'values'=>$values];    
        } catch (Exception $ex) {
            throw new Exception('Error Purchases filter: '.$ex->getMessage());
        }
    }
}
