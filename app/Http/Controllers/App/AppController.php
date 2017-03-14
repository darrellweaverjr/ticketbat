<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Image;
use App\Http\Models\ShowTime;

/**
 * Manage Users
 *
 * @author ivan
 */
class AppController extends Controller{
    
    /*
     * return arrays of all cities in json format
     */
    public function cities()
    {
        $cities = DB::table('venues')
                    ->join('venue_images', 'venue_images.venue_id', '=' ,'venues.id')
                    ->join('images', 'venue_images.image_id', '=' ,'images.id')
                    ->join('locations', 'locations.id', '=' ,'venues.location_id')
                    ->select('locations.city')
                    ->where('venues.is_featured','>',0)->where('images.image_type','=','Logo')
                    ->whereNotNull('images.url')
                    ->orderBy('locations.city')->groupBy('locations.city')
                    ->distinct()->get();
        return $cities->toJson();       
    }
    
    /*
     * return arrays of all shows (or by id, or by venue id) in json format
     */
    public function shows($id=null,$venue_id=null)
    {
        $current = date('Y-m-d');
        if(!empty($id) && is_numeric($id))
        {
            $shows = DB::table('shows')
                    ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                    ->join('locations', 'locations.id', '=' ,'venues.location_id')
                    ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                    ->leftJoin('show_images', 'show_images.show_id', '=' ,'shows.id')
                    ->leftJoin('images', 'show_images.image_id', '=' ,'images.id')
                    ->select('shows.id','shows.name','images.url','shows.description','shows.slug',
                             'locations.address','locations.city','locations.state','locations.zip','locations.lat','locations.lng')
                    ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('show_times.is_active','=',1)
                    ->where('show_times.show_time','>',\Carbon\Carbon::now())->where('shows.id','=',$id)
                    ->whereIn('images.image_type',['Header','Logo'])
                    ->orderBy('shows.name')->groupBy('shows.id')
                    ->distinct()->get(); 
            $showtimes = ShowTime::where('show_id','=',$id)->where('show_time','>',$current)->orderBy('show_time')->take(50)->get(['id','show_time']); 
            $videos = DB::table('videos')
                        ->join('show_videos', 'show_videos.video_id', '=' ,'videos.id')
                        ->join('shows', 'show_videos.show_id', '=' ,'shows.id')
                        ->select('videos.id','videos.embed_code')
                        ->where('shows.id','=',$id)
                        ->distinct()->get();
            foreach ($videos as $v)
            {
                $part1 = explode('src="',$v->embed_code);
                $part2 = explode('"',$part1[1]);
                $v->embed_code = $part2[0];
            } 
            $images = DB::table('images')
                        ->join('show_images', 'show_images.image_id', '=' ,'images.id')
                        ->join('shows', 'show_images.show_id', '=' ,'shows.id')
                        ->select('images.id','images.url','images.image_type')
                        ->where('shows.id','=',$id)
                        ->where('images.image_type','=','Image')
                        ->distinct()->get();
            foreach ($images as $i)
                $i->url = Image::view_image($i->url);
            foreach ($shows as $s)
            {
                $s->showtimes = $showtimes;
                $s->videos = $videos;
                $s->images = $images;
            }                
        }         
        else if(!empty($venue_id) && is_numeric($venue_id))
        {
            $shows = DB::table('shows')
                        ->join('show_images', 'show_images.show_id', '=' ,'shows.id')
                        ->join('images', 'show_images.image_id', '=' ,'images.id')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->select('shows.id','shows.venue_id','shows.name','images.url','locations.city')
                        ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('images.image_type','=','Logo')
                        ->where('show_times.show_time','>',\Carbon\Carbon::now())->where('show_times.is_active','=',1)
                        ->whereNotNull('images.url')->where('venues.id','=',$venue_id)
                        ->orderBy('shows.sequence ASC')->orderBy('show_times.show_time ASC')
                        ->groupBy('shows.id')
                        ->distinct()->get();
        }
        else
        {
            $shows = DB::table('shows')
                        ->join('show_images', 'show_images.show_id', '=' ,'shows.id')
                        ->join('images', 'show_images.image_id', '=' ,'images.id')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->select('shows.id','shows.venue_id','shows.name','images.url','locations.city')
                        ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('images.image_type','=','Logo')
                        ->where('show_times.show_time','>',\Carbon\Carbon::now())->where('show_times.is_active','=',1)
                        ->whereNotNull('images.url')
                        ->orderBy('shows.sequence ASC')->orderBy('show_times.show_time ASC')
                        ->groupBy('shows.id')
                        ->distinct()->get();
        }    
        foreach ($shows as $s)
            if(!empty($s->url))
                $s->url = Image::view_image($s->url);
        return $shows->toJson();     
    }
    
    /*
     * return arrays of all venues in json format
     */
    public function venues()
    {
        $venues = DB::table('venues')
                    ->join('venue_images', 'venue_images.venue_id', '=' ,'venues.id')
                    ->join('images', 'venue_images.image_id', '=' ,'images.id')
                    ->join('locations', 'locations.id', '=' ,'venues.location_id')
                    ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                    ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                    ->select('venues.id','venues.name','images.url','locations.city')
                    ->where('venues.is_featured','>',0)->where('images.image_type','=','Logo')
                    ->where('show_times.show_time','>',\Carbon\Carbon::now())
                    ->whereNotNull('images.url')
                    ->orderBy('venues.name')->groupBy('venues.id')
                    ->distinct()->get();
        foreach ($venues as $v)
            $v->url = Image::view_image($v->url);
        return $venues->toJson();     
    }
    
}
