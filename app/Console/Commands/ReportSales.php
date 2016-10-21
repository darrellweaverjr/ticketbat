<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail\EmailSG;

class ReportSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Report:sales {days=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for sending information about sales for the day or X days (default yesterday)';

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
            $days = $this->argument('days');
            ($days == 1)? $bound = ' = ' : $bound = ' >= ';
            $date_report = date("F j, Y", strtotime("yesterday"));
            setlocale(LC_MONETARY, 'en_US');

            $sqlMain = "SELECT v.id as v_id, v.name as v_name, v.accounting_email as v_email, s.id as s_id, s.name as s_name, s.accounting_email as s_email, t.ticket_type,
                        v.daily_sales_emails AS v_daily_sales_emails, s.daily_sales_emails AS s_daily_sales_emails,
                        DATE_FORMAT(st.show_time,'%m/%d/%Y %h:%s %p') AS shows_time, sum(p.quantity) AS qty, COUNT(*) AS purchase_count, sum(p.retail_price) AS retail_price, 
                        SUM(p.processing_fee) AS processing_fee, SUM(p.savings) AS savings, SUM(p.price_paid) AS gross_revenue, ROUND(AVG(p.commission_percent),2) AS commission_percent, 
                        SUM(p.price_paid) AS total_paid, ROUND(SUM(p.retail_price)-SUM(p.commission),2) AS due_to_show, ROUND(SUM(p.commission),2) AS commission, 
                        SUBSTRING_INDEX(SUBSTRING_INDEX(p.referrer_url, '://', -1),'/', 1) AS referrer_url,
                        SUBSTRING_INDEX(p.referrer_url, '://', -1) AS url, SUM(p.price_paid)-SUM(p.commission)-SUM(p.processing_fee) AS net ";

