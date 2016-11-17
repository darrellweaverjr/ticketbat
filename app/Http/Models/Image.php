<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Image as Img;

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
    //PERSONALIZED METHODS
    /**
     * Upload images
     */
    public static function upload_image($file,$input)
    {
        try {  
            $image = Img::make($file->getRealPath());
            $originalName = $file->getClientOriginalName();
            $originalExtension = $file->getClientOriginalExtension();
            //crop
            if($input['action']=='crop')
                $image->crop($input['crop_width'],$input['crop_height'],$input['crop_x'],$input['crop_y']);  
            //resize
            else
                $image->resize($input['resize_width'], $input['resize_height']);
            //if element with image is for change
            if($input['pre_upload'])
                $path = 'uploads_edit/';
            else
                $path = 'uploads/';
            //if file exists in the server create this like a new copy (_c)
            while(File::exists($path.$originalName))
                $originalName = substr($originalName,0,strrpos($originalName,'.')).'_c'.'.'.$originalExtension;  
            //save image
            $image->save($path.$originalName);
            return ['success'=>true,'file'=>'/'.$path.$originalName,'msg'=>'Image uploaded successfully!'];
        } catch (Exception $ex) {
            return ['success'=>false,'file'=>'','msg'=>'There was an error uploading the image!'];
        }
    }
}
