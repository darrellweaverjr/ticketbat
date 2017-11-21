<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Band class
 *
 * @author ivan
 */
class RestaurantAwards extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'restaurant_awards';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the restaurant record associated with the award.
     */
    public function restaurant()
    {
        return $this->belongsTo('App\Http\Models\Restaurant','restaurants_id');
    }
    //RELATIONSHIPS MANY-MANY
    //PERSONALIZED METHODS
    /**
     * Set the url for the current banner.
     */
    public function set_image($url)
    {
        $this->image_id = Image::stablish_image('restaurants/awards',$url);
    }
    /**
     * Remove the image file for the current banner.
     */
    public function delete_image()
    {
        if(Image::remove_image($this->image_id))
        {
            $this->image_id = null;
            return true;
        }
        return true;   
    }
}
