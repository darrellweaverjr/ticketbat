<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Shoppingcart class
 *
 * @author ivan
 */
class Shoppingcart extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shoppingcart';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the show_time record associated with the shoppingcart.
     */
    public function item()
    {
        return $this->belongsTo('App\Http\Models\ShowTime','item_id');
    }
    /**
     * Get the ticket record associated with the shoppingcart.
     */
    public function ticket()
    {
        return $this->belongsTo('App\Http\Models\Ticket','ticket_id');
    }
}
