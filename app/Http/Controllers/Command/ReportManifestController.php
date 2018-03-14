<?php

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail\EmailSG;
use App\Http\Models\Manifest;

/**
 * Manage ReportSales options for the commands
 *
 * @author ivan
 */
class ReportManifestController extends Controller{
    
    protected $manifests = ['Preliminary','Primary','LastMinute']; 
    
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
            //init
            $current = date('Y-m-d');
            //send reports for each type of manifest
            foreach ($this->manifests as $type) 
            {
                //create report
                $info = $this->create_report($type,$current);   
                if(!empty($info['dates']))
                {
                    //create and send report for each date
                    foreach($info['dates'] as $date)
                    {
                        $date = $this->create_data($date);
                        $manifest = $this->save_data($date);
                        $files = $this->create_files($consignment);
                        $sent = $this->send_email($files, $consignment);             
                    }
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
     * create data to storage on DB
     */
    public function create_report($type,$current)
    {
        try {
            $info = ['dates'=>[],'type'=>'','subject'=>''];
            //init variables    
            switch ($type) 
            {
                case 'Preliminary':
                    $dates = DB::table('show_times')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->select(DB::raw('show_times.id, show_times.show_time, 
                                            shows.emails, shows.name, shows.manifest_emails,
                                            COUNT(purchases.id) AS transactions,
                                            SUM(purchases.quantity) AS tickets'))
                            ->where('purchases.status','=','Active')
                            ->whereDate('show_times.show_time','>',$current)
                            ->whereNotExists(function ($query) {
                                $query->select(DB::raw(1))
                                      ->from('manifest_emails')
                                      ->whereRaw('show_times.id = manifest_emails.show_time_id')
                                      ->where('manifest_type','=','Preliminary');
                            })
                            ->whereRaw('DATE_SUB(show_times.show_time, INTERVAL shows.prelim_hours HOUR) < NOW()')
                            ->groupBy('show_times.id')
                            ->distinct()->take(1)->get()->toArray();
                    $info = ['dates'=>$dates,'type'=>'Preliminary','subject'=>'Preliminary Manifest for '];
                    break;
                case 'Primary':
                    $dates = DB::table('show_times')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->select(DB::raw('show_times.id, show_times.show_time, 
                                            shows.emails, shows.name, shows.manifest_emails,
                                            COUNT(purchases.id) AS transactions,
                                            SUM(purchases.quantity) AS tickets'))
                            ->where('purchases.status','=','Active')
                            ->whereDate('show_times.show_time','>',$current)
                            ->whereNotExists(function ($query) {
                                $query->select(DB::raw(1))
                                      ->from('manifest_emails')
                                      ->whereRaw('show_times.id = manifest_emails.show_time_id')
                                      ->where('manifest_type','=','Primary');
                            })
                            ->whereRaw('DATE_SUB(show_times.show_time, INTERVAL shows.prelim_hours HOUR) < NOW()')
                            ->groupBy('show_times.id')
                            ->distinct()->take(1)->get()->toArray();
                    $info = ['dates'=>$dates,'type'=>'Primary','subject'=>'Primary Manifest for '];
                    break;
                case 'LastMinute':
                    $dates = DB::table('show_times')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('manifest_emails', 'manifest_emails.show_time_id', '=' ,'show_times.id')
                            ->join('manifest_emails', function ($join) {
                                $join->on('manifest_emails.show_time_id', '=' ,'show_times.id')
                                     ->on('manifest_emails.manifest_type', '=', 'Primary');
                            })
                            ->select(DB::raw('show_times.id, show_times.show_time, 
                                            shows.emails, shows.name, shows.manifest_emails,
                                            COUNT(purchases.id) AS transactions,
                                            SUM(purchases.quantity) AS tickets'))
                            ->where('purchases.status','=','Active')
                            ->havingRaw('NOW() BETWEEN DATE_ADD(DATE_SUB(show_times.show_time, INTERVAL shows.cutoff_hours HOUR), INTERVAL 15 MINUTE) AND show_times.show_time')
                            ->havingRaw('COUNT(purchases.id) != manifest_emails.num_purchases')
                            ->groupBy('show_times.id')
                            ->distinct()->get()->toArray();
                    $info = ['dates'=>$dates,'type'=>'Primary','subject'=>'Last Minute Manifest for '];
                    break;    
                default:break;
            }  
            //return files
            return $info;
        } catch (Exception $ex) {
            
        } finally {
            return $info;
        }        
    }  
    
    /*
     * create data to storage on DB
     */
    public function create_data($data)
    {
        try {
            //get purchases   
            $purchases = DB::table('purchases')
                            ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                            ->join('locations', 'locations.id', '=' ,'customers.location_id')
                            ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                            ->select(DB::raw('show_times.id, show_times.show_time, 
                                            shows.emails, shows.name, shows.manifest_emails,
                                            COUNT(purchases.id) AS transactions,
                                            SUM(purchases.quantity) AS tickets'))
                            ->where('purchases.status','=','Active')
                            ->havingRaw('NOW() BETWEEN DATE_ADD(DATE_SUB(show_times.show_time, INTERVAL shows.cutoff_hours HOUR), INTERVAL 15 MINUTE) AND show_times.show_time')
                            ->havingRaw('COUNT(purchases.id) != manifest_emails.num_purchases')
                            ->groupBy('show_times.id')
                            ->distinct()->get()->toArray();
            
            
            
            
            
            $purchases = DB::select("SELECT s.name AS event_name, st.show_time, CONCAT(c.last_name, ', ', c.first_name) AS customer_name, l.address, c.phone, c.email,
                p.quantity, d.code, p.ticket_type as description, p.price_paid as amount, p.savings, p.customer_id, p.id, p.created, IF(p.status='Active','Active','Canceled') AS p_status
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
        } catch (Exception $ex) {
            
        } finally {
            return $files;
        }        
    }     
    
    /*
     * save data into DB
     */
    public function save_data($data)
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
     * send email
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
