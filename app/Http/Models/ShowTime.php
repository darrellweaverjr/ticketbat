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
    //RELATIONSHIPS MANY-MANY
    /**
     * The soldout tickets that belong to the show.
     */
    public function soldout_tickets()
    {
        return $this->belongsToMany('App\Http\Models\Ticket','soldout_tickets','show_time_id','ticket_id')->withPivot('created');
    }
}
