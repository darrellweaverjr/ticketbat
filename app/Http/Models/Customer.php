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
    /**
     * Get the location record associated with the customer.
     */
    public function location()
    {
        return $this->belongsTo('App\Http\Models\Location','location_id');
    }
}
