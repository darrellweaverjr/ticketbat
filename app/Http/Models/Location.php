<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Location class
 *
 * @author ivan
 */
class Location extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'locations';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
