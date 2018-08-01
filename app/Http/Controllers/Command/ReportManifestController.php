<?php

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Manifest;
use Carbon\Carbon;

/**
 * Manage ReportManifest options for the commands
 *
 * @author ivan
 */
class ReportManifestController extends Controller{

    protected $manifests = ['Preliminary','Primary','LastMinute','NoSales'];
    protected $date_manifest;
    protected $previous_date;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($date=null)
    {
        if((!empty($date) && strtotime($date)))
        {
            $this->date_manifest = date('Y-m-d',strtotime($date));
            $this->previous_date = true;
        }
        else
        {
            $this->date_manifest = date('Y-m-d H:i:s',strtotime(Carbon::now()));
            $this->previous_date = false;
        }
    }

    /*
     * get sales report pdf
     */
    public function init($log=false)
    {
        $logs = '';
        try {
            //send reports for each type of manifest
            foreach ($this->manifests as $type)
            {
                //create report
                $logs .= '<b><i>*  Manifest "'.$type.'" has ';
                $info = $this->create_report($type);
                if(!empty($info['dates']))
                {
                    $logs .= count($info['dates']).' result(s) for "'.date('m/d/Y',strtotime($this->date_manifest)).'":</i></b><br>';
                    //create and send report for each date
                    foreach($info['dates'] as $k=>$date)
                    {
                        //if the show has sales
                        if($date->num_purchases>0)
                        {
                            $logs .= '* * '.($k+1).'. <small>"'.$date->name.'" on "'.date('m/d/Y g:ia',strtotime($date->show_time)).'"<br>* * * => ';
                            $date->type = $type;
                            $data = $this->create_data($date);
                            $manifest = $this->save_data($data);
                            if($manifest)
                            {
                                $logs .= ' Saved OK,';
                                if($data['s_manifest_emails']>0 && !empty($data['emails']))
                                {
                                    $logs .= ' Sending to "'.$data['emails'].'",';
                                    $sent = $manifest->send($data['emails'], $info['subject']);
                                    //storage if email was sent successfully
                                    $data['sent'] = ($sent)? 1 : 0;
                                    $manifest->email = json_encode($data);
                                    $manifest->save();
                                    if($sent)
                                        $logs .= ' Sent OK.';
                                    else
                                        $logs .= ' Sent failure.';
                                }
                                else
                                {
                                    $logs .= ' No email';
                                    $sent = true;
                                }
                            }
                            else
                                $logs .= ' Saved failure.';
                        }
                        //empty email with no purchases
                        else if($date->s_manifest_emails>0 && !empty($date->emails))
                        {
                            $logs .= '* * '.($k+1).'. <small>"'.$date->name.'" on "'.date('m/d/Y g:ia',strtotime($date->show_time)).'"<br>* * * => No sales.';
                            $manifest = new Manifest;
                            $manifest->send($date->emails, $info['subject'].' '.$date->name,true);
                        }
                        $logs .= '</small><br>';
                    }
                }
                else
                    $logs .= 'no results for "'.date('m/d/Y',strtotime($this->date_manifest)).'".</i></b><br>';
            }
            if($log)
            {
                if(isset($sent))
                    return ['success'=>$sent,'msg'=>$logs];
                return ['success'=>false,'msg'=>$logs];
            }
            else
            {
                if(isset($sent))
                    return $sent;
                return false;
            }
        } catch (Exception $ex) {
            if($log)
                return ['success'=>false,'msg'=>$logs];
            return false;
        }
    }
    /*
     * create data to storage on DB
     */
    public function create_report($type)
    {
        try {
            $info = ['dates'=>[],'type'=>'','subject'=>''];
            $query_date = date('Y-m-d',strtotime($this->date_manifest));
            //init variables
            switch ($type)                      
            {
                case 'Preliminary':
                    $dates = DB::table('show_times')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->select(DB::raw('show_times.id, show_times.show_time,
                                            shows.emails, shows.name, shows.manifest_emails AS s_manifest_emails,
                                            COUNT(purchases.id) AS num_purchases,
                                            SUM(purchases.quantity) AS num_people'))
                            ->where('purchases.status','=','Active')
                            ->whereDate('show_times.show_time','=',$query_date)
                            ->whereNotExists(function ($query) {
                                $query->select(DB::raw(1))
                                      ->from('manifest_emails')
                                      ->whereRaw('show_times.id = manifest_emails.show_time_id')
                                      ->where('manifest_type','=','Preliminary');
                            });
                    if($this->previous_date==false)
                        $dates = $dates->where(DB::raw('DATE_SUB(show_times.show_time, INTERVAL shows.prelim_hours HOUR)'),'<=',$this->date_manifest);
                    $dates = $dates->groupBy('show_times.id')->distinct()->get()->toArray();
                    $info = ['dates'=>$dates,'type'=>'Preliminary','subject'=>'Preliminary Manifest for '];
                    break;
                case 'Primary':
                    $dates = DB::table('show_times')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->select(DB::raw('show_times.id, show_times.show_time,
                                            shows.emails, shows.name, shows.manifest_emails AS s_manifest_emails,
                                            COUNT(purchases.id) AS num_purchases,
                                            SUM(purchases.quantity) AS num_people'))
                            ->where('purchases.status','=','Active')
                            ->whereDate('show_times.show_time','=',$query_date)
                            ->whereNotExists(function ($query) {
                                $query->select(DB::raw(1))
                                      ->from('manifest_emails')
                                      ->whereRaw('show_times.id = manifest_emails.show_time_id')
                                      ->where('manifest_type','=','Primary');
                            });
                    if($this->previous_date==false)
                        $dates = $dates->where(DB::raw('DATE_SUB(show_times.show_time, INTERVAL shows.cutoff_hours HOUR)'),'<=',$this->date_manifest);
                    $dates = $dates->groupBy('show_times.id')->distinct()->get()->toArray();
                    $info = ['dates'=>$dates,'type'=>'Primary','subject'=>'Primary Manifest for '];
                    break;
                case 'LastMinute':
                    $dates = DB::table('show_times')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('manifest_emails', 'manifest_emails.show_time_id', '=' ,'show_times.id')
                            ->select(DB::raw('show_times.id, show_times.show_time,
                                            shows.emails, shows.name, shows.manifest_emails AS s_manifest_emails,
                                            COUNT(purchases.id) AS num_purchases, manifest_emails.num_purchases,
                                            SUM(purchases.quantity) AS num_people'))
                            ->where('manifest_emails.manifest_type', '=', 'Primary')
                            ->where('purchases.status','=','Active')
                            ->whereRaw('"'.$this->date_manifest.'" BETWEEN DATE_SUB(show_times.show_time, INTERVAL 15 MINUTE) AND show_times.show_time')
                            ->havingRaw('COUNT(purchases.id) != manifest_emails.num_purchases')
                            ->groupBy('show_times.id')->distinct()->get()->toArray();
                    $info = ['dates'=>$dates,'type'=>'Primary','subject'=>'Last Minute Manifest for '];
                    break;
                case 'NoSales':
                    $dates = DB::table('show_times')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->leftJoin('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->select(DB::raw('show_times.id, show_times.show_time,
                                            shows.emails, shows.name, shows.manifest_emails AS s_manifest_emails,
                                            COUNT(purchases.id) AS num_purchases, manifest_emails.num_purchases,
                                            SUM(purchases.quantity) AS num_people'))
                            ->where(function($query) {
                                $query->where('purchases.status','=','Active')
                                      ->orWhereNull('purchases.id');
                            })
                            ->whereRaw('"'.$this->date_manifest.'" BETWEEN DATE_ADD(show_times.show_time, INTERVAL 30 MINUTE) AND DATE_ADD(show_times.show_time, INTERVAL 40 MINUTE)')
                            ->havingRaw('COUNT(purchases.id) < 1')
                            ->groupBy('show_times.id')->distinct()->get()->toArray();
                    $info = ['dates'=>$dates,'type'=>'NoSales','subject'=>'No Sales Manifest for '];
                    break;
                default:break;
            }   
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
                            ->select(DB::raw('purchases.id, shows.name AS event_name, show_times.show_time,
                                            CONCAT(customers.last_name,",",customers.first_name) AS customer_name,
                                            locations.address, customers.phone, customers.email,
                                            purchases.quantity, purchases.ticket_type AS description, purchases.price_paid AS amount, purchases.savings,
                                            purchases.customer_id, purchases.created, IF(purchases.status="Active","Active","Canceled") AS p_status,
                                            discounts.code'))
                            ->where('show_times.id','=',$data->id)
                            ->groupBy('purchases.id')->orderBy('customers.last_name')
                            ->distinct()->get()->toArray();
            //get gifts
            $gifts = DB::table('ticket_number')
                            ->join('purchases', 'purchases.id', '=' ,'ticket_number.purchases_id')
                            ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('customers', function($join)
                            {
                                $join->on('customers.id', '=', 'ticket_number.customers_id')
                                     ->on('purchases.customer_id','<>','ticket_number.customers_id');
                            })
                            ->select(DB::raw('ticket_number.purchases_id, ticket_number.customers_id, purchases.customer_id,
                                            GROUP_CONCAT( DISTINCT CONCAT(customers.last_name,",",customers.first_name) ORDER BY customers.last_name SEPARATOR "//") AS customers'))
                            ->where('show_times.id','=',$data->id)
                            ->groupBy('ticket_number.purchases_id')
                            ->distinct()->get()->toArray();
            //add purchases and gifts to data
            foreach ($purchases as $p)
            {
                $p->gifts = '';
                foreach ($gifts as $g)
                {
                    if($p->id == $g->purchases_id)
                        $p->gifts = $g->customers;
                }
            }
            $data->purchases = $purchases;
        } catch (Exception $ex) {

        } finally {
            return get_object_vars($data);
        }
    }
    /*
     * save data into DB
     */
    public function save_data($data)
    {
        try {
            //create record to save to DB
            $manifest = Manifest::where('show_time_id',$data['id'])->where('manifest_type',$data['type'])->first();
            if(empty($manifest))
                $manifest = new Manifest;
            $manifest->show_time_id = $data['id'];
            $manifest->manifest_type = $data['type'];
            $manifest->num_purchases = $data['num_purchases'];
            $manifest->num_people = $data['num_people'];
            $manifest->recipients = $data['emails'];
            $manifest->email = json_encode($data);
            if($manifest->save())
                return $manifest;
            return null;
        } catch (Exception $ex) {
            return null;
        }
    }

}
