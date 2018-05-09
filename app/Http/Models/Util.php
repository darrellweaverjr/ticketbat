<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\File;


/**
 * Utilities class
 *
 * @author ivan
 */
class Util extends Model
{
    /**
     * Search for enum values in the DB.
     *
     * @return Array with enum values
     */
    public static function getEnumValues($table,$column)
    {
        try {
            $type = DB::select(DB::raw("SHOW COLUMNS FROM $table WHERE Field = '{$column}'"))[0]->Type ;
            $enum = array();
            $is_enum = preg_match('/^enum\((.*)\)$/', $type, $matches);
            if(!$is_enum)
                $is_set = preg_match('/^set\((.*)\)$/', $type, $matches);
            if($matches && count($matches))
            {
                foreach( explode(',', $matches[1]) as $value )
                {
                  $v = trim( $value, "'" );
                  $enum = array_add($enum, $v, $v);
                }
            }
            return $enum;
        } catch (Exception $ex) {
            throw new Exception('Error Util getEnumValues: '.$ex->getMessage());
        }
    }
    /**
     * Set enum values in the DB.
     *
     * @return Boolean
     */
    public static function setEnumValues($table,$column,$values)
    {
        try {
            (count($values))? $default = 'NULL DEFAULT "'.array_values($values)[0].'" COMMENT ""' : $default = '';
            return DB::statement('ALTER TABLE '.$table.' CHANGE COLUMN '.$column.' '.$column.' ENUM("'.implode('","',$values).'") '.$default);
        } catch (Exception $ex) {
            throw new Exception('Error Util getEnumValues: '.$ex->getMessage());
        }
    }
    /**
     * Check if current string is JSON.
     *
     * @return bool
     */
    public static function isJSON($string)
    {
        try {
            return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
        } catch (Exception $ex) {
            throw new Exception('Error Util isJSON: '.$ex->getMessage());
        }
    }
    /**
     * Generate csv to download.
     *
     * @return csv
     */
    public static function downloadCSV($view,$name)
    {
        try {
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="'.$name.'.csv";');
            $f = fopen('php://output', 'w');
            fwrite($f, $view->render());
        } catch (Exception $ex) {
            throw new Exception('Error Util downloadCSV: '.$ex->getMessage());
        }
    }
    /**
     * Generate QR code.
     *
     * @return csv
     */
    public static function getQRcode($purchase_id,$user_id,$ticket_number,$size=100)
    {
        try {
            $code = 'TB'.str_pad((string)$purchase_id,6,'0',STR_PAD_LEFT).str_pad((string)$user_id,5,'0',STR_PAD_LEFT).$ticket_number;
            return 'https://chart.googleapis.com/chart?chs='.$size.'x'.$size.'&cht=qr&chl='.htmlentities($code).'&choe=UTF-8';
        } catch (Exception $ex) {
            throw new Exception('Error Util getQRcode: '.$ex->getMessage());
        }
    }
    /**
     * Create slug by name and venue slug if it is a show.
     */
    public static function generate_slug($name,$venue_id=null,$show_id=null)
    {
        try {
            //lower and trim
            $name = strtolower(trim($name));
            if(!empty($name))
            {
                $show = (!empty($show_id))? Show::find($show_id) : null;
                $venue = (!empty($venue_id))? Show::find($venue_id) : null;
                //replace white spaces
                $name = preg_replace('/\s+/','-',$name);
                //replace strange characters for "_"
                $name = preg_replace('/[^a-z0-9-]/','_',$name);
                //remove duplicate "_"
                $slug = preg_replace('/([_])\1+/','$1',$name);
                //if show
                if(!empty($venue_id) && isset($show_id))
                    $slugs = Show::pluck('slug')->toArray();
                else
                    $slugs = Venue::pluck('slug')->toArray();
                //if it is existing show and the slug it's the same like slug, no change
                if(!empty($show_id))
                {
                    if($show && $show->slug == $slug)
                        return $slug;
                }
                else if(!empty($venue_id))
                {
                    if($venue && $venue->slug == $slug)
                        return $slug;
                }
                //check if the slug exists
                while (in_array($slug,$slugs))
                {
                    $skip = false;
                    //search if show slug
                    if(!empty($venue_id) && isset($show_id))
                    {
                        if($venue && !empty($venue->slug))
                        {
                            $venue_slug = $venue->slug;
                            if (strpos($slug,$venue_slug) === false)
                            {
                                $slug.='-'.$venue_slug;
                                $skip = true;
                            }
                        }
                    }
                    //concat with numbers or if it is a venue
                    if(!$skip)
                    {
                        $subslugs = explode('-', $slug);
                        $last = end($subslugs);
                        if(is_numeric($last))
                            $subslugs[count($subslugs)-1] = (int)$last + 1;
                        else $subslugs[] = 1;
                        $slug = implode('-',$subslugs);
                    }
                }
            }
            return $slug;
        } catch (Exception $ex) {
            return '';
        }
    }
    /**
     * Upload files
     */
    public static function upload_file($file,$folder)
    {
        try {
            //init
            $originalName = $file->getClientOriginalName();
            $originalExt = $file->getClientOriginalExtension();
            //get file
                //if file exists in the server create this like a new copy (_c)
                while(Storage::disk('s3')->exists($folder.'/'.$originalName.'.'.$originalExt))
                    $originalName .= '_c';
                //move file to amazon s3
                //Storage::disk('s3')->put($folder.$$originalName.'.'.$originalExt, new File($file->getRealPath()), 'public');
                Storage::disk('s3')->putFileAs($folder, new File($file->getRealPath()),$originalName.'.'.$originalExt);
                //return url if file exists
                if(Storage::disk('s3')->exists($folder.'/'.$originalName.'.'.$originalExt))
                    return '/s3/'.$folder.'/'.$originalName.'.'.$originalExt;
                return '';
        } catch (Exception $ex) {
            return '';
        }
    }
    /**
     * Remove files
     */
    public static function remove_file($file_url)
    {
        try {
            //init
            $originalName = pathinfo($file_url, PATHINFO_FILENAME);
            $originalExt = pathinfo($file_url, PATHINFO_EXTENSION);
            //check if is in s3 server (the new server)
            if(preg_match('/\/s3\//',$file_url) || strpos($file_url,env('IMAGE_URL_AMAZON_SERVER')) !== false)
            {
                $file_url = substr(strrchr(dirname($file_url,1), '/'), 1).'/'.$originalName.'.'.$originalExt;
                if(Storage::disk('s3')->exists($file_url))
                {
                    Storage::disk('s3')->delete($file_url);
                    return true;
                }
                return true;
            }
            //other url in another place
            else return true;
        } catch (Exception $ex) {
            return true;
        }
    }
    /**
     * View files
     */
    public static function view_file($file_url)
    {
        try {
            //init
            if(preg_match('/\/s3\//',$file_url))
                return env('IMAGE_URL_AMAZON_SERVER').str_replace('/s3/','/',$file_url);
            return '';
        } catch (Exception $ex) {
            return '';
        }
    }

