<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Seat class
 *
 * @author ivan
 */
class Seat extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seats';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the ticket for the seat.
     */
    public function ticket()
    {
        return $this->belongsTo('App\Http\Models\Ticket','ticket_id');
    }
    /**
     * Get the Consignment for the seat.
     */
    public function consignment()
    {
        return $this->belongsTo('App\Http\Models\Consignment','consignment_id');
    }
    /**
     * Get the purchase for the seat.
     */
    public function purchase()
    {
        return $this->belongsTo('App\Http\Models\Purchase','purchase_id');
    }
}
