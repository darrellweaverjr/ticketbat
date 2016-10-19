<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail\EmailSG;
use App\Http\Models\Manifest;

class ReportManifest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Report:manifest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for sending information to the BO when ticket sales have been shut down.';

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
        $manifests = array('Preliminary','Primary','LastMinute'); 
        //send reports for each type of manifest
        foreach ($manifests as $type) 
        {
            //init variables    
            switch ($type) 
            {
                case 'Preliminary':
                    $anotherSelect = "";
                    $datesCondition = " WHERE st.show_time >= CURDATE() AND DATE_SUB(st.show_time, INTERVAL s.prelim_hours HOUR) < NOW() AND st.id NOT IN (SELECT show_time_id FROM manifest_emails WHERE manifest_type = 'Preliminary')  GROUP BY st.id LIMIT 1";
                    $typeName = "Preliminary";
                    $emailSubject = "Preliminary Manifest for ";                    
                    break;
                case 'Primary':
                    $anotherSelect = "";
                    $datesCondition = " WHERE st.show_time >= CURDATE() AND DATE_SUB(st.show_time, INTERVAL s.cutoff_hours HOUR) < NOW() AND st.id NOT IN (SELECT show_time_id FROM manifest_emails WHERE manifest_type = 'Primary') GROUP BY st.id LIMIT 1";
                    $typeName = "Primary";
                    $emailSubject = "Primary Manifest for ";                    
                    break;
                case 'LastMinute':
                    $anotherSelect = " ,s.cutoff_hours, m.num_purchases";
                    $datesCondition = " INNER JOIN manifest_emails m ON m.show_time_id = st.show_time AND m.manifest_type = 'Primary' GROUP BY st.id HAVING (NOW() BETWEEN DATE_ADD(DATE_SUB(st.show_time, INTERVAL s.cutoff_hours HOUR), INTERVAL 15 MINUTE) AND st.show_time) AND COUNT(p.id) !=  m.num_purchases";
                    $typeName = "Primary";
                    $emailSubject = "Last Minute Manifest for ";                    
                    break;    
                default:break;
            }            
            //get dates
	    $dates = DB::select("SELECT st.id, st.show_time, s.emails, s.name, count(p.id) as num_purchases, sum(p.quantity) as num_people, now() as date_now, s.manifest_emails AS s_manifest_emails ".$anotherSelect." 
                                FROM show_times st
                                INNER JOIN shows s ON s.id = st.show_id
                                INNER JOIN purchases p ON p.show_time_id = st.id AND p.status = 'Active' ".$datesCondition);
            foreach($dates as $date)
            {
                //get purchases    
                $purchases = DB::select("SELECT s.name AS event_name, st.show_time, CONCAT(c.last_name, ', ', c.first_name) AS customer_name, l.address, c.phone, c.email,
                    p.quantity, d.code, p.ticket_type as description, p.price_paid as amount, p.customer_id, p.id, p.created
                    FROM purchases p
                    INNER JOIN show_times st ON st.id = p.show_time_id
                    INNER JOIN shows s ON s.id = st.show_id
                    INNER JOIN transactions t ON t.id = p.transaction_id
                    INNER JOIN customers c ON c.id = t.customer_id
                    INNER JOIN locations l ON c.location_id = l.id
                    INNER JOIN discounts d ON d.id = p.discount_id
                    WHERE st.id = ? ORDER BY c.last_name, c.first_name", array($date->id));

                //get gifts                   
                $gifts = DB::select("SELECT CONCAT(c.last_name, ', ', c.first_name) AS customer_name, tn.purchases_id, tn.customers_id 
                    FROM ticket_number tn 
                    INNER JOIN purchases p ON tn.purchases_id = p.id 
                    INNER JOIN show_times st ON st.id = p.show_time_id 
                    INNER JOIN customers c ON c.id = tn.customers_id
                    WHERE st.id = ?", array($date->id));
                           
                //create record to save to DB
                $format = 'db';
                $manifest_email = View::make('command.report_manifest', compact('purchases', 'date', 'gifts','format')); 
                $manifest = new Manifest;
                $manifest->show_time_id = $date->id;
                $manifest->manifest_type = $typeName;
                $manifest->num_purchases = $date->num_purchases;
                $manifest->num_people = $date->num_people;
                $manifest->recipients = $date->emails;
                $manifest->email = $manifest_email->render();
                
                //if it is saved and is config to send email then send it
                if($manifest->save() && $date->s_manifest_emails == 1 && $date->emails)
                {
                    //create csv
                    $format = 'csv';
                    $manifest_csv = View::make('command.report_manifest', compact('purchases', 'date', 'gifts','format'));
                    $csv_path = '/tmp/ReportManifest_'.$typeName.'_'.$date->id.'_'.date('U').'.csv';
                    $fp_csv= fopen($csv_path, "w"); fwrite($fp_csv, $manifest_csv->render()); fclose($fp_csv);
                    $date->attachments = $csv_path;

                    //create pdf    
                    /*$pdf =  PDF::load($manifest_email->render(), 'A4', 'landscape')->output();
                    $pdf_path = "/tmp/" . $date->id . "_".$typeName.".pdf";
                    $fp_pdf = fopen($pdf_path, "w");
                    fwrite($fp_pdf, $pdf);
                    fclose($fp_pdf);
                    PDF::reinit();*/
                    
                    $date->type = $typeName;             
                    $email = new EmailSG(env('MAIL_REPORT_FROM'),$date->emails,$emailSubject.$date->name);
                    $email->body('manifest',(array) $date);
                    $email->category('Manifests');
                    $email->attachment($date->attachments);
                    $email->template('89890051-c3ba-4d94-a2ff-ac237f8295ba');
                    $sent= $email->send();
                    
                    //if the email was sent successfully delete files
                    if($sent)
                    {
                        unlink($csv_path);
                    }                    
                }                     
            }
        } 
        return true;
    }
}
