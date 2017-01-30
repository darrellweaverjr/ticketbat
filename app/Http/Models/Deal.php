<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Deal class
 *
 * @author ivan
 */
class Deal extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'deals';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Set the url for the current deal.
     */
    public function set_image_url($image_url)
    {
        if($this->image_url && $this->image_url!='')
            Image::remove_image($this->image_url);
        $this->image_url = Image::stablish_image('deals',$image_url);
    }
    /**
     * Remove the image file for the current deal.
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
