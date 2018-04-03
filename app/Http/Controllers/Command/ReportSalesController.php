<?php

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail\EmailSG;
use Barryvdh\DomPDF\Facade as PDF;

/**
 * Manage ReportSales options for the commands
 *
 * @author ivan
 */
class ReportSalesController extends Controller{

    protected $days = 1;
    protected $only_admin = 0;
    protected $start_date;
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
        $this->start_date = date('Y-m-d',strtotime('-'.$this->days.' days'));
        $date_format = 'D, F j, Y';
        $this->report_date = ($this->days==1)? date($date_format,strtotime('yesterday')) :
                                  date($date_format,strtotime('-'.$this->days.' days')).' - '.date($date_format,strtotime('yesterday'));
        $this->subject = 'Daily Sales Report ';
    }
    /*
     * get sales report pdf
     */
    public function init()
    {
        try {
            //init main variables
            $report = ['sales'=>[],'future'=>[]];

            //get all the venues with purchases and if admin add extra fields
            if($this->only_admin>0) //admin, get all data with values
            {
                $venues = DB::table('venues')
                            ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->select(DB::raw('venues.id, venues.name, venues.accounting_email, venues.daily_sales_emails,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets,
                                          SUM(purchases.price_paid) AS paid,
                                          SUM(purchases.commission_percent) AS commissions,
                                          SUM(purchases.processing_fee) AS fees,
                                          SUM(purchases.price_paid)-SUM(purchases.commission_percent)-SUM(purchases.processing_fee) AS amount'))
                            ->where('purchases.status','=','Active')
                            ->whereDate('purchases.created','>=',$this->start_date)
                            ->groupBy('venues.id')->orderBy('venues.name')
                            ->distinct()->get()->toArray();
                //create each report data
                $report['sales'][] = $this->report_sales('admin',$venues,'TicketBat Totals');
                $report['future'][] = $this->report_future_liabilities('admin',null,'TicketBat Totals');
            }
            else    //regular other sales
            {
                $venues = DB::table('venues')
                            ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->select('venues.id','venues.name','venues.accounting_email','venues.daily_sales_emails')
                            ->where('purchases.status','=','Active')
                            ->whereDate('purchases.created','>=',$this->start_date)
                            ->groupBy('venues.id')->orderBy('venues.name')
                            ->distinct()->get()->toArray();
            }
            //loop through all venues to create each report for each one
            foreach ($venues as $v)
            {
                $report_venue = [];
                $report_venue['sales'] = [ $this->report_sales('venue',$v->id,$v->name) ];
                $report_venue['future'] = [ $this->report_future_liabilities('venue',$v->id,$v->name) ];

                //merge them to admin
                if($this->only_admin>0)
                {
                    $report['sales'][] = $report_venue['sales'][0];
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
            //send admin email
            if($this->only_admin>0)
            {
                $files = $this->create_files($report,'TicketBat Totals');
                //send the email
                if(!empty($files))
                    $sent = $this->send_email($files,env('MAIL_REPORT_TO'),'TicketBat Totals');
            }
            if(isset($sent))
                return $sent;
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }
    /*
     * table_sales_shows
     */
    public function report_sales($type,$venue,$title)
    {
        try {
            if($type=='admin')
            {
                $types = $this->create_table_types();
                $financial = $this->report_financial();
                $shows = $this->create_table_shows();
                $channels = $this->create_table_channels();
            }
            else
            {
                $types = $this->create_table_types($venue);
                $financial = $this->report_financial($venue);
                $shows = $this->create_table_shows($venue);
                $channels = $this->create_table_channels($venue);
            }
            return ['type'=>$type,'title'=>$title,'date'=>$this->report_date,'table_shows'=>$shows,'table_types'=>$types,'table_channels'=>$channels,'table_financial'=>$financial];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_sales_shows
     */
    public function create_table_shows($venue_id=null)
    {
        try {
            if(empty($venue_id))
                return DB::table('shows')
                            ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->select(DB::raw('venues.name AS venue, shows.name, tickets.ticket_type, DATE_FORMAT(show_times.show_time, "%c/%e/%y %l:%i%p") AS show_time,
                                            (CASE WHEN (purchases.ticket_type = "Consignment") THEN purchases.ticket_type
                                            WHEN (purchases.ticket_type != "Consignment") AND (tickets.retail_price<0.01) THEN "Free event"
                                            ELSE purchases.payment_type END) AS payment_type,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets,
                                          SUM(purchases.retail_price) AS retail_price,
                                          SUM(purchases.savings) AS savings,
                                          SUM(purchases.price_paid) AS paid,
                                          SUM(purchases.commission_percent) AS commissions,
                                          SUM(purchases.processing_fee) AS fees,
                                          SUM(purchases.price_paid)-SUM(purchases.commission_percent)-SUM(purchases.processing_fee) AS amount'))
                            ->where('purchases.status','=','Active')
                            ->whereDate('purchases.created','>=',$this->start_date)
                            ->groupBy('venues.id')->groupBy('shows.id')->groupBy('show_times.show_time')->groupBy('tickets.ticket_type')->groupBy(DB::raw('payment_type'))
                            ->orderBy('venues.name')->orderBy('shows.name')->orderBy('show_times.show_time')->orderBy('tickets.ticket_type')->orderBy(DB::raw('payment_type'))
                            ->distinct()->get()->toArray();
            else
                //get all records
                return DB::table('shows')
                            ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->select(DB::raw('shows.name, tickets.ticket_type, DATE_FORMAT(show_times.show_time, "%c/%e/%y %l:%i%p") AS show_time,
                                            (CASE WHEN (purchases.ticket_type = "Consignment") THEN purchases.ticket_type
                                            WHEN (purchases.ticket_type != "Consignment") AND (tickets.retail_price<0.01) THEN "Free event"
                                            ELSE purchases.payment_type END) AS payment_type,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets,
                                          SUM(purchases.savings) AS savings,
                                          SUM(purchases.price_paid) AS paid,
                                          SUM(purchases.commission_percent) AS commissions,
                                          SUM(purchases.processing_fee) AS fees,
                                          SUM(purchases.price_paid)-SUM(purchases.commission_percent)-SUM(purchases.processing_fee) AS amount'))
                            ->where('purchases.status','=','Active')->where('venues.id','=',$venue_id)
                            ->whereDate('purchases.created','>=',$this->start_date)
                            ->groupBy('shows.id')->groupBy('show_times.show_time')->orderBy('shows.name')
                            ->distinct()->get()->toArray();
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_sales_types
     */
    public function create_table_types($venue_id=null)
    {
        try {
            $where = [['purchases.status','=','Active']];
            if($venue_id)
                $where[] = ['venues.id',$venue_id];
            //get all records
            $types = DB::table('venues')
                            ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->select(DB::raw('(CASE WHEN (purchases.ticket_type = "Consignment") THEN purchases.ticket_type
                                            WHEN (purchases.ticket_type != "Consignment") AND (tickets.retail_price<0.01) THEN "Free event"
                                            ELSE purchases.payment_type END) AS payment_type,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets,
                                          SUM(purchases.price_paid) AS paid,
                                          SUM(purchases.commission_percent) AS commissions,
                                          SUM(purchases.processing_fee) AS fees,
                                          SUM(purchases.price_paid)-SUM(purchases.commission_percent)-SUM(purchases.processing_fee) AS amount'))
                            ->where($where)
                            ->whereDate('purchases.created','>=',$this->start_date)
                            ->groupBy(DB::raw('payment_type'))->orderBy(DB::raw('payment_type'))
                            ->distinct()->get()->toArray();
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
    public function create_table_channels($venue_id=null)
    {
        try {
            $table = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=', 'show_times.show_id')
                        ->select(DB::raw('purchases.channel,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets,
                                          SUM(purchases.price_paid) AS paid, SUM(purchases.commission_percent) AS commissions,
                                          SUM(purchases.processing_fee) AS fees,
                                          SUM(purchases.commission_percent)+SUM(purchases.processing_fee) AS amount'))
                        ->where('purchases.status','=','Active')
                        ->whereDate('purchases.created','>=',$this->start_date)
                        ->groupBy('purchases.channel')->orderBy('purchases.channel');
            if(!empty($venue_id))
                $table->where('shows.venue_id',$venue_id);
            $table = $table->distinct()->get()->toArray();
            return ['data'=>$table, 'total'=> $this->calc_totals($table)];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_sales_shows
     */
    public function report_future_liabilities($type,$venue_id=null,$title)
    {
        try {
            if(empty($venue_id))    //admin
                $future = $this->create_table_future_liabilities();
            else
                $future = $this->create_table_future_liabilities($venue_id);
            return ['type'=>$type,'title'=>$title,'date'=>date('D, F j, Y',strtotime('today')),'table_future'=>$future['data'],'total'=>$future['total']];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_sales_types
     */
    public function create_table_future_liabilities($venue_id=null)
    {
        try {
            $where = [['purchases.status','=','Active']];
            if($venue_id)   //when each venue
            {
                $where[] = ['venues.id',$venue_id];
                $future = DB::table('venues')
                            ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->select(DB::raw('venues.name AS venue, shows.name AS event, DATE_FORMAT(show_times.show_time, "%c/%e/%y %l:%i%p") AS show_time,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets,
                                          SUM(purchases.price_paid) AS paid,
                                          SUM(purchases.commission_percent) AS commissions,
                                          SUM(purchases.processing_fee) AS fees,
                                          SUM(purchases.price_paid)-SUM(purchases.commission_percent)-SUM(purchases.processing_fee) AS amount'))
                            ->where($where)
                            ->where('show_times.show_time','>',date('Y-m-d H:i'))
                            ->groupBy('show_times.show_time')->groupBy('shows.id')
                            ->orderBy('show_times.show_time')->orderBy('shows.name')
                            ->distinct()->get()->toArray();
            }
            else    //when admin
            {
                $future = DB::table('venues')
                            ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->select(DB::raw('venues.name AS venue, shows.name AS event, show_times.show_time,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets,
                                          SUM(purchases.price_paid) AS paid,
                                          SUM(purchases.commission_percent) AS commissions,
                                          SUM(purchases.processing_fee) AS fees,
                                          SUM(purchases.commission_percent)+SUM(purchases.processing_fee) AS amount'))
                            ->where($where)
                            ->where('show_times.show_time','>',date('Y-m-d H:i'))
                            ->groupBy('show_times.show_time')->groupBy('shows.id')
                            ->orderBy('show_times.show_time')->orderBy('shows.name')
                            ->distinct()->get()->toArray();
            }
            return ['data'=>$future, 'total'=> $this->calc_totals($future)];
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_financial
     */
    public function report_financial($venue_id=null)
    {
        try {
            //init
            $start = $this->start_date;
            $end = date('Y-m-d');
            $tables = [];

            //table sales by period or daily by property    -0
            $_start = $start;
            $_end = $end;
            $name = 'Total';
            $title = ($start==$end)? 'DAILY BY PROPERTY:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j, Y',strtotime($_end)) : 'PERIOD BY PROPERTY:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j, Y',strtotime($_start)).' - '.date('D, F j, Y',strtotime($_end)) ;
            $tables[] = $this->create_table_financial($_start,$_end,$name,$title,$venue_id);

            //table roll up month MTD   -1
            $_start = date('Y-m-01',strtotime($end));
            $_end = $end;
            $name = 'Total MTD ('.date('F Y',strtotime($_end)).')';
            $title = 'ROLL UP MTD CURRENT ('.date('F Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j',strtotime($_start)).' - '.date('D, F j',strtotime($_end)) ;
            $tables[] = $this->create_table_financial($_start,$_end,$name,$title,$venue_id);

            //table roll up previous month MTD  -2
            $_start = date('Y-m-d', $this->rollup_date( date('Y-m-01',strtotime($end)) ));
            $_end = date('Y-m-d', $this->rollup_date($end));
            $name = 'Total MTD ('.date('F Y',strtotime($_end)).')';
            $title = 'ROLL UP MTD PERIOD ('.date('F Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j',strtotime($_start)).' - '.date('D, F j',strtotime($_end)) ;
            $tables[] = $this->create_table_financial($_start,$_end,$name,$title,$venue_id);

            //table roll up year YTD    -3
            $_start = date('Y-01-01',strtotime($end));
            $_end = $end;
            $name = 'Total YTD ('.date('Y',strtotime($_end)).')';
            $title = 'ROLL UP YTD CURRENT ('.date('Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j',strtotime($_start)).' - '.date('D, F j',strtotime($_end)) ;
            $tables[] = $this->create_table_financial($_start,$_end,$name,$title,$venue_id);

            //table roll up previous year YTD   -4
            $_start = date('Y-m-d', $this->rollup_date( date('Y-01-01',strtotime($end)) ));
            $_end = date('Y-m-d', $this->rollup_date($end));
            $name = 'Total YTD ('.date('Y',strtotime($_end)).')';
            $title = 'ROLL UP YTD PERIOD ('.date('Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j',strtotime($_start)).' - '.date('D, F j',strtotime($_end)) ;
            $tables[] = $this->create_table_financial($_start,$_end,$name,$title,$venue_id);

            //percent MTD

            $tables[1]['percent'] = (end($tables[2]['total'])>0)? round(( end($tables[1]['total']) - end($tables[2]['total']) ) / end($tables[2]['total']) * 100,1) : 100;
            //percent YTD
            $tables[3]['percent'] = (end($tables[4]['total'])>0)? round(( end($tables[3]['total']) - end($tables[4]['total']) ) / end($tables[4]['total']) * 100,1) : 100;

            return $tables;
        } catch (Exception $ex) {
            return [];
        }
    }
    /*
     * table_financial
     */
    public function create_table_financial($start,$end,$name,$title,$venue_id)
    {
        try {
            $table = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=', 'show_times.show_id')
                        ->join('venues', 'venues.id', '=', 'shows.venue_id')
                        ->select(DB::raw('venues.id, venues.name, venues.financial_report_emails,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets,
                                          SUM(purchases.price_paid) AS paid, SUM(purchases.commission_percent) AS commissions,
                                          SUM(purchases.processing_fee) AS fees,
                                          SUM(purchases.commission_percent)+SUM(purchases.processing_fee) AS amount'))
                        //->where('venues.financial_report_emails','>',0)
                        ->where('purchases.status','=','Active')
                        ->whereDate('purchases.created','>=',$start)->whereDate('purchases.created','<=',$end)
                        ->groupBy('venues.id')->orderBy('venues.name');
            if(!empty($venue_id))
                $table->where('venues.id',$venue_id);
            $table = $table->distinct()->get()->toArray();
            return ['title'=>$title, 'data'=>$table, 'total'=> $this->calc_totals($table)];
        } catch (Exception $ex) {
            return [];
        }
    }

    /*
     * calculate create_files
     */
    public function create_files($report,$name)
    {
        try {
            $files = [];

            //sales report pdf
            $format = 'sales'; $data = $report['sales'];
            $view= View::make('command.report_sales', compact('data','format'));
            $file = '/tmp/ReportSales_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$name).'_'.date('Y-m-d').'_'.date('U').'.pdf';
            PDF::loadHTML($view->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($file);
            $files[] = $file;

            //future liabilities report pdf
            $format = 'future_liabilities'; $data = $report['future'];
            $file = '/tmp/ReportFutureLiabilities_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$name).'_'.date('Y-m-d').'_'.date('U').'.pdf';
            $view = View::make('command.report_sales', compact('data','format'));
            PDF::loadHTML($view->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($file);
            $files[] = $file;

            //sales report csv
            if(!empty($report['sales'][0]['table_shows']))
            {
                $format = 'csv'; $data = $report['sales'][0]['table_shows'];
                $view = View::make('command.report_sales', compact('data','format'));
                $url = '/tmp/ReportSales_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$name).'_'.date('Y-m-d').'_'.date('U').'.csv';
                $file= fopen($url, "w"); fwrite($file, $view->render()); fclose($file);
                $files[] = $url;
            }
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
            $email->body('sales_report',array('date'=>date('m/d/Y H:ia')));
            $email->template('a6e2bc2e-5852-4d14-b8ff-d63e5044fd14');
            $email->attachment( $files );
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
                          'commissions'=>array_sum(array_column($table,'commissions')),
                          'fees'=>array_sum(array_column($table,'fees')),
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
            return strtotime($year.'W'.$week.$day);
        } catch (Exception $ex) {
            return '';
        }
    }



}
