<?php

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail\EmailSG;
use App\Http\Models\Consignment;

/**
 * Manage ReportSales options for the commands
 *
 * @author ivan
 */
class ReportConsignmentController extends Controller{
    
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }    
    /*
     * get sales report pdf
     */
    public function init()
    {
        try {
            //get all consignments with cuttoff hours for today, that the report is not sent
            $consignments = DB::table('consignments')
                                ->join('users', 'users.id', '=' ,'consignments.seller_id')
                                ->join('show_times', 'show_times.id', '=' ,'consignments.show_time_id')
                                ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                ->leftJoin('seats', 'seats.consignment_id', '=' ,'consignments.id')
                                ->leftJoin('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->leftJoin('purchases', 'purchases.id', '=' ,'seats.purchase_id')
                                ->select(DB::raw('consignments.id,consignments.status,shows.emails, show_times.show_time,
                                        shows.name AS show_name, CONCAT(users.first_name," ",users.last_name) AS seller,
                                        (CASE WHEN (consignments.created = purchases.created) THEN 1 ELSE 0 END) as purchase'))
                                ->where(function ($query) {
                                    return $query->whereNull('seats.status')
                                                 ->orWhere('seats.status','<>','Voided');
                                })
                                ->where('consignments.report','!=',1)->where('consignments.status','<>','Voided')
                                ->whereDate('show_times.show_time', '=', date('Y-m-d'))
                                ->where(DB::raw('HOUR(show_times.show_time) - shows.cutoff_hours'),'<=',date('H'))
                                ->groupBy('consignments.id')    
                                ->orderBy('show_times.show_time')
                                ->get();  
            //create report for each consignment and send it
            foreach ($consignments as $c)
            {
                if(!empty($c->emails))
                {
                    //get all seats for the consignment
                    $seats = DB::table('seats')
                                    ->join('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                    ->select(DB::raw('seats.id, tickets.ticket_type, seats.seat, 
                                                      (CASE WHEN (seats.status = "Created") THEN "Cancelled" ELSE seats.status END) as status'))
                                    ->where('seats.consignment_id','=',$c->id)
                                    ->orderBy('tickets.ticket_type')->orderByRaw('CAST(seats.seat AS UNSIGNED)')
                                    ->distinct()->get()->toArray();
                    $c->seats = $seats;
                    //create files
                    $files = $this->create_files($c);
                    //send the email
                    if(!empty($files))
                        $sent = $this->send_email ($files, $c);
                }
            }
            if(isset($sent))
                return $sent;
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }      
    
    /*
     * calculate create_files
     */
    public function create_files($consignment)
    {
        try {
            $files = [];
            
            $format = 'csv';
            $view = View::make('command.report_consignments', compact('consignment','format'));                
            $url = '/tmp/ReportConsignment_'.$consignment->id.'_'.date('Y-m-d').'_'.date('U').'.csv';
            $file= fopen($url, "w"); fwrite($file, $view->render()); fclose($file);
            $files[] = $file;
            
            //return files
            return $files;
        } catch (Exception $ex) {
            
        } finally {
            return $files;
        }        
    }       
    /*
     * calculate create_files
     */
    public function send_email($files,$consignment)
    {
        try {   
            //sending email
            $email = new EmailSG(env('MAIL_REPORT_FROM'),$consignment->emails,'Consignment Report #'.$consignment->id.' - '.$consignment->show_name.' @ '.date('m/d/Y g:ia',strtotime($consignment->show_time)));
            //if(env('MAIL_REPORT_CC',null))
                //$email->cc(env('MAIL_REPORT_CC'));
            $email->text('Report Consignment sent at: '.date('m/d/Y g:ia'));
            $email->category('Consignments');
            $email->attachment($files);
            if($email->send())
                Consignment::where('id','=',$consignment->id)->update(['report'=>1]);
            foreach ($files as $f)
                if(file_exists($f))
                    unlink($f);
            return $sent;  
        } catch (Exception $ex) {
            return false;
        }
    }      
    
}
