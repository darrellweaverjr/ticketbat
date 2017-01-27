<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Venue;
use App\Http\Models\Ticket;
use App\Http\Models\Image;
use App\Http\Models\Deal;
use App\Http\Models\ShowTime;
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
                $current = date('Y-m-d');
                //get selected record 
                $venue = DB::table('venues')
                                ->join('locations', 'locations.id', '=' ,'venues.location_id')
                                ->select('venues.*','locations.address','locations.city','locations.state','locations.zip','locations.country')
                                ->where('venues.id','=',$input['id'])->first();
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
            else
            {      
                $current = date('Y-m-d H:i:s');
                //conditions to search
                $where = [['venues.id','>',0]];
                //$where = [['images.image_type','=','Header']];
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
                $venues = DB::table('venues')
                                ->join('locations', 'locations.id', '=' ,'venues.location_id')
                                ->leftJoin('venue_images', 'venue_images.venue_id', '=' ,'venues.id')
                                ->leftJoin('images', 'venue_images.image_id', '=' ,'images.id')
                                ->select('venues.*','images.url AS image_url','locations.address','locations.city','locations.state','locations.zip','locations.country')
                                ->where($where)
                                ->orderBy('venues.name')->groupBy('venues.id')
                                ->distinct()->get();
                $restrictions = Util::getEnumValues('venues','restrictions');
                $image_types = Util::getEnumValues('images','image_type');
                $banner_types = Util::getEnumValues('banners','type');
                $video_types = Util::getEnumValues('videos','video_type');
                //return view
                return view('admin.venues.index',compact('venues','restrictions','banner_types','image_types','video_types','onlyerrors'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Venues Index: '.$ex->getMessage());
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
                    $show = Show::find($input['id']);
                    $show->updated = $current;
                    if(preg_match('/media\/preview/',$input['sponsor_logo_id'])) 
                        $show->delete_image_file();
                }                    
                else
                {                    
                    $show = new Show;
                    $show->audit_user_id = Auth::user()->id;
                    $show->created = $current;
                }
                //save show
                $show->venue_id = $input['venue_id'];
                $show->stage_id = $input['stage_id'];
                $show->category_id = $input['category_id'];
                $show->name = $input['name'];
                $show->slug = $input['slug'];
                $show->presented_by = $input['presented_by'];
                $show->sponsor = $input['sponsor'];
                $show->short_description = $input['short_description'];
                $show->description = $input['description'];
                $show->emails = $input['emails'];
                $show->accounting_email = $input['accounting_email'];
                $show->url = $input['url'];
                $show->restrictions = $input['restrictions'];
                $show->is_featured = $input['is_featured'];
                $show->cutoff_hours = $input['cutoff_hours'];
                $show->is_active = $input['is_active'];
                $show->facebook = $input['facebook'];
                $show->twitter = $input['twitter'];
                $show->youtube = $input['youtube'];
                $show->instagram = $input['instagram'];
                $show->yelpbadge = $input['yelpbadge'];
                $show->on_sale = $input['on_sale'];
                $show->printed_tickets = $input['printed_tickets'];
                $show->individual_emails = $input['individual_emails'];
                $show->manifest_emails = $input['manifest_emails'];
                $show->daily_sales_emails = $input['daily_sales_emails'];
                $show->financial_report_emails = $input['financial_report_emails'];
                if(isset($input['amex_only_start_date']) && $input['amex_only_start_date']!='' && isset($input['amex_only_end_date']) && $input['amex_only_end_date']!=''
                        && isset($input['ticket_types']) && count($input['ticket_types']))
                {
                    $show->amex_only_ticket_types = Ticket::where('show_id','=',$input['id'])->whereIn('id',$input['ticket_types']) ->distinct()->get()->implode('ticket_type',',')                            ;
                    $show->amex_only_start_date = $input['amex_only_start_date'];
                    $show->amex_only_end_date = $input['amex_only_end_date'];
                }
                else
                {
                    $show->amex_only_ticket_types = null;
                    $show->amex_only_start_date = null;
                    $show->amex_only_end_date = null;
                }
                if(preg_match('/media\/preview/',$input['sponsor_logo_id'])) 
                    $show->set_sponsor_logo_id($input['sponsor_logo_id']);
                $show->save();
                //return
                return ['success'=>true,'msg'=>'Show saved successfully!'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the show.<br>The server could not retrieve the data.'];
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
                //get show
                $show = Show::find($id);
                if($show)
                {
                    $dependences = false;
                    //showtimes
                    $showtimes = ShowTime::where('show_times.show_id','=',$show->id)->get();
                    foreach ($showtimes as $st)
                    {
                        if(!$dependences)
                        {
                            $depend = DB::table('show_times')
                                        ->leftJoin('purchases', 'purchases.show_time_id', '=', 'show_times.id')
                                        ->leftJoin('transactions', 'transactions.show_time_id', '=', 'show_times.id')
                                        ->leftJoin('consignments', 'consignments.show_time_id', '=', 'show_times.id')
                                        ->select(DB::raw('count(*) as dependences'))
                                        ->where('show_times.id','=',$st->id)->first();
                            if($depend->dependences > 1)
                            {
                                $dependences = true;
                                if($msg=='')
                                    $msg = 'The following shows have dependences (purchases, transactions and/or consignments) and the system cannot delete them:<br><br><ol style="max-height:200px;overflow:auto;text-align:left;">';
                                $msg .= '<li style="color:red;">'.$show->name.'</li>';
                            }
                        }
                    }
                    //if has no dependences delete all subtables
                    if(!$dependences)
                    {
                        //tickets(soldout_tickets,discount_tickets)
                        $tickets = Ticket::where('show_id',$show->id)->get();
                        foreach ($tickets as $t)
                        {
                            DB::table('soldout_tickets')->where('ticket_id',$t->id)->delete();
                            DB::table('discount_tickets')->where('ticket_id',$t->id)->delete();
                        }
                        $tickets = Ticket::where('show_id',$show->id)->delete();
                        //banners
                        $banners = Banner::where('parent_id',$show->id)->where('belongto','show')->delete();
                        //broker_rates
                        $broker_rates = DB::table('broker_rates')->where('show_id',$show->id)->delete();
                        //deals
                        $deals = Deal::where('show_id',$show->id)->delete();
                        //discount_shows
                        $discount_shows = DB::table('discount_shows')->where('show_id',$show->id)->delete();
                        //merchandise(merchandise_photos)
                        $merchandise = DB::table('merchandise')->where('show_id',$show->id)->get();
                        foreach ($merchandise as $m)
                            DB::table('merchandise_photos')->where('ticket_id',$m->id)->delete();
                        $merchandise = DB::table('merchandise')->where('show_id',$show->id)->delete();
                        //show_awards
                        $show_awards = DB::table('show_awards')->where('show_id',$show->id)->delete();
                        //show_bands
                        $show_bands = DB::table('show_bands')->where('show_id',$show->id)->delete();
                        //show_images(images)
                        $show_images = DB::table('show_images')->where('show_id',$show->id)->get();
                        foreach ($show_images as $i)
                        {
                            $image = Image::find($i->image_id);
                            Image::remove_image($image->url);
                            $image->delete();
                        }
                        $show_images = DB::table('show_images')->where('show_id',$show->id)->delete();
                        //show_passwords
                        $show_passwords = DB::table('show_passwords')->where('show_id',$show->id)->delete();
                        //show_reviews
                        $show_reviews = DB::table('show_reviews')->where('show_id',$show->id)->delete();
                        //show_videos(videos)
                        $show_videos = DB::table('show_videos')->where('show_id',$show->id)->get();
                        foreach ($show_videos as $v)
                            DB::table('videos')->where('id',$v->video_id)->delete();
                        $show_videos = DB::table('show_videos')->where('show_id',$show->id)->delete();
                        //show_times
                        $show_times = DB::table('show_times')->where('show_id',$show->id)->delete();
                        //try to delete final show if it has not dependences
                        if(!$show->delete())
                        {
                            if($msg1=='')
                                $msg1 = 'The following shows have problems deleting them:<br><br><ol style="max-height:200px;overflow:auto;text-align:left;">';
                            $msg1 .= '<li style="color:red;">'.$show->name.'</li>';
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
            throw new Exception('Error Shows Remove: '.$ex->getMessage());
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
