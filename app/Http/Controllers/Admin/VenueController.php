<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Venue;
use App\Http\Models\Location;
use App\Http\Models\Image;
use App\Http\Models\Banner;
use App\Http\Models\Show;
use App\Http\Models\Stage;
use App\Http\Models\Video;
use App\Http\Models\Util;

/**
 * Manage Venues
 *
 * @author ivan
 */
class VenueController extends Controller{
    
    /**
     * List all Venues and return default view.
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
                return $this->get($input['id']);
            }
            else
            {      
                $restrictions = [];
                $image_types = [];
                $banner_types = [];
                $video_types = [];
                $onlyerrors = 0;
                $venues = [];
                //if user has permission to view
                if(in_array('View',Auth::user()->user_type->getACLs()['VENUES']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['VENUES']['permission_scope'] != 'All')
                    {
                        if(isset($input) && isset($input['onlyerrors']) && $input['onlyerrors']==1)
                        {
                            $onlyerrors = 1;
                            //get all records        
                            $venues = DB::table('venues')
                                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                                        ->leftJoin('venue_images', 'venue_images.venue_id', '=' ,'venues.id')
                                        ->leftJoin('images', 'venue_images.image_id', '=' ,'images.id')
                                        ->leftJoin('stages', 'stages.venue_id', '=' ,'venues.id')
                                        ->select('venues.id','venues.name','venues.slug','venues.description','venues.is_featured',
                                                 'venues.facebook','venues.twitter','venues.googleplus','venues.yelpbadge','venues.youtube','venues.instagram',
                                                 'images.url AS image_url',
                                                 'locations.address','locations.city','locations.state','locations.zip','locations.country')
                                        ->where('venues.audit_user_id','=',Auth::user()->id)
                                        ->whereNull('stages.id')
                                        ->orWhereNull('images.url')
                                        ->orderBy('venues.name')->groupBy('venues.id')
                                        ->distinct()->get();
                        }
                        else
                        {
                            $onlyerrors = 0;
                            //get all records        
                            $venues = DB::table('venues')
                                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                                        ->leftJoin('venue_images', 'venue_images.venue_id', '=' ,'venues.id')
                                        ->leftJoin('images', 'venue_images.image_id', '=' ,'images.id')
                                        ->select('venues.id','venues.name','venues.slug','venues.description','venues.is_featured',
                                                 'venues.facebook','venues.twitter','venues.googleplus','venues.yelpbadge','venues.youtube','venues.instagram',
                                                 'images.url AS image_url',
                                                 'locations.address','locations.city','locations.state','locations.zip','locations.country')
                                        ->where('venues.audit_user_id','=',Auth::user()->id)
                                        ->where(function ($query) {
                                            $query->where('images.image_type','Logo')
                                                  ->orWhereNull('images.url');
                                        })
                                        ->orderBy('venues.name')->groupBy('venues.id')
                                        ->distinct()->get();
                        }
                    }  //all elements
                    else 
                    {
                        if(isset($input) && isset($input['onlyerrors']) && $input['onlyerrors']==1)
                        {
                            $onlyerrors = 1;
                            //get all records        
                            $venues = DB::table('venues')
                                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                                        ->leftJoin('venue_images', 'venue_images.venue_id', '=' ,'venues.id')
                                        ->leftJoin('images', 'venue_images.image_id', '=' ,'images.id')
                                        ->leftJoin('stages', 'stages.venue_id', '=' ,'venues.id')
                                        ->select('venues.id','venues.name','venues.slug','venues.description','venues.is_featured',
                                                 'venues.facebook','venues.twitter','venues.googleplus','venues.yelpbadge','venues.youtube','venues.instagram',
                                                 'images.url AS image_url',
                                                 'locations.address','locations.city','locations.state','locations.zip','locations.country')
                                        ->whereNull('stages.id')
                                        ->orWhereNull('images.url')
                                        ->orderBy('venues.name')->groupBy('venues.id')
                                        ->distinct()->get();
                        }
                        else
                        {
                            $onlyerrors = 0;
                            //get all records        
                            $venues = DB::table('venues')
                                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                                        ->leftJoin('venue_images', 'venue_images.venue_id', '=' ,'venues.id')
                                        ->leftJoin('images', 'venue_images.image_id', '=' ,'images.id')
                                        ->select('venues.id','venues.name','venues.slug','venues.description','venues.is_featured',
                                                 'venues.facebook','venues.twitter','venues.googleplus','venues.yelpbadge','venues.youtube','venues.instagram',
                                                 'images.url AS image_url',
                                                 'locations.address','locations.city','locations.state','locations.zip','locations.country')
                                        ->where(function ($query) {
                                            $query->where('images.image_type','Logo')
                                                  ->orWhereNull('images.url');
                                        })
                                        ->orderBy('venues.name')->groupBy('venues.id')
                                        ->distinct()->get();
                        }
                    }  
                    //other enum
                    $restrictions = Util::getEnumValues('venues','restrictions');
                    $image_types = Util::getEnumValues('images','image_type');
                    $banner_types = Util::getEnumValues('banners','type');
                    $video_types = Util::getEnumValues('videos','video_type');
                }
                //return view
                return view('admin.venues.index',compact('venues','restrictions','banner_types','image_types','video_types','onlyerrors'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Venues Index: '.$ex->getMessage());
        }
    } 
    /**
     * Get venue by id.
     *
     * @return view
     */
    private function get($id)
    {
        try {   
            //init
            if(!empty($id) && is_numeric($id))
            {
                $current = date('Y-m-d');
                //get selected record 
                $venue = DB::table('venues')
                                ->join('locations', 'locations.id', '=' ,'venues.location_id')
                                ->select('venues.*','locations.address','locations.city','locations.state','locations.zip','locations.country')
                                ->where('venues.id','=',$id)->first();
                if(!$venue)
                    return ['success'=>false,'msg'=>'There was an error getting the venue.<br>Maybe it is not longer in the system.'];
                //search sub elements
                $stages = Stage::where('venue_id',$venue->id)->get();
                foreach ($stages as $s)
                    $s->image_url = Image::view_image($s->image_url);
                $images = DB::table('images')->join('venue_images', 'venue_images.image_id', '=' ,'images.id')
                                ->select('images.*')->where('venue_images.venue_id','=',$venue->id)->distinct()->get();
                foreach ($images as $i)
                    $i->url = Image::view_image($i->url);
                $banners = Banner::where('parent_id','=',$venue->id)->where('belongto','=','venue')->distinct()->get();
                foreach ($banners as $b)
                    $b->file = Image::view_image($b->file);
                $videos = DB::table('videos')->join('venue_videos', 'venue_videos.video_id', '=' ,'videos.id')
                                ->select('videos.*')->where('venue_videos.venue_id','=',$venue->id)->distinct()->get();
                return ['success'=>true,'venue'=>$venue,'stages'=>$stages,'images'=>$images,'banners'=>$banners,'videos'=>$videos];
            }
        } catch (Exception $ex) {
            throw new Exception('Error Venues Get: '.$ex->getMessage());
        }
    } 
    /**
     * Save new or updated Venues or subtable related with Venues.
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
                $current = date('Y-m-d H:i:s');
                if(isset($input['id']) && $input['id'])
                {
                    $venue = Venue::find($input['id']);
                    $venue->updated = $current;
                    $location = $venue->location;
                    $location->updated = $current;
                }                    
                else
                {                    
                    $venue = new Venue;
                    $venue->audit_user_id = Auth::user()->id;
                    $venue->created = $current;
                    $location = new Location;
                    $location->created = $current;
                    $location->updated = $current;
                }
                //save location
                $location->address = $input['address'];
                $location->city = $input['city'];
                $location->state = strtoupper($input['state']);
                $location->zip = $input['zip'];
                $location->set_lng_lat();
                $location->save();
                //save venue
                $venue->location()->associate($location);
                $venue->name = $input['name'];
                $venue->slug = $input['slug'];
                $venue->accounting_email = $input['accounting_email'];
                $venue->weekly_email = $input['weekly_email'];
                $venue->description = strip_tags($input['description'],'<p><a><br>');
                $venue->ticket_info = $input['ticket_info'];
                $venue->is_featured = $input['is_featured'];
                $venue->restrictions = $input['restrictions'];
                $venue->facebook = $input['facebook'];
                $venue->twitter = $input['twitter'];
                $venue->googleplus = $input['googleplus'];
                $venue->yelpbadge = $input['yelpbadge'];
                $venue->youtube = $input['youtube'];
                $venue->instagram = $input['instagram'];
                $venue->cutoff_text = (!empty($input['cutoff_text']))? $input['cutoff_text'] : null;
                $venue->daily_sales_emails = $input['daily_sales_emails'];
                $venue->financial_report_emails = $input['financial_report_emails'];
                $venue->weekly_sales_emails = $input['weekly_sales_emails'];
                $venue->enable_weekly_promos = $input['enable_weekly_promos'];
                $venue->default_processing_fee = $input['default_processing_fee'];
                $venue->default_percent_pfee = $input['default_percent_pfee'];
                $venue->default_fixed_commission = $input['default_fixed_commission'];
                $venue->default_percent_commission = $input['default_percent_commission'];
                $venue->save();
                //return
                if(isset($input['id']) && $input['id'])
                    return ['success'=>true,'msg'=>'Venue saved successfully!'];
                return $this->get($venue->id);
            }
            return ['success'=>false,'msg'=>'There was an error saving the venue.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Venues Save: '.$ex->getMessage());
        }
    }
    /**
     * Get slug for Venue.
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
     * Remove Venues.
     *
     * @void
     */
    public function remove()
    {
        try {
            //init
            $input = Input::all();
            $msg = $msg1 = ''; 
            //delete all records   
            foreach ($input['id'] as $id)
            {
                //get venue
                $venue = Venue::find($id);
                if($venue)
                {
                    $dependences = false;
                    //shows
                    $shows = Show::where('shows.venue_id','=',$venue->id)->count();
                    if($shows)
                    {
                        $dependences = true; 
                        if($msg=='')
                            $msg = 'The following venues have dependences (shows) and the system cannot delete them:<br><br><ol style="max-height:200px;overflow:auto;text-align:left;">';
                        $msg .= '<li style="color:red;">'.$venue->name.'</li>';
                    }
                    //if has no dependences delete all subtables
                    if(!$dependences)
                    {
                        //banners
                        $banners = Banner::where('parent_id',$venue->id)->where('belongto','venue')->delete();
                        //stages
                        $stages = Stage::where('venue_id',$venue->id)->get();
                        foreach ($stages as $s)
                            Image::remove_image($s->image_url);
                        $stages = Stage::where('venue_id',$venue->id)->delete();
                        //venue_checks
                        $venue_checks = DB::table('venue_checks')->where('venue_id',$venue->id)->delete();
                        //venue_images(images)
                        $venue_images = DB::table('venue_images')->where('venue_id',$venue->id)->get();
                        foreach ($venue_images as $i)
                        {
                            $image = Image::find($i->image_id);
                            Image::remove_image($image->url);
                            $image->delete();
                        }
                        $venue_images = DB::table('venue_images')->where('venue_id',$venue->id)->delete();
                        //venue_videos(videos)
                        $venue_videos = DB::table('venue_videos')->where('venue_id',$venue->id)->get();
                        foreach ($venue_videos as $v)
                            DB::table('videos')->where('id',$v->video_id)->delete();
                        $venue_videos = DB::table('venue_videos')->where('venue_id',$venue->id)->delete();
                        //try to delete final show if it has not dependences
                        if(!$venue->delete())
                        {
                            if($msg1=='')
                                $msg1 = 'The following venues have problems deleting them:<br><br><ol style="max-height:200px;overflow:auto;text-align:left;">';
                            $msg1 .= '<li style="color:red;">'.$venue->name.'</li>';
                        } 
                    }
                }
            }
            if($msg != '' || $msg1 != '')
            {
                if($msg!='') $msg .= '</ol><br> Please, contact an administrator if you want a force delete.';
                if($msg1!='') $msg1 .= '</ol><br> Please, contact an administrator.';
                return ['success'=>false,'msg'=>$msg.$msg1];
            }  
            return ['success'=>true,'msg'=>'All records deleted successfully!'];
        } catch (Exception $ex) {
            throw new Exception('Error Venues Remove: '.$ex->getMessage());
        }
    }
    /**
     * Get, Edit, Remove stages for Venues
     *
     * @return view
     */
    public function stages()
    {
        try {   
            //init
            $input = Input::all();
            $current = date('Y-m-d H:i:s');
            //update
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $stage = Stage::find($input['id']);
                if($stage)
                {
                    if(preg_match('/media\/preview/',$input['image_url'])) 
                        $stage->set_image_url($input['image_url']);
                    $stage->name = $input['name'];
                    $stage->description = $input['description'];
                    $stage->updated = $current;
                    $stage->save();
                    $stage->image_url = Image::view_image($stage->image_url);
                    return ['success'=>true,'action'=>0,'stage'=>$stage];
                }
                return ['success'=>false,'msg'=>'There was an error updating the stage.<br>The server could not retrieve the data.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                $stage = Stage::find($input['id']);
                if($stage)
                {
                    $dependences = DB::table('shows')->where('stage_id',$stage->id)->count()                            ;
                    if($dependences)
                        return ['success'=>false,'msg'=>'There was an error deleting the stage.<br>It has some dependencies (shows).'];
                    Image::remove_image($stage->image_url);
                    $stage->delete();
                    return ['success'=>true,'action'=>-1];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the stage.<br>The server could not retrieve the data.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
                $stage = new Stage;
                $stage->created = $current;
                $stage->set_image_url($input['image_url']);
                $stage->audit_user_id = Auth::user()->id;
                $stage->name = $input['name'];
                $stage->venue_id = $input['venue_id'];
                $stage->description = $input['description'];
                $stage->updated = $current;
                $stage->save();
                if($stage)
                {
                    $stage->image_url = Image::view_image($stage->image_url);
                    return ['success'=>true,'action'=>1,'stage'=>$stage];
                } 
                return ['success'=>false,'msg'=>'There was an error adding the stage.<br>The server could not retrieve the data.'];
            }
            //get
            else if(isset($input) && isset($input['id']))
            {
                $stage = Stage::find($input['id']);
                if($stage)
                {   
                    $stage->image_url = Image::view_image($stage->image_url);
                    return ['success'=>true,'stage'=>$stage];
                }  
                return ['success'=>false,'msg'=>'There was an error getting the stage.<br>The server could not retrieve the data.'];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error VenueStages Index: '.$ex->getMessage());
        }
    } 
    /**
     * Get, Edit, Remove images for Venues
     *
     * @return view
     */
    public function images()
    {
        try {   
            //init
            $input = Input::all();
            $current = date('Y-m-d H:i:s');
            //update
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $image = Image::find($input['id']);
                if($image)
                {
                    $image->image_type = $input['image_type'];
                    $image->caption = ($input['caption']!='')? $input['caption'] : null;
                    $image->updated = $current;
                    $image->save();
                    $image->url = Image::view_image($image->url);
                    return ['success'=>true,'action'=>0,'image'=>$image];
                }
                return ['success'=>false,'msg'=>'There was an error updating the image.<br>The server could not retrieve the data.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                $image = Image::find($input['id']);
                if($image)
                {
                    DB::table('venue_images')->where('venue_id',$input['venue_id'])->where('image_id',$image->id)->delete()                            ;
                    $image->delete_image_file();
                    $image->delete();
                    return ['success'=>true,'action'=>-1];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the image.<br>The server could not retrieve the data.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
                $image = new Image;
                $image->created = $current;
                if(preg_match('/media\/preview/',$input['url'])) 
                    $image->set_url($input['url']);
                $image->image_type = $input['image_type'];
                $image->caption = ($input['caption']!='')? $input['caption'] : null;
                $image->save();
                if($image)
                {
                    DB::table('venue_images')->insert(['venue_id'=>$input['venue_id'],'image_id'=>$image->id]);
                    $image->url = Image::view_image($image->url);
                    return ['success'=>true,'action'=>1,'image'=>$image];
                } 
                return ['success'=>false,'msg'=>'There was an error adding the image.<br>The server could not retrieve the data.'];
            }
            //get
            else if(isset($input) && isset($input['id']))
            {
                $image = Image::find($input['id']);
                if($image)
                {   
                    $image->url = Image::view_image($image->url);
                    return ['success'=>true,'image'=>$image];
                }  
                return ['success'=>false,'msg'=>'There was an error getting the image.<br>The server could not retrieve the data.'];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error VenueImages Index: '.$ex->getMessage());
        }
    } 
    /**
     * Get, Edit, Remove banners for Venues
     *
     * @return view
     */
    public function banners()
    {
        try {   
            //init
            $input = Input::all();
            //update
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $banner = Banner::find($input['id']);
                if($banner)
                {
                    $banner->url = $input['url'];
                    $banner->type = (isset($input['type']) && count($input['type']))? implode($input['type'],',') : null;
                    $banner->save();
                    $banner->file = Image::view_image($banner->file);
                    return ['success'=>true,'action'=>0,'banner'=>$banner];
                }
                return ['success'=>false,'msg'=>'There was an error updating the banner.<br>The server could not retrieve the data.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                $banner = Banner::find($input['id']);
                if($banner)
                {
                    $banner->delete_image_file();
                    $banner->delete();
                    return ['success'=>true,'action'=>-1];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the banner.<br>The server could not retrieve the data.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
                $banner = new Banner;
                if(preg_match('/media\/preview/',$input['file'])) 
                    $banner->set_file($input['file']);
                $banner->type = (isset($input['type']) && count($input['type']))? implode($input['type'],',') : null;
                $banner->url = $input['url'];
                $banner->parent_id = $input['parent_id'];
                $banner->belongto = 'venue';
                $banner->save();
                if($banner)
                {
                    $banner->file = Image::view_image($banner->file);
                    return ['success'=>true,'action'=>1,'banner'=>$banner];
                } 
                return ['success'=>false,'msg'=>'There was an error adding the banner.<br>The server could not retrieve the data.'];
            }
            //get
            else if(isset($input) && isset($input['id']))
            {
                $banner = Banner::find($input['id']);
                if($banner)
                {   
                    $banner->file = Image::view_image($banner->file);
                    return ['success'=>true,'banner'=>$banner];
                }  
                return ['success'=>false,'msg'=>'There was an error getting the banner.<br>The server could not retrieve the data.'];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error VenueBanners Index: '.$ex->getMessage());
        }
    } 
    /**
     * Get, Edit, Remove videos for Venues
     *
     * @return view
     */
    public function videos()
    {
        try {   
            //init
            $input = Input::all();
            $current = date('Y-m-d H:i:s');
            //update
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $video = Video::find($input['id']);
                if($video)
                {
                    $video->video_type = $input['video_type'];
                    $video->embed_code = $input['embed_code'];
                    $video->description = ($input['description']!='')? $input['description'] : null;
                    $video->updated = $current;
                    $video->save();
                    return ['success'=>true,'action'=>0,'video'=>$video];
                }
                return ['success'=>false,'msg'=>'There was an error updating the video.<br>The server could not retrieve the data.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                $video = Video::find($input['id']);
                if($video)
                {
                    DB::table('venue_videos')->where('venue_id',$input['venue_id'])->where('video_id',$video->id)->delete()                            ;
                    $video->delete();
                    return ['success'=>true,'action'=>-1];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the video.<br>The server could not retrieve the data.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
                $video = new Video;
                $video->created = $current;
                $video->video_type = $input['video_type'];
                $video->embed_code = $input['embed_code'];
                $video->description = ($input['description']!='')? $input['description'] : null;
                $video->audit_user_id = Auth::user()->id;
                $video->save();
                if($video)
                {
                    DB::table('venue_videos')->insert(['venue_id'=>$input['venue_id'],'video_id'=>$video->id]);
                    return ['success'=>true,'action'=>1,'video'=>$video];
                } 
                return ['success'=>false,'msg'=>'There was an error adding the video.<br>The server could not retrieve the data.'];
            }
            //get
            else if(isset($input) && isset($input['id']))
            {
                $video = Video::find($input['id']);
                if($video)
                    return ['success'=>true,'video'=>$video];
                return ['success'=>false,'msg'=>'There was an error getting the video.<br>The server could not retrieve the data.'];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error VenueVideos Index: '.$ex->getMessage());
        }
    } 
    
}
