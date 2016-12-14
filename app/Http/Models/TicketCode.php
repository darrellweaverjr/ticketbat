<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ticket Code  class
 *
 * @author ivan
 */
class TicketCode extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ticket_codes';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the showtime record associated with the code.
     */
    public function show_time()
    {
        return $this->belongsTo('App\Http\Models\ShowTime','show_time_id');
    }
    /**
     * Get the ticket record associated with the code.
     */
    public function ticket()
    {
        return $this->belongsTo('App\Http\Models\Ticket','ticket_id');
    }
    /**
     * Get the seller record associated with the code.
     */
    public function seller()
    {
        return $this->belongsTo('App\Http\Models\User','user_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * The ticket_code_purchases that belong to the ticket code.
     */
    public function ticket_code_purchases()
    {
        return $this->belongsToMany('App\Http\Models\Purchase','ticket_code_purchases','ticket_code_id','purchase_id')->withPivot('created');
    }  
    
}
