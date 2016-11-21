<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
}
