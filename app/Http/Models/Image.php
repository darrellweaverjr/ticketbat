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
            else if($input['action']=='resize')
                $image->resize($input['resize_width'], $input['resize_height']);
            else {}
            //if element with image is for change
            if($input['tmp'])
                $path = env('UPLOAD_FILE_TEMP','uploads_tmp').'/';
            else
                $path = env('UPLOAD_FILE_DEFAULT','uploads').'/';
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
    /**
     * Change to real location images images
     */
    public static function stablish_image($image_url)
    {
        try { 
            $realPath = realpath(base_path()).'/public';
            if(File::exists($realPath.$image_url))
            {
                if(!(stripos($image_url,env('UPLOAD_FILE_TEMP','uploads_tmp').'/')===false))
                {
                    $new_path = $realPath.'/'.env('UPLOAD_FILE_DEFAULT','uploads').'/';
                    $new_name = File::name($image_url).'.'.File::extension($image_url);
                    //if file exists in the server create this like a new copy (_c)
                    while(File::exists($new_path.$new_name))
                        $new_name = File::name($new_path.$new_name).'_c'.'.'.File::extension($image_url); 
                    //move file to final location
                    if(File::move($realPath.$image_url,$new_path.$new_name))
                        return '/'.env('UPLOAD_FILE_DEFAULT','uploads').'/'.$new_name;
                    else 
                        return '';
                }
                return $image_url;
            }
            else
                return '';
        } catch (Exception $ex) {
            return '';
        }
    }
}
