<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Transaction class
 *
 * @author ivan
 */
class Transaction extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Get the show_time record associated with the transaction.
     */
    public function show_time()
    {
        return $this->belongsTo('App\Http\Models\ShowTime','show_time_id');
    }
    /**
     * Get the customer record associated with the transaction.
     */
    public function customer()
    {
        return $this->belongsTo('App\Http\Models\Customer','customer_id');
    }
    /**
     * Get the user record associated with the transaction.
     */
    public function user()
    {
        return $this->belongsTo('App\Http\Models\User','user_id');
    }
}
