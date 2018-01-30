<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Show class
 *
 * @author ivan
 */
class Show extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shows';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the venue record associated with the Show.
     */
    public function venue()
    {
        return $this->belongsTo('App\Http\Models\Venue','venue_id');
    }
    /**
     * Get the stage record associated with the Show.
     */
    public function stage()
    {
        return $this->belongsTo('App\Http\Models\Stage','stage_id');
    }
    /**
     * Get the category record associated with the Show.
     */
    public function category()
    {
        return $this->belongsTo('App\Http\Models\Category','category_id');
    }
    /**
     * Get the tickets for the show.
     */
    public function tickets()
    {
        return $this->hasMany('App\Http\Models\Ticket','show_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * The show_awards that belong to the show.
     */
    public function show_awards()
    {
        return $this->belongsToMany('App\Http\Models\Image','show_awards','show_id','image_id')->withPivot('url', 'updated');
    }
    /**
     * The show_bands that belong to the show.
     */
    public function show_bands()
    {
        return $this->belongsToMany('App\Http\Models\Band','show_bands','show_id','band_id')->withPivot('n_order');
    }
    /**
     * The show_images that belong to the show.
     */
    public function show_images()
    {
        return $this->belongsToMany('App\Http\Models\Image','show_images','show_id','image_id');
    }
    /**
     * The show_videos that belong to the show.
     */
    public function show_videos()
    {
        return $this->belongsToMany('App\Http\Models\Video','show_videos','show_id','video_id');
    } 
    /**
     * The discount_shows that belong to the show.
     */
    public function discount_shows()
    {
        return $this->belongsToMany('App\Http\Models\Discount','discount_shows','show_id','discount_id');
    }
    //PERSONALIZED METHODS
    /**
     * Set the image_url for the current show.
     */
    public function set_sponsor_logo_id($sponsor_logo_id)
    {
        $this->sponsor_logo_id = Image::stablish_image('shows',$sponsor_logo_id);
    }
    /**
     * Remove the image file for the current band.
     */
    public function delete_image_file()
    {
        if(Image::remove_image($this->sponsor_logo_id))
        {
            $this->sponsor_logo_id = '';
            return true;
        }
        return true;   
    }
}
