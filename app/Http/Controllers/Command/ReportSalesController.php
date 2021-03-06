<?php

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Http\Models\ShowTime;
use App\Mail\EmailSG;
use Barryvdh\DomPDF\Facade as PDF;

/**
 * Manage ReportSales options for the commands
 *
 * @author ivan
 */
class ReportSalesController extends Controller{

    protected $days = 1;
    protected $only_admin = 0;  //0-> venues, 1-> admin, 2-> admin with venues
    protected $start_date;
    protected $end_date;
    protected $date_format;
    protected $report_date;
    protected $subject;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($days, $only_admin)
    {
        $this->days = $days;
        $this->only_admin = $only_admin;
        $this->start_date = date('Y-m-d H:i',strtotime('-'.(24*$this->days).' hours'));
        $this->end_date = date('Y-m-d H:i',strtotime('now'));
        $this->date_format = 'D, F j, Y @ g:iA';
        $this->report_date = ($this->days<1)? date($this->date_format,strtotime($this->end_date)) :
                                  date($this->date_format,strtotime($this->start_date)).' - '.date($this->date_format,strtotime($this->end_date));
        $this->subject = 'Daily Sales Report ';
    }
    /*
     * ************************************************************************************************************************   method for showtime only
     * get sales report pdf by specific event
     */
    public function event($show_time_id,$email)
    {
        try {
            //init main variables
            $showtime = ShowTime::find($show_time_id);
            $title = $showtime->show->name.' @ '.date($this->date_format, strtotime($showtime->show_time)).' - Total Sales';
            $this->report_date = date($this->date_format,strtotime($this->end_date));
            //create report
            $report = [];
            $report['sales'] = [ $this->report_sales('showtime',$show_time_id,$title) ];
            $report['sellers'] = [ $this->report_sellers('showtime',$show_time_id,$title) ];
            $report['debits'] = [ $this->report_debits('showtime',$show_time_id,$title) ];
            $files = $this->create_files($report,$title);
            //send the email
            if(!empty($files))
                $sent = $this->send_email($files,$email,$title);
            if(isset($sent))
                return $sent;
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }
    /*
     * ************************************************************************************************************************   init method
     * get sales report pdf
     */
    public function init()
    {
        try {
            //init main variables
            $report = $ven = [];
            //get all the venues with purchases and if admin add extra fields
            if($this->only_admin>0) //admin, get all data with values
            {
                $venues = DB::table('venues')
                            ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->select(DB::raw('venues.id, venues.name, venues.accounting_email, venues.daily_sales_emails,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets,
                                          SUM(purchases.price_paid) AS paid, SUM(purchases.sales_taxes) AS taxes,
                                          SUM(purchases.commission_percent) AS commissions, SUM(purchases.cc_fees) AS cc_fee,
                                          SUM(purchases.printed_fee) AS printed_fee,
                                          SUM( IF(purchases.inclusive_fee>0, ROUND(purchases.processing_fee,2), 0) ) AS fees_incl,
                                          SUM( IF(purchases.inclusive_fee>0, 0, ROUND(purchases.processing_fee,2)) ) AS fees_over,
                                          SUM(purchases.commission_percent+purchases.processing_fee+purchases.printed_fee) AS amount'))
                            ->where('purchases.status','<>','Void')
                            ->where('purchases.created','>=',$this->start_date)
                            ->groupBy('venues.id')->orderBy('venues.name')
                            ->distinct()->get()->toArray();
                //create each report data
                $report['financial'][] = $this->report_financial('admin',$venues,'TicketBat Totals');
                $report['sales'][] = $this->report_sales('admin',$venues,'TicketBat Totals');
                $report['sellers'][] = $this->report_sellers('admin',null,'TicketBat Totals');
                $report['debits'][] = $this->report_debits('admin',null,'TicketBat Totals');
                $report['events'][] = $this->report_event_breakdown('admin',null,'TicketBat Totals');
                $report['future'][] = $this->report_future_liabilities('admin',null,'TicketBat Totals');
            }
            else    //regular other sales
            {
                $venues = DB::table('venues')
                            ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->select('venues.id','venues.name','venues.accounting_email','venues.daily_sales_emails')
                            ->where('purchases.status','<>','Void')
                            ->where('purchases.created','>=',$this->start_date)
                            ->groupBy('venues.id')->orderBy('venues.name')
                            ->distinct()->get()->toArray();
            }
            //if create report for venues or merge venues with admin
            if($this->only_admin!=1)
            {
                //loop through all venues to create each report for each one
                foreach ($venues as $v)
                {
                    $ven[] = $v->id;
                    $report_venue = [];
                    $report_venue['financial'] = [ $this->report_financial('venue',$v->id,$v->name) ];
                    $report_venue['sales'] = [ $this->report_sales('venue',$v->id,$v->name) ];
                    $report_venue['sellers'] = [ $this->report_sellers('venue',$v->id,$v->name) ];
                    $report_venue['debits'] = [ $this->report_debits('venue',$v->id,$v->name) ];
                    $report_venue['events'] = [ $this->report_event_breakdown('venue',$v->id,$v->name) ];
                    $report_venue['future'] = [ $this->report_future_liabilities('venue',$v->id,$v->name) ];

                    //merge them to admin when available admin and venues report
                    if($this->only_admin>1)
                    {
                        $report['financial'][] = $report_venue['financial'][0];
                        $report['sales'][] = $report_venue['sales'][0];
                        $report['sellers'][] = $report_venue['sellers'][0];
                        $report['debits'][] = $report_venue['debits'][0];
                        $report['events'][] = $report_venue['events'][0];
                        $report['future'][] = $report_venue['future'][0];
                    }
                    //send the emails if available option
                    else if($v->daily_sales_emails > 0 && !empty($v->accounting_email))
                    {
                        $files = $this->create_files($report_venue,$v->name);
                        //send the email
                        if(!empty($files))
                            $sent = $this->send_email($files,$v->accounting_email,$v->name);
                    }
                }   
            }
            //send admin email
            if($this->only_admin>0)
            {
                $files = $this->create_files($report,'TicketBat Totals');
                //send the email
                if(!empty($files))
                    $sent = $this->send_email($files,env('MAIL_REPORT_TO'),'TicketBat Totals');
            }   
            //send email to venues with no sales
            else
            {
                $venues = DB::table('venues')
                            ->select('venues.id','venues.name','venues.accounting_email')
                            ->where('venues.daily_sales_emails','>',0)
                            ->whereNotIn('venues.id',$ven)
                            ->groupBy('venues.id')->orderBy('venues.name')
                            ->distinct()->get()->toArray();
                foreach ($venues as $v)
                    $sent = $this->send_email([],$v->accounting_email,$v->name);
            }
            if(isset($sent))
                return $sent;
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }
    /*
     * ************************************************************************************************************************   report sales
     * create report sales
     */
    public function report_sales($type,$e,$title)
    {
        try {
            $types = $this->create_table_types($type,$e);
            $channels = $this->create_table_channels($type,$e);
            $tickets = $this->create_table_tickets($type,$e); 
            //debits
            $debits = $this->create_table_debits($type, $e);
            //return
            return ['type'=>$type,'title'=>$title,'date'=>$this->report_date,'created'=>date($this->date_format,strtotime($this->end_date)),'table_types'=>$types,'table_channels'=>$channels,
                     'table_tickets'=>$tickets,'table_debits'=>$debits];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_sales_types
     */
    public function create_table_tickets($type='admin',$e_id=null)
    {
        try {
            $amount = ($type=='admin')? 'SUM(purchases.commission_percent+purchases.processing_fee+purchases.printed_fee) AS amount' :
                                        'SUM(purchases.price_paid-purchases.sales_taxes-purchases.cc_fees-purchases.commission_percent-purchases.processing_fee-purchases.printed_fee) AS amount';
            //get all records
            $types = DB::table('venues')
                            ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                            ->select(DB::raw('tickets.ticket_type, IF(packages.title="None", "", packages.title) AS title, SUM(purchases.printed_fee) AS printed_fee,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets,
                                          SUM(purchases.price_paid) AS paid, SUM(purchases.sales_taxes) AS taxes,
                                          SUM(purchases.commission_percent) AS commissions, SUM(purchases.cc_fees) AS cc_fee,
                                          SUM( IF(purchases.inclusive_fee>0, ROUND(purchases.processing_fee,2), 0) ) AS fees_incl,
                                          SUM( IF(purchases.inclusive_fee>0, 0, ROUND(purchases.processing_fee,2)) ) AS fees_over, '.$amount))
                            ->where('purchases.status','<>','Void')
                            ->groupBy('tickets.ticket_type')->groupBy('packages.title')->orderBy('tickets.id')->orderBy('packages.title');
            
            if($type=='admin' || empty($e_id))
                $types->where('purchases.created','>=',$this->start_date);
            else if(!empty($e_id))
            {
                if($type=='venue')
                    $types->where('purchases.created','>=',$this->start_date)->where('venues.id','=',$e_id);
                else if($type=='showtime')
                    $types->where('show_times.id','=',$e_id);
            }
            $types = $types->distinct()->get()->toArray();   
            return ['data'=>$types, 'total'=> $this->calc_totals($types)];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_sales_types
     */
    public function create_table_types($type='admin',$e_id=null)
    {
        try {
            $amount = ($type=='admin')? 'SUM(purchases.commission_percent+purchases.processing_fee+purchases.printed_fee) AS amount' :
                                        'SUM(purchases.price_paid-purchases.sales_taxes-purchases.cc_fees-purchases.commission_percent-purchases.processing_fee-purchases.printed_fee) AS amount';
            //get all records
            $types = DB::table('venues')
                            ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->select(DB::raw('purchases.payment_type AS payment_type, SUM(purchases.printed_fee) AS printed_fee,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets,
                                          SUM(purchases.price_paid) AS paid, SUM(purchases.sales_taxes) AS taxes,
                                          SUM(purchases.commission_percent) AS commissions, SUM(purchases.cc_fees) AS cc_fee,
                                          SUM( IF(purchases.inclusive_fee>0, ROUND(purchases.processing_fee,2), 0) ) AS fees_incl,
                                          SUM( IF(purchases.inclusive_fee>0, 0, ROUND(purchases.processing_fee,2)) ) AS fees_over, '.$amount))
                            ->where('purchases.status','<>','Void')
                            ->groupBy(DB::raw('payment_type'))->orderBy(DB::raw('payment_type'));
            
            if($type=='admin' || empty($e_id))
                $types->where('purchases.created','>=',$this->start_date);
            else if(!empty($e_id))
            {
                if($type=='venue')
                    $types->where('purchases.created','>=',$this->start_date)->where('venues.id',$e_id);
                else if($type=='showtime')
                    $types->where('show_times.id','=',$e_id);
            }
            $types = $types->distinct()->get()->toArray();   
            $total = $this->calc_totals($types);
            $others = [];
            foreach ($types as $k=>$t)
            {
                if($t->payment_type=='Consignment')
                {
                    $others[] = $t;
                    unset($types[$k]);
                    break;
                }
            }
            //subtotal
            $subtotal = (empty($others))? $total : $this->calc_totals($types) ;
            return ['data'=>$types, 'total'=> $total, 'others'=>$others, 'subtotal'=>$subtotal];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_selling_ways
     */
    public function create_table_channels($type='admin',$e_id=null)
    {
        try {
            $amount = ($type=='admin')? 'SUM(purchases.commission_percent+purchases.processing_fee+purchases.printed_fee) AS amount' :
                                        'SUM(purchases.price_paid-purchases.sales_taxes-purchases.cc_fees-purchases.commission_percent-purchases.processing_fee-purchases.printed_fee) AS amount';
            $table = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=', 'show_times.show_id')
                        ->select(DB::raw('purchases.channel, SUM(purchases.printed_fee) AS printed_fee,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets, 
                                          SUM(purchases.price_paid) AS paid, SUM(purchases.commission_percent) AS commissions,
                                          SUM(purchases.sales_taxes) AS taxes, SUM(purchases.cc_fees) AS cc_fee,
                                          SUM( IF(purchases.inclusive_fee>0, ROUND(purchases.processing_fee,2), 0) ) AS fees_incl,
                                          SUM( IF(purchases.inclusive_fee>0, 0, ROUND(purchases.processing_fee,2)) ) AS fees_over, '.$amount))
                        ->where('purchases.status','<>','Void')
                        ->groupBy('purchases.channel')->orderBy('purchases.channel');
            
            if($type=='admin' || empty($e_id))
                $table->where('purchases.created','>=',$this->start_date);
            else if(!empty($e_id))
            {
                if($type=='venue')
                    $table->where('purchases.created','>=',$this->start_date)->where('shows.venue_id',$e_id);
                else if($type=='showtime')
                    $table->where('show_times.id','=',$e_id);
            }
            $table = $table->distinct()->get()->toArray();
            return ['data'=>$table, 'total'=> $this->calc_totals($table)];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * ************************************************************************************************************************   report sellers
     * report_sellers
     */
    public function report_sellers($type,$e,$title)
    {
        try {
            $sellers = $this->create_table_sellers($type,$e);
            $debits = $this->create_table_debits($type, $e);
            //return
            return ['type'=>$type,'title'=>$title,'date'=>$this->report_date,'created'=>date($this->date_format,strtotime($this->end_date)),'table_debits'=>$debits,'table_sellers'=>$sellers];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_sales_sellers
     */
    public function create_table_sellers($type='admin',$e_id=null)
    {
        try {
            $amount = ($type=='admin')? 'SUM(purchases.commission_percent+purchases.processing_fee+purchases.printed_fee) AS amount' :
                                        'SUM(purchases.price_paid-purchases.sales_taxes-purchases.cc_fees-purchases.commission_percent-purchases.processing_fee-purchases.printed_fee) AS amount';
            //get all records
            $sellers = DB::table('venues')
                            ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                            ->join('users', 'users.id', '=' ,'purchases.user_id')
                            ->join('user_types', 'user_types.id', '=' ,'users.user_type_id')
                            ->select(DB::raw('users.email, DATE_FORMAT(show_times.show_time, "%c/%e/%y %l:%i%p") AS show_time, venues.name AS venue, shows.name AS event, purchases.payment_type, tickets.ticket_type, IF(packages.title="None", "", packages.title) AS title, 
                                          SUM(purchases.printed_fee) AS printed_fee,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets,
                                          SUM(purchases.price_paid) AS paid, SUM(purchases.sales_taxes) AS taxes,
                                          SUM(purchases.commission_percent) AS commissions, SUM(purchases.cc_fees) AS cc_fee,
                                          SUM( IF(purchases.inclusive_fee>0, ROUND(purchases.processing_fee,2), 0) ) AS fees_incl,
                                          SUM( IF(purchases.inclusive_fee>0, 0, ROUND(purchases.processing_fee,2)) ) AS fees_over, '.$amount))
                            ->where('purchases.status','<>','Void')
                            ->where('user_types.user_type','=','Seller')
                            ->groupBy('users.id')->groupBy('show_times.id')->groupBy('venues.id')->groupBy('shows.id')->groupBy('purchases.payment_type')->groupBy('tickets.id')
                            ->groupBy('users.email')->orderBy('show_times.show_time','DESC')->orderBy('venues.name')->orderBy('shows.name')->orderBy('purchases.payment_type')->orderBy('tickets.id');
            
            if($type=='admin' || empty($e_id))
                $sellers->where('purchases.created','>=',$this->start_date);
            else if(!empty($e_id))
            {
                if($type=='venue')
                    $sellers->where('purchases.created','>=',$this->start_date)->where('venues.id','=',$e_id);
                else if($type=='showtime')
                    $sellers->where('show_times.id','=',$e_id);
            }
            $sellers = $sellers->distinct()->get()->toArray();   
            return ['data'=>$sellers, 'total'=> $this->calc_totals($sellers)];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * ************************************************************************************************************************   report debits
     * report_debits
     */
    public function report_debits($type,$e,$title)
    {
        try {
            $refunds = $this->create_table_refunds($type,$e);
            //return
            return ['type'=>$type,'title'=>$title,'date'=>$this->report_date,'created'=>date($this->date_format,strtotime($this->end_date)),'table_refunds'=>$refunds];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_sales_refunds
     */
    public function create_table_refunds($type='admin',$e_id=null)
    {
        try {
            return $this->create_table_debits($type,$e_id,null,null,$breakdown=true);
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_refunds
     */
    public function create_table_debits($type='admin',$e_id=null,$start=null,$end=null,$breakdown=false,$groupSt=false)
    {
        try {
            if(empty($start))
                $start = $this->start_date;            
            $amount = ($type=='admin')? 'SUM(transaction_refunds.commission_percent+transaction_refunds.processing_fee+transaction_refunds.printed_fee)*-1 AS amount' :
                                        'SUM(transaction_refunds.amount-transaction_refunds.sales_taxes-purchases.cc_fees-transaction_refunds.commission_percent-transaction_refunds.processing_fee-transaction_refunds.printed_fee)*-1 AS amount';
            //get all records
            $debits = DB::table('venues')
                            ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                            ->join('transaction_refunds', function($join){
                                $join->on('transaction_refunds.purchase_id', '=', 'purchases.id')
                                     ->where('transaction_refunds.result','=','Approved');
                            })
                            ->select(DB::raw('DATE_FORMAT(transaction_refunds.created, "%c/%e/%y %l:%i%p") AS created, venues.name AS venue, shows.name AS event, transaction_refunds.payment_type, tickets.ticket_type, IF(packages.title="None", "", packages.title) AS title, purchases.status, 
                                          DATE_FORMAT(show_times.show_time, "%c/%e/%y %l:%i%p") AS show_time, purchases.channel,
                                          SUM(transaction_refunds.printed_fee)*-1 AS printed_fee,
                                          COUNT(transaction_refunds.id)*-1 AS transactions, SUM(transaction_refunds.quantity)*-1 AS tickets,
                                          SUM(transaction_refunds.amount)*-1 AS paid, SUM(transaction_refunds.sales_taxes)*-1 AS taxes,
                                          SUM(transaction_refunds.commission_percent)*-1 AS commissions, SUM(purchases.cc_fees)*-1 AS cc_fee,
                                          SUM( IF(purchases.inclusive_fee>0, ROUND(transaction_refunds.processing_fee,2), 0) )*-1 AS fees_incl,
                                          SUM( IF(purchases.inclusive_fee>0, 0, ROUND(transaction_refunds.processing_fee,2)) )*-1 AS fees_over, '.$amount))
                            ->where(function($query) {
                                $query->where('purchases.status','=','Refunded')
                                      ->orWhere('purchases.status','=','Chargeback');
                            });
            //filter
            if($type=='admin' || empty($e_id))
            {
                if($groupSt)
                {
                    $debits->where('show_times.show_time','>=',$start);
                    if(!empty($end))
                        $debits->where('show_times.show_time','<=',$end);
                }
                else
                {
                    $debits->where('transaction_refunds.created','>=',$start);
                    if(!empty($end))
                        $debits->where('transaction_refunds.created','<=',$end);
                }
            }
            else if(!empty($e_id))
            {
                if($type=='venue')
                {
                    if($groupSt)
                    {
                        $debits->where('show_times.show_time','>=',$start);
                        if(!empty($end))
                            $debits->where('show_times.show_time','<=',$end);
                    }
                    else
                    {
                        $debits->where('transaction_refunds.created','>=',$start);
                        if(!empty($end))
                            $debits->where('transaction_refunds.created','<=',$end);
                    }
                    $debits->where('venues.id','=',$e_id);
                }
                else if($type=='showtime')
                    $debits->where('show_times.id','=',$e_id);
            }
            //breakdown or regular summary
            if($breakdown)
                $debits = $debits->groupBy('transaction_refunds.id')
                                 ->orderBy('transaction_refunds.created','DESC');
            else
                $debits = $debits->groupBy('purchases.status');
            $debits = $debits->distinct()->get()->toArray();   
            return ['data'=>$debits, 'total'=> $this->calc_totals($debits)];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * ************************************************************************************************************************   report future
     * report_future_liabilities
     */
    public function report_future_liabilities($type,$venue_id=null,$title)
    {
        try {
            $future = $this->create_table_event_details($type,$venue_id,$this->end_date,null);
            $consig = $this->create_table_event_details($type,$venue_id,$this->end_date,null,true);
            //debits
            $debits = $this->create_table_debits($type, $venue_id,$this->end_date,null,true,true);     
            return ['type'=>$type,'title'=>$title,'date'=>date($this->date_format,strtotime($this->end_date)),'created'=>date($this->date_format,strtotime($this->end_date)),'table_future'=>$future,'table_consignments'=>$consig,'table_debits'=>$debits];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * ************************************************************************************************************************   report events breakdown
     * report_events_breakdown
     */
    public function report_event_breakdown($type,$venue_id=null,$title)
    {
        try {
            $events = $this->create_table_event_details($type,$venue_id,$this->start_date,$this->end_date);
            $consig = $this->create_table_event_details($type,$venue_id,$this->start_date,$this->end_date,true);
            //debits
            $debits = $this->create_table_debits($type, $venue_id,$this->start_date,$this->end_date,true,true);     
            return ['type'=>$type,'title'=>$title,'date'=>$this->report_date,'created'=>date($this->date_format,strtotime($this->end_date)),'table_events'=>$events,'table_consignments'=>$consig,'table_debits'=>$debits];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_event_details
     */
    public function create_table_event_details($type='admin',$e_id=null,$start_date,$end_date=null,$onlyConsign=false)
    {
        try {
            $amount = ($type=='admin')? 'SUM(purchases.commission_percent+purchases.processing_fee+purchases.printed_fee) AS amount' :
                                        'SUM(purchases.price_paid-purchases.sales_taxes-purchases.cc_fees-purchases.commission_percent-purchases.processing_fee-purchases.printed_fee) AS amount';
            $events = DB::table('venues')
                            ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                            ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                            ->select(DB::raw('DATE_FORMAT(show_times.show_time, "%c/%e/%y %l:%i%p") AS show_time, venues.name AS venue, shows.name AS event, purchases.channel, purchases.payment_type, 
                                          tickets.ticket_type, IF(packages.title="None", "", packages.title) AS title, LEFT(purchases.status,1) AS status, discounts.code,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets, SUM(purchases.printed_fee) AS printed_fee,
                                          SUM(purchases.price_paid) AS paid, SUM(purchases.sales_taxes) AS taxes,
                                          SUM(purchases.commission_percent) AS commissions, SUM(purchases.cc_fees) AS cc_fee,
                                          SUM( IF(purchases.inclusive_fee>0, ROUND(purchases.processing_fee,2), 0) ) AS fees_incl,
                                          SUM( IF(purchases.inclusive_fee>0, 0, ROUND(purchases.processing_fee,2)) ) AS fees_over, '.$amount))
                            ->where('purchases.status','<>','Void');
            if($onlyConsign)
                $events->where(function($query) {
                            $query->where('purchases.channel','=','Consignment')
                                  ->orWhere('purchases.payment_type','=','Consignment');
                        });
            else
                $events->where('purchases.channel','<>','Consignment')->where('purchases.payment_type','<>','Consignment');
            
            if(!empty($e_id))
            {
                if($type=='venue')
                    $events->where('venues.id',$e_id);
            }
            if(!empty($start_date))
                $events->where('show_times.show_time','>=',$start_date);
            if(!empty($end_date))
                $events->where('show_times.show_time','<=',$end_date);
            $events = $events->groupBy('show_times.id')->groupBy('venues.id')->groupBy('shows.id')->groupBy('purchases.channel')->groupBy('purchases.payment_type')->groupBy('tickets.id')->groupBy('discounts.id')->groupBy('purchases.status')
                             ->orderBy('show_times.show_time','ASC')->orderBy('venues.name')->orderBy('shows.name')->orderBy('purchases.channel')->orderBy('purchases.payment_type')->orderBy('tickets.ticket_type')->orderBy('discounts.code')->orderBy('purchases.status')
                             ->distinct()->get()->toArray();
            return ['data'=>$events, 'total'=> $this->calc_totals($events)];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * ************************************************************************************************************************   report financial
     * create report sales
     */
    public function report_financial($type,$e_id,$title_)
    {
        try {
            //init
            $start = $this->start_date;
            $end = $this->end_date;
            $tables = [];

            //table sales by period or daily by property    -0
            $_start = $start;
            $_end = $end;
            $title = ($start==$end)? 'DAILY BY PROPERTY:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date($this->date_format,strtotime($_end)) : 'PERIOD BY PROPERTY:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date($this->date_format,strtotime($_start)).' - '.date($this->date_format,strtotime($_end)) ;
            $tables[] = $this->create_table_financial($_start,$_end,$title,$e_id,$type);

            //table roll up month MTD   -1
            $_start = date('Y-m-01 H:i',strtotime($end));
            $_end = $end;
            $title = 'ROLL UP MTD CURRENT ('.date('F Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date($this->date_format,strtotime($_start)).' - '.date($this->date_format,strtotime($_end)) ;
            $tables[] = $this->create_table_financial($_start,$_end,$title,$e_id,$type);

            //table roll up previous month MTD  -2
            $_start = date('Y-m-d H:i', $this->rollup_date( date('Y-m-01 H:i',strtotime($end)) ));
            $_end = date('Y-m-d H:i', $this->rollup_date($end));
            $title = 'ROLL UP MTD PERIOD ('.date('F Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date($this->date_format,strtotime($_start)).' - '.date($this->date_format,strtotime($_end)) ;
            $tables[] = $this->create_table_financial($_start,$_end,$title,$e_id,$type);

            //table roll up year YTD    -3
            $_start = date('Y-01-01 H:i',strtotime($end));
            $_end = $end;
            $title = 'ROLL UP YTD CURRENT ('.date('Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date($this->date_format,strtotime($_start)).' - '.date($this->date_format,strtotime($_end)) ;
            $tables[] = $this->create_table_financial($_start,$_end,$title,$e_id,$type);

            //table roll up previous year YTD   -4
            $_start = date('Y-m-d H:i', $this->rollup_date( date('Y-01-01 H:i',strtotime($end)) ));
            $_end = date('Y-m-d H:i', $this->rollup_date($end));
            $title = 'ROLL UP YTD PERIOD ('.date('Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date($this->date_format,strtotime($_start)).' - '.date($this->date_format,strtotime($_end)) ;
            $tables[] = $this->create_table_financial($_start,$_end,$title,$e_id,$type);

            //percent MTD
            $tables[1]['percent'] = (end($tables[2]['total'])>0)? round(( end($tables[1]['total']) - end($tables[2]['total']) ) / end($tables[2]['total']) * 100,1) : 100;
            //percent YTD
            $tables[3]['percent'] = (end($tables[4]['total'])>0)? round(( end($tables[3]['total']) - end($tables[4]['total']) ) / end($tables[4]['total']) * 100,1) : 100;
            
            //create obj
            $financial = $tables;
            //debits
            $debits = $this->create_table_debits($type, $e_id);
            
            //return
            return ['type'=>$type,'title'=>$title_,'date'=>$this->report_date,'created'=>date($this->date_format,strtotime($this->end_date)),'table_financial'=>$financial,'table_debits'=>$debits];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_financial
     */
    public function create_table_financial($start,$end,$title,$e_id,$type)
    {
        try {
            $amount = ($type=='admin')? 'SUM(purchases.commission_percent+purchases.processing_fee+purchases.printed_fee) AS amount' :
                                        'SUM(purchases.price_paid-purchases.sales_taxes-purchases.cc_fees-purchases.commission_percent-purchases.processing_fee-purchases.printed_fee) AS amount';
            $table = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=', 'show_times.show_id')
                        ->join('venues', 'venues.id', '=', 'shows.venue_id')
                        ->select(DB::raw('venues.id, venues.name, SUM(purchases.sales_taxes) AS taxes, SUM(purchases.cc_fees) AS cc_fee,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets, SUM(purchases.printed_fee) AS printed_fee,
                                          SUM(purchases.price_paid) AS paid, SUM(purchases.commission_percent) AS commissions,
                                          SUM( IF(purchases.inclusive_fee>0, ROUND(purchases.processing_fee,2), 0) ) AS fees_incl,
                                          SUM( IF(purchases.inclusive_fee>0, 0, ROUND(purchases.processing_fee,2)) ) AS fees_over, '.$amount))
                        ->where('purchases.status','<>','Void')
                        ->groupBy('venues.id')->orderBy('venues.name');
            
            if($type=='admin' || empty($e_id))
                $table->where('purchases.created','>=',$start)->where('purchases.created','<=',$end);
            else if(!empty($e_id))
            {
                if($type=='venue')
                    $table->where('purchases.created','>=',$start)->where('purchases.created','<=',$end)->where('venues.id',$e_id);
            }
            $table = $table->distinct()->get()->toArray();
            $data = ['title'=>$title, 'data'=>$table, 'total'=> $this->calc_totals($table)];
            //debits
            $debits = $this->create_table_debits($type, $e_id, $start, $end);
            $data['debits'] = $debits['data'];
            $data['grand_total'] = $this->calc_totals([$data['total'], $debits['total']]);
            //return
            return $data;
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * ************************************************************************************************************************   general funcions
     * calculate create_files
     */
    public function create_files($report,$name)
    {
        try {
            $files = [];    
            //financial report pdf
            if(isset($report['financial']))
            {
                $name_report = 'Sales Financial Report';
                $format = 'financial'; $data = $report['financial'];
                $file = '/tmp/ReportSalesFinancial_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$name).'_'.date('Y-m-d').'_'.date('U').'.pdf';
                $view = View::make('command.report_sales', compact('data','format','name_report'));
                $pdf = PDF::loadHTML($view->render())->setPaper('letter', 'portrait')->setWarnings(false)->save($file);
                $pdf->getDomPDF()->get_canvas()->page_text(35, 770, $name_report." | Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
                $pdf->save($file);                
                $files[] = $file;
            }    
            //sales report pdf
            if(isset($report['sales']))
            {
                $name_report = 'Sales Breakdown Report';
                $format = 'sales'; $data = $report['sales'];
                $file = '/tmp/ReportSalesBreakdown_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$name).'_'.date('Y-m-d').'_'.date('U').'.pdf';
                $view = View::make('command.report_sales', compact('data','format','name_report'));
                $pdf = PDF::loadHTML($view->render())->setPaper('letter', 'portrait')->setWarnings(false)->save($file);
                $pdf->getDomPDF()->get_canvas()->page_text(35, 770, $name_report." | Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
                $pdf->save($file);                
                $files[] = $file;
            }    
            //events breakdown report pdf
            if(isset($report['events']))
            {
                $name_report = 'Events Breakdown Report';
                $format = 'event_breakdown'; $data = $report['events'];
                $file = '/tmp/ReportEventBreakdown_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$name).'_'.date('Y-m-d').'_'.date('U').'.pdf';
                $view = View::make('command.report_sales', compact('data','format','name_report'));
                $pdf = PDF::loadHTML($view->render())->setPaper('letter', 'landscape')->setWarnings(false)->save($file);
                $pdf->getDomPDF()->get_canvas()->page_text(35, 590, $name_report." | Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
                $pdf->save($file);                
                $files[] = $file;
            }
            //sellers report pdf
            if(isset($report['sellers']))
            {
                $name_report = 'Sellers POS Report';
                $format = 'sellers'; $data = $report['sellers'];
                $file = '/tmp/ReportSellersPOS_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$name).'_'.date('Y-m-d').'_'.date('U').'.pdf';
                $view = View::make('command.report_sales', compact('data','format','name_report'));
                $pdf = PDF::loadHTML($view->render())->setPaper('letter', 'landscape')->setWarnings(false)->save($file);
                $pdf->getDomPDF()->get_canvas()->page_text(35, 590, $name_report." | Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
                $pdf->save($file);                
                $files[] = $file;
            }
            //debits report pdf
            if(isset($report['debits']))
            {
                $name_report = 'Refunds & Chargebacks Report';
                $format = 'debits'; $data = $report['debits'];
                $file = '/tmp/ReportRefundsChargebacks_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$name).'_'.date('Y-m-d').'_'.date('U').'.pdf';
                $view = View::make('command.report_sales', compact('data','format','name_report'));
                $pdf = PDF::loadHTML($view->render())->setPaper('letter', 'landscape')->setWarnings(false)->save($file);
                $pdf->getDomPDF()->get_canvas()->page_text(35, 590, $name_report." | Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
                $pdf->save($file);                
                $files[] = $file;
            }
            //future liabilities report pdf
            if(isset($report['future']))
            {
                $name_report = 'Future Sales Report';
                $format = 'future_liabilities'; $data = $report['future'];
                $file = '/tmp/ReportFutureSales_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$name).'_'.date('Y-m-d').'_'.date('U').'.pdf';
                $view = View::make('command.report_sales', compact('data','format','name_report'));
                $pdf = PDF::loadHTML($view->render())->setPaper('letter', 'landscape')->setWarnings(false)->save($file);
                $pdf->getDomPDF()->get_canvas()->page_text(35, 590, $name_report." | Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
                $pdf->save($file);                
                $files[] = $file;
            }
            //sales report csv
            /*if(!empty($report['purchases']))
            {
                $format = 'csv'; $data = $report['purchases'];
                $view = View::make('command.report_sales', compact('data','format'));
                $url = '/tmp/ReportSales_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$name).'_'.date('Y-m-d').'_'.date('U').'.csv';
                $file= fopen($url, "w"); fwrite($file, $view->render()); fclose($file);
                $files[] = $url;
            }*/
            //return files
            return $files;
        } catch (Exception $ex) {

        } finally {
            return $files;
        }
    }
    /*
     * calculate create_files
     */
    public function send_email($files,$to_email,$subject)
    {
        try {
            $email = new EmailSG(env('MAIL_REPORT_FROM'), $to_email ,$this->subject.'( '.$subject.' ) '.$this->report_date);
            /*if(env('MAIL_REPORT_CC',null))
                $email->cc(env('MAIL_REPORT_CC'));*/
            $email->category('Reports');            
            if(!empty($files))
            {
                $msg = '<center>Attached is the report for sales completed on '.date($this->date_format, strtotime($this->end_date)).'<br><h1>:)</h1></center>';
                $email->attachment( $files );
            }
            else
            {
                $msg = '<center>This venue has no purchases in this period<br><h1>:(</h1></center>';
            }
            $email->body('custom',['body'=>$msg]);
            $email->template('46388c48-5397-440d-8f67-48f82db301f7');
            $sent = $email->send();
            foreach ($files as $f)
                if(file_exists($f))
                    unlink($f);
            return $sent;
        } catch (Exception $ex) {
            return false;
        }
    }
    /*
     * calculate totals
     */
    public function calc_totals($table)
    {
        try {
            $table = (array)$table;
            return array( 'tickets'=>array_sum(array_column($table,'tickets')),
                          'transactions'=>array_sum(array_column($table,'transactions')),
                          'paid'=>array_sum(array_column($table,'paid')),
                          'taxes'=>array_sum(array_column($table,'taxes')),
                          'cc_fee'=>array_sum(array_column($table,'cc_fee')),
                          'printed_fee'=>array_sum(array_column($table,'printed_fee')),
                          'commissions'=>array_sum(array_column($table,'commissions')),
                          'fees_incl'=>array_sum(array_column($table,'fees_incl')),
                          'fees_over'=>array_sum(array_column($table,'fees_over')),
                          'amount'=>array_sum(array_column($table,'amount')));
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_financial rollup_date
     */
    public function rollup_date($date)
    {
        try {
            $ts = strtotime($date);
            $year = date('o', $ts) - 1;
            $week = date('W', $ts);
            $day = date('N', $ts);
            $hour = date('H', $ts);
            $min = date('i', $ts);
            return strtotime($year.'W'.$week.$day.' '.$hour.':'.$min);
        } catch (Exception $ex) {
            return '';
        }
    }



}
