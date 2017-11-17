<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Band class
 *
 * @author ivan
 */
class Restaurant extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'restaurants';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the category record associated with the band.
     */
    public function venue()
    {
        return $this->belongsTo('App\Http\Models\Venue','venue_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * Get the albums for the restaurant.
     */
    public function albums()
    {
        return $this->hasMany('App\Http\Models\RestaurantAlbum','restaurants_id');
    }
    /**
     * Get the awards for the restaurant.
     */
    public function awards()
    {
        return $this->hasMany('App\Http\Models\RestaurantAward','restaurants_id');
    }
    /**
     * Get the Comments for the restaurant.
     */
    public function Comments()
    {
        return $this->hasMany('App\Http\Models\RestaurantComment','restaurants_id');
    }
    /**
     * Get the Items for the restaurant.
     */
    public function Items()
    {
        return $this->hasMany('App\Http\Models\RestaurantItem','restaurants_id');
    }
    /**
     * Get the Reviews for the restaurant.
     */
    public function Reviews()
    {
        return $this->hasMany('App\Http\Models\RestaurantReview','restaurants_id');
    }
    /**
     * Get the Specials for the restaurant.
     */
    public function Specials()
    {
        return $this->hasMany('App\Http\Models\RestaurantSpecial','restaurants_id');
    }
    //PERSONALIZED METHODS
}
