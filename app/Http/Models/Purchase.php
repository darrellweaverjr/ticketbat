<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Purchase class
 *
 * @author ivan
 */
class Purchase extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchases';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Get the transaction record associated with the purchase.
     */
    //RELATIONSHIPS ONE-MANY
    public function transaction()
    {
        return $this->belongsTo('App\Http\Models\Transaction','transaction_id');
    }
    /**
     * Get the user record associated with the purchase.
     */
    public function user()
    {
        return $this->belongsTo('App\Http\Models\User','user_id');
    }
    /**
     * Get the discount record associated with the purchase.
     */
    public function discount()
    {
        return $this->belongsTo('App\Http\Models\Discount','discount_id');
    }
    /**
     * Get the customer record associated with the purchase.
     */
    public function customer()
    {
        return $this->belongsTo('App\Http\Models\Customer','customer_id');
    }
    /**
     * Get the ticket record associated with the purchase.
     */
    public function ticket()
    {
        return $this->belongsTo('App\Http\Models\Ticket','ticket_id');
    }
    /**
     * Get the show_time record associated with the purchase.
     */
    public function show_time()
    {
        return $this->belongsTo('App\Http\Models\ShowTime','show_time_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * Get the ticket_number record associated with the purchase.
     */
    public function ticket_numbers()
    {
        return $this->belongsToMany('App\Http\Models\Customer','ticket_number','purchases_id','customers_id')->withPivot('id','tickets','checked','comment');
    }
}
