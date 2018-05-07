<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
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
    /**
     * Set the url for the current image.
     */
    public function set_url($url)
    {
        $this->url = Image::stablish_image('images',$url);
    }
    /**
     * Remove the image file for the current image.
     */
    public function delete_image_file()
    {
        if(Image::remove_image($this->url))
        {
            $this->url = '';
            return true;
        }
        return true;   
    }
    //PERSONALIZED METHODS
    /**
     * Upload images
     */
    public static function upload_image($file,$input)
    {
        try {  
            //get file attributes
            $originalName = preg_replace('/\..+$/', '', $file->getClientOriginalName());  
            $originalName = preg_replace('/\s+/','-',$originalName);
            $originalName = preg_replace('/[^a-zA-Z0-9\_\-\.]/','',$originalName);
            $originalExt = $file->getClientOriginalExtension();
            //open image to edit
            $image = Img::make($file->getRealPath());
            //crop
            if($input['action']=='crop')
                $image->crop($input['crop_width'],$input['crop_height'],$input['crop_x'],$input['crop_y']);  
            //resize
            else if($input['action']=='resize')
                $image->resize($input['resize_width'], $input['resize_height']);
            else {}
            //save changes
            $image->save($file->getRealPath());
            //if file exists in the server create this like a new copy (_c)
            while(Storage::disk('local')->exists($originalName.'.'.$originalExt))
                $originalName .= '_c';  
            //save edited file into local disk
            Storage::disk('local')->putFileAs('tmp', new File($file->getRealPath()),$originalName.'.'.$originalExt);
            //return 
            if(Storage::disk('local')->exists('tmp/'.$originalName.'.'.$originalExt))
                return ['success'=>true,'file'=>'media/preview/'.$originalName.'.'.$originalExt];
            return ['success'=>false,'file'=>'','msg'=>'There was an error uploading the image!'];
        } catch (Exception $ex) {
            return ['success'=>false,'file'=>'','msg'=>'There was an error uploading the image!'];
        }
    }
    /**
     * Change to real location images s3
     */
    public static function stablish_image($subfolder='',$image_url)
    {
        try { 
            //format to upload to the server :  media/preview/x.jpg
            if(preg_match('/media\/preview\//',$image_url)) 
            {
                //init
                $originalName = pathinfo($image_url, PATHINFO_FILENAME);
                $originalExt = pathinfo($image_url, PATHINFO_EXTENSION);
                $oldUrl = 'tmp/'.$originalName.'.'.$originalExt;
                if($subfolder!='')$subfolder .= '/';
                //check if file exists in local folder
                if(Storage::disk('local')->exists($oldUrl))
                {
                    //get file
                    $file = Storage::disk('local')->get($oldUrl);
                    //if file exists in the server create this like a new copy (_c)
                    while(Storage::disk('s3')->exists($subfolder.$originalName.'.'.$originalExt))
                        $originalName .= '_c';  
                    //move file to amazon s3
                    Storage::disk('s3')->put($subfolder.$originalName.'.'.$originalExt, $file, 'public');
                    //remove old file
                    Storage::disk('local')->delete($oldUrl);
                    //return url if file exists
                    if(Storage::disk('s3')->exists($subfolder.$originalName.'.'.$originalExt))
                        return '/s3/'.$subfolder.$originalName.'.'.$originalExt;
                    return '';
                }
                else
                    return '';
            }
            else 
                return $image_url;
            
        } catch (Exception $ex) {
            return '';
        }
    }
    /**
     * Upload images
     */
    public static function remove_image($image_url)
    {
        try {  
            //init
            $originalName = pathinfo($image_url, PATHINFO_FILENAME);
            $originalExt = pathinfo($image_url, PATHINFO_EXTENSION);
            //check if is in uploads (the old server)
            if(preg_match('/\/uploads\//',$image_url)) 
            {
                //it cannot delete it
                return true;
            }
            //check if is in s3 server (the new server)
            else if(preg_match('/\/s3\//',$image_url) || strpos($image_url,env('IMAGE_URL_AMAZON_SERVER')) !== false) 
            {
                $file_url = substr(strrchr(dirname($image_url,1), '/'), 1).'/'.$originalName.'.'.$originalExt;
                if(Storage::disk('s3')->exists($file_url))
                {
                    Storage::disk('s3')->delete($file_url);
                    return true;
                }
                return true;
            }
            //other url in another place
            else return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    /**
     * View images
     */
    public static function view_image($image_url)
    {
        try {  
            // change relative url s3 for real one
            if(preg_match('/^\/s3\//',$image_url) ) 
                return env('IMAGE_URL_AMAZON_SERVER').str_replace('/s3/','/',$image_url);
            // the image has full url
            else if(!preg_match('/^\/uploads\//',$image_url) ) 
                return $image_url;
            // change relative url uploads for real one
            else
                //return env('IMAGE_URL_OLDTB_SERVER').$image_url;
                return '';
        } catch (Exception $ex) {
            return $image_url;
        }
    }
}
