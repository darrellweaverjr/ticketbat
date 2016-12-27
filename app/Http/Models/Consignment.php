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
     * The purchase_seats that belong to the Consignment.
     */
    public function purchase_seats()
    {
        return $this->belongsToMany('App\Http\Models\Seat','purchase_seats','consignment_id','seat_id')->withPivot('purchase_id','status','updated');
    } 
    //PERSONALIZED FUNCTIONS
    public function set_agreement($file)
    {
        if($this->agreement != '')
            Util::remove_file ($this->agreement);
        $this->agreement = Util::upload_file ($file,'consignment');
    }
    
}
