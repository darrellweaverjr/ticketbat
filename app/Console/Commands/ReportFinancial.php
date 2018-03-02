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
            //start date
            $start = $this->argument('start');
            if(empty($start) || !strtotime($start))
                $start = date('Y-m-d', strtotime('-7 days'));
            else
                $start = date('Y-m-d', strtotime($start));
            //end date
            $end = $this->argument('end');
            if(empty($end) || !strtotime($end))
                $end = date('Y-m-d', strtotime('now'));
            else
                $end = date('Y-m-d', strtotime($end));
            //init
            $summary = $tables = [];
            function calc_totals($table,$name)
            {
                return array( 'name'=>$name,
                              'tickets'=>array_sum(array_column($table,'tickets')),
                              'purchases'=>array_sum(array_column($table,'purchases')),
                              'paid'=>array_sum(array_column($table,'paid')),
                              'commissions'=>array_sum(array_column($table,'commissions')),
                              'fees'=>array_sum(array_column($table,'fees')),
                              'amount'=>array_sum(array_column($table,'amount')));
            }
            //get all purchases totals grouped by venues that has to be include it in the report
            $table1 = (array)DB::table('purchases')
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
            $tables[] = ['title'=>'Period: '.date('F j, Y', strtotime($start)).' to '.date('F j, Y', strtotime($end)),
                         'data'=>$table1,'total'=>calc_totals($table1,'Total Tickets')];
            //get all purchases totals grouped by month
            $table2 = (array)DB::table('purchases')
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
                        ->whereDate('purchases.created','>=',date('Y-m',strtotime($start)).'-01')
                        ->whereDate('purchases.created','<=',date('Y-m-t',strtotime($end)))
                        ->groupBy('venues.id')->orderBy('venues.name')
                        ->distinct()->get()->toArray();
            $tables[] = ['title'=>'Month: '.date('F/Y', strtotime($end)),
                         'data'=>$table2,'total'=>calc_totals($table2,'Total Month ('.date('M',strtotime($end)).')')];
            //get all purchases totals YTD
            $table3 = (array)DB::table('purchases')
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
                        ->whereDate('purchases.created','>=',date('Y',strtotime($start)).'-01-01')
                        ->whereDate('purchases.created','<=',date('Y-m-d',strtotime($end)))
                        ->groupBy('venues.id')->orderBy('venues.name')
                        ->distinct()->get()->toArray();
            $tables[] = ['title'=>'YTD: '.'January 1, '.date('Y',strtotime($start)).' to '.date('F j, Y',strtotime($end)),
                         'data'=>$table3,'total'=>calc_totals($table3,'Total Tickets YTD')];
            //get all purchases totals YOY same period
            $table4 = (array)DB::table('purchases')
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
                        ->whereDate('purchases.created','>=',date('Y-m-d',strtotime('-1 year '.$start)))
                        ->whereDate('purchases.created','<=',date('Y-m-d',strtotime('-1 year '.$end)))
                        ->groupBy('venues.id')->orderBy('venues.name')
                        ->distinct()->get()->toArray();
            $tables[] = ['title'=>'YOY: '.date('F j, Y',strtotime('-1 year '.$start)).' to '.date('F j, Y',strtotime('-1 year '.$end)),
                         'data'=>$table4,'total'=>calc_totals($table4,'YOY Same Period')];
            //calc percent
            $percent = ($tables[0]['total']['amount']>$tables[3]['total']['amount'])? ' <h3> +' : ' <h3> ';
            $percent.= (round(($tables[0]['total']['amount']-$tables[3]['total']['amount'])/$tables[3]['total']['amount']*100,2));
            $tables[3]['total']['name'].= $percent.' % profits compared with same period last year.</h3>';
            //create report
            $pdf_path = '/tmp/ReportFinancial_'.date('Y-m-d').'_'.date('U').'.pdf';
            $view_email = View::make('command.report_financial', compact('summary','tables')); 
            PDF::loadHTML($view_email->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdf_path);
            //send the report
            $email = new EmailSG(env('MAIL_REPORT_FROM'), env('MAIL_REPORT_TO','ivan@ticketbat.com') ,'Financial Report');
            $email->cc(env('MAIL_REPORT_CC'));
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
