<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Venue class
 *
 * @author ivan
 */
class Venue extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'venues';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Get the location record associated with the band.
     */
    public function location()
    {
        return $this->belongsTo('App\Http\Models\Location','location_id');
    }
}
