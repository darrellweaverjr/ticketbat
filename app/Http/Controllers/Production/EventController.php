<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Slider;
use App\Http\Models\Image;
use App\Http\Models\Category;
use App\Http\Models\Show;
use App\Http\Models\Util;

class EventController extends Controller
{
    /**
     * Show the default method for the event page.
     *
     * @return Method
     */
    public function index($slug)
    {
        try {
            if(empty($slug))
                return redirect()->route('index');
            //get all records
            $event = DB::table('shows')
                        ->join('venues', 'venues.id', '=', 'shows.venue_id')
                        ->join('locations', 'locations.id', '=', 'venues.location_id')
                        ->select(DB::raw('shows.id as show_id, shows.slug, shows.on_sale, shows.short_description, shows.description, shows.url, 
                                          shows.facebook, shows.twitter,shows.googleplus, shows.yelpbadge, shows.youtube, shows.instagram,
                                          venues.name as venue, shows.name, locations.*,shows.presented_by, shows.sponsor, 
                                          shows.sponsor_logo_id, venues.cutoff_text, shows.restrictions, shows.venue_id,
                                          IF(shows.restrictions!="None",shows.restrictions,venues.restrictions) AS restrictions'))
                        ->where('shows.is_active','>',0)->where('venues.is_featured','>',0)
                        ->where('shows.slug', $slug)->first();
            if(!$event)
                return redirect()->route('index');
            //get header
            $event->header = DB::table('images')
                                ->join('show_images', 'show_images.image_id', '=', 'images.id')
                                ->select(DB::raw('images.url, images.caption'))
                                ->where('show_images.show_id',$event->show_id)->where('images.image_type','=','Header')->first();
            //set header of venue if not show header
            if(!$event->header)
                $event->header = DB::table('images')
                                ->join('venue_images', 'venue_images.image_id', '=', 'images.id')
                                ->select(DB::raw('images.url, images.caption'))
                                ->where('venue_images.venue_id',$event->venue_id)->where('images.image_type','=','Header')->first();
            $event->header->url = Image::view_image($event->header->url);
            //get logo
            $event->logo = DB::table('images')
                                ->join('show_images', 'show_images.image_id', '=', 'images.id')
                                ->select(DB::raw('images.url, images.caption'))
                                ->where('show_images.show_id',$event->show_id)->where('images.image_type','=','Logo')->first();
            $event->logo->url = Image::view_image($event->logo->url);
            //get images
            $event->images = DB::table('images')
                                ->join('show_images', 'show_images.image_id', '=', 'images.id')
                                ->select(DB::raw('images.url, images.caption'))
                                ->where('show_images.show_id',$event->show_id)->where('images.image_type','=','Image')->get();
            foreach ($event->images as $i)
                $i->url = Image::view_image($i->url);
            //$event->images = $event->images->toArray();
            //get banners
            $event->banners = DB::table('banners')
                                ->select(DB::raw('banners.id, banners.url, banners.file'))
                                ->where('banners.parent_id',$event->show_id)->where('banners.belongto','=','shows')
                                ->where('banners.type','like','%Show Page%')->get();
            foreach ($event->banners as $b)
                $b->file = Image::view_image($b->file);
            //get videos
            $event->videos = DB::table('videos')
                                ->join('show_videos', 'show_videos.video_id', '=', 'videos.id')
                                ->select(DB::raw('videos.id, videos.embed_code, videos.description'))
                                ->where('show_videos.show_id',$event->show_id)/*->where('videos.video_type','=','Video')*/->get();
            foreach ($event->videos as $v)
            {
                $part1 = explode('src="',$v->embed_code);
                $part2 = explode('"',$part1[1]);
                $v->embed_code = $part2[0];
            } 
            //get bands
            $event->bands = DB::table('bands')
                                ->join('categories', 'bands.category_id', '=', 'categories.id')
                                ->join('show_bands', 'show_bands.band_id', '=', 'bands.id')
                                ->select(DB::raw('bands.*, categories.name AS category'))
                                ->where('show_bands.show_id',$event->show_id)->orderBy('show_bands.n_order')->get();
            foreach ($event->bands as $b)
                $b->image_url = Image::view_image($b->image_url);
            //get showtimes
            $event->showtimes = DB::table('show_times')
                                ->join('shows', 'show_times.show_id', '=', 'shows.id')
                                ->select(DB::raw('show_times.id, show_times.time_alternative,
                                                 DATE_FORMAT(show_times.show_time,"%Y/%m/%d %H:%i") AS show_time,
                                                 DATE_FORMAT(show_times.show_time,"%W") AS show_day,
                                                 DATE_FORMAT(show_times.show_time,"%M %D") AS show_date,
                                                 DATE_FORMAT(show_times.show_time,"%l:%i %p") AS show_hour,
                                                 show_times.slug AS ext_slug_st, shows.ext_slug AS ext_slug,
                                                 IF(NOW()>DATE_SUB(show_times.show_time,INTERVAL shows.cutoff_hours HOUR) AND NOW()<show_times.show_time, 1, 0) as presale'))
                                ->where('show_times.show_id',$event->show_id)->where('show_times.is_active','>',0)
                                ->whereRaw(DB::raw('show_times.show_time > NOW()'))
                                ->where(function($query) {
                                    if(Auth::check() && Auth::user()->user_type_id!=1)
                                        $query->whereRaw(DB::raw('DATE_SUB(show_times.show_time, INTERVAL shows.cutoff_hours HOUR) > NOW()'));
                                })
                                ->orderBy('show_times.show_time')->get();
            //return view
            return view('production.events.index',compact('event'));
        } catch (Exception $ex) {
            throw new Exception('Error Production Event Index: '.$ex->getMessage());
        }
    }
       
}
