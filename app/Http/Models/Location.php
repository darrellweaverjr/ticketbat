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
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the customers for the location.
     */
    public function customers()
    {
        return $this->hasMany('App\Http\Models\Customer','location_id');
    }
    /**
     * Get the users for the location.
     */
    public function users()
    {
        return $this->hasMany('App\Http\Models\User','location_id');
    }
    /**
     * Get the venues for the location.
     */
    public function venues()
    {
        return $this->hasMany('App\Http\Models\Venue','location_id');
    }
}