    /**
     * Generates a json response checking numbers
     */
    public static function json($response)
    {
        try {
            return Response::json($response,201,[],JSON_NUMERIC_CHECK);
        } catch (Exception $ex) {
            json_encode($response);
        }
    }

    /**
     * round price
     */
    public static function round($number)
    {
        try {
            return round($number,2, PHP_ROUND_HALF_UP);
        } catch (Exception $ex) {
            return $number;
        }
    }

    /**
     * get system info
     */
    public static function system_info()
    {
        try {
            return 'IP('.Request::getClientIp().') - '.Request::header('User-Agent');
        } catch (Exception $ex) {
            return '';
        }
    }

    /**
     * generate uniq session_id
     */
    public static function s_token($for_app=false,$insert_session=false,$store_token=null,$reset_token=false)
    {
        if(!empty($store_token))
        {
            $s_token = $store_token;
            Session::put('s_token', $s_token);
        }
        else
        {
            $s_token = Session::get('s_token',null);
            if(empty($s_token) || $reset_token)
            {
                $prefix = ($for_app)? 'app_' : 'web_';
                $s_token = uniqid($prefix).mt_rand (10,99);
                if($insert_session)
                    Session::put('s_token', $s_token);
            }
        }
        return $s_token;
    }
    /**
     * Get tickets that coupon applies in session.
     */
    public static function tickets_coupon()
    {
        try {
            $tickets = [];
            $coupon = Session::get('coup',null);
            if(!empty($coupon) && Util::isJSON($coupon))
            {
                $coup = json_decode($coupon,true);
                if(!empty($coup['tickets']))
                {
                    foreach ($coup['tickets'] as $dt)
                        if(!in_array($dt['ticket_id'], $tickets))
                            $tickets[] = $dt['ticket_id'];
                }
            }
            return $tickets;
        } catch (Exception $ex) {
            return [];
        }
    }
    /**
     * Return values that uses the pages to display events according to the user logged.
     */
    public static function display_options_by_user()
    {
        try {
            $current = date('Y-m-d H:i:s');
            $date_limit = date('Y-m-d H:i:s', strtotime('yesterday'));
            $data = ['where'=>[['show_times.show_time','>=',$current]], 'venues'=>null, 'link'=>'event/'];
            if(Auth::check())
            {
                if(in_array(Auth::user()->user_type_id, explode(',', env('POS_OPTION_USER_TYPE'))))
                {
                    $data['where'] = [['show_times.show_time','>=',$date_limit]];
                    $data['where'][] = [DB::raw('DATE_SUB(show_times.show_time,INTERVAL venues.cutoff_hours_start HOUR)'),'<=',$current];
                    $data['where'][] = [DB::raw('DATE_ADD(show_times.show_time,INTERVAL venues.cutoff_hours_end HOUR)'),'>=',$current];
                    $venues_edit = Auth::user()->venues_check_ticket;
                    $data['venues'] = (!empty($venues_edit))? explode(',',$venues_edit) : [0];
                    $data['link'] = 'shoppingcart/viewcart?slug=';
                }
                else if(in_array(Auth::user()->id, explode(',', env('ROOT_USER_ID'))))
                {
                    $data['where'] = [['show_times.show_time','>=',$date_limit]];
                }
            }
            return $data;
        } catch (Exception $ex) {
            return [];
        }
    }
    
