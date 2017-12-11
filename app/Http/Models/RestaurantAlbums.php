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
        return $this->belongsToMany('App\Http\Models\Image','restaurant_album_images','restaurant_albums_id','image_id');
    }
    //PERSONALIZED METHODS
    /**
     * Set the url for the current banner.
     */
    public function add_image($url)
    {
        $url = Image::stablish_image('restaurants/albums',$url);
        $image = new Image;
        $image->url = $url;
        $image->created = date('Y-m-d H:i:s');
        $image->save();
        if($image)
        {
            $this->images()->attach($image->id);
            return true;
        }
        else
        {
            Image::remove_image($url);
            return false;
        }
    }
    /**
     * Remove the image file for the current banner.
     */
    public function delete_image($id)
    {
        $image = Image::find($id);
        if($image)
        {
            $image->delete_image_file();
            $this->images()->detach($image->id);
            $image->delete();
        }
        return true;   
    }
}
