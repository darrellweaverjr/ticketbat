<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Show class
 *
 * @author ivan
 */
class Show extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shows';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Get the venue record associated with the band.
     */
    public function venue()
    {
        return $this->belongsTo('App\Http\Models\Venue','venue_id');
    }
    /**
     * Get the stage record associated with the band.
     */
    public function stage()
    {
        return $this->belongsTo('App\Http\Models\Stage','stage_id');
    }
}
