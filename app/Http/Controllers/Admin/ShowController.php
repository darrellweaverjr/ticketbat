<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Category;
use App\Http\Models\Venue;
use App\Http\Models\Show;
use App\Http\Models\Stage;
use App\Http\Models\Util;
use App\Http\Models\Band;

/**
 * Manage Shows
 *
 * @author ivan
 */
class ShowController extends Controller{
    
    /**
     * List all shows and return default view.
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
                $current = date('Y-m-d H:i:s');
                //conditions to search
                $where = [['images.image_type','=','Header']];
                //search venue
                if(isset($input) && isset($input['venue']))
                {
                    $venue = $input['venue'];
                    if($venue != '')
                        $where[] = ['venues.id','=',$venue];
                }
                else
                    $venue = '';
                //search showtime
                if(isset($input) && isset($input['showtime']))
                {
                    $showtime = $input['showtime'];
                    if($showtime == 'P')
                        $where[] = ['show_times.show_time','<',$current];
                    if($showtime == 'U')
                        $where[] = ['show_times.show_time','>',$current];
                }
                else
                    $showtime = 'A';
                //search status
                if(isset($input) && isset($input['status']))
                    $status = $input['status'];
                else
                    $status = 1;
                $where[] = ['shows.is_active','=',$status];    
                //search with error
                if(isset($input) && isset($input['onlyerrors']))
                {
                    $onlyerrors = $input['onlyerrors'];
                     if($onlyerrors == 1)
                     {
                         
                     }
                }
                else
                    $onlyerrors = 0;
                //get all records        
                $shows = DB::table('shows')
                                ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                                ->join('show_times', 'show_times.show_id', '=' ,'shows.id')
                                ->join('categories', 'categories.id', '=' ,'shows.category_id')
                                ->join('show_images', 'show_images.show_id', '=' ,'shows.id')
                                ->join('images', 'show_images.image_id', '=' ,'images.id')
                                ->join('stages', 'stages.id', '=' ,'shows.stage_id')
                                ->select('shows.*','categories.name AS category','images.url AS image_url','venues.name AS venue_name','stages.name AS stage_name')
                                ->where($where)
                                ->orderBy('shows.name')
                                ->distinct()->get();
                $categories = Category::all();
                $venues = Venue::all();
                $stages = Stage::all();
                $restrictions = Util::getEnumValues('shows','restrictions');
                //return view
                return view('admin.shows.index',compact('shows','categories','venues','stages','restrictions','venue','showtime','status','onlyerrors'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Shows Index: '.$ex->getMessage());
        }
    } 
    /**
     * Save new or updated show or subtable related with show.
     *
     * @void
     */
    public function save($subtable=null)
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
     * Get slug for show.
     *
     * @void
     */
    public function slug()
    {
        try {
            //init
            $input = Input::all(); 
            //get all record      
            if($input && isset($input['name']) && isset($input['venue_id']))
                return Util::generate_slug($input['name'], $input['venue_id']);
            return '';
        } catch (Exception $ex) {
            return '';
        }
    }
    
}
