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
    
    /**
     * List all sliders and return default view.
     *
     * @return view
     */
    public function index()
    {
        try {   
            //init
            $input = Input::all(); 
            if(isset($input) && isset($input['id']))
            {
                //get selected record 
                $slider = Slider::where('id','=',$input['id'])->first();
                $slider->image_url = Image::view_image($slider->image_url);
                if(!$slider)
                    return ['success'=>false,'msg'=>'There was an error getting the slider.<br>Maybe it is not longer in the system.'];
                return ['success'=>true,'slider'=>$slider];
            }
            else
            {      
                //if user has permission to view
                $sliders = [];
                if(in_array('View',Auth::user()->user_type->getACLs()['SLIDERS']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['SLIDERS']['permission_scope'] == 'All')
                    {
                        $sliders = Slider::orderBy('n_order')->get();
                        foreach ($sliders as $s)
                            $s->image_url = Image::view_image($s->image_url);
                    }
                }
                //return view
                return view('admin.sliders.index',compact('sliders'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Sliders Index: '.$ex->getMessage());
        }
    }
    /**
     * Save new or updated slider.
     *
     * @void
     */
    public function save()
    {
        try {
            //init
            $input = Input::all(); 
            //save all record      
            if($input)
            {
                if(isset($input['id']) && $input['id'])
                {
                    $slider = Slider::find($input['id']);
                    if($slider->n_order != $input['n_order'])
                    {
                        $slider_from = Slider::where('n_order','=',$input['n_order'])->first();
                        if($slider_from)
                        {
                            $slider_from->n_order = $slider->n_order;
                            $slider_from->save();
                        }
                        $slider->n_order = $input['n_order'];
                    }
                }                    
                else
                {                    
                    $slider = new Slider;
                    $slider->n_order = Slider::count() + 1;
                }
                //save show
                if(preg_match('/media\/preview/',$input['image_url'])) 
                    $slider->set_image_url($input['image_url']);
                $slider->slug = $input['slug'];
                $slider->alt = $input['alt'];
                $slider->save();
                //return
                return ['success'=>true,'msg'=>'Slider saved successfully!'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the show.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Sliders Save: '.$ex->getMessage());
        }
    }
    /**
     * Remove sliders.
     *
     * @void
     */
    public function remove()
    {
        try {
            //init
            $input = Input::all();
            $msg = ''; $new_order = 1;
            //delete all records   
            foreach ($input['id'] as $id)
            {
                //get slider
                $slider = Slider::find($id);
                if($slider)
                {
                    $order = $slider->n_order;
                    Image::remove_image($slider->image_url);
                    if(!$slider->delete())
                    {
                        if($msg=='')
                            $msg = 'The following sliders have problems deleting them:<br><br><ol style="max-height:200px;overflow:auto;text-align:left;">';
                        $msg .= '<li style="color:red;">'.$slider->n_order.' - '.$slider->alt.'</li>';
                    }
                }
            }
            //organize
            $sliders = Slider::orderBy('n_order')->get();
            foreach ($sliders as $s)
            {
                $s->n_order = $new_order++;
                $s->save();
            }
            //return
            if($msg != '')
            {
                if($msg!='') $msg .= '</ol><br> Please, contact an administrator if you want a force delete.';
                return ['success'=>false,'msg'=>$msg];
            }  
            return ['success'=>true,'msg'=>'All records deleted successfully!'];
        } catch (Exception $ex) {
            throw new Exception('Error Sliders Remove: '.$ex->getMessage());
        }
    }
    
}
