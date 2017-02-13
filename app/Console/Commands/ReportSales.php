<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail\EmailSG;
use Barryvdh\DomPDF\Facade as PDF;

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
                        SUM(p.processing_fee) AS processing_fee, SUM(p.savings) AS savings, SUM(p.price_paid) AS gross_revenue, 
                        SUM(p.price_paid) AS total_paid, ROUND(SUM(p.retail_price)-SUM(p.commission_percent),2) AS due_to_show, ROUND(SUM(p.commission_percent),2) AS commission, 
                        (CASE WHEN (p.ticket_type = 'Consignment') THEN p.ticket_type ELSE p.payment_type END) AS method,
                        SUBSTRING_INDEX(SUBSTRING_INDEX(p.referrer_url, '://', -1),'/', 1) AS referral_url,
                        SUBSTRING_INDEX(p.referrer_url, '://', -1) AS url, SUM(p.price_paid)-SUM(p.commission_percent)-SUM(p.processing_fee) AS net ";
            $sqlTypes = "SELECT (CASE WHEN p.ticket_type = 'Consignment' THEN p.ticket_type ELSE p.payment_type END) AS payment_type, sum(p.quantity) AS qty, COUNT(*) AS purchase_count, SUM(p.price_paid) AS gross_revenue, SUM(p.processing_fee) AS processing_fee,
                        ROUND(SUM(p.commission_percent),2) AS commission, SUM(p.price_paid)-SUM(p.commission_percent)-SUM(p.processing_fee) AS net ";

            $sqlFrom =" FROM purchases p
                        LEFT JOIN show_times st ON st.id = p.show_time_id
                        LEFT JOIN shows s ON s.id = st.show_id
                        INNER JOIN venues v ON v.id = s.venue_id
                        LEFT JOIN tickets t ON p.ticket_id = t.id 
                        WHERE date(p.created) ".$bound." (CURRENT_DATE - INTERVAL ".$days." DAY) AND p.status = 'Active' ";

            //FUNCTION CALCULATE SUBTOTALS
            function calculate_total($elements)
            {
                $total = array( 't_ticket'=>array_sum(array_column($elements,'qty')),
                                't_purchases'=>array_sum(array_column($elements,'purchase_count')),
                                't_gross_revenue'=>array_sum(array_column($elements,'gross_revenue')),
                                't_processing_fee'=>array_sum(array_column($elements,'processing_fee')),
                                't_net'=>array_sum(array_column($elements,'net')),
                                't_commission'=>array_sum(array_column($elements,'commission')));
                return $total;
            }
            //FUNCTION CALCULATE SUBTOTALS
            function calculate_types($elements)
            {
                $types = []; 
                //get values into array by type
                foreach ($elements as $t)
                {
                    if($t->payment_type!='Subtotal')
                    {
                        $types[$t->payment_type][] = (object)['payment_type'=>$t->payment_type,'qty'=>$t->qty,'purchase_count'=>$t->purchase_count,'gross_revenue'=>$t->gross_revenue,'processing_fee'=>$t->processing_fee,'commission'=>$t->commission, 'net'=>$t->net];
                        if($t->payment_type!='Consignment')
                            $types['Subtotal'][] = (object)['payment_type'=>'Subtotal','qty'=>$t->qty,'purchase_count'=>$t->purchase_count,'gross_revenue'=>$t->gross_revenue,'processing_fee'=>$t->processing_fee,'commission'=>$t->commission, 'net'=>$t->net];
                    }
                }
                //sum values of each sub array
                foreach ($types as $k => $t)
                {
                    $c = calculate_total($t); 
                    $types[$k] = (object)['payment_type'=>$k,'qty'=>$c['t_ticket'],'purchase_count'=>$c['t_purchases'],'gross_revenue'=>$c['t_gross_revenue'],'processing_fee'=>$c['t_processing_fee'],'commission'=>$c['t_commission'], 'net'=>$c['t_net']];
                }
                //move at last the subtotal and consignments
                if(isset($types['Subtotal']))
                {
                    $e = $types['Subtotal'];
                    unset($types['Subtotal']);
                    $types['Subtotal'] = $e;
                }
                if(isset($types['Consignment']))
                {
                    $e = $types['Consignment'];
                    unset($types['Consignment']);
                    $types['Consignment'] = $e;
                }
                return $types;
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
                    $elements = []; $types = []; 
                    foreach ($data as $d)
                    {
                        $elements[] = (object)['name'=>$d['name'], 'ticket_type'=>'', 'qty'=>$d['total']['t_ticket'], 'purchase_count'=>$d['total']['t_purchases'], 'gross_revenue'=>$d['total']['t_gross_revenue'], 'processing_fee'=>$d['total']['t_processing_fee'], 'commission'=>$d['total']['t_commission'], 'net'=>$d['total']['t_net']];
                        foreach ($d['types'] as $t)
                            $types[] = (object)['payment_type'=>$t->payment_type,'qty'=>$t->qty,'purchase_count'=>$t->purchase_count,'gross_revenue'=>$t->gross_revenue,'processing_fee'=>$t->processing_fee,'commission'=>$t->commission, 'net'=>$t->net];
                    } 
                    //result array
                    $result = array('elements'=>$elements,'total'=>calculate_total($elements),'types'=>calculate_types($types),'name'=>'Totals','email'=>' ','type'=>'venue','date'=>$date_report);
                    array_unshift($data,$result);
                }
                
                //MANIFEST SALES CUTOMIZED ACCORDING TO VENUES, SHOWS OR ADMIN                
                $format = 'customized';
                $pdf_path = '/tmp/ReportSales_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$namex).'_'.date('Y-m-d').'_'.date('U').'.pdf';
                $manifest_email = View::make('command.report_sales', compact('data','send','format'));                
                PDF::loadHTML($manifest_email->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdf_path);

                //SENDING EMAIL
                $email = new EmailSG(env('MAIL_REPORT_FROM'), $emailx ,'Daily Sales Report to '.$namex);
                $email->cc(env('MAIL_REPORT_CC'));
                $email->category('Reports');
                $email->body('sales_report',array('date'=>date('m/d/Y',strtotime($date_report))));
                $email->template('a6e2bc2e-5852-4d14-b8ff-d63e5044fd14');
                $email->attachment($pdf_path);
                if($send == 'admin')
                {
                    //add resume of types on the email body
                    $format = 'types';
                    $email_body = View::make('command.report_sales', compact('data','send','format'));          
                    $email->view($email_body);
                    
                    //SALES REFERRER PDF
                    $format = 'referrer';
                    $pdf_referrer_ = '/tmp/ReportSales_Referrer_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$namex).'_'.date('Y-m-d').'_'.date('U').'.pdf';
                    $purchases = DB::select($sqlMain.$sqlFrom." GROUP BY referral_url,p.show_time_id, p.ticket_type;");
                    $manifest_email = View::make('command.report_sales', compact('purchases', 'date_report','format'));
                    PDF::loadHTML($manifest_email->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdf_referrer_);

                    //MANIFES SALES CSV
                    $format = 'csv';
                    $purchases = DB::select($sqlMain.$sqlFrom." GROUP BY p.show_time_id, p.ticket_type;");
                    $manifest_csv = View::make('command.report_sales', compact('purchases' ,'date_report','format'));
                    $csv_path = '/tmp/ReportSales_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$namex).'_'.date('Y-m-d').'_'.date('U').'.csv';
                    $fp_csv= fopen($csv_path, "w"); fwrite($fp_csv, $manifest_csv->render()); fclose($fp_csv);
                    $email->attachment([$csv_path,$pdf_referrer_]);
                }
                
                if($email->send())
                {
                    if($send == 'admin')
                    {
                        unlink($pdf_referrer_);
                        unlink($csv_path);
                    }
                    unlink($pdf_path);
                }                
            }

            //ARRAY TO MERGE BOTH REPORTS
            $resultArray = array();

            //CREATING REPORTS FOR VENUES
            $venues = (array)DB::select("SELECT v.id, v.name, v.accounting_email as email, v.daily_sales_emails AS v_daily_sales_emails ".$sqlFrom." GROUP BY v.name");
            //create progress bar
            $progressbar = $this->output->createProgressBar(count($venues));
            foreach ($venues as $venue)
            {   
                $elements = DB::select($sqlMain.$sqlFrom." AND v.id = ? GROUP BY s.name;",array($venue->id));      
                $types = DB::select($sqlTypes.$sqlFrom." AND v.id = ? GROUP BY payment_type;",array($venue->id));       
                $result = array('elements'=>$elements,'types'=>calculate_types($types),'total'=>calculate_total($elements),'name'=>$venue->name, 'email'=>$venue->email, 'type'=>'venue', 'date'=>$date_report);
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
