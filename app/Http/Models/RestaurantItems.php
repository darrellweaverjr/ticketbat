<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Band class
 *
 * @author ivan
 */
class RestaurantItems extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'restaurant_items';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the restaurant record associated with the item.
     */
    public function restaurant()
    {
        return $this->belongsTo('App\Http\Models\Restaurant','restaurants_id');
    }
    /**
     * Get the menu record with the dish.
     */
    public function menu()
    {
        return $this->belongsTo('App\Http\Models\RestaurantMenu','restaurant_menu_id');
    }
    //RELATIONSHIPS MANY-MANY
    //PERSONALIZED METHODS
}
