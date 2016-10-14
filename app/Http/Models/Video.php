<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Video class
 *
 * @author ivan
 */
class Video extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'videos';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS MANY-MANY
    /**
     * The show_videos that belong to the video.
     */
    public function show_videos()
    {
        return $this->belongsToMany('App\Http\Models\Show','show_videos','video_id','show_id');
    }
    /**
     * The venue_videos that belong to the video.
     */
    public function venue_videos()
    {
        return $this->belongsToMany('App\Http\Models\Venue','venue_videos','video_id','venue_id');
    }
}
