<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Consignment  class
 *
 * @author ivan
 */
class Consignment extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'consignments';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the showtime record associated with the Consignment.
     */
    public function show_time()
    {
        return $this->belongsTo('App\Http\Models\ShowTime','show_time_id');
    }
    /**
     * Get the seller record associated with the Consignment.
     */
    public function seller()
    {
        return $this->belongsTo('App\Http\Models\User','seller_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * The consignment_purchases that belong to the Consignment.
     */
    public function consignment_purchases()
    {
        return $this->belongsToMany('App\Http\Models\Purchase','purchase_seats','consignment_id','purchase_id')->withPivot('seat_id','status','updated');
    }  
    
}
