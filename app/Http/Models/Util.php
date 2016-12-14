<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            preg_match('/^enum\((.*)\)$/', $type, $matches);
            $enum = array();
            foreach( explode(',', $matches[1]) as $value )
            {
              $v = trim( $value, "'" );
              $enum = array_add($enum, $v, $v);
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
     * Create slug by name and venue slug if it is a show.
     */
    public static function generate_slug($name,$venue_id=null)
    {
        try {
            //lower and trim
            $name = strtolower(trim($name));
            //replace white spaces
            $name = preg_replace('/\s+/','-',$name);
            //remove all not needed characters
            $slug = preg_replace('/[^a-z0-9-]/','',$name);
            //if show
            if($venue_id)
                $slugs = Show::pluck('slug')->toArray();
            else
                $slugs = Venue::pluck('slug')->toArray();
            //check if the slug exists
            while (in_array($slug,$slugs))
            {
                $skip = false;
                if($venue_id)
                {
                    if(Venue::find($venue_id) && isset(Venue::find($venue_id)->slug))
                    {
                        $venue_slug = Venue::find($venue_id)->slug;
                        if (strpos($slug,$venue_slug) === false) 
                        {
                            $slug.='-'.$venue_slug;
                            $skip = true;
                        }
                    }
                }
                //concat with numbers
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
            return $slug;
        } catch (Exception $ex) {
            return '';
        }
    }
}