            $sqlFrom =" FROM (SELECT *, ROUND((retail_price - savings) * commission_percent/100,2) AS commission
                        FROM purchases WHERE date(created) ".$bound." (CURRENT_DATE - INTERVAL ".$days." DAY) AND Status = 'Active') AS p
                        LEFT JOIN show_times st ON st.id = p.show_time_id
                        LEFT JOIN shows s ON s.id = st.show_id
                        INNER JOIN venues v ON v.id = s.venue_id
                        LEFT JOIN tickets t ON p.ticket_id = t.id ";

            //FUNCTION CALCULATE SUBTOTALS
            function calculate_total($elements)
            {
                $total = array('t_ticket'=>0, 't_purchases'=>0, 't_gross_revenue'=>0, 't_processing_fee'=>0, 't_commission_percent'=>0, 't_net'=>0, 't_commission'=>0);
                (count($elements)>0)? $cant=count($elements):$cant=1;
                foreach ($elements as $e)
                    $total = array('t_ticket'=>$total['t_ticket']+$e['qty'], 't_purchases'=>number_format($total['t_purchases']+$e['purchase_count'],2), 
                                   't_gross_revenue'=>number_format($total['t_gross_revenue']+$e['gross_revenue'],2), 't_processing_fee'=>number_format($total['t_processing_fee']+$e['processing_fee'],2), 
                                   't_commission_percent'=>$total['t_commission_percent']+$e['commission_percent'], 't_net'=>number_format($total['t_net']+$e['net'],2), 
                                   't_commission'=>number_format($total['t_commission']+$e['commission'],2));
                $total['t_commission_percent'] = number_format($total['t_commission_percent']/$cant,2);
                return $total;
            }

            //CHANGE ELEMENTS FORMAT
            function format($elements)
            {
                $data = array();
                foreach ($elements as $e)
                    $data[] = (array)$e;
                return $data;
            }

            //FUNCTION SENDING EMAIL
            function sendEmailReport($data,$send,$date_report,$sqlMain,$sqlFrom)
            {
                $emailx = env('MAIL_REPORT_TO');    
                $namex = 'Administrator';
                if($send != 'admin')
                {
                    $emailx = $data[0]['email'];
                    $namex = $data[0]['name'];
                }
                else
                {
                    $elements = array();
                    foreach ($data as $d)
                        $elements[] = array('name'=>$d['name'], 'ticket_type'=>'', 'qty'=>$d['total']['t_ticket'], 'purchase_count'=>$d['total']['t_purchases'], 'gross_revenue'=>$d['total']['t_gross_revenue'], 'processing_fee'=>$d['total']['t_processing_fee'], 'commission_percent'=>$d['total']['t_commission_percent'], 'commission'=>$d['total']['t_commission'], 'net'=>$d['total']['t_net']);
                    $result = array('elements'=>$elements, 'total'=>calculate_total($elements), 'name'=>'Totals', 'email'=>' ', 'type'=>'venue', 'date'=>$date_report);
                    array_unshift($data,$result);
                }
                
                //MANIFEST SALES CUTOMIZED ACCORDING TO VENUES, SHOWS OR ADMIN
                /*$format = 'customized';
                $manifest_email = View::make('command.report_sales', compact('data','send','format'));
                $pdf =  PDF::load($manifest_email->render(), 'A4', 'landscape')->output();
                $pdf_path = '/tmp/ReportSales_'.$namex.'_'.date('Y-m-d').'_'.date('U').'.pdf';
                $fp_pdf = fopen($pdf_path, "w"); fwrite($fp_pdf, $pdf); fclose($fp_pdf); PDF::reinit();*/
               
                //SENDING EMAIL
                $email = new EmailSG(env('MAIL_REPORT_FROM'), $emailx ,'Daily Sales Report to '.$namex);
                //$email->cc(env('MAIL_REPORT_CC'));
                $email->category('Reports');
                $email->body('sales_report',array('date'=>date('m/d/Y',strtotime($date_report))));
                $email->template('a6e2bc2e-5852-4d14-b8ff-d63e5044fd14');
                //$email->attachment($pdf_path);
                if($send == 'admin')
                {
                    //SALES REFERRER PDF
                    /*$format = 'referrer';
                    $purchases = DB::select($sqlMain.$sqlFrom." GROUP BY referrer_url,p.show_time_id, p.ticket_type;");
                    $manifest_email = View::make('command.report_sales', compact('purchases', 'date_report','format'));
                    $pdf_ =  PDF::load($manifest_email->render(), 'A4', 'landscape')->output();
                    $pdf_referrer_ = '/tmp/ReportSales_Referrer_'.$namex.'_'.date('Y-m-d').'_'.date('U').'.pdf';
                    $fp_pdf_ = fopen($pdf_referrer_, "w"); fwrite($fp_pdf_, $pdf_); fclose($fp_pdf_); PDF::reinit();
                    $email->attachment($pdf_referrer_);*/

                    //MANIFES SALES CSV
                    $format = 'csv';
                    $purchases = DB::select($sqlMain.$sqlFrom." GROUP BY p.show_time_id, p.ticket_type;");
                    $manifest_csv = View::make('command.report_sales', compact('purchases' ,'date_report','format'));
                    $csv_path = '/tmp/ReportSales_'.$namex.'_'.date('Y-m-d').'_'.date('U').'.csv';
                    $fp_csv= fopen($csv_path, "w"); fwrite($fp_csv, $manifest_csv->render()); fclose($fp_csv);
                    $email->attachment($csv_path);
                }
                //$response = $email->send();
                if($send == 'admin')
                {
                    //unlink($pdf_referrer_);
                    unlink($csv_path);
                }
                //unlink($pdf_path);
            }

            //ARRAY TO MERGE BOTH REPORTS
            $resultArray = array();

            //CREATING REPORTS FOR VENUES
            $venues = (array)DB::select("SELECT v.id, v.name, v.accounting_email as email, v.daily_sales_emails AS v_daily_sales_emails ".$sqlFrom." GROUP BY v.name;");
            //create progress bar
            $progressbar = $this->output->createProgressBar(count($venues));
            foreach ($venues as $venue)
            {
                $elements = format((array)DB::select($sqlMain.$sqlFrom." WHERE v.id = ? GROUP BY s.name;",array($venue->id)));
                $result = array('elements'=>$elements, 'total'=>calculate_total($elements), 'name'=>$venue->name, 'email'=>$venue->email, 'type'=>'venue', 'date'=>$date_report);

                if($venue->email && $venue->v_daily_sales_emails==1)
                {
                    $dataSend = array();
                    $dataSend[] = $result;
                    sendEmailReport($dataSend,'regular',$date_report,$sqlMain,$sqlFrom);
                }
                $resultArray[] = $result;                
                //advance progress bar
                $progressbar->advance(); 
            }
            //finish progress bar
            $progressbar->finish();             

            //CREATING REPORTS FOR SHOWS
            $shows = DB::select($sqlMain.$sqlFrom." GROUP BY s.name;");
            //create progress bar
            $progressbar = $this->output->createProgressBar(count($shows));
            foreach ($shows as $show)
            {
                if($show->s_email && $show->s_daily_sales_emails==1)
                {
                    $elements = array((array)$show);
                    $result = array('elements'=>$elements, 'total'=>calculate_total($elements), 'name'=>$show->s_name, 'email'=>$show->s_email, 'type'=>'show', 'date'=>$date_report);
                    $dataSend = array();
                    $dataSend[] = $result;
                    sendEmailReport($dataSend,'regular',$date_report,$sqlMain,$sqlFrom);
                }
                //advance progress bar
                $progressbar->advance(); 
            }
            //finish progress bar
            $progressbar->finish(); 

            //MERGING REPORTS FOR ADMIN
            //create progress bar
            $progressbar = $this->output->createProgressBar(1);
            sendEmailReport($resultArray,'admin',$date_report,$sqlMain,$sqlFrom);
            //advance progress bar
            $progressbar->advance(); 
            //finish progress bar
            $progressbar->finish(); 
            return true;
        } catch (Exception $ex) {
            throw new Exception('Error creating and sending emails with ReportSales Command: '.$ex->getMessage());
        }
    }
}
