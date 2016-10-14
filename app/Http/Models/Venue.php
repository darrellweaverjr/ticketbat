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
     * Get the location record associated with the band.
     */
    public function location()
    {
        return $this->belongsTo('App\Http\Models\Location','location_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * The images that belong to the venue.
     */
    public function venue_images()
    {
        return $this->belongsToMany('App\Http\Models\Image','venue_images','venue_id','image_id');
    }
    /**
     * The videos that belong to the venue.
     */
    public function venue_videos()
    {
        return $this->belongsToMany('App\Http\Models\Video','venue_videos','venue_id','video_id');
    }
}
