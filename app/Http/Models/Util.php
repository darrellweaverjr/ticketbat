<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
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
    public static function s_token($for_app=false,$insert_session=false,$store_token=null)
    {
        if(!empty($store_token))
        {
            $s_token = $store_token;
            Session::put('s_token', $s_token);
        }
        else
        {
            $s_token = Session::get('s_token',null);
            if(empty($s_token))
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

}
