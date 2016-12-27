<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
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
                // change relative url uploads for real one
                if(preg_match('/\/uploads\//',$band->image_url)) 
                    $band->image_url = env('IMAGE_URL_OLDTB_SERVER').$band->image_url;
                // change relative url s3 for real one
                if(preg_match('/\/s3\//',$band->image_url)) 
                    $band->image_url = env('IMAGE_URL_AMAZON_SERVER').str_replace('/s3/','/',$band->image_url);
                return ['success'=>true,'band'=>array_merge($band->getAttributes(),['shows[]'=>$shows])];
            }
            else
            {
                if(isset($input) && isset($input['onlyerrors']) && $input['onlyerrors']==1)
                {
                    $onlyerrors = 'checked';
                    //get all records with errors    
                    $bands = DB::table('bands')
                                    ->join('categories', 'categories.id', '=' ,'bands.category_id')
                                    ->select('bands.*', 'categories.name AS category')
                                    ->whereNull('bands.image_url')
                                    ->orWhereNull('bands.short_description')
                                    ->orderBy('categories.name')
                                    ->get();
                }
                else
                {
                    $onlyerrors = '';
                    //get all records        
                    $bands = DB::table('bands')
                                    ->join('categories', 'categories.id', '=' ,'bands.category_id')
                                    ->select('bands.*', 'categories.name AS category')
                                    ->orderBy('categories.name')
                                    ->get();
                }
                $categories = Category::all();
                //return view
                return view('admin.bands.index',compact('bands','categories','onlyerrors'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Bands Index: '.$ex->getMessage());
        }
    } 
    /**
     * Save new or updated band.
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
                    $band = Band::find($input['id']);
                    $band->delete_image_file();
                }                    
                else
                {                    
                    $band = new Band;
                }
                //save band
                $band->category()->associate(Category::find($input['category_id']));
                $band->name = $input['name'];
                $band->short_description = $input['short_description'];
                $band->description = $input['description'];
                $band->youtube = $input['youtube'];
                $band->facebook = $input['facebook'];
                $band->twitter = $input['twitter'];
                $band->my_space = $input['my_space'];
                $band->flickr = $input['flickr'];
                $band->instagram = $input['instagram'];
                $band->soundcloud = $input['soundcloud'];
                $band->website = $input['website'];
                $band->set_image_url($input['image_url']);
                $band->save();
                //return
                return ['success'=>true,'msg'=>'Band saved successfully!'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the band.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Bands Save: '.$ex->getMessage());
        }
    }
    /**
     * Remove bands.
     *
     * @void
     */
    public function remove()
    {
        try {
            //init
            $input = Input::all();
            //delete all records   
            foreach ($input['id'] as $id)
            {
                Band::find($id)->delete_image_file();
                if(!Band::destroy($id))
                    return ['success'=>false,'msg'=>'There was an error deleting the band(s)!<br>They might have some dependences.'];
            }
            return ['success'=>true,'msg'=>'All records deleted successfully!'];
        } catch (Exception $ex) {
            throw new Exception('Error Bands Remove: '.$ex->getMessage());
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
