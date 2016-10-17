<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Mail\EmailSG;

/**
 * Manage Contacts
 *
 * @author ivan
 */
class ContactController extends Controller{
    
    public function index()
    {
              
    }
    
    public static function reportManifest()
    {
        $manifests = array('Preliminary','Primary','LastMinute'); 
        $model = new BaseModel();
        //send reports for each type of manifest
        foreach ($manifests as $type) 
        {
            //init variables    
            switch ($type) 
            {
                case 'Preliminary':
                    $anotherSelect = "";
                    $datesCondition = " WHERE st.show_time >= CURDATE() AND DATE_SUB(st.show_time, INTERVAL s.prelim_hours HOUR) < NOW() AND st.id NOT IN (SELECT show_time_id FROM manifest_emails WHERE manifest_type = 'Preliminary') GROUP BY st.id LIMIT 1";
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
            $sql = "Select st.id, st.show_time, s.emails, s.name, count(p.id) as num_purchases, sum(p.quantity) as num_people, now() as date_now, s.manifest_emails AS s_manifest_emails ".$anotherSelect." 
            From show_times st
            inner join shows s on s.id = st.show_id
            inner join purchases p on p.show_time_id = st.id and p.status = 'Active' ".$datesCondition;

	    $dates = DB::select($sql);

            foreach($dates as $date)
            {
                //get purchases    
                $purchases = DB::select("Select s.name as event_name, st.show_time, concat(c.last_name, ', ', c.first_name) as customer_name, l.address, c.phone, c.email,
                    p.quantity, d.code, p.ticket_type as description, p.price_paid as amount, p.customer_id, p.id, p.created
                    From purchases p
                    inner join show_times st on st.id = p.show_time_id
                    inner join shows s on s.id = st.show_id
                    inner join transactions t on t.id = p.transaction_id
                    inner join customers c on c.id = t.customer_id
                    inner join locations l on c.location_id = l.id
                    inner join discounts d on d.id = p.discount_id
                    Where st.id = ? order by c.last_name, c.first_name", array($date->id));

                //get gifts                   
                $gifts = DB::select("SELECT CONCAT(c.last_name, ', ', c.first_name) AS customer_name, tn.purchases_id, tn.customers_id 
                    FROM ticket_number tn 
                    INNER JOIN purchases p ON tn.purchases_id = p.id 
                    INNER JOIN show_times st ON st.id = p.show_time_id 
                    INNER JOIN customers c ON c.id = tn.customers_id
                    WHERE st.id = ?", array($date->id));
                //format email    
                $manifest_email = View::make('emails.manifest', compact('purchases', 'date', 'gifts')); 
                //if allow to send report create attachments
                if($date->s_manifest_emails == 1 && $date->emails)
                {
                    //create pdf    
                    /*$pdf =  PDF::load($manifest_email->render(), 'A4', 'landscape')->output();
                    $pdf_path = "/tmp/" . $date->id . "_".$typeName.".pdf";
                    $fp_pdf = fopen($pdf_path, "w");
                    fwrite($fp_pdf, $pdf);
                    fclose($fp_pdf);
                    PDF::reinit();*/
                    //create csv
                    $manifest_csv = View::make('emails.manifest_csv', compact('purchases', 'date', 'gifts'));
                    $csv_path = "/tmp/" . $date->id . "_".$typeName.".csv";
                    $fp_csv= fopen($csv_path, "w");
                    fwrite($fp_csv, $manifest_csv->render());
                    fclose($fp_csv);
                    
                    $date->csv_path = $csv_path;
                    $date->pdf_path = $pdf_path;
                }            
                //create obj to save to DB
                $rec = array('manifest_emails' => array(
                    'show_time_id' => $date->id,
                    'manifest_type' => $typeName,
                    'num_purchases' => $date->num_purchases,
                    'num_people' => $date->num_people,
                    'recipients' => $date->emails,
                    'email' => $manifest_email->render()
                )); 

                if($model->validate($rec) && $model->insert($rec))
                {
                    //if allow to send report 
                    if($date->s_manifest_emails == 1 && $date->emails)
                    {
                        //send email    
                        $date->type = $typeName;
                        $temp = (array) $date;
                        $email = new EmailSG(Config::get('mail.from_reports'),$temp['emails'],$emailSubject.$temp['name']);
                        //$email->cc(Config::get('mail.admin_email'));
                        $email->body('manifest',$temp);
                        $email->category('Manifests');
                        $email->attachment($temp['pdf_path']);
                        $email->attachment($temp['csv_path']);
                        $email->template('89890051-c3ba-4d94-a2ff-ac237f8295ba');
                        $response = $email->send();
                        //delete attachments
                        unlink($pdf_path);
                        unlink($csv_path);
                    }  
                }else return false;  
            }
        } 
        return true;
    }
    
}
