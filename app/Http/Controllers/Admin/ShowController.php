<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Category;
use App\Http\Models\Venue;
use App\Http\Models\Ticket;
use App\Http\Models\ShowTime;
use App\Http\Models\Banner;
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
                $show = Show::find($input['id']);  
                if(!$show)
                    return ['success'=>false,'msg'=>'There was an error getting the show.<br>Maybe it is not longer in the system.'];
                //search sub elements
                $tickets = Ticket::where('show_id','=',$show->id)->distinct()->get();
                $tt_inactive = DB::table('ticket_types_inactive')->select('ticket_types_inactive.*')->distinct()->implode('ticket_types_inactive.ticket_type',',')                            ;
                $show_times = ShowTime::where('show_id','=',$show->id)->distinct()->get();
                $passwords = DB::table('show_passwords')->select('show_passwords.*')
                                ->where('show_passwords.show_id','=',$show->id)->distinct()->get();
                $bands = DB::table('bands')->join('show_bands', 'show_bands.band_id', '=' ,'bands.id')
                                ->select('bands.*','show_bands.n_order')->where('show_bands.show_id','=',$show->id)->distinct()->get();
                $images = DB::table('images')->join('show_images', 'show_images.image_id', '=' ,'images.id')
                                ->select('images.*')->where('show_images.show_id','=',$show->id)->distinct()->get();
                $banners = Banner::where('parent_id','=',$show->id)->where('belongto','=','show')->distinct()->get();
                $videos = DB::table('videos')->join('show_videos', 'show_videos.video_id', '=' ,'videos.id')
                                ->select('videos.*')->where('show_videos.show_id','=',$show->id)->distinct()->get();
                return ['success'=>true,'show'=>array_merge($show->getAttributes()),'tickets'=>$tickets,'ticket_types_inactive'=>$tt_inactive,'show_times'=>$show_times,'passwords'=>$passwords,'bands'=>$bands,'images'=>$images,'banners'=>$banners,'videos'=>$videos];
            }
            if(isset($input) && isset($input['venue_id']))
            {
                //search sub elements
                $stages = Stage::where('venue_id','=',$input['venue_id'])->distinct()->get();
                return ['success'=>true,'stages'=>$stages];
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
                $banner_types = [];//Util::getEnumValues('banners','type');
                //return view
                return view('admin.shows.index',compact('shows','categories','venues','stages','restrictions','banner_types','venue','showtime','status','onlyerrors'));
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
    
    
    /**
     * Get, Edit, Remove passwords for show
     *
     * @return view
     */
    public function passwords()
    {
        try {   
            //init
            $input = Input::all(); 
            //get
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $passwords = DB::table('show_passwords')->select('show_passwords.*')
                                ->where('show_passwords.id','=',$input['id'])->distinct()->first();
                $tickets = Ticket::where('show_id','=',$passwords->show_id)->whereIn('ticket_type', explode(',',$passwords->ticket_types)) ->distinct()->pluck ('id')                            ;
                $passwords->ticket_types = $tickets;
                return ['success'=>true,'password'=>$passwords];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                //search sub elements
                $stages = Stage::where('venue_id','=',$input['venue_id'])->distinct()->get();
                return ['success'=>true,'stages'=>$stages];
            }
            //save
            else if(isset($input))
            {
                $tt = Ticket::where('show_id','=',$input['show_id'])->whereIn('id',$input['ticket_types']) ->distinct()->get()->implode('ticket_type',',')                            ;
                $show_password = ['show_id'=>$input['show_id'],'start_date'=>$input['start_date'],'end_date'=>$input['end_date'],'password'=>$input['password'],'ticket_types'=>$tt];
               
                //update
                if(isset($input['id']) && $input['id'])
                    $password = DB::table('show_passwords')->where('id','=',$input['id'])->update($show_password);
                //add
                else
                    $password = DB::table('show_passwords')->insert($show_password);
                if($password)
                    return ['success'=>true,'password'=>$password];
                else
                    return ['success'=>false,'msg'=>'There was an error. Invalid Option.<br>The server could not retrieve the data.'];
            }
            else
                return ['success'=>false,'msg'=>'There was an error saving the password.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Shows Index: '.$ex->getMessage());
        }
    } 
    
}