    /**
     * Filter purchases according to conditions.
     */
    public static function filter_purchases($module,$input,$default_date_range=null)
    {
        $data = [ 'where'=>[ ['purchases.id','>',0] ],'search'=>[ 'venues'=>[],'shows'=>[] ] ];
        
        try {
            //FILTER SEARCH INPUT
            $data['search']['payment_types'] = Util::getEnumValues('purchases','payment_type');
            $data['search']['ticket_types'] = Util::getEnumValues('tickets','ticket_type');
            $data['search']['status'] = Util::getEnumValues('purchases','status');
            $data['search']['channels'] = Util::getEnumValues('purchases','channel');
            //if values
            if(isset($input))
            {
                //search venue
                $data['search']['venue'] = (!empty($input['venue']))? $input['venue'] : '';
                if(!empty($input['venue']))
                    $data['where'][] = ['shows.venue_id','=',$data['search']['venue']];                
                
                //search show
                $data['search']['show'] = (!empty($input['show']))? $input['show'] : '';
                if(!empty($input['show']))
                    $data['where'][] = ['shows.id','=',$data['search']['show']];
                //search showtime range
                $data['search']['showtime_start_date'] = (!empty($input['showtime_start_date']))? $input['showtime_start_date'] : '';
                $data['search']['showtime_end_date'] = (!empty($input['showtime_end_date']))? $input['showtime_end_date'] : '';
                if(!empty($data['search']['showtime_start_date']) && !empty($data['search']['showtime_end_date']))
                {
                    $data['where'][] = [DB::raw('DATE(show_times.show_time)'),'>=',date('Y-m-d',strtotime($data['search']['showtime_start_date']))];
                    $data['where'][] = [DB::raw('DATE(show_times.show_time)'),'<=',date('Y-m-d',strtotime($data['search']['showtime_end_date']))];
                }
                //search showtime   
                $data['search']['showtime_date'] = (!empty($input['showtime_date']))? $input['showtime_date'] : '';
                if(!empty($data['search']['showtime_date']))
                    $data['where'][] = ['show_times.show_time','=',date('Y-m-d H:i:s',strtotime($data['search']['showtime_date']))];
                // showtime_id
                $data['search']['showtime_id'] = (!empty($input['showtime_id']) && is_numeric($input['showtime_id']))? $input['showtime_id'] : '';
                if(!empty($data['search']['showtime_id']))
                    $data['where'][] = ['show_times.id','=',$data['search']['showtime_id']];
                //search soldtime
                if(!empty($default_date_range))
                {
                    $default_start_date = date('n/d/y', strtotime($default_date_range.' DAY')).' 12:00 AM';
                    $default_end_date = date('n/d/y').' 11:59 PM';
                }
                else
                    $default_start_date = $default_end_date = '';
                $data['search']['soldtime_start_date'] = (isset($input['soldtime_start_date']))? $input['soldtime_start_date'] : $default_start_date;
                $data['search']['soldtime_end_date'] = (isset($input['soldtime_end_date']))? $input['soldtime_end_date'] : $default_end_date;
                if(!empty($data['search']['soldtime_start_date']) && !empty($data['search']['soldtime_end_date']))
                {
                    $data['where'][] = [DB::raw('purchases.created'),'>=',date('Y-m-d H:i:s',strtotime($data['search']['soldtime_start_date']))];
                    $data['where'][] = [DB::raw('purchases.created'),'<=',date('Y-m-d H:i:s',strtotime($data['search']['soldtime_end_date']))];
                }
                //search payment types
                $data['search']['payment_type'] = (!empty($input['payment_type']))? $input['payment_type'] : array_values($data['search']['payment_types']);
                //search channel
                $data['search']['channel'] = (!empty($input['channel']))? $input['channel'] : '';
                if(!empty($input['channel']))
                    $data['where'][] = ['purchases.channel','=',$data['search']['channel']];
                //search date range
                $data['search']['start_amount'] = (!empty($input['start_amount']) && is_numeric($input['start_amount']))? trim($input['start_amount']) : '';
                $data['search']['end_amount'] = (!empty($input['end_amount']) && is_numeric($input['end_amount']))? trim($input['end_amount']) : '';
                if(!empty($input['start_amount']))
                    $data['where'][] = ['purchases.price_paid','>=',$data['search']['start_amount']];
                if(!empty($input['end_amount']))
                    $data['where'][] = ['purchases.price_paid','<=',$data['search']['end_amount']];
                //search ticket_type
                $data['search']['ticket_type'] = (!empty($input['ticket_type']))? $input['ticket_type'] : '';
                if(!empty($input['ticket_type']))
                    $data['where'][] = ['tickets.ticket_type','=',$data['search']['ticket_type']];
                //search status
                $data['search']['statu'] = (!empty($input['statu']))? $input['statu'] : '';
                if(!empty($input['statu']))
                    $data['where'][] = ['purchases.status','=',$data['search']['statu']];
                //search user
                if(!empty($input['user']))
                {
                    $data['search']['user'] = trim($input['user']);
                    if(is_numeric($data['search']['user']))
                        $data['where'][] = ['users.id','=',$data['search']['user']];
                    else if(filter_var($data['search']['user'], FILTER_VALIDATE_EMAIL))
                        $data['where'][] = ['users.email','=',$data['search']['user']];
                    else
                        $data['search']['user'] = '';
                }
                else
                    $data['search']['user'] = '';
                //search customer
                if(!empty($input['customer']))
                {
                    $data['search']['customer'] = trim($input['customer']);
                    if(is_numeric($data['search']['customer']))
                        $data['where'][] = ['customers.id','=',$data['search']['customer']];
                    else if(filter_var($data['search']['customer'], FILTER_VALIDATE_EMAIL))
                        $data['where'][] = ['customers.email','=',$data['search']['customer']];
                    else
                        $data['search']['customer'] = '';
                }
                else
                    $data['search']['customer'] = '';
                //search order_id
                $data['search']['order_id'] = (!empty($input['order_id']) && is_numeric($input['order_id']))? trim($input['order_id']) : '';
                if(!empty($input['order_id']))
                    $data['where'][] = ['purchases.id','=',$data['search']['order_id']];
                //search authcode
                $data['search']['authcode'] = (!empty($input['authcode']))? trim($input['authcode']) : '';
                if(!empty($input['authcode']))
                    $data['where'][] = ['transactions.authcode','=',$data['search']['authcode']];
                //search refnum
                $data['search']['refnum'] = (!empty($input['refnum']))? trim($input['refnum']) : '';
                if(!empty($input['refnum']))
                    $data['where'][] = ['transactions.refnum','=',$data['search']['refnum']];
                
                //search printing
                if(isset($input['mirror_type']) && !empty($input['mirror_type']))
                    $data['search']['mirror_type'] = $input['mirror_type'];
                else
                    $data['search']['mirror_type'] = 'previous_period';

                if(isset($input['mirror_period']) && !empty($input['mirror_period']) && is_numeric($input['mirror_period']))
                    $data['search']['mirror_period'] = $input['mirror_period'];
                else
                    $data['search']['mirror_period'] = 0;

                if(isset($input['replace_chart']) && !empty($input['replace_chart']))
                    $data['search']['replace_chart'] = 1;
                else
                    $data['search']['replace_chart'] = 1;

                if(isset($input['coupon_report']) && !empty($input['coupon_report']))
                    $data['search']['coupon_report'] = 1;
                else
                    $data['search']['coupon_report'] = 0;
            }
            
            //FILTER SEARCH BY PERMISSIONS     
            if(in_array('View',Auth::user()->user_type->getACLs()[$module]['permission_types']))
            {
                if(Auth::user()->user_type->getACLs()[$module]['permission_scope'] != 'All')
                {
                    if(!empty(Auth::user()->venues_edit) && count(explode(',',Auth::user()->venues_edit)))
                    {
                        $data['where'][] = [DB::raw('shows.venue_id IN ('.Auth::user()->venues_edit.') OR shows.create_user_id'),'=',Auth::user()->id];
                        //add shows and venues for search
                        $data['search']['venues'] = Venue::whereIn('id',explode(',',Auth::user()->venues_edit))->orderBy('name')->get(['id','name']);
                        $data['search']['shows'] = Show::whereIn('venue_id',explode(',',Auth::user()->venues_edit))->orWhere('create_user_id',Auth::user()->id)->orderBy('name')->get(['id','name','venue_id']);
                    }
                    else
                        $data['where'][] = ['shows.create_user_id','=',Auth::user()->id];
                }
                //all
                else
                {
                    //add shows and venues for search
                    $data['search']['venues'] = Venue::orderBy('name')->get(['id','name']);
                    $data['search']['shows'] = Show::orderBy('name')->get(['id','name','venue_id']);
                }
            }
            else
                $data['where'][] = ['purchases.id','=',0];
            
        } catch (Exception $ex) {
            
        } finally {
            return $data;
        }
    }

}
