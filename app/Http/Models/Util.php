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
}
