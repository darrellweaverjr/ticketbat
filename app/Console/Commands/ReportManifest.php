<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail\EmailSG;
use Barryvdh\DomPDF\Facade as PDF;
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
        try {
            $manifests = array('Preliminary','Primary','LastMinute'); 
            
            //create progress bar
            $progressbar = $this->output->createProgressBar(count($manifests));
            
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
                $dates = DB::select("SELECT st.id, st.show_time, s.emails, s.name, now() as date_now, s.manifest_emails AS s_manifest_emails, 
                                            COUNT( IF(p.status='Active',p.id,NULL) ) AS num_purchases,
                                            SUM( IF(p.status='Active',p.quantity,0) ) AS num_people
                                            ".$anotherSelect."
                                    FROM show_times st
                                    INNER JOIN shows s ON s.id = st.show_id
                                    INNER JOIN purchases p ON p.show_time_id = st.id ".$datesCondition);
                foreach($dates as $date)
                {
                    //get purchases    
                    $purchases = DB::select("SELECT s.name AS event_name, st.show_time, CONCAT(c.last_name, ', ', c.first_name) AS customer_name, l.address, c.phone, c.email,
                        p.quantity, d.code, p.ticket_type as description, p.price_paid as amount, p.customer_id, p.id, p.created, IF(p.status='Active','Active','Canceled') AS p_status
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

                    //format all data
                    $date->type = $typeName;
                    $data = (array)$date;
                    foreach ($purchases as $n => $p)
                    {
                        foreach ($gifts as $g)
                        {
                            $p->gifts = [];
                            if (($p->id == $g->purchases_id) && ($p->customer_id != $g->customers_id))
                                $p->gifts[]= $g->customer_name;
                            $p->gifts = implode(", ",$p->gifts);
                        }
                        $data['purchases'][] = (array)$p;
                    }
                    
                    //create record to save to DB
                    $manifest = new Manifest;
                    $manifest->show_time_id = $data['id'];
                    $manifest->manifest_type = $data['type'];
                    $manifest->num_purchases = $data['num_purchases'];
                    $manifest->num_people = $data['num_people'];
                    $manifest->recipients = $data['emails'];
                    $manifest->email = json_encode($data);
                    
                    //if it is saved and is config to send email then send it
                    if($manifest->save() && $data['s_manifest_emails'] == 1 && $data['emails'])
                        $manifest->send ($data['emails'], $emailSubject);                
                }
                //advance progress bar
                $progressbar->advance();                
            } 
            //finish progress bar
            $progressbar->finish(); 
            return true;
        } catch (Exception $ex) {
            throw new Exception('Error creating, saving and sending emails with ReportManifest Command: '.$ex->getMessage());
        }        
    }
}
