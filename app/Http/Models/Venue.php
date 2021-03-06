<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Venue class
 *
 * @author ivan
 */
class Venue extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'venues';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the location record associated with the venue.
     */
    public function location()
    {
        return $this->belongsTo('App\Http\Models\Location','location_id');
    }
    /**
     * Get the shows for the venue.
     */
    public function shows()
    {
        return $this->hasMany('App\Http\Models\Show','venue_id');
    }
    /**
     * Get the stages for the venue.
     */
    public function stages()
    {
        return $this->hasMany('App\Http\Models\Stage','venue_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * The venue_images that belong to the venue.
     */
    public function venue_images()
    {
        return $this->belongsToMany('App\Http\Models\Image','venue_images','venue_id','image_id');
    }
    /**
     * The venue_videos that belong to the venue.
     */
    public function venue_videos()
    {
        return $this->belongsToMany('App\Http\Models\Video','venue_videos','venue_id','video_id');
    }
    //PERSONALIZED METHODS
    /**
     * Set the image_url for the current venue.
     */
    public function set_image_file($type,$image)
    {
        if($type=='logo')
            $this->logo_url = Image::stablish_image('venues',$image);
        else if($type=='header')
            $this->header_url = Image::stablish_image('venues',$image);
    }
    /**
     * Remove the image file for the current band.
     */
    public function delete_image_file($type)
    {
        if($type=='logo' && Image::remove_image($this->logo_url))
        {
            $this->logo_url = null;
            return true;
        }
        else if($type=='header' && Image::remove_image($this->header_url))
        {
            $this->header_url = null;
            return true;
        }
        return true;
    }
}
