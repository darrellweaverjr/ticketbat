<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Discount class
 *
 * @author ivan
 */
class Discount extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'discounts';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS MANY-MANY
    /**
     * The discount_shows that belong to the discount.
     */
    public function discount_shows()
    {
        return $this->belongsToMany('App\Http\Models\Show','discount_shows','discount_id','show_id');
    }
    /**
     * The discount_tickets that belong to the discount.
     */
    public function discount_tickets()
    {
        return $this->belongsToMany('App\Http\Models\Ticket','discount_tickets','discount_id','ticket_id');
    }
    /**
     * The user_discounts that belong to the discount.
     */
    public function user_discounts()
    {
        return $this->belongsToMany('App\Http\Models\User','user_discounts','discount_id','user_id');
    }
}
