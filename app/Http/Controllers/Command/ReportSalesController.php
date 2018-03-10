<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Models\Venue;
use App\Http\Models\Show;
use App\Http\Models\Purchase;
use App\Http\Models\Util;

/**
 * Manage ReportSales options for the commands
 *
 * @author ivan
 */
class ReportSalesController extends Controller{
    
    protected $days = 1;
    protected $only_admin = 0;
    protected $start_date;
    
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
    }    
    /*
     * get sales report pdf
     */
    public function init()
    {
        try {
            //init main variables
            $report = ['sales'=>[],'future'=>[],'financial'=>[]];
            $date_format = 'F j, Y';
            $date = ($this->days==1)? date($date_format,strtotime('yesterday')) : 
                                      date($date_format,strtotime('-'.$this->days.' days')).' to '.date($date_format,strtotime('yesterday'));
            $subject = 'Daily Sales Report ('.$date.') ';
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
                //set up data for regular sales
                $types = $this->table_sales_types();
                $report['sales'][] = ['type'=>'admin','title'=>'','date'=>$date,'table_data'=>$venues,'table_types'=>$types['data'],'total'=>$types['total']];
                //set up data for future liabilities
                $future = $this->table_future_liabilities();
                $report['future'][] = ['type'=>'admin','title'=>'','table_future'=>$future['data'],'total'=>$future['total']];
                //set up data for financial
                $report['financial'] = $this->table_financial();
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
                $report_venue = ['sales'=>[],'future'=>[],'financial'=>[]];                
                //set up data for regular sales
                $types = $this->table_sales_types($v->id);
                $shows = $this->table_sales_shows($v->id);
                $report_venue['sales'] = [['type'=>'venue','title'=>'','date'=>$date,'table_data'=>$shows,'table_types'=>$types['data'],'total'=>$types['total']]];
                //set up data for future liabilities
                $future = $this->table_future_liabilities($v->id);
                $report_venue['future'] = [['type'=>'venue','title'=>'','table_future'=>$future['data'],'total'=>$future['total']]];
                //set up data for financial
                $report_venue['financial'] = $this->table_financial($v->id);
                
                //merge them to admin
                if($this->only_admin>0)
                {
                    $report['sales'][] = $report_venue['sales'];
                    $report['future'][] = $report_venue['future'];
                }
                //send the emails if available option
                else if($v->daily_sales_emails > 0 && !empty($v->accounting_email))
                {
                    $files = $this->create_files($report_venue);
                    //send the email
                    $sent = $this->send_email($files,$v->accounting_email,$subject.' - '.$v->name);
                }
            }
            //send admin email
            if($this->only_admin>0)
            {
                $files = $this->create_files($report);
                //send the email
                $sent = $this->send_email($files,env('MAIL_REPORT_TO'),$subject.' - Total TicketBat');
            }
                
                
        } catch (Exception $ex) {
            return false;
        }
    }        
    /*
     * calculate create_files
     */
    public function create_files($report)
    {
        try {
            $files = [];
            
            //MANIFEST SALES CUTOMIZED ACCORDING TO VENUES, SHOWS OR ADMIN                
            $format = 'customized';
            $pdf_path = '/tmp/ReportSales_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$namex).'_'.date('Y-m-d').'_'.date('U').'.pdf';
            $manifest_email = View::make('command.report_sales', compact('data','send','format'));                
            PDF::loadHTML($manifest_email->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdf_path);
            $files[] = $pdf_path;
            //FUTURE LIABILITIES ACCORDING TO VENUES, SHOWS OR ADMIN                
            $format = 'future_liabilities'; 
            $pdf_future_path = '/tmp/ReportFutureLiabilities_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$namex).'_'.date('Y-m-d').'_'.date('U').'.pdf';
            $future_email = View::make('command.report_sales', compact('data','send','format'));                
            PDF::loadHTML($future_email->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdf_future_path);
            $files[] = $pdf_future_path;
            //MANIFES SALES CSV
            $format = 'csv';
            $purchases = DB::select($sqlMain.$sqlFrom.$sqlPaid." GROUP BY p.show_time_id, p.ticket_type;");
            $manifest_csv = View::make('command.report_sales', compact('purchases' ,'date_report','format'));
            $csv_path = '/tmp/ReportSales_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$namex).'_'.date('Y-m-d').'_'.date('U').'.csv';
            $fp_csv= fopen($csv_path, "w"); fwrite($fp_csv, $manifest_csv->render()); fclose($fp_csv);
            $files[] = $csv_path;
            
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
            $email = new EmailSG(env('MAIL_REPORT_FROM'), $to_email ,$subject);
            if(env('MAIL_REPORT_CC',null))
                $email->cc(env('MAIL_REPORT_CC'));
            $email->category('Reports');
            $email->body('sales_report',array('date'=>date('m/d/Y H:ia')));
            $email->template('a6e2bc2e-5852-4d14-b8ff-d63e5044fd14');
            $email->attachments( $files );
            $sent = $email->send();
            foreach ($files as $f)
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
            return array( 'tickets'=>array_sum(array_column($table,'tickets')),
                          'purchases'=>array_sum(array_column($table,'purchases')),
                          'paid'=>array_sum(array_column($table,'paid')),
                          'commissions'=>array_sum(array_column($table,'commissions')),
                          'fees'=>array_sum(array_column($table,'fees')),
                          'amount'=>array_sum(array_column($table,'amount')));
        } catch (Exception $ex) {
            return [];
        }
    }       
    /*
     * table_sales_shows
     */
    public function table_sales_shows($venue_id)
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
    public function table_sales_types($venue_id=null)
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
                            ->groupBy('venues.id')->orderBy('venues.name')
                            ->distinct()->get();
            return ['data'=>$types, 'total'=> $this->calc_totals($types)];
        } catch (Exception $ex) {
            return [];
        }
    }     
    /*
     * table_sales_types
     */
    public function table_future_liabilities($venue_id=null)
    {
        try {
            $where = [['purchases.status','=','Active']];
            if($venue_id)
                $where[] = ['venues.id',$venue_id];
            //get all records 
            $future = DB::table('venues')
                            ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->select(DB::raw('venues.name AS venue, shows.name AS show, show_times.show_time,
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
            return ['data'=>$future, 'total'=> $this->calc_totals($future)];
        } catch (Exception $ex) {
            return [];
        }
    }     
    /*
     * table_financial
     */
    public function table_financial($venue_id=null)
    {
        try {
            //init
            $start = $this->start_date;
            $end = date('Y-m-d');
            $tables = [];
            function create_table($start,$end,$name,$title)
            {
                $table = (array)DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=', 'show_times.show_id')
                        ->join('venues', 'venues.id', '=', 'shows.venue_id')
                        ->select(DB::raw('venues.id, venues.name, 
                                          COUNT(purchases.id) AS purchases, SUM(purchases.quantity) AS tickets, 
                                          SUM(purchases.price_paid) AS paid, SUM(purchases.commission_percent) AS commissions,
                                          SUM(purchases.processing_fee) AS fees,
                                          SUM(purchases.commission_percent)+SUM(purchases.processing_fee) AS amount'))
                        ->where('venues.financial_report_emails','>',0)
                        ->where('purchases.status','=','Active')
                        ->whereDate('purchases.created','>=',$start)->whereDate('purchases.created','<=',$end)
                        ->groupBy('venues.id')->orderBy('venues.name')
                        ->distinct()->get()->toArray();
                return ['title'=>$title, 'data'=>$table, 'total'=> $this->calc_totals($table)];
            }
            function rollup_date($date)
            {
                $ts = strtotime($date);
                $year = date('o', $ts) - 1; 
                $week = date('W', $ts);
                $day = date('N', $ts);
                return strtotime($year.'W'.$week.$day);
            }
            
            //table sales by period or daily by property    -0
            $_start = $start;
            $_end = $end;
            $name = 'Total';
            $title = ($start==$end)? 'DAILY BY PROPERTY:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j, Y',strtotime($_end)) : 'PERIOD BY PROPERTY:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j, Y',strtotime($_start)).' - '.date('D, F j, Y',strtotime($_end)) ;
            $tables[] = create_table($_start,$_end,$name,$title);
            
            //table roll up month MTD   -1
            $_start = date('Y-m-01',strtotime($end));
            $_end = $end;
            $name = 'Total MTD ('.date('F Y',strtotime($_end)).')';
            $title = 'ROLL UP MTD CURRENT ('.date('F Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j',strtotime($_start)).' - '.date('D, F j',strtotime($_end)) ;
            $tables[] = create_table($_start,$_end,$name,$title);
            
            //table roll up previous month MTD  -2
            $_start = date('Y-m-d', rollup_date( date('Y-m-01',strtotime($end)) ));
            $_end = date('Y-m-d', rollup_date($end));
            $name = 'Total MTD ('.date('F Y',strtotime($_end)).')';
            $title = 'ROLL UP MTD PERIOD ('.date('F Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j',strtotime($_start)).' - '.date('D, F j',strtotime($_end)) ;
            $tables[] = create_table($_start,$_end,$name,$title);
            
            //table roll up year YTD    -3
            $_start = date('Y-01-01',strtotime($end));
            $_end = $end;
            $name = 'Total YTD ('.date('Y',strtotime($_end)).')';
            $title = 'ROLL UP YTD CURRENT ('.date('Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j',strtotime($_start)).' - '.date('D, F j',strtotime($_end)) ;
            $tables[] = create_table($_start,$_end,$name,$title);
            
            //table roll up previous year YTD   -4
            $_start = date('Y-m-d', rollup_date( date('Y-01-01',strtotime($end)) ));
            $_end = date('Y-m-d', rollup_date($end));
            $name = 'Total YTD ('.date('Y',strtotime($_end)).')';
            $title = 'ROLL UP YTD PERIOD ('.date('Y',strtotime($_end)).'):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('D, F j',strtotime($_start)).' - '.date('D, F j',strtotime($_end)) ;
            $tables[] = create_table($_start,$_end,$name,$title);
            
            //percent MTD
            $tables[1]['percent'] = round(( end($tables[1]['total']) - end($tables[2]['total']) ) / end($tables[2]['total']) * 100,1);
            //percent YTD
            $tables[3]['percent'] = round(( end($tables[3]['total']) - end($tables[4]['total']) ) / end($tables[4]['total']) * 100,1);
            
            return $tables;
        } catch (Exception $ex) {
            return [];
        }
    } 
    
}
