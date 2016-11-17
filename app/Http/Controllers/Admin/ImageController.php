<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Http\Models\Image;
/**
 * Manage utilities for the site
 *
 * @author ivan
 */
class ImageController extends Controller{
    
    /**
     * Empty method.
     *
     * @return view
     */
    public function index()
    {
        
    }    
    /**
     * Upload images
     */
    public function upload_image()
    {
        try {
            if(Input::hasFile('image'))
            {
                $file = Input::file('image');
                $input = Input::all();
                return Image::upload_image($file,$input);
            }
            else 
                return ['success'=>false,'file'=>'','msg'=>'No image has been recived by the server.'];
        } catch (Exception $ex) {
            throw new Exception('Error Upload Image: '.$ex->getMessage());
        }
    }
    
}
