<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Stage class
 *
 * @author ivan
 */
class Stage extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stages';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Get the venue record associated with the purchase.
     */
    public function venue()
    {
        return $this->belongsTo('App\Http\Models\Venue','venue_id');
    }
}
