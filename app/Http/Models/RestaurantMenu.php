<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Band class
 *
 * @author ivan
 */
class RestaurantMenu extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'restaurant_menu';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Get the parent Menu.
     */
    public function parent()
    {
        return $this->belongsTo('App\Http\Models\RestaurantMenu', 'parent_id');
    }
    /**
     * Get the children menu.
     */
    public function children()
    {
        return $this->hasMany('App\Http\Models\RestaurantMenu','parent_id','id');
    }
    //RELATIONSHIPS ONE-MANY
    //RELATIONSHIPS MANY-MANY
    //PERSONALIZED METHODS
}
