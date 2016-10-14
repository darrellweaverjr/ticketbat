<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Customer class
 *
 * @author ivan
 */
class Customer extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the location record associated with the customer.
     */
    public function location()
    {
        return $this->belongsTo('App\Http\Models\Location','location_id');
    }
    /**
     * Get the purchases for the customer.
     */
    public function purchases()
    {
        return $this->hasMany('App\Http\Models\Purchase','customer_id');
    }
    /**
     * Get the transactions for the customer.
     */
    public function transactions()
    {
        return $this->hasMany('App\Http\Models\Transaction','customer_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * Get the ticket_number record associated with the customer.
     */
    public function ticket_numbers()
    {
        return $this->belongsToMany('App\Http\Models\Purchase','ticket_number','customers_id','purchases_id')->withPivot('id','tickets','checked','comment');
    }
}
