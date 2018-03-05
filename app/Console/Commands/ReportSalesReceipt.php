<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail\EmailSG;
use Barryvdh\DomPDF\Facade as PDF;

class ReportSalesReceipt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Report:sales_receipt {days=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used to push the receipts (daily or every X days) to us (default daily)';

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
            if($days == 1)
            {
                $email_to_send = 'v.daily_sales_emails';
                $show_time_condition = '';
                $part_name_report = 'Daily';
            }
            else 
            {
                $email_to_send = 'v.weekly_sales_emails';
                $show_time_condition = ' AND st.show_time >= NOW() ';
                $part_name_report = 'Weekly';
            } 
            $info = array();            
            $venues = DB::select("  SELECT DISTINCT v.id, v.name, v.weekly_email AS email, ".$email_to_send." AS email_report
                                    FROM purchases p 
                                    INNER JOIN show_times st ON st.id = p.show_time_id 
                                    INNER JOIN shows s ON s.id = st.show_id 
                                    INNER JOIN venues v ON v.id = s.venue_id
                                    WHERE DATE_FORMAT(p.created,'%Y-%m-%d') >= DATE_FORMAT(CURDATE() - INTERVAL ".$days." DAY,'%Y-%m-%d')".$show_time_condition);       
            
            //create progress bar
            $progressbar = $this->output->createProgressBar(count($venues));
            
            foreach ($venues as $venue) 
            {
                $info[$venue->id] = array('name' => $venue->name, 'email'=>$venue->email, 'email_report' => $venue->email_report, 'sales'=>array());
                $sales_per_show =DB::select("   SELECT DISTINCT s.name AS show_name,p.id,c.first_name,c.last_name,t.ticket_type,d.code,p.quantity AS qty,p.retail_price,pa.title,
                                                p.processing_fee, p.savings, p.price_paid, st.show_time,s.restrictions,st.time_alternative,t.ticket_type AS ticket_type_type,
                                                ROUND(p.price_paid/p.quantity,2) AS price_each, p.payment_type,v.ticket_info
                                                FROM purchases p 
                                                INNER JOIN discounts d ON p.discount_id = d.id
                                                INNER JOIN show_times st ON st.id = p.show_time_id 
                                                INNER JOIN shows s ON s.id = st.show_id
                                                INNER JOIN customers c ON c.id = p.customer_id 
                                                INNER JOIN venues v ON v.id = s.venue_id
                                                INNER JOIN tickets t ON t.id = p.ticket_id
                                                INNER JOIN packages pa ON t.package_id = pa.id
                                                WHERE DATE_FORMAT(p.created,'%Y-%m-%d') >= (CURDATE() - INTERVAL ".$days." DAY) AND v.id = ? ".$show_time_condition." 
                                                ORDER BY s.name, p.id",array($venue->id));
                 foreach ($sales_per_show as $p) 
                     $info[$venue->id]['sales'][] = (array)$p;
            }   
            
            //  PROCESS SENDING EMAILS ACCORDING TO CONDITIONS 
            function sendEmail($data,$to,$name,$part_name_report)
            {   
                //create file csv 
                $format = 'csv';
                $email_report = View::make('command.report_sales_receipt', compact('data','format'));            
                $csv_url = '/tmp/ReportSalesReceipt_'.date('Y-m-d').'_'.date('U').'.csv';
                $file_csv = fopen($csv_url, "w"); fwrite($file_csv, $email_report->render()); fclose($file_csv);
                $receipts = [$csv_url];
                
                //get all receipts                
                foreach ($data as $purchase) 
                {   
                    $format = 'pdf';
                    $pdfUrl = '/tmp/Receipt_'.preg_replace('/[^a-zA-Z0-9\_]/','_',$purchase['ticket_type']).'_'.date("m_d_Y_h_i_a",strtotime($purchase['show_time'])).'.pdf';
                    $customer_receipt = View::make('command.report_sales_receipt', compact('purchase','format'));  
                    PDF::loadHTML($customer_receipt->render())->setPaper('a4', 'portrait')->setWarnings(false)->save($pdfUrl);
                    $receipts[] = $pdfUrl;
                }     
                //send email           
                $email = new EmailSG(env('MAIL_REPORT_FROM'), $to ,$part_name_report.' Sales Report to '.$name);
                /*if(env('MAIL_REPORT_CC',null))
                    $email->cc(env('MAIL_REPORT_CC'));*/
                $email->category('Reports');
                $email->text($part_name_report.' Sales Report Receipt. Created at '.date('m/d/Y g:ia'));
                $email->attachment($receipts);
                $email->send();          
                
                //delete all files
                foreach ($receipts as $r) 
                    unlink($r); 
            } 
            
            //sending proccess for all venues
            foreach ($info as $type => $venue) 
                if($venue['email_report'] == 1 && $venue['email']) 
                {
                    sendEmail($venue['sales'],$venue['email'],$venue['name'],$part_name_report);                     
                    //advance progress bar
                    $progressbar->advance();
                }                    
                
            //finish progress bar
            $progressbar->finish();  
            
        } catch (Exception $ex) {
            throw new Exception('Error sending emails with ReportSalesReceipt Command: '.$ex->getMessage());
        }
    }
}
