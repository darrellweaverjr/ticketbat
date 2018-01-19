<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use App\Mail\EmailSG;
use Barryvdh\DomPDF\Facade as PDF;

/**
 * Manifest class
 *
 * @author ivan
 */
class Manifest extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'manifest_emails';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the show_time record associated with the manifest.
     */
    public function show_time()
    {
        return $this->belongsTo('App\Http\Models\ShowTime','show_time_id');
    }
    //PERSONALIZED
    /**
     * Send manifest to person.
     */
    public function send($mailer=null, $subject=null)
    {
        //data
        $data = json_decode($this->email,true);
       
        if(!empty($mailer))
            $data['emails'] = $mailer;
        if(empty($subject))
            $subject = 'Re-send '.$data['type'].' Manifest for ';
        
        //create pdf  
        $format = 'pdf';
        $pdf_path = '/tmp/ReportManifest_'.$data['type'].'_'.$data['id'].'_'.date('U').'.pdf';
        $manifest_pdf = View::make('command.report_manifest', compact('data','format'));
        PDF::loadHTML($manifest_pdf->render())->setPaper('a4', 'landscape')->setWarnings(false)->save($pdf_path);

        //create csv
        $format = 'csv';
        $manifest_csv = View::make('command.report_manifest', compact('data','format'));
        $csv_path = '/tmp/ReportManifest_'.$data['type'].'_'.$data['id'].'_'.date('U').'.csv';
        $fp_csv= fopen($csv_path, "w"); fwrite($fp_csv, $manifest_csv->render()); fclose($fp_csv);

        //sending email
        $email = new EmailSG(env('MAIL_REPORT_FROM'),$data['emails'],$subject.$data['name']);
        $email->body('manifest',$data);
        $email->category('Manifests');
        $email->attachment([$csv_path,$pdf_path]);
        $email->template('89890051-c3ba-4d94-a2ff-ac237f8295ba');

        //if the email was sent successfully delete files
        if($email->send())
        {
            unlink($csv_path);
            unlink($pdf_path);
            return true;
        }   
        return false;
    }
}
