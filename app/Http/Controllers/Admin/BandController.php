<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Category;
use App\Http\Models\Band;
use App\Http\Models\Image;
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
    public function index($autopen=null)
    {
        try {
            //autopen modal with id or new
            if($autopen!=null && $autopen!=0)
                return redirect()->route('logout');
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
                $band->image_url = Image::view_image($band->image_url);
                return ['success'=>true,'band'=>array_merge($band->getAttributes(),['shows[]'=>$shows])];
            }
            else
            {
                $categories = [];
                $onlyerrors = 0;
                $bands = [];
                //if user has permission to view
                if(in_array('View',Auth::user()->user_type->getACLs()['BANDS']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['BANDS']['permission_scope'] == 'All')
                    {
                        if(isset($input) && isset($input['onlyerrors']) && $input['onlyerrors']==1)
                        {
                            $onlyerrors = 1;
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
                            $onlyerrors = 0;
                            //get all records        
                            $bands = DB::table('bands')
                                            ->join('categories', 'categories.id', '=' ,'bands.category_id')
                                            ->select('bands.*', 'categories.name AS category')
                                            ->orderBy('categories.name')
                                            ->get();
                        }
                        foreach ($bands as $b)
                        {
                            //check image
                            $b->image_url = Image::view_image($b->image_url);
                            //set errors
                            $b->errors = '';
                            if(empty($b->image_url))
                                $b->errors .= '<br>- No logo image.';
                            if(empty($b->short_description))
                                $b->errors .= '<br>- No short description.';
                        } 
                        $categories = Category::get_categories('-&emsp;&emsp;');
                    }  
                }
                //return view
                return view('admin.bands.index',compact('bands','categories','onlyerrors','autopen'));
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
                    if(Band::where('name','=',$input['name'])->where('id','!=',$input['id'])->count())
                        return ['success'=>false,'msg'=>'There was an error saving the band.<br>That name is already in the system.','errors'=>'name'];
                    $band = Band::find($input['id']);
                    if(preg_match('/media\/preview/',$input['image_url'])) 
                        $band->delete_image_file();
                }                    
                else
                {                    
                    if(Band::where('name','=',$input['name'])->count())
                        return ['success'=>false,'msg'=>'There was an error saving the band.<br>That name is already in the system.','errors'=>'name'];
                    $band = new Band;
                }
                //save band
                $band->category()->associate(Category::find($input['category_id']));
                $band->name = trim(strip_tags($input['name']));
                $band->short_description = trim(strip_tags($input['short_description']));
                $band->description = trim(strip_tags($input['description'],'<p><a><br>'));

                $band->youtube = strip_tags($input['youtube']);
                $band->facebook = strip_tags($input['facebook']);
                $band->twitter = strip_tags($input['twitter']);
                $band->my_space = strip_tags($input['my_space']);
                $band->flickr = strip_tags($input['flickr']);
                $band->instagram = strip_tags($input['instagram']);
                $band->soundcloud = strip_tags($input['soundcloud']);
                $band->website = strip_tags($input['website']);

                $tempSocialArray = [$band->youtube,$band->facebook,$band->twitter,$band->my_space,$band->flickr,$band->instagram,$band->soundcloud,$band->website ];
                $tempSocialAlert = "";

                foreach ( $tempSocialArray as $item) {
                    if ($item !== "") {
                        if (!filter_var($item, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
                            $tempSocialAlert .= $item . " is not a valid URL. ";
                        }
                    }
                }

                if ( $tempSocialAlert !== ""){
                    return ['success' => false, 'msg' => $tempSocialAlert];
                }

                if(!empty($input['image_url']) && preg_match('/media\/preview/',$input['image_url']))
                {
                    $band->delete_image_file();
                    $band->set_image_url($input['image_url']);
                }
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
            $errors = [];
            //delete all records   
            foreach ($input['id'] as $id)
            {
                $band = Band::find($id);
                if($band)
                {
                    $band->delete_image_file();
                    $band->show_bands()->detach();
                    $name = $band->name;
                    if(!$band->delete())
                        $errors[] = $name;
                }
            }
            if(empty($errors))
                return ['success'=>true,'msg'=>'All records deleted successfully!'];
            return ['success'=>false,'msg'=>'There was an error deleting the band(s)!<br>They might have some dependences on the following bands:<br>'.implode('<br>', $errors)];
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
