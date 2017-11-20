<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Band class
 *
 * @author ivan
 */
class RestaurantAlbums extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'restaurant_albums';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the restaurant record associated with the album.
     */
    public function restaurant()
    {
        return $this->belongsTo('App\Http\Models\Restaurant','restaurants_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * Get the images for the album.
     */
    public function images()
    {
        return $this->hasMany('App\Http\Models\RestaurantAlbumImages','restaurant_albums_id');
    }
    //PERSONALIZED METHODS
}
