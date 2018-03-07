<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail\EmailSG;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Models\Venue;

class ReportFinancial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Report:financial {start=0} {end=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for sending information about financial report in period (default, last 7 days)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            //parameters
            $start = $this->argument('start');
            if(empty($start) || !strtotime($start))
                $start = date('Y-m-d', strtotime('yesterday'));
            else
                $start = date('Y-m-d', strtotime($start));
            $end = $this->argument('end');
            if(empty($end) || !strtotime($end))
                $end = date('Y-m-d', strtotime('yesterday'));
            else
                $end = date('Y-m-d', strtotime($end));
            
            //init
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
                $total = array( 'name'=>$name,
                              'tickets'=>array_sum(array_column($table,'tickets')),
                              'purchases'=>array_sum(array_column($table,'purchases')),
                              'paid'=>array_sum(array_column($table,'paid')),
                              'commissions'=>array_sum(array_column($table,'commissions')),
                              'fees'=>array_sum(array_column($table,'fees')),
                              'amount'=>array_sum(array_column($table,'amount')));
                return ['title'=>$title, 'data'=>$table, 'total'=>$total];
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
            
            //create report
            $pdf_path = '/tmp/ReportFinancial_'.date('Y-m-d').'_'.date('U').'.pdf';
            $view_email = View::make('command.report_financial', compact('tables')); 
            PDF::loadHTML($view_email->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdf_path);
            
            //send the report
            $email = new EmailSG(env('MAIL_REPORT_FROM'), env('MAIL_REPORT_TO','ivan@ticketbat.com') ,'Financial Report');
            /*if(env('MAIL_REPORT_CC',null))
                $email->cc(env('MAIL_REPORT_CC'));*/
            $email->category('Reports');
            $email->body('sales_report',array('date'=>$tables[0]['title']));
            $email->template('a6e2bc2e-5852-4d14-b8ff-d63e5044fd14');
            $email->attachment($pdf_path);
            if($email->send())
                unlink($pdf_path);
            return true;
        } catch (Exception $ex) {
            throw new Exception('Error creating report with ReportFinancial Command: '.$ex->getMessage());
        }
    }
}
