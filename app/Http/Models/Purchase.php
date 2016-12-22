<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
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
                            ->select('purchases.*', 'purchases.quantity AS qty', 'tickets.ticket_type AS ticket_type_type', 'show_times.time_alternative', 'show_times.show_time', 'discounts.code', 'packages.title', 'locations.lat', 'locations.lng', 
                                    'shows.name AS show_name', 'shows.slug', 'shows.restrictions', 'shows.emails', 'shows.printed_tickets', 'shows.individual_emails AS s_individual_emails',
                                    'shows.manifest_emails AS s_manifest_emails', 'shows.daily_sales_emails AS s_daily_sales_emails', 'shows.financial_report_emails AS s_financial_report_emails', 
                                    'venues.name AS venue_name', 'venues.ticket_info', 'venues.daily_sales_emails AS v_daily_sales_emails', 'venues.financial_report_emails AS v_financial_report_emails', 
                                    'venues.weekly_sales_emails AS v_weekly_sales_emails')
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
        $ticket_numbers = DB::table('ticket_number')
                            ->join('customers', 'customers.id', '=' ,'ticket_number.customers_id')
                            ->select('ticket_number.*', 'customers.first_name', 'customers.last_name', 'customers.email')
                            ->where('ticket_number.purchases_id', '=', $this->id)
                            ->orderBy('ticket_number.id')
                            ->get();
        //if it has ticket number is the old way, without a seat by ticket
        if($ticket_numbers->count())
        {
            for ($i=1; $i<=$this->quantity; $i++)
            foreach($ticket_numbers as $tn)
                if(in_array($i,explode(',',$tn->tickets)))
                {
                    $main_info = ['number'=>$i,'customer_name'=>$tn->first_name.' '.$tn->last_name,'customer_email'=>$tn->email,'checked'=>(in_array($i,explode(',',$tn->checked)))? $checked_= 1 : $checked_= 0,'comment'=>$tn->comment,'QRcode'=>Util::getQRcode($this->id,$this->user_id,$i)];
                    $extra_info = ['show_name'=>$purchase->show_name,'show_time'=>$purchase->show_time,'price_each'=>number_format($this->price_paid/$this->quantity,2),'id'=>$this->id,'venue_name'=>$purchase->venue_name,'restrictions'=>$purchase->restrictions,'user_id'=>$this->user_id,'ticket_type'=>$purchase->ticket_type_type,'time_alternative'=>$purchase->time_alternative,'package'=>$purchase->title];
                    $tickets[] = array_merge($main_info,$extra_info);
                }    
        }
        //if it sells section/row/seat
        else
        {
            $seats = DB::table('purchase_seats')
                                ->join('seats', 'purchase_seats.seat_id', '=' ,'seats.id')
                                ->join('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->join('purchases', 'purchases.id', '=' ,'purchase_seats.purchase_id')
                                ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                                ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                                ->select('purchase_seats.*','seats.seat','tickets.ticket_type','tickets.retail_price','tickets.processing_fee','tickets.percent_commission',
                                        'shows.name AS show_name','show_times.show_time','venues.name AS venue_name','shows.restrictions','show_times.time_alternative')
                                ->where('purchase_seats.purchase_id',$this->id)
                                ->orderBy('tickets.ticket_type','seats.seat')
                                ->distinct()->get();
            foreach ($seats as $s)
            {
                $main_info = ['number'=>$s->id,'customer_name'=>'','customer_email'=>'','checked'=>($s->status == 'Checked')? $checked_= 1 : $checked_= 0,'comment'=>'','QRcode'=>Util::getQRcode($this->id,$this->user_id,$s->id)];
                $extra_info = ['show_name'=>$s->show_name,'show_time'=>$s->show_time,'price_each'=>number_format($s->retail_price+$s->processing_fee-($s->retail_price*$s->percent_commission/100),2),'id'=>$this->id,'venue_name'=>$s->venue_name,'restrictions'=>$s->restrictions,'user_id'=>$this->user_id,'ticket_type'=>$s->seat_id,'time_alternative'=>$s->time_alternative,'package'=>$s->ticket_type.' Seat: '.$s->seat];
                $tickets[] = array_merge($main_info,$extra_info);
            }
        }
        //get banners from shows, if not then banners from venues
        $banners = DB::table('banners')
                            ->join('show_times', 'show_times.show_id', '=' ,'banners.parent_id')
                            ->join('purchases', 'purchases.show_time_id', '=' ,'show_times.id')
                            ->select('banners.*')
                            ->where('banners.belongto', '=', 'show')
                            ->where('purchases.id', '=', $this->id)
                            ->where('banners.type', 'like', '%Thank you Page%')
                            ->distinct()->get();
        if(!$banners->count())
            $banners = DB::table('banners')
                            ->join('shows', 'shows.venue_id', '=' ,'banners.parent_id')
                            ->join('show_times', 'show_times.show_id', '=' ,'shows.id')
                            ->join('purchases', 'purchases.show_time_id', '=' ,'show_times.id')
                            ->select('banners.*')
                            ->where('banners.belongto', '=', 'venue')
                            ->where('purchases.id', '=', $this->id)
                            ->where('banners.type', 'like', '%Thank you Page%')
                            ->distinct()->get();
        //return data
        return ['purchase' => $purchase, 'customer' => $customer, 'tickets' => $tickets, 'banners'=> $banners];
    }
    /**
     * Send by email given purchases receipts.
     */
    public static function email_receipts($subject,$receipts,$type_email)
    {
        try {
            if(is_array($receipts) && count($receipts) && is_string($subject) && is_string($type_email))
            {
                //init variables
                $rows_html = $totals_html = '';
                $pdf_receipts = $pdf_tickets = $purchases = [];
                $totals = ['qty'=>0,'processing_fee'=>0,'retail_price'=>0,'discount'=>0];

                //loop receipts
                foreach ($receipts as $receipt)
                {
                    $purchases[] = $receipt['purchase'];
                   
                    $format = 'pdf';
                    //create pdf receipt
                    $purchase = array_merge((array)$receipt['purchase'],(array)$receipt['customer']);
                    $purchase['price_each'] = round($purchase['retail_price']/$purchase['qty'],2);
                    $pdfUrlR = '/tmp/Receipt_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$receipt['purchase']->ticket_type).'_'.date("m_d_Y_h_i_a",strtotime($receipt['purchase']->show_time)).'.pdf';
                    $pdf_receipt = View::make('command.report_sales_receipt', compact('purchase','format'));  
                    PDF::loadHTML($pdf_receipt->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdfUrlR);
                    $pdf_receipts[] = $pdfUrlR;
                    
                    //create pdf tickets
                    $tickets = $receipt['tickets'];
                    $type = 'C';
                    $pdfUrlT = '/tmp/Tickets_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$receipt['purchase']->ticket_type).'_'.date("m_d_Y_h_i_a",strtotime($receipt['purchase']->show_time)).'.pdf';
                    $pdf_ticket = View::make('command.report_sales_receipt_tickets', compact('tickets','type','format'));  
                    PDF::loadHTML($pdf_ticket->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdfUrlT);
                    $pdf_tickets[] = $pdfUrlT;
                    
                    if($type != 'reminder')
                    {  
                        //row on email to each purchase
                        $rows_html.='<tr>'
                                        . '<td align="center">'.$receipt['purchase']->ticket_type_type.' For '.$receipt['purchase']->show_name.'<br/>On '.date('l, F jS - g:i A',strtotime($receipt['purchase']->show_time)).'</td> '
                                        . '<td align="center">'.$receipt['purchase']->quantity.'</td> '
                                        . '<td align="center">'.number_format($purchase['price_each'],2).'</td>  '
                                        . '<td align="center">'.number_format($purchase['retail_price'],2).'</td> '
                                        . '<td align="center">'.number_format($purchase['processing_fee'],2).'</td> </tr>';
                        //sum values to show
                        $totals['qty']+=$receipt['purchase']->quantity;
                        $totals['processing_fee']+=$receipt['purchase']->processing_fee;
                        $totals['retail_price']+=$receipt['purchase']->retail_price;
                        $totals['discount']+=$receipt['purchase']->retail_price-$receipt['purchase']->price_paid+$receipt['purchase']->processing_fee;
                    }
                }
                //send email           
                $email = new EmailSG(null, $receipt['customer']->email , $subject);
                //$email->cc(env('MAIL_REPORT_CC'));
                $email->category('Receipts');
                $email->attachment(array_merge($pdf_receipts,$pdf_tickets));
                //check type of email to send
                if($type === 'reminder')
                {
                    $email->body('reminder',['purchase'=>$purchases,'customer'=>$receipt['customer']]);
                    $email->template('330de7c4-3d1c-47b5-9f48-ca376cbbea99');
                }
                else
                {
                    //table on email to show all totals
                    $totals['total'] = $totals['retail_price'] + $totals['processing_fee'] - $totals['discount'];
                    $totals_html = '<tr> <td align="right" width="85%">Subtotal:</td> <td width="15%" align="right">$ '.number_format($totals['retail_price'],2).'</td> </tr>
                                    <tr> <td align="right">Processing Fee:</td> <td align="right">$ '.number_format($totals['processing_fee'],2).'</td> </tr>';
                    if($totals['discount'] > 0)
                        $totals_html.='<tr> <td align="right">Discount:</td> <td align="right">$ '.number_format($totals['discount'],2).'</td> </tr>';
                    $totals_html.='<tr> <td align="right" style="color:#1F9F0B;"><b>GRAND TOTAL</b>:</td> <td align="right" style="color:#1F9F0B;">$ '.number_format($totals['total'],2).'</td> </tr>';
                    //info to send by email content
                    $email->body('receipt',['rows'=>$rows_html,'totals'=>$totals_html,'banners'=>'']);
                    $email->template('98066597-4797-40bf-b95a-0219da4ca1dc');
                }
                $response = $email->send();
                foreach(array_merge($pdf_receipts,$pdf_tickets) as $link)
                    unlink($link);
                return $response;
            }
            else return false;
        } catch (Exception $ex) {
            return false;
        }
    }
}
