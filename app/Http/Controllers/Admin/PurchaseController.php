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
            if(isset($input) && isset($input['id']))
            {
                $current = DB::table('purchases')
                                ->join('show_times', 'purchases.show_time_id', '=', 'show_times.id')
                                ->join('shows', 'shows.id', '=', 'show_times.show_id')
                                ->join('tickets','purchases.ticket_id','=','tickets.id')
                                ->join('packages','packages.id','=','tickets.package_id')
                                ->join('discounts','discounts.id','=','purchases.discount_id')
                                ->select('tickets.ticket_type','tickets.retail_price','tickets.processing_fee','tickets.percent_pf','tickets.fixed_commission',
                                          'tickets.percent_commission','tickets.is_active','purchases.quantity','purchases.retail_price AS p_retail_price', 'tickets.is_active',
                                          'purchases.processing_fee AS p_processing_fee','purchases.savings','purchases.commission_percent','purchases.price_paid','discounts.code',
                                          'show_times.show_time','packages.title','purchases.ticket_id','purchases.id AS purchase_id','shows.id AS show_id','purchases.show_time_id')
                                ->where('purchases.id','=',$input['id'])->first();
                $showtimes = DB::table('show_times')->select('id','show_time')
                                ->where('show_id','=',$current->show_id)->where('is_active','=',1)->where('show_times.show_time','>',date('Y-m-d H:i:s'))
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
                        //calculate target result
                        $target = ['t_ticket_type'=>$ticket->ticket_type,'t_title'=>$ticket->package->title,'t_retail_price'=>$ticket->retail_price,
                                   't_is_active'=>$ticket->is_active,'t_code'=>$discount->code,
                                   't_processing_fee'=>$ticket->processing_fee,'t_percent_pf'=>$ticket->t_percent_pf,'t_fixed_commission'=>$ticket->fixed_commission,
                                   't_percent_commission'=>$ticket->percent_commission,'t_quantity'=>$qty,'t_show_time'=>$showtime->show_time,
                                   't_p_retail_price'=>$ticket->retail_price*$qty,
                                   't_p_processing_fee'=>(!empty($ticket->processing_fee))? $ticket->processing_fee*$qty : $ticket->t_percent_pf/100*$ticket->retail_price*$qty];
                        //calculate savings result
                        $target['t_savings'] = $discount->calculate_savings($qty,$target['t_p_retail_price'] + $target['t_p_processing_fee']);
                        //calculate commission result
                        $c = DB::table('discount_tickets')->select('fixed_commission')
                                    ->where('discount_id','=',$discount->id)->where('ticket_id','=',$ticket->id)->first();
                        $fixed_commission = (!empty($c->fixed_commission))? $c->fixed_commission : $ticket->fixed_commission;
                        $target['t_commission_percent'] = (!empty($fixed_commission))? $fixed_commission*$qty : $ticket->percent_commission/100*$ticket->retail_price*$qty;
                        //calculate total result
                        $target['t_price_paid'] = $target['t_p_retail_price'] + $target['t_p_processing_fee'] - $target['t_savings'];
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
                $search = [];
                $status = [];
                $search['venues'] = [];
                $search['shows'] = [];
                $search['payment_types'] = Util::getEnumValues('purchases','payment_type');
                $search['users'] = User::orderBy('email')->get(['id','email']);
                $search['customers'] = Customer::orderBy('email')->get(['id','email']);
                $purchases = [];
                $where = [['purchases.id','>',0]];
                //search user
                if(isset($input) && isset($input['user']))
                {
                    $search['user'] = $input['user'];
                    if($search['user'] != '')
                        $where[] = ['purchases.user_id','=',$input['user']];
                }
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
                //search soldtime
                if(isset($input) && isset($input['soldtime_start_date']) && isset($input['soldtime_end_date']))
                {
                    $search['soldtime_start_date'] = $input['soldtime_start_date'];
                    $search['soldtime_end_date'] = $input['soldtime_end_date'];
                }
                else
                {
                    $search['soldtime_start_date'] = date('Y-m-d', strtotime('-7 DAY'));
                    $search['soldtime_end_date'] = date('Y-m-d');
                }
                if($search['soldtime_start_date'] != '' && $search['soldtime_end_date'] != '')
                {
                    $where[] = [DB::raw('DATE(purchases.created)'),'>=',$search['soldtime_start_date']];
                    $where[] = [DB::raw('DATE(purchases.created)'),'<=',$search['soldtime_end_date']];
                } 
                //search payment types        
                if(isset($input) && isset($input['payment_type']) && !empty($input['payment_type']))
                {
                    $search['payment_type'] = $input['payment_type'];
                }
                else
                {
                    $search['payment_type'] = array_values($search['payment_types']);
                }
                //search user      
                if(isset($input) && !empty($input['user']))
                {
                    $search['user'] = $input['user'];
                    $where[] = ['purchases.user_id','=',$search['user']];
                }
                else
                    $search['user'] = '';
                //search customer      
                if(isset($input) && !empty($input['customer']))
                {
                    $search['customer'] = $input['customer'];
                    $where[] = ['purchases.customer_id','=',$search['customer']];
                }
                else
                    $search['customer'] = '';
                //search order id      
                if(isset($input) && !empty($input['order_id']) && is_numeric($input['order_id']))
                {
                    $search['order_id'] = $input['order_id'];
                    $where[] = ['purchases.id','=',$search['order_id']];
                }
                else
                    $search['order_id'] = ''; 
                //if user has permission to view                
                if(in_array('View',Auth::user()->user_type->getACLs()['PURCHASES']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['PURCHASES']['permission_scope'] != 'All')
                    {
                        if(count($input)) 
                        $purchases = DB::table('purchases')
                                    ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                                    ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                                    ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                                    ->join('shows', 'shows.id', '=', 'show_times.show_id')
                                    ->join('venues', 'venues.id', '=', 'shows.venue_id')
                                    ->join('tickets', 'tickets.id', '=', 'purchases.ticket_id')
                                    ->join('packages', 'packages.id', '=', 'tickets.package_id')
                                    ->leftJoin('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                                    ->select(DB::raw('purchases.*, transactions.card_holder, transactions.authcode, transactions.refnum, transactions.last_4,
                                                      IF(transactions.amount IS NOT NULL,transactions.amount,purchases.price_paid) AS amount, 
                                                      (CASE WHEN (purchases.ticket_type = "Consignment") THEN purchases.ticket_type ELSE purchases.payment_type END) AS method,
                                                      IF(transactions.id IS NOT NULL,transactions.id,CONCAT(purchases.session_id,purchases.created)) AS color,
                                                      discounts.code, tickets.ticket_type AS ticket_type_type,
                                                      venues.name AS venue_name, customers.first_name, customers.last_name, customers.email, customers.phone,
                                                      show_times.show_time, shows.name AS show_name, packages.title'))
                                    ->where($where)
                                    ->where(function($query)
                                    {
                                        $query->whereIn('shows.venue_id',[Auth::user()->venues_edit])
                                              ->orWhere('shows.audit_user_id','=',Auth::user()->id);
                                    })
                                    ->orderBy('purchases.created','purchases.transaction_id','purchases.user_id','purchases.price_paid')
                                    ->havingRaw('method IN ("'.implode('","',$search['payment_type']).'")')
                                    ->get();
                        $search['venues'] = Venue::whereIn('id',explode(',',Auth::user()->venues_edit))->orderBy('name')->get(['id','name']);
                        $search['shows'] = Show::whereIn('venue_id',explode(',',Auth::user()->venues_edit))->orWhere('audit_user_id',Auth::user()->id)->orderBy('name')->get(['id','name','venue_id']);

                    }//all
                    else
                    {
                        if(count($input)) 
                        $purchases = DB::table('purchases')
                                    ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                                    ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                                    ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                                    ->join('shows', 'shows.id', '=', 'show_times.show_id')
                                    ->join('venues', 'venues.id', '=', 'shows.venue_id')
                                    ->join('tickets', 'tickets.id', '=', 'purchases.ticket_id')
                                    ->join('packages', 'packages.id', '=', 'tickets.package_id')
                                    ->leftJoin('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                                    ->select(DB::raw('purchases.*, transactions.card_holder, transactions.authcode, transactions.refnum, transactions.last_4,
                                                      IF(transactions.amount IS NOT NULL,transactions.amount,purchases.price_paid) AS amount, 
                                                      (CASE WHEN (purchases.ticket_type = "Consignment") THEN purchases.ticket_type ELSE purchases.payment_type END) AS method,
                                                      IF(transactions.id IS NOT NULL,transactions.id,CONCAT(purchases.session_id,purchases.created)) AS color,
                                                      discounts.code, tickets.ticket_type AS ticket_type_type,
                                                      venues.name AS venue_name, customers.first_name, customers.last_name, customers.email, customers.phone,
                                                      show_times.show_time, shows.name AS show_name, packages.title'))
                                    ->where($where)
                                    ->orderBy('purchases.created','purchases.transaction_id','purchases.user_id','purchases.price_paid')
                                    ->havingRaw('method IN ("'.implode('","',$search['payment_type']).'")')
                                    ->get();
                        $search['venues'] = Venue::orderBy('name')->get(['id','name']);
                        $search['shows'] = Show::orderBy('name')->get(['id','name','venue_id']);
                    }   
                    $status = Util::getEnumValues('purchases','status');
                }
                $modal = (count($input))? 0 : 1;
                return view('admin.purchases.index',compact('purchases','status','search','modal'));
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
                    //re-send email if change form active to any inactive and viceversa
                    if($input['status']=='Active' || $old_status=='Active')
                    {
                        $receipt = $purchase->get_receipt();
                        $status = ($input['status']=='Active')? 'ACTIVATED' : 'CANCELED';
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
            else if($input && isset($input['purchase_id']))
            {
                $purchase = Purchase::find($input['purchase_id']);
                if($purchase)
                {
                    $note = '&nbsp;<br><b>'.Auth::user()->first_name.' '.Auth::user()->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b> Change ';
                    if(!empty($input['to_show_time_id']) && $purchase->show_time_id != $input['to_show_time_id'])
                    {
                        $from = ShowTime::find($purchase->show_time_id);
                        $to = ShowTime::find($input['to_show_time_id']);
                        $note.= ', date from '.date('m/d/Y g:i a',strtotime($from->show_time)).' to '.date('m/d/Y g:i a',strtotime($to->show_time));
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
                        $note.= ', qty from '.$purchase->quantity.' to '.$input['to_quantity'];
                        $purchase->quantity = $input['to_quantity'];
                    }
                    $purchase->note = ($purchase->note)? $purchase->note.$note : $note;                     
                    $purchase->retail_price = $input['t_p_retail_price'];
                    $purchase->processing_fee = $input['t_p_processing_fee'];
                    $purchase->savings = $input['t_savings'];
                    $purchase->commission_percent = $input['t_commission_percent'];
                    if($purchase->price_paid != $input['t_price_paid'])
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
                    $purchase->save();
                    return ['success'=>true,'msg'=>'Purchase saved successfully!'];
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
                            //$email->cc(env('MAIL_REPORT_CC'));
                            $email->category('Custom');
                            $email->body('custom',['body'=>$input['email'][3]['value']]);
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
}                    