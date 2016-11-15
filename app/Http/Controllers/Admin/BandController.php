<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Http\Models\Category;
use App\Http\Models\Band;

/**
 * Manage Bands
 *
 * @author ivan
 */
class BandController extends Controller{
    
    /**
     * List all bands and return default view.
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
                $band = Band::find($input['id']);  
                if(!$band)
                    return ['success'=>false,'msg'=>'There was an error getting the band.<br>Maybe it is not longer in the system.'];
                $shows = [];
                foreach($band->show_bands as $s)
                    $shows[] = [$s->name,$s->pivot->n_order];
                $band->image_url = 'https://www.ticketbat.com'.$band->image_url; //$band->image_url = asset($band->image_url);
                return ['success'=>true,'band'=>array_merge($band->getAttributes(),['shows[]'=>$shows])];
            }
            else
            {
                //get all records        
                $bands = Band::orderBy('name')->get();
                $categories = Category::all();
                //return view
                return view('admin.bands.index',compact('bands','categories'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Bands Index: '.$ex->getMessage());
        }
    } 
    /**
     * Search for social media in certain url given.
     *
     * @return Array with social media urls
     */
    public function load_social_media()
    {
        try {
            $input = Input::all(); 
            return Band::load_social_media($input['url']);
        } catch (Exception $ex) {
            throw new Exception('Error Bands Load Social Media: '.$ex->getMessage());
        }
    }
}
