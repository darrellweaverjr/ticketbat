<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Stage class
 *
 * @author ivan
 */
class Stage extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stages';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the venue record associated with the stage.
     */
    public function venue()
    {
        return $this->belongsTo('App\Http\Models\Venue','venue_id');
    }
    /**
     * Get the shows for the stage.
     */
    public function shows()
    {
        return $this->hasMany('App\Http\Models\Show','stage_id');
    }
    /**
     * Set the url for the current stage.
     */
    public function set_image_url($image_url)
    {
        if($this->image_url && $this->image_url!='')
            Image::remove_image($this->image_url);
        $this->image_url = Image::stablish_image('images',$image_url);
    }
    /**
     * Remove the image file for the current stage.
     */
    public function delete_image_file()
    {
        if(Image::remove_image($this->image_url))
        {
            $this->image_url = '';
            return true;
        }
        return true;   
    }
}
