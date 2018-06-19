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
    public function send($mailer=null, $subject=null, $empty=false)
    {
        if(!$empty)
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
            $email->category('Manifests');        
            $msg = '<center>Attached is the CSV and PDF '.$data['type'].' Manifest for '.$data['name'].' on '.date('m/d/Y g:ia').'<br><h1>:)</h1></center>';
            $email->body('custom',['body'=>$msg]);
            $email->template('46388c48-5397-440d-8f67-48f82db301f7');
            $email->attachment([$csv_path,$pdf_path]);        

            //if the email was sent successfully delete files
            $response = $email->send();
            unlink($csv_path);
            unlink($pdf_path);
            return $response;
        }
        else
        {
            $email = new EmailSG(env('MAIL_REPORT_FROM'),$mailer,$subject);
            $email->category('Manifests');            
            $msg = '<center>There are no purchases for this show when it started.<br>Reported on '.date('m/d/Y g:ia').'<br><h1>:(</h1></center>';
            $email->body('custom',['body'=>$msg]);
            $email->template('46388c48-5397-440d-8f67-48f82db301f7');
            return $email->send();
        }
    }
}
