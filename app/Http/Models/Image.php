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
    //PERSONALIZED METHODS
    /**
     * Upload images
     */
    public static function upload_image($file,$input)
    {
        try {  
            //get file attributes
            $originalName = preg_replace('/\..+$/', '', $file->getClientOriginalName());  
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
            //init
            $originalName = pathinfo($image_url, PATHINFO_FILENAME);
            $originalExt = pathinfo($image_url, PATHINFO_EXTENSION);
            $oldUrl = 'tmp/'.$originalName.'.'.$originalExt;
            if($subfolder!='')$subfolder .= '/';
            //$subfolder = 'ticketbat/'.$subfolder;
            //check if file exists in local folder
            echo $subfolder.$originalName.'.'.$originalExt;
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
                    return Storage::disk('s3')->url($subfolder.$originalName.'.'.$originalExt);
                return '';
            }
            else
                return '';
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
            //check parent folder of image
            if(dirname($image_url, 1)=='/uploads')
            {
                return true;
            }
            else
            {
                if(dirname($image_url, 2)=='/ticketbat')
                {
                    $parentfolder = dirname($image_url, 2).dirname($image_url, 1).'/';
                    if(Storage::disk('s3')->exists($parentfolder.$originalName.'.'.$originalExt))
                    {
                        Storage::disk('s3')->delete($parentfolder.$originalName.'.'.$originalExt);
                    }
                    return true;
                }
                return true;
            }
            
            
            
            
            $oldUrl = 'tmp/'.$originalName.'.'.$originalExt;
            if($subfolder!='')$subfolder.='/';
            $subfolder = 'ticketbat/'.$subfolder;
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
                    return Storage::disk('s3')->url($subfolder.$originalName.'.'.$originalExt);
                return '';
            }
            else
                return '';
        } catch (Exception $ex) {
            return false;
        }
    }
}
