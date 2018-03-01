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
            $date_report = date('F j, Y', strtotime($start)).' to '.date('F j, Y', strtotime($end));
            //get all purchases totals grouped by venues that has to be include it in the report
            $venues = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=', 'show_times.show_id')
                        ->join('venues', 'venues.id', '=', 'shows.venue_id')
                        ->select(DB::raw('venues.id, venues.name, 
                                          COUNT(purchases.id) AS purchases, SUM(purchases.quantity) AS tickets, 
                                          SUM(purchases.price_paid)-SUM(purchases.commission_percent)-SUM(purchases.processing_fee) AS amount'))
                        ->where('venues.financial_report_emails','>',0)
                        ->where('purchases.status','=','Active')
                        ->whereDate('purchases.created','>=',$start)->whereDate('purchases.created','<=',$end)
                        ->groupBy('venues.id')->orderBy('venues.name')
                        ->distinct()->get()->toArray();
            //totals for this period
            $total_tickets = array( 'tickets'=>array_sum(array_column($venues,'tickets')),
                            'purchases'=>array_sum(array_column($venues,'purchases')),
                            'amount'=>array_sum(array_column($venues,'amount')));
            //get all purchases totals grouped by month
            $total_month = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=', 'show_times.show_id')
                        ->join('venues', 'venues.id', '=', 'shows.venue_id')
                        ->select(DB::raw('COUNT(purchases.id) AS purchases, SUM(purchases.quantity) AS tickets, 
                                          SUM(purchases.price_paid)-SUM(purchases.commission_percent)-SUM(purchases.processing_fee) AS amount'))
                        ->where('venues.financial_report_emails','>',0)
                        ->where('purchases.status','=','Active')
                        ->whereDate('purchases.created','>=',date('Y-m',strtotime($start)).'-01')
                        ->whereDate('purchases.created','<=',date('Y-m-t',strtotime($end)))
                        ->first();
            //get all purchases totals YTD
            $total_ytd = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=', 'show_times.show_id')
                        ->join('venues', 'venues.id', '=', 'shows.venue_id')
                        ->select(DB::raw('COUNT(purchases.id) AS purchases, SUM(purchases.quantity) AS tickets, 
                                          SUM(purchases.price_paid)-SUM(purchases.commission_percent)-SUM(purchases.processing_fee) AS amount'))
                        ->where('venues.financial_report_emails','>',0)
                        ->where('purchases.status','=','Active')
                        ->whereDate('purchases.created','>=',date('Y',strtotime($start)).'-01-01')
                        ->whereDate('purchases.created','<=',date('Y-m-d',strtotime($end)))
                        ->first();
            //get all purchases totals YOY same period
            $total_yoy = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=', 'show_times.show_id')
                        ->join('venues', 'venues.id', '=', 'shows.venue_id')
                        ->select(DB::raw('COUNT(purchases.id) AS purchases, SUM(purchases.quantity) AS tickets, 
                                          SUM(purchases.price_paid)-SUM(purchases.commission_percent)-SUM(purchases.processing_fee) AS amount'))
                        ->where('venues.financial_report_emails','>',0)
                        ->where('purchases.status','=','Active')
                        ->whereDate('purchases.created','>=',date('Y-m-d',strtotime('-1 year '.$start)))
                        ->whereDate('purchases.created','<=',date('Y-m-d',strtotime('-1 year '.$end)))
                        ->first();
            //create report
            $pdf_path = '/tmp/ReportFinancial_'.date('Y-m-d').'_'.date('U').'.pdf';
            $view_email = View::make('command.report_financial', compact('date_report','venues','total_tickets','total_month','total_ytd','total_yoy'));                
            PDF::loadHTML($view_email->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdf_path);
            //send the report
            $emailx = 'ivan@ticketbat.com';
            $email = new EmailSG(env('MAIL_REPORT_FROM'), $emailx ,'Financial Report');
            $email->cc(env('MAIL_REPORT_CC'));
            $email->category('Reports');
            $email->body('sales_report',array('date'=>$date_report));
            $email->template('a6e2bc2e-5852-4d14-b8ff-d63e5044fd14');
            $email->attachment($pdf_path);
            if($email->send())
                unlink($pdf_path);
            
            //echo $view_email->render();
            return true;
        } catch (Exception $ex) {
            throw new Exception('Error creating report with ReportFinancial Command: '.$ex->getMessage());
        }
    }
}
