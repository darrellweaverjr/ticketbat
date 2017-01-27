<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Http\Models\Slider;
use App\Http\Models\Image;

/**
 * Manage Sliders
 *
 * @author ivan
 */
class SliderController extends Controller{
    
    public function index()
    {
        try {   
            //init
            $input = Input::all(); 
            if(isset($input) && isset($input['id']))
            {
                //get selected record 
                $slider = Slider::where('id','=',$input['id'])->first();
                if(!$slider)
                    return ['success'=>false,'msg'=>'There was an error getting the slider.<br>Maybe it is not longer in the system.'];
                return ['success'=>true,'slider'=>$slider];
            }
            else
            {      
                //get all records        
                $sliders = Slider::orderBy('sliders.n_order')->get();
                foreach ($sliders as $s)
                    $s->image_url = Image::view_image($s->image_url);
                //return view
                return view('admin.sliders.index',compact('sliders'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Sliders Index: '.$ex->getMessage());
        }
    }
    
}
