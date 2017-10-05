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
    public function index()
    {
        try {
            
        } catch (Exception $ex) {
            throw new Exception('Error Production Event Index: '.$ex->getMessage());
        }
    }
    /**
     * Get event.
     *
     * @return Method
     */
    public function event($slug)
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
                                          venues.name as location_name, shows.name, locations.*,shows.presented_by, shows.sponsor, 
                                          shows.sponsor_logo_id, venues.cutoff_text, shows.restrictions, shows.venue_id'))
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
                                ->where('show_videos.show_id',$event->show_id)->where('videos.video_type','=','Video')->get();
            //get bands
            $event->bands = DB::table('bands')
                                ->join('categories', 'bands.category_id', '=', 'categories.id')
                                ->join('show_bands', 'show_bands.band_id', '=', 'bands.id')
                                ->select(DB::raw('bands.*, categories.name AS category'))
                                ->where('show_bands.show_id',$event->show_id)->orderBy('show_bands.n_order')->get();
            foreach ($event->bands as $b)
                $b->image_url = Image::view_image($b->image_url);
            //return view
            return view('production.events.event',compact('event'));
        } catch (Exception $ex) {
            throw new Exception('Error Production Event Event: '.$ex->getMessage());
        }
    }
       
}
