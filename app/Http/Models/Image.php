<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Image class
 *
 * @author ivan
 */
class Image extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'images';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS MANY-MANY
    /**
     * The show_images that belong to the images.
     */
    public function show_images()
    {
        return $this->belongsToMany('App\Http\Models\Show','show_images','image_id','show_id');
    }
    /**
     * The show_awards that belong to the images.
     */
    public function show_awards()
    {
        return $this->belongsToMany('App\Http\Models\Show','show_awards','image_id','show_id')->withPivot('url','updated');
    }
    /**
     * The user_images that belong to the image.
     */
    public function user_images()
    {
        return $this->belongsToMany('App\Http\Models\User','user_images','image_id','user_id');
    }
    /**
     * The venue_images that belong to the image.
     */
    public function venue_images()
    {
        return $this->belongsToMany('App\Http\Models\Venue','venue_images','image_id','venue_id');
    }
}
