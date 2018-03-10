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
        $date_format = 'F j, Y';
        $this->report_date = ($this->days==1)? date($date_format,strtotime('yesterday')) : 
                                  date($date_format,strtotime('-'.$this->days.' days')).' - '.date($date_format,strtotime('yesterday'));
        $this->subject = 'Daily Sales Report ('.$this->report_date.') ';
    }    
    /*
     * get sales report pdf
     */
    public function init()
    {
        try {
            //init main variables
            $report = ['sales'=>[],'future'=>[],'financial'=>[]];
            
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
                            ->distinct()->get();
                //create each report data
                $report['sales'][] = $this->report_sales('admin',$venues,'TicketBat Totals');
                $report['future'][] = $this->report_future_liabilities('admin',null,'TicketBat Totals');
                $report['financial'] = $this->report_financial();
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
                            ->distinct()->get();
            }   
            //loop through all venues to create each report for each one
            foreach ($venues as $v)
            {
                $report_venue = []; 
                $report_venue['sales'] = [ $this->report_sales('venue',$v->id,$v->name) ];
                $report_venue['future'] = [ $this->report_future_liabilities('venue',$v->id,$v->name) ];
                $report_venue['financial'] = $this->report_financial($v->id);
                
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
                    $sent = $this->send_email($files,env('MAIL_REPORT_TO'),' - Total TicketBat');
            }
            return true;
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
                $data = $venue;
            }
            else
            {
                $types = $this->create_table_types($venue);
                $data = $this->create_table_shows($venue);
            }
            return ['type'=>$type,'title'=>$title,'date'=>$this->report_date,'table_data'=>$data,'table_types'=>$types];
        } catch (Exception $ex) {
            return [];
        }
    }     
    /*
     * table_sales_shows
     */
    public function create_table_shows($venue_id)
    {
        try {
            //get all records 
            $shows = DB::table('shows')
                            ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->select(DB::raw('shows.name, show_times.show_time, tickets.ticket_type,
                                            (CASE WHEN (purchases.ticket_type = "Consignment") THEN purchases.ticket_type 
                                            WHEN (purchases.ticket_type != "Consignment") AND (tickets.retail_price<0.01) THEN "Free" 
                                            ELSE purchases.payment_type END) AS payment_type,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets, 
                                          SUM(purchases.price_paid) AS paid, 
                                          SUM(purchases.savings) AS savings, 
                                          SUM(purchases.commission_percent) AS commissions,
                                          SUM(purchases.processing_fee) AS fees,
                                          SUM(purchases.price_paid)-SUM(purchases.commission_percent)-SUM(purchases.processing_fee) AS amount'))
                            ->where('purchases.status','=','Active')->where('venues.id','=',$venue_id)
                            ->whereDate('purchases.created','>=',$this->start_date)
                            ->groupBy('shows.id')->groupBy('show_times.show_time')->orderBy('shows.name')
                            ->distinct()->get();
            return ['data'=>$shows, 'total'=> $this->calc_totals($shows)];
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
                                            WHEN (purchases.ticket_type != "Consignment") AND (tickets.retail_price<0.01) THEN "Free" 
                                            ELSE purchases.payment_type END) AS payment_type,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets, 
                                          SUM(purchases.price_paid) AS paid, 
                                          SUM(purchases.commission_percent) AS commissions,
                                          SUM(purchases.processing_fee) AS fees,
                                          SUM(purchases.price_paid)-SUM(purchases.commission_percent)-SUM(purchases.processing_fee) AS amount'))
                            ->where($where)
                            ->whereDate('purchases.created','>=',$this->start_date)
                            ->groupBy(DB::raw('payment_type'))->orderBy(DB::raw('payment_type'))
                            ->distinct()->get();
            $total = $this->calc_totals($types);
            $others = [];
            foreach ($types as $k=>$t)
            {
                if($t->payment_type=='Consignment')
                {
                    $others[] = $types->pull($k);
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
     * table_sales_shows
     */
    public function report_future_liabilities($type,$venue_id=null,$title)
    {
        try {
            if(empty($venue_id))    //admin
                $future = $this->create_table_future_liabilities();
            else
                $future = $this->create_table_future_liabilities($venue_id);
            return ['type'=>$type,'title'=>$title,'date'=>date('F j, Y',strtotime('today')),'table_future'=>$future['data'],'total'=>$future['total']];
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
                            ->select(DB::raw('venues.name AS venue, shows.name AS event, show_times.show_time,
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets, 
                                          SUM(purchases.price_paid) AS paid, 
                                          SUM(purchases.commission_percent) AS commissions,
                                          SUM(purchases.processing_fee) AS fees,
                                          SUM(purchases.price_paid)-SUM(purchases.commission_percent)-SUM(purchases.processing_fee) AS amount'))
                            ->where($where)
                            ->where('show_times.show_time','>',date('Y-m-d H:i'))
                            ->groupBy('show_times.show_time')->groupBy('shows.id')
                            ->orderBy('show_times.show_time')->orderBy('shows.name')
                            ->distinct()->get();
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
                            ->distinct()->get();
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
            $tables[] = $this->create_table_financial($_start,$_end,$name,$title);
            
            //table roll up month MTD   -1
            $_start = date('Y-m-01',strtotime($end));
            $_end = $end;
            $name = 'Total MTD ('.date('F Y',strtotime($_end)).')';
            $title = 'ROLL UP MTD CURRENT ('.date('F Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j',strtotime($_start)).' - '.date('D, F j',strtotime($_end)) ;
            $tables[] = $this->create_table_financial($_start,$_end,$name,$title);
            
            //table roll up previous month MTD  -2
            $_start = date('Y-m-d', $this->rollup_date( date('Y-m-01',strtotime($end)) ));
            $_end = date('Y-m-d', $this->rollup_date($end));
            $name = 'Total MTD ('.date('F Y',strtotime($_end)).')';
            $title = 'ROLL UP MTD PERIOD ('.date('F Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j',strtotime($_start)).' - '.date('D, F j',strtotime($_end)) ;
            $tables[] = $this->create_table_financial($_start,$_end,$name,$title);
            
            //table roll up year YTD    -3
            $_start = date('Y-01-01',strtotime($end));
            $_end = $end;
            $name = 'Total YTD ('.date('Y',strtotime($_end)).')';
            $title = 'ROLL UP YTD CURRENT ('.date('Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j',strtotime($_start)).' - '.date('D, F j',strtotime($_end)) ;
            $tables[] = $this->create_table_financial($_start,$_end,$name,$title);
            
            //table roll up previous year YTD   -4
            $_start = date('Y-m-d', $this->rollup_date( date('Y-01-01',strtotime($end)) ));
            $_end = date('Y-m-d', $this->rollup_date($end));
            $name = 'Total YTD ('.date('Y',strtotime($_end)).')';
            $title = 'ROLL UP YTD PERIOD ('.date('Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j',strtotime($_start)).' - '.date('D, F j',strtotime($_end)) ;
            $tables[] = $this->create_table_financial($_start,$_end,$name,$title);
            
            //percent MTD
            $tables[1]['percent'] = round(( end($tables[1]['total']) - end($tables[2]['total']) ) / end($tables[2]['total']) * 100,1);
            //percent YTD
            $tables[3]['percent'] = round(( end($tables[3]['total']) - end($tables[4]['total']) ) / end($tables[4]['total']) * 100,1);
            
            return $tables;
        } catch (Exception $ex) {
            return [];
        }
    } 
    /*
     * table_financial
     */
    public function create_table_financial($start,$end,$name,$title)
    {
        try {
            $table = (array)DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=', 'show_times.show_id')
                        ->join('venues', 'venues.id', '=', 'shows.venue_id')
                        ->select(DB::raw('venues.id, venues.name, 
                                          COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets, 
                                          SUM(purchases.price_paid) AS paid, SUM(purchases.commission_percent) AS commissions,
                                          SUM(purchases.processing_fee) AS fees,
                                          SUM(purchases.commission_percent)+SUM(purchases.processing_fee) AS amount'))
                        ->where('venues.financial_report_emails','>',0)
                        ->where('purchases.status','=','Active')
                        ->whereDate('purchases.created','>=',$start)->whereDate('purchases.created','<=',$end)
                        ->groupBy('venues.id')->orderBy('venues.name')
                        ->distinct()->get()->toArray();
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
            $pdf_path = '/tmp/ReportSales_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$name).'_'.date('Y-m-d').'_'.date('U').'.pdf';
            $manifest_email = View::make('command.report_sales', compact('data','format')); 
            PDF::loadHTML($manifest_email->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdf_path);
            $files[] = $pdf_path;
            
            //sales report csv
            $format = 'csv'; $data = $report['sales']; 
            $manifest_csv = View::make('command.report_sales', compact('purchases' ,'date_report','format'));
            $csv_path = '/tmp/ReportSales_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$name).'_'.date('Y-m-d').'_'.date('U').'.csv';
            $fp_csv= fopen($csv_path, "w"); fwrite($fp_csv, $manifest_csv->render()); fclose($fp_csv);
            $files[] = $csv_path;
            
            //future liabilities report pdf        
            $format = 'future_liabilities'; $data = $report['future']; 
            $pdf_future_path = '/tmp/ReportFutureLiabilities_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$name).'_'.date('Y-m-d').'_'.date('U').'.pdf';
            $future_email = View::make('command.report_sales', compact('data','format'));                
            PDF::loadHTML($future_email->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdf_future_path);
            $files[] = $pdf_future_path;
            
            //financial report pdf
            $format = 'financial'; $data = $report['financial']; 
            $pdf_path = '/tmp/ReportFinancial_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$name).'_'.date('Y-m-d').'_'.date('U').'.pdf';
            $view_email = View::make('command.report_sales', compact('data','format')); 
            PDF::loadHTML($view_email->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdf_path);
            $files[] = $csv_path;
            
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
            $email = new EmailSG(env('MAIL_REPORT_FROM'), $to_email ,$this->subject.$subject);
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
            return array( 'tickets'=>array_sum(array_column((array)$table,'tickets')),
                          'transactions'=>array_sum(array_column((array)$table,'transactions')),
                          'paid'=>array_sum(array_column((array)$table,'paid')),
                          'commissions'=>array_sum(array_column((array)$table,'commissions')),
                          'fees'=>array_sum(array_column((array)$table,'fees')),
                          'amount'=>array_sum(array_column((array)$table,'amount')));
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
