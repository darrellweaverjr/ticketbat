<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ticket class
 *
 * @author ivan
 */
class Ticket extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tickets';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the show record associated with the ticket.
     */
    public function show()
    {
        return $this->belongsTo('App\Http\Models\Show','show_id');
    }
    /**
     * Get the package record associated with the ticket.
     */
    public function package()
    {
        return $this->belongsTo('App\Http\Models\Package','package_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * The discount_tickets that belong to the ticket.
     */
    public function discount_tickets()
    {
        return $this->belongsToMany('App\Http\Models\Discount','discount_tickets','ticket_id','discount_id');
    }
    /**
     * The soldout tickets that belong to the showtime.
     */
    public function soldout_tickets()
    {
        return $this->belongsToMany('App\Http\Models\ShowTime','soldout_tickets','ticket_id','show_time_id')->withPivot('created');
    }  
    
}
