<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Slider class
 *
 * @author ivan
 */
class Slider extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sliders';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Set the url for the current stage.
     */
    public function set_image_url($image_url)
    {
        if($this->image_url && $this->image_url!='')
            Image::remove_image($this->image_url);
        $this->image_url = Image::stablish_image('sliders',$image_url);
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
