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
            $file = Input::file('image');
            $input = Input::all();
            dd($input);
            return Image::upload_image($file);
        } catch (Exception $ex) {
            throw new Exception('Error Upload Image: '.$ex->getMessage());
        }
    }
    
}
