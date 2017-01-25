<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Banner class
 *
 * @author ivan
 */
class Banner extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'banners';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Set the url for the current banner.
     */
    public function set_file($file)
    {
        $this->file = Image::stablish_image('banners',$file);
    }
    /**
     * Remove the image file for the current banner.
     */
    public function delete_image_file()
    {
        if(Image::remove_image($this->file))
        {
            $this->file = '';
            return true;
        }
        return true;   
    }
}
