<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Mail\EmailSG;
use Barryvdh\DomPDF\Facade as PDF;

/**
 * Purchase class
 *
 * @author ivan
 */
class Purchase extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchases';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Get the transaction record associated with the purchase.
     */
    //RELATIONSHIPS ONE-MANY
    public function transaction()
    {
        return $this->belongsTo('App\Http\Models\Transaction','transaction_id');
    }
    /**
     * Get the user record associated with the purchase.
     */
    public function user()
    {
        return $this->belongsTo('App\Http\Models\User','user_id');
    }
    /**
     * Get the discount record associated with the purchase.
     */
    public function discount()
    {
        return $this->belongsTo('App\Http\Models\Discount','discount_id');
    }
    /**
     * Get the customer record associated with the purchase.
     */
    public function customer()
    {
        return $this->belongsTo('App\Http\Models\Customer','customer_id');
    }
    /**
     * Get the ticket record associated with the purchase.
     */
    public function ticket()
    {
        return $this->belongsTo('App\Http\Models\Ticket','ticket_id');
    }
    /**
     * Get the show_time record associated with the purchase.
     */
    public function show_time()
    {
        return $this->belongsTo('App\Http\Models\ShowTime','show_time_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * Get the ticket_number record associated with the purchase.
     */
    public function ticket_numbers()
    {
        return $this->belongsToMany('App\Http\Models\Customer','ticket_number','purchases_id','customers_id')->withPivot('id','tickets','checked','comment');
    }
    //PERSONALIZED FUNCTIONS
    /**
     * Get the purchase receipt info.
     */
    public function share_tickets($shared=[])
    {
        try {
            $current = date('Y-m-d H:i:s');
            $qty_shared = 0;
            $ticket_number = [];
            foreach ($shared as $s)
            {
                if($s['email'] != $this->customer->email)
                {
                    if($qty_shared < $this->quantity)
                    {
                        //check qty
                        if($s['qty']>$this->quantity-$qty_shared)
                            $s['qty'] = $this->quantity-$qty_shared;
                        //set up customer
                        $customer = Customer::where('email',$s['email'])->first();
                        if(!$customer)
                        {
                            $user = User::where('email',$s['email'])->first();
                            $customer = new Customer;
                            $location = new Location;
                            $location->created = $current;
                            $location->updated = $current;
                            $location->address = ($user)? $user->location->address : 'Unknown';
                            $location->city = ($user)? $user->location->city : 'Unknown';
                            $location->state = ($user)? $user->location->state : 'NA';
                            $location->zip = ($user)? $user->location->zip : null;
                            $location->country = ($user)? $user->location->country : 'US';
                            $location->lng = ($user)? $user->location->lng : null;
                            $location->lat = ($user)? $user->location->lat : null;
                            $location->save();
                            //save customer
                            $customer->location_id = $location->id;
                            $customer->first_name = trim(strip_tags($s['first_name']));
                            $customer->last_name = (!empty($s['last_name']))? trim(strip_tags($s['last_name'])) : (($user)? $user->last_name : null);
                            $customer->email = trim(strip_tags($s['email']));
                            $customer->phone = ($user)? $user->phone : null;
                            $customer->created = $current;
                            $customer->updated = $current;
                            $customer->save();
                        }
                        //create tickets number
                        $tickets = implode(',',range($qty_shared+1,$qty_shared+$s['qty']));
                        $qty_shared+=$s['qty'];
                        $comment = (!empty(trim(strip_tags($s['comment']))))? trim(strip_tags($s['comment'])) : null;
                        $ticket_number[] = ['purchases_id'=> $this->id, 'customers_id'=>$customer->id, 'tickets'=>$tickets, 'comment'=>$comment];
                    }
                }
            }
            //if missing tickets to share put them to the customer
            if($qty_shared<$this->quantity)
            {
                $tickets = implode(',',range($qty_shared+1,$this->quantity));
                $ticket_number[] = ['purchases_id'=> $this->id, 'customers_id'=> $this->customer_id, 'tickets'=>$tickets, 'comment'=>null];
            }
            //save if there is values to save
            if(count($ticket_number))
            {
                DB::table('ticket_number')->where('purchases_id', $this->id)->delete();
                DB::table('ticket_number')->insert($ticket_number);
            }
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    /**
     * Get the purchase receipt info.
     */
    public function get_receipt()
    {
        //get purchase info mix
        $purchase = DB::table('purchases')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                            ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                            ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                            ->join('shows', 'shows.id', '=', 'show_times.show_id')
                            ->join('venues', 'venues.id', '=', 'shows.venue_id')
                            ->join('locations', 'locations.id', '=', 'venues.location_id')
                            ->leftJoin('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                            ->leftJoin('ticket_number', 'ticket_number.purchases_id', '=', 'purchases.id')
                            ->select(DB::raw('purchases.*, purchases.quantity AS qty, tickets.ticket_type AS ticket_type_type, show_times.time_alternative,
                                    show_times.show_time, discounts.code, packages.title, locations.lat, locations.lng, tickets.inclusive_fee,
                                    shows.name AS show_name, shows.slug, shows.restrictions, shows.emails, shows.printed_tickets, shows.ticket_info AS s_ticket_info,
                                    shows.individual_emails AS s_individual_emails, shows.manifest_emails AS s_manifest_emails, show_times.show_id,
                                    shows.daily_sales_emails AS s_daily_sales_emails, tickets.retail_price AS price_each,
                                    venues.name AS venue_name, venues.ticket_info, venues.daily_sales_emails AS v_daily_sales_emails,
                                    venues.weekly_sales_emails AS v_weekly_sales_emails,
                                    IF(ticket_number.id IS NULL, 0, 1) as section'))
                            ->where('purchases.id', '=', $this->id)
                            ->distinct()->first();
        //get customer info mix
        $customer = DB::table('customers')
                            ->join('locations', 'locations.id', '=' ,'customers.location_id')
                            ->select('customers.*', 'locations.*')
                            ->where('customers.id', '=', $this->customer_id)
                            ->first();
        //get tickets
        $tickets = [];

        if($purchase)
        {
            //get all tickets by section
            if($purchase->section)
            {
                $ticket_numbers = DB::table('ticket_number')
                                ->join('customers', 'customers.id', '=' ,'ticket_number.customers_id')
                                ->select('ticket_number.*', 'customers.first_name', 'customers.last_name', 'customers.email')
                                ->where('ticket_number.purchases_id', '=', $this->id)
                                ->orderBy('ticket_number.id')
                                ->get();
                for ($i=1; $i<=$this->quantity; $i++)
                foreach($ticket_numbers as $tn)
                    if(in_array($i,explode(',',$tn->tickets)))
                    {
                        $main_info = ['number'=>$i,'customer_name'=>$tn->first_name.' '.$tn->last_name,'customer_email'=>$tn->email,'checked'=>(in_array($i,explode(',',$tn->checked)))? $checked_= 1 : $checked_= 0,'comment'=>$tn->comment,'QRcode'=>Util::getQRcode($this->id,$this->user_id,$i)];
                        $extra_info = ['show_name'=>$purchase->show_name,'show_time'=>$purchase->show_time,'price_each'=>number_format($this->price_paid/$this->quantity,2),'id'=>$this->id,'venue_name'=>$purchase->venue_name,'restrictions'=>$purchase->restrictions,'user_id'=>$this->user_id,'ticket_type'=>$purchase->ticket_type_type,'time_alternative'=>$purchase->time_alternative,'package'=>$purchase->title];
                        $tickets[] = array_merge($main_info,$extra_info);
                    }
            }
            //get all tickets by section/row/seat
            else
            {
                $seats = DB::table('seats')
                                    ->join('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                    ->join('purchases', 'purchases.id', '=' ,'seats.purchase_id')
                                    ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                                    ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                    ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                                    ->select(DB::raw('seats.id,seats.purchase_id,seats.consignment_id,seats.ticket_id,seats.seat,seats.show_seat,seats.status,seats.updated, tickets.ticket_type,
                                                      COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0)) AS retail_price, purchases.savings,
                                                      COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0)) AS processing_fee,
                                                      COALESCE(seats.percent_commission,COALESCE(tickets.percent_commission,0)) AS percent_commission,
                                                      shows.name AS show_name,show_times.show_time,venues.name AS venue_name,shows.restrictions,show_times.time_alternative'))
                                    ->where('seats.purchase_id',$this->id)
                                    ->orderBy('tickets.ticket_type','seats.seat')
                                    ->distinct()->get();
                foreach ($seats as $s)
                {
                    $location = $s->ticket_type;
                    if($s->show_seat)
                        $location .= ' Seat: '.$s->seat;
                    $main_info = ['number'=>$s->id,'customer_name'=>'','customer_email'=>'','checked'=>($s->status == 'Checked')? $checked_= 1 : $checked_= 0,'comment'=>'','QRcode'=>Util::getQRcode($this->id,$this->user_id,$s->id)];
                    $extra_info = ['show_name'=>$s->show_name,'show_time'=>$s->show_time,'price_each'=>number_format($s->retail_price+$s->processing_fee,2),'id'=>$this->id,'venue_name'=>$s->venue_name,'restrictions'=>$s->restrictions,
                                   'user_id'=>$this->user_id,'ticket_type'=>$s->ticket_id,'time_alternative'=>$s->time_alternative,'package'=>$location];
                    $tickets[] = array_merge($main_info,$extra_info);
                }
            }
        }
        //get banners from shows, if not then banners from venues
        $use = ['show_id'=>$this->ticket->show_id, 'venue_id'=>$this->ticket->show->venue_id];
        $banners = DB::table('banners')
                            ->select(DB::raw('banners.id, banners.url, banners.file'))
                            ->where(function($query) use ($use) {
                                $query->whereRaw('banners.parent_id = '.$use['show_id'].' AND banners.belongto="show" ')
                                      ->orWhereRaw('banners.parent_id = '.$use['venue_id'].' AND banners.belongto="venue" ');
                            })
                            ->where('banners.type','like','%Receipt Email%')->get();
        foreach ($banners as $b)
            $b->file = Image::view_image($b->file);
        //return data
        return ['purchase' => $purchase, 'customer' => $customer, 'tickets' => $tickets, 'banners'=> $banners];
    }
    /**
     * Get the purchase receipt info.
     */
    public function set_pending()
    {
        //get purchase info mix
        $purchase = DB::table('purchases')
                            ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                            ->leftJoin('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                            ->select(DB::raw('purchases.id, purchases.quantity AS qty, purchases.payment_type, purchases.status, purchases.ticket_type,
                                    IF(transactions.amount IS NOT NULL, transactions.amount, purchases.price_paid) as amount,
                                    transactions.trans_result, transactions.card_holder, transactions.authcode, transactions.refnum, transactions.last_4,
                                    customers.first_name, customers.last_name, customers.phone, customers.email, transactions.id AS transaction_id,
                                    purchases.created'))
                            ->where('purchases.id', '=', $this->id)
                            ->groupBy('purchases.id')->first();
        if(!$purchase)
            return false;
        $link = str_replace('/save', '', url()->current()).'?order_id='.$this->id.'&soldtime_start_date=&soldtime_end_date=';
        $html  = '<b>PURCHASE PENDING TO REFUND</b><br><br>';
        $html .= '<b>Request by:</b> '.Auth::user()->first_name.' '.Auth::user()->last_name.' ('.Auth::user()->email.')<br><br>';
        $html .= '<b>Customer:</b> '.$purchase->first_name.' '.$purchase->last_name.'<br>';
        $html .= '<b>Email:</b> <a href="mailto:'.$purchase->email.'" target="_top">'.$purchase->email.'</a> <b>Phone:</b> '.$purchase->phone.'<br><br>';
        $html .= '<b>Purchase:</b> '.$purchase->id.' <b>Status:</b> '.$purchase->status.'<br>';
        $html .= '<b>Tickets:</b> '.$purchase->qty.' / '.$purchase->ticket_type.'<br>';
        $html .= '<b>Created:</b> '.$purchase->created.'<br>';
        $html .= '<b>Payment:</b> '.$purchase->payment_type.' <b>Amount:</b> $ '.$purchase->amount.'<br><br>';
        $html .= '<b>Transacion:</b> '.$purchase->transaction_id.' <b>Status:</b> '.$purchase->trans_result.'<br>';
        $html .= '<b>Cardholder:</b> '.$purchase->card_holder.' <b>Card:</b> ...'.$purchase->last_4.'<br>';
        $html .= '<b>Authcode:</b> '.$purchase->authcode.' <b>Refnum:</b> '.$purchase->refnum.'<br><br>';
        $html .= '<b>Click here to update status:</b> <a href="'.$link.'">'.$link.'</a><br><br>';
        //send email
        $email = new EmailSG(null, env('MAIL_PURCHASE_PENDING','MAIL_ADMIN'), 'TicketBat Admin: Purchase pending to refund');
        $email->category('Custom');
        $email->body('custom',['body'=>$html]);
        $email->template('46388c48-5397-440d-8f67-48f82db301f7');
        return ($email->send());
    }
    /**
     * Send by email given purchases receipts.
     */
    public static function email_receipts($subject,$receipts,$type_email,$change=null,$promotor_copy=false,$receipt_view=false,$resend_to=null,$only_receipt=false)
    {
        try {
            if(is_array($receipts) && count($receipts) && is_string($subject) && is_string($type_email))
            {   
                //init variables
                $rows_html = $totals_html = $coupon_code = '';
                $pdf_receipts = $pdf_tickets = $purchases = $ticket_info = [];
                $totals = ['qty'=>0,'processing_fee'=>0,'retail_price'=>0,'printed_fee'=>0,'sales_taxes'=>0,'discount'=>0];
                $top = $banners = '';
                //set customer
                $customer = $receipts[0]['customer'];
                //loop receipts
                foreach ($receipts as $receipt)
                {
                    if($receipt['purchase']->qty>0)
                    {
                        $purchases[] = $receipt['purchase'];

                        //receipt
                        $purchase = array_merge((array)$receipt['customer'],(array)$receipt['purchase']);
                        if(!empty($receipt['purchase']->s_ticket_info) && !isset($ticket_info[$receipt['purchase']->show_id]))
                            $ticket_info[$receipt['purchase']->show_id] = $receipt['purchase']->s_ticket_info;
                        
                        //create pdf receipt
                        $format = 'pdf';
                        $pdfUrlR = '/tmp/Receipt_'.$receipt['purchase']->id.'_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$receipt['purchase']->ticket_type).'_'.date("m_d_Y_h_i_a",strtotime($receipt['purchase']->show_time)).'.pdf';
                        $pdf_receipt = View::make('command.report_sales_receipt', compact('purchase','format'));
                        if(file_exists($pdfUrlR)) unlink($pdfUrlR);
                        PDF::loadHTML($pdf_receipt->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdfUrlR);
                        $pdf_receipts[] = $pdfUrlR;

                        //create pdf tickets                
                        if($receipt['purchase']->printed_tickets == 0)
                        {
                            $tickets = $receipt['tickets'];
                            $type = 'C';
                            $pdfUrlT = '/tmp/Tickets_'.$receipt['purchase']->id.'_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$receipt['purchase']->ticket_type).'_'.date("m_d_Y_h_i_a",strtotime($receipt['purchase']->show_time)).'.pdf';
                            $pdf_ticket = View::make('command.report_sales_receipt_tickets', compact('tickets','type','format'));
                            if(file_exists($pdfUrlT)) unlink($pdfUrlT);
                            PDF::loadHTML($pdf_ticket->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdfUrlT);
                            $pdf_tickets[] = $pdfUrlT;
                        }

                        if($type_email != 'reminder')
                        {
                            if($receipt['purchase']->inclusive_fee>0)
                                $purchase['processing_fee']=0;
                            //row on email to each purchase
                            $rows_html.='<tr>'
                                            . '<td align="center">'.$receipt['purchase']->ticket_type_type.' For '.$receipt['purchase']->show_name.'<br/>On '.date('l, F jS - g:i A',strtotime($receipt['purchase']->show_time)).'</td> '
                                            . '<td align="center">'.$receipt['purchase']->quantity.'</td> '
                                            . '<td align="center">'.number_format($purchase['price_each'],2).'</td>  '
                                            . '<td align="center">'.number_format($purchase['retail_price'],2).'</td> '
                                            . '<td align="center">'.number_format($purchase['processing_fee'],2).'</td> </tr>';
                            //sum values to show
                            $totals['qty']+=$receipt['purchase']->quantity;
                            $totals['processing_fee']+=$purchase['processing_fee'];
                            $totals['retail_price']+=$receipt['purchase']->retail_price;
                            $totals['discount']+=$receipt['purchase']->savings;
                            $totals['printed_fee']+=$receipt['purchase']->printed_fee;
                            $totals['sales_taxes']+=$receipt['purchase']->sales_taxes;
                            //show on top if change status
                            if($change=='CANCELED' || $change=='REFUNDED')
                                $top = '<h1><b style="color:red">THIS PURCHASE HAS BEEN CANCELLED</b></h1>' ;
                            
                            else if($change=='ACTIVATED')
                                $top = '<h1><b style="color:green">THIS PURCHASE HAS BEEN ACTIVED</b></h1>' ;
                            else
                                $top = '';
                        }
                        //show on top if change date
                        if($type_email == 'changed')
                        {
                            $top = 'This purchase of '.$receipt['purchase']->quantity.' '.$receipt['purchase']->ticket_type_type.' ticket(s) for '.
                                    $receipt['purchase']->show_name.' on '.date('l, F jS - g:i A',strtotime($change)).
                                    ' has been changed to '.date('l, F jS - g:i A',strtotime($receipt['purchase']->show_time)).
                                    '.<br>The updated receipt and tickets are attached.<br><br>' ;
                        }
                        //show coupon code used
                        if(empty($coupon_code))
                            $coupon_code = $receipt['purchase']->code;
                    }
                }
                //table on email to show all totals
                $totals['total'] = $totals['retail_price'] + $totals['processing_fee'] - $totals['discount']  + $totals['printed_fee'] + $totals['sales_taxes'];
                $totals_html = '<tr> <td align="right" width="80%">Subtotal:</td> <td width="20%" align="right">$ '.number_format($totals['retail_price'],2).'</td> </tr>
                                <tr> <td align="right">Processing Fee:</td> <td align="right">$ '.number_format($totals['processing_fee'],2).'</td> </tr>';
                if($totals['discount'] > 0)
                    $totals_html.='<tr> <td align="right">Discount (<b>'.$coupon_code.'</b>):</td> <td align="right">- $ '.number_format($totals['discount'],2).'</td> </tr>';
                if($totals['printed_fee'] > 0)
                    $totals_html.='<tr> <td align="right">Printer fee:</td> <td align="right">$ '.number_format($totals['printed_fee'],2).'</td> </tr>';
                $totals_html.='<tr> <td align="right">Sales taxes:</td> <td align="right">$ '.number_format($totals['sales_taxes'],2).'</td> </tr>';
                $totals_html.='<tr> <td align="right" style="color:#1F9F0B;"><b>GRAND TOTAL</b>:</td> <td align="right" style="color:#1F9F0B;">$ '.number_format($totals['total'],2).'</td> </tr>';

                //banners
                if(!empty($receipt['banners']))
                    foreach ($receipt['banners'] as $b)
                        $banners .= '<div><a href="'.$b->url.'"><img src="'.$b->file.'"/></a></div>';
                
                //ticket_info
                if(!empty($ticket_info))
                    $top .= '<b>'.implode('<br>', $ticket_info).'</b>';

                //send email
                $send_to = ($resend_to && filter_var($resend_to, FILTER_VALIDATE_EMAIL))? $resend_to : $customer->email;
                $email = new EmailSG(null, $send_to , $subject);
                $email->category('Receipts');
                if($only_receipt)
                    $email->attachment($pdf_receipts);
                else
                    $email->attachment(array_merge($pdf_receipts,$pdf_tickets));
                //check type of email to send
                if($type_email === 'reminder')
                {
                    $email->body('reminder',['purchase'=>$purchases,'customer'=>$customer]);
                    $email->template('330de7c4-3d1c-47b5-9f48-ca376cbbea99');
                }
                else
                {
                    //info to send by email content
                    $email->body('receipt',['rows'=>$rows_html,'totals'=>$totals_html,'banners'=>$banners,'top'=>$top]);
                    $email->template('98066597-4797-40bf-b95a-0219da4ca1dc');
                }
                $response = $email->send();

                //send copy to event promotor if available option
                if($promotor_copy)
                {
                    $p = $receipts[0]['purchase'];
                    if($p->s_individual_emails == 1 && !empty($p->emails))
                    {
                        if($change=='REFUNDED')
                        {
                            $subject = 'TicketBat :: Credit Card Dispute # '.$receipt['purchase']->id;
                            $top_copy  = '<b style="color:red">Credit Card Dispute<br><br>';
                            $top_copy .= 'Please verify that this guest picked up their tickets and attended the show by providing the signed header card and Seat Retrieval Report showing they entered the showroom.<br><br>';
                            $top_copy .= 'If the guest was a no show, DO NOT RETURN the tickets.  The Settlement Team will make the adjustment.<br><br>';
                            $top_copy .= 'Please reply to this email.</b><br><br>';
                        }
                        else if($type_email=='changed')
                        {
                            $subject = 'TicketBat :: Date Changed for order #'.$receipt['purchase']->id;
                            $top_copy = $top ;
                            $to_e1 = explode(',', $p->emails);
                            $to_e2 = explode(',', env('MAIL_ACCOUNTING_TO','') );
                            $p->emails = array_unique( array_merge($to_e1,$to_e2) );
                        }
                        else 
                        {
                            $subject = $subject.' (BO Receipt)';
                            $top_copy = $top;
                        }
                        $email = new EmailSG(null, $p->emails , $subject);
                        $email->category('Receipts');
                        $email->attachment(array_merge($pdf_receipts,$pdf_tickets));
                        //check type of email to send
                        if($type_email === 'reminder')
                        {
                            $email->body('reminder',['purchase'=>$purchases,'customer'=>$customer]);
                            $email->template('330de7c4-3d1c-47b5-9f48-ca376cbbea99');
                        }
                        else
                        {
                            //info to send by email content
                            $email->body('receipt',['rows'=>$rows_html,'totals'=>$totals_html,'banners'=>$banners,'top'=>$top_copy]);
                            $email->template('98066597-4797-40bf-b95a-0219da4ca1dc');
                        }
                        $email->send();
                    }
                }
                //clean up and return
                foreach(array_merge($pdf_receipts,$pdf_tickets) as $link)
                    if(file_exists($link)) unlink($link);
                return $response;
            }
            else return false;
        } catch (Exception $ex) {
            return false;
        }
    }
    
    /**
     * Send by email given purchases receipts.
     */
    public static function print_receipts($purchases)
    {
        //init variables
        $format = 'printer';
        $printer = ['order_id'=>$purchases, 'restrictions'=>[], 'items'=>[], 'qty'=>0, 'total'=>0, 'info'=>[]];
        try {
            
            if(!empty($purchases))
            {
                $purchases = explode(',', $purchases);
                $items = DB::table('purchases')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                            ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                            ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                            ->join('shows', 'shows.id', '=', 'show_times.show_id')
                            ->join('venues', 'venues.id', '=', 'shows.venue_id')
                            ->select(DB::raw('purchases.id, purchases.quantity, purchases.price_paid, tickets.ticket_type AS ticket_type_type, show_times.time_alternative,
                                    show_times.show_time, discounts.code, IF(packages.title!="None", packages.title, "") AS title, tickets.inclusive_fee,
                                    shows.name AS show_name, shows.restrictions, 
                                    venues.name AS venue_name, venues.ticket_info'))
                            ->whereIn('purchases.id', $purchases)
                            ->groupBy('purchases.id')->get(); 
                
                //get info
                foreach ($items as $i)
                {
                    
                    if(!empty($i->restrictions) && $i->restrictions != 'None' && !in_array($i->restrictions, $printer['restrictions']))
                        $printer['restrictions'][] = $i->restrictions;
                    $printer['qty'] += $i->quantity;
                    $printer['total'] += $i->price_paid;
                    if(!empty($i->ticket_info) && !in_array($i->ticket_info, $printer['info']))
                        $printer['info'][] = $i->ticket_info;
                    $printer['items'][] = $i;
                }
            }
        } catch (Exception $ex) {
            
        } finally {
            return View::make('command.report_sales_receipt', compact('printer','format'))->render();
        }
    }

    /*
     * saving the purchase into the database
     */
    public static function purchase_save($x_token,$client,$shoppingcart,$current,$app=false)
    {
        try {
            $purchase_ids=[];
            $errors_ids=[];
            foreach ($shoppingcart['items'] as $i)
            {
                //create purchase
                $purchase = new Purchase;
                $purchase->user_id = $client['user_id'];
                $purchase->customer_id = $client['customer_id'];
                $purchase->transaction_id = (!empty($shoppingcart['transaction_id']))? $shoppingcart['transaction_id'] : null;
                $purchase->discount_id = $i->discount_id;
                $purchase->ticket_id = $i->ticket_id;
                $purchase->show_time_id = $i->item_id;
                $purchase->session_id = $x_token;
                $purchase->referrer_url = ($app)? 'http://app.ticketbat.com' : substr(strval( url()->current() ),0,499);
                $purchase->quantity = $i->number_of_items;
                $purchase->savings = $i->savings;
                $purchase->status = 'Active';
                $purchase->ticket_type = $i->name.' '.$i->product_type;
                $purchase->retail_price = $i->retail_price;
                $purchase->commission_percent = $i->commission;
                $purchase->processing_fee = $i->processing_fee;
                $purchase->price_paid = $i->total_cost;
                $purchase->inclusive_fee = (!empty($i->inclusive_fee))? 1 : 0;
                $purchase->payment_type = ($purchase->retail_price<0.01 && $purchase->price_paid<0.01)? 'Free event' : ( (!empty($shoppingcart['payment_type']))? $shoppingcart['payment_type'] : 'None' );
                //taxes and other fees
                $purchase->sales_taxes = $i->sales_taxes;
                $purchase->printed_fee = $shoppingcart['printed'];
                $purchase->cc_fees = ($purchase->payment_type=='Credit')? Util::round($purchase->price_paid*env('USAEPAY_CREDIT_CARD_FEE_PERCENT',0)/100) : 0.00;
                $purchase->updated = $current;
                $purchase->created = $current;
                $purchase->merchandise = ($i->product_type=='merchandise')? 1 : 0;
                $purchase->channel = ($app)? 'App' : ((Auth::check() && in_array(Auth::user()->user_type_id,explode(',',env('SELLER_OPTION_USER_TYPE')))? 'POS' : 'Web'));
                if($purchase->save())
                {
                    //get id for receipts
                    $purchase_ids[] = $purchase->id;
                    //get shoppingcart
                    $sc = Shoppingcart::find($i->id);
                    if($sc)
                    {
                        if(!empty($i->consignment) && !empty($i->seat))
                        {
                            $seat = Seat::find($i->seat);
                            if($seat)
                            {
                                $seat->purchase_id = $purchase->id;
                                $seat->status = 'Sold';
                                $seat->save();
                            }
                        }
                        else
                        {
                            if(!empty($cs->gifts) && Util::isJSON($cs->gifts))
                            {
                                $shared = [];
                                $indexes = json_decode($cs->gifts,true);
                                foreach ($indexes as $i)
                                    $shared[] = ['first_name'=>$i['first_name'],'last_name'=>$i['last_name'],'email'=>$i['email'],
                                                 'comment'=>(!empty($i['comment']))? $i['comment'] : null,'qty'=>$i['qty']];
                                $purchase->share_tickets($shared);
                            }
                            else
                            {
                                //create tickets, no gifts
                                $tickets = implode(',',range(1,$purchase->quantity));
                                DB::table('ticket_number')->insert( ['purchases_id'=>$purchase->id,'customers_id'=>$purchase->customer_id,'tickets'=>$tickets] );
                            }
                        }
                        //remove item from shoppingcart
                        $sc->delete();
                    }
                }
                else
                    $errors_ids[] = $i->id;
            }
            return ['success'=>true, 'ids'=>$purchase_ids, 'errors'=>$errors_ids];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
    
    /**
     * Filter purchases according to conditions.
     */
    public static function filter_options($module,$input,$default_date_range=null)
    {
        $data = [ 'where'=>[ ['purchases.id','>',0] ],'search'=>[ 'venues'=>[],'shows'=>[] ] ];
        
        try {
            //FILTER SEARCH INPUT
            $data['search']['payment_types'] = Util::getEnumValues('purchases','payment_type');
            $data['search']['ticket_types'] = Util::getEnumValues('tickets','ticket_type');
            $data['search']['status'] = Util::getEnumValues('purchases','status');
            $data['search']['channels'] = Util::getEnumValues('purchases','channel');
            //if values
            if(isset($input))
            {
                //search venue
                $data['search']['venue'] = (!empty($input['venue']))? $input['venue'] : '';
                if(!empty($input['venue']))
                    $data['where'][] = ['shows.venue_id','=',$data['search']['venue']];                
                
                //search show
                $data['search']['show'] = (!empty($input['show']))? $input['show'] : '';
                if(!empty($input['show']))
                    $data['where'][] = ['shows.id','=',$data['search']['show']];
                //search showtime range
                $data['search']['showtime_start_date'] = (!empty($input['showtime_start_date']))? $input['showtime_start_date'] : '';
                $data['search']['showtime_end_date'] = (!empty($input['showtime_end_date']))? $input['showtime_end_date'] : '';
                if(!empty($data['search']['showtime_start_date']) && !empty($data['search']['showtime_end_date']))
                {
                    $data['where'][] = [DB::raw('DATE(show_times.show_time)'),'>=',date('Y-m-d',strtotime($data['search']['showtime_start_date']))];
                    $data['where'][] = [DB::raw('DATE(show_times.show_time)'),'<=',date('Y-m-d',strtotime($data['search']['showtime_end_date']))];
                }
                //search showtime   
                $data['search']['showtime_date'] = (!empty($input['showtime_date']))? $input['showtime_date'] : '';
                if(!empty($data['search']['showtime_date']))
                    $data['where'][] = ['show_times.show_time','=',date('Y-m-d H:i:s',strtotime($data['search']['showtime_date']))];
                // showtime_id
                $data['search']['showtime_id'] = (!empty($input['showtime_id']) && is_numeric($input['showtime_id']))? $input['showtime_id'] : '';
                if(!empty($data['search']['showtime_id']))
                    $data['where'][] = ['show_times.id','=',$data['search']['showtime_id']];
                //search soldtime
                if(!empty($default_date_range))
                {
                    $default_start_date = date('n/d/y', strtotime($default_date_range.' DAY')).' 12:00 AM';
                    $default_end_date = date('n/d/y').' 11:59 PM';
                }
                else
                    $default_start_date = $default_end_date = '';
                $data['search']['soldtime_start_date'] = (isset($input['soldtime_start_date']))? $input['soldtime_start_date'] : $default_start_date;
                $data['search']['soldtime_end_date'] = (isset($input['soldtime_end_date']))? $input['soldtime_end_date'] : $default_end_date;
                if(!empty($data['search']['soldtime_start_date']) && !empty($data['search']['soldtime_end_date']))
                {
                    $data['where'][] = [DB::raw('purchases.created'),'>=',date('Y-m-d H:i:s',strtotime($data['search']['soldtime_start_date']))];
                    $data['where'][] = [DB::raw('purchases.created'),'<=',date('Y-m-d H:i:s',strtotime($data['search']['soldtime_end_date']))];
                }
                //search payment types
                $data['search']['payment_type'] = (!empty($input['payment_type']))? $input['payment_type'] : array_values($data['search']['payment_types']);
                $data['where'][] =  [DB::raw('purchases.payment_type IN ("'.implode('","',$data['search']['payment_type']).'") AND purchases.id'),'>',0];
                //search channel
                $data['search']['channel'] = (!empty($input['channel']))? $input['channel'] : '';
                if(!empty($input['channel']))
                    $data['where'][] = ['purchases.channel','=',$data['search']['channel']];
                //search date range
                $data['search']['start_amount'] = (!empty($input['start_amount']) && is_numeric($input['start_amount']))? trim($input['start_amount']) : '';
                $data['search']['end_amount'] = (!empty($input['end_amount']) && is_numeric($input['end_amount']))? trim($input['end_amount']) : '';
                if(!empty($input['start_amount']))
                    $data['where'][] = ['purchases.price_paid','>=',$data['search']['start_amount']];
                if(!empty($input['end_amount']))
                    $data['where'][] = ['purchases.price_paid','<=',$data['search']['end_amount']];
                //search ticket_type
                $data['search']['ticket_type'] = (!empty($input['ticket_type']))? $input['ticket_type'] : '';
                if(!empty($input['ticket_type']))
                    $data['where'][] = ['tickets.ticket_type','=',$data['search']['ticket_type']];
                //search ticket
                $data['search']['ticket'] = (!empty($input['ticket']))? $input['ticket'] : '';
                if(!empty($input['ticket']))
                    $data['where'][] = ['tickets.id','=',$data['search']['ticket']];
                //search status
                $data['search']['statu'] = (!empty($input['statu']))? $input['statu'] : '';
                if(!empty($input['statu']))
                    $data['where'][] = ['purchases.status','=',$data['search']['statu']];
                //search user
                if(!empty($input['user']))
                {
                    $data['search']['user'] = trim($input['user']);
                    if(is_numeric($data['search']['user']))
                        $data['where'][] = ['users.id','=',$data['search']['user']];
                    else if(filter_var($data['search']['user'], FILTER_VALIDATE_EMAIL))
                        $data['where'][] = ['users.email','=',$data['search']['user']];
                    else
                        $data['search']['user'] = '';
                }
                else
                    $data['search']['user'] = '';
                //search customer
                if(!empty($input['customer']))
                {
                    $data['search']['customer'] = trim($input['customer']);
                    if(is_numeric($data['search']['customer']))
                        $data['where'][] = ['customers.id','=',$data['search']['customer']];
                    else if(filter_var($data['search']['customer'], FILTER_VALIDATE_EMAIL))
                        $data['where'][] = ['customers.email','=',$data['search']['customer']];
                    else
                        $data['search']['customer'] = '';
                }
                else
                    $data['search']['customer'] = '';
                //search order_id
                $data['search']['order_id'] = (!empty($input['order_id']) && is_numeric($input['order_id']))? trim($input['order_id']) : '';
                if(!empty($input['order_id']))
                    $data['where'][] = ['purchases.id','=',$data['search']['order_id']];
                //search authcode
                $data['search']['authcode'] = (!empty($input['authcode']))? trim($input['authcode']) : '';
                if(!empty($input['authcode']))
                    $data['where'][] = [DB::raw('transactions.authcode = "'.$data['search']['authcode'].'" OR transaction_refunds.authcode'),'=',$data['search']['authcode']];
                //search refnum
                $data['search']['refnum'] = (!empty($input['refnum']))? trim($input['refnum']) : '';
                if(!empty($input['refnum']))
                    $data['where'][] = [DB::raw('transactions.refnum = "'.$data['search']['refnum'].'" OR transaction_refunds.ref_num'),'=',$data['search']['refnum']];
                
                //search printing
                if(isset($input['mirror_type']) && !empty($input['mirror_type']))
                    $data['search']['mirror_type'] = $input['mirror_type'];
                else
                    $data['search']['mirror_type'] = 'previous_period';

                if(isset($input['mirror_period']) && !empty($input['mirror_period']) && is_numeric($input['mirror_period']))
                    $data['search']['mirror_period'] = $input['mirror_period'];
                else
                    $data['search']['mirror_period'] = 0;

                if(isset($input['replace_chart']))
                    $data['search']['replace_chart'] = (!empty($input['replace_chart']))? 1 : 0;
                else
                    $data['search']['replace_chart'] = 1;

                if(isset($input['coupon_report']))
                    $data['search']['coupon_report'] = (!empty($input['coupon_report']))? 1 : 0;
                else
                    $data['search']['coupon_report'] = 1;
            }
            
            //FILTER SEARCH BY PERMISSIONS     
            if(in_array('View',Auth::user()->user_type->getACLs()[$module]['permission_types']))
            {
                if(Auth::user()->user_type->getACLs()[$module]['permission_scope'] != 'All')
                {
                    if(!empty(Auth::user()->venues_edit) && count(explode(',',Auth::user()->venues_edit)))
                    {
                        $data['where'][] = [DB::raw('shows.venue_id IN ('.Auth::user()->venues_edit.') OR shows.create_user_id'),'=',Auth::user()->id];
                        //add venues for search
                        $data['search']['venues'] = Venue::whereIn('id',explode(',',Auth::user()->venues_edit))->orderBy('name')->get(['id','name']);
                    }
                    else
                        $data['where'][] = ['shows.create_user_id','=',Auth::user()->id];
                }
                //all
                else
                {
                    //add venues for search
                    $data['search']['venues'] = Venue::orderBy('name')->get(['id','name']);
                }
            }
            else
                $data['where'][] = ['purchases.id','=',0];
            //fill out enums for shows and tickets
            $data['search']['shows'] = ((!empty($data['search']['venue'])))? Show::where('venue_id',$data['search']['venue'])->orderBy('name')->get(['id','name','venue_id']) : [];
            $data['search']['tickets'] = ((!empty($data['search']['show'])))? DB::table('tickets')
                                                    ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                                                    ->select(DB::raw('tickets.id, CONCAT(tickets.ticket_type," - ",packages.title) AS name'))
                                                    ->where('tickets.show_id', $data['search']['show'])->groupBy('tickets.id')->get() : [];
            
        } catch (Exception $ex) {
            
        } finally {
            return $data;
        }
    }
}
