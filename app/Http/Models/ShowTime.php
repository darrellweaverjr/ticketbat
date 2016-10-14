<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ShowTime class
 *
 * @author ivan
 */
class ShowTime extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'show_times';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; 
    //RELATIONSHIPS OME-MANY
    /**
     * Get the bands for the showtime.
     */
    public function manifests()
    {
        return $this->hasMany('App\Http\Models\Manifest','show_time_id');
    }
    /**
     * Get the purchases for the showtime.
     */
    public function purchases()
    {
        return $this->hasMany('App\Http\Models\Purchase','show_time_id');
    }
    /**
     * Get the transactions for the showtime.
     */
    public function transactions()
    {
        return $this->hasMany('App\Http\Models\Transaction','show_time_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * The soldout tickets that belong to the showtime.
     */
    public function soldout_tickets()
    {
        return $this->belongsToMany('App\Http\Models\Ticket','soldout_tickets','show_time_id','ticket_id')->withPivot('created');
    }    
}
