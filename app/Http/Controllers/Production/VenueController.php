<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Image;

class VenueController extends Controller
{
    /*
     * return cutoff_date for checking the showtime
     */
    public function cutoff_date()
    {
        return 'DATE_FORMAT(show_times.show_time + INTERVAL 1 DAY,"%Y-%m-%d 04:00:00")';
    }  
    
    /**
     * Show the default method for the event page.
     *
     * @return Method
     */
    public function index()
    {
        try {
            //get all records
            $venues = [];
            $_venues = DB::table('venues')
                        ->join('locations', 'locations.id', '=', 'venues.location_id')
                        ->join('venue_images', 'venue_images.venue_id', '=' ,'venues.id')
                        ->join('images', 'venue_images.image_id', '=' ,'images.id')
                        ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                        ->select(DB::raw('venues.id as venue_id, venues.slug, venues.description, venues.name,
                                          venues.facebook, venues.twitter, venues.googleplus, venues.yelpbadge, venues.youtube, venues.instagram,
                                          locations.*, images.url, images.caption'))
                        ->where('venues.is_featured','>',0)->where('shows.is_active','>',0)->where('shows.is_featured','>',0)
                        ->where('show_times.is_active','>',0)
                        ->where(DB::raw($this->cutoff_date()),'>', \Carbon\Carbon::now())
                        ->where('images.image_type','=','Logo')->where('tickets.is_active','>',0)
                        ->whereNotNull('images.url')
                        ->groupBy('venues.id')->distinct()->get();
            foreach ($_venues as $v)
            {
                $v->url = Image::view_image($v->url);
                $city = preg_replace("/[^A-Za-z0-9]/", '_', $v->city);
                if(isset($venues[$city]))
                    $venues[$city]['venues'][] = $v;
                else 
                    $venues[$city] = ['city'=>$v->city,'venues'=>[$v]];
            }
            //return view
            return view('production.venues.index',compact('venues'));
        } catch (Exception $ex) {
            throw new Exception('Error Production Venue Index: '.$ex->getMessage());
        }
    }
    
    /**
     * View each venue.
     *
     * @return Method
     */
    public function view($slug)
    {
        try {
            if(empty($slug))
                return redirect()->route('index');
            //get all records
            $venue = DB::table('venues')
                        ->join('locations', 'locations.id', '=', 'venues.location_id')
                        ->select(DB::raw('venues.id as venue_id, venues.slug, venues.description, venues.name,
                                          venues.facebook, venues.twitter, venues.googleplus, venues.yelpbadge, venues.youtube, venues.instagram,
                                          locations.*, IF(venues.restrictions!="None",venues.restrictions,"") AS restrictions'))
                        ->where('venues.is_featured','>',0)->where('venues.slug', $slug)->first();
            if(!$venue)
                return redirect()->route('index');
            //get header
            $venue->header = DB::table('images')
                                ->join('venue_images', 'venue_images.image_id', '=', 'images.id')
                                ->select(DB::raw('images.url, images.caption'))
                                ->where('venue_images.venue_id',$venue->venue_id)->where('images.image_type','=','Header')->first();
            $venue->header->url = Image::view_image($venue->header->url);
            //get events
            $venue->events = DB::table('shows')
                        ->join('show_images', 'show_images.show_id', '=' ,'shows.id')
                        ->join('images', 'show_images.image_id', '=' ,'images.id')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                        ->join('categories', 'shows.category_id', '=' ,'categories.id')
                        ->select(DB::raw('shows.id AS show_id, shows.name, images.url, locations.city, categories.name AS category,
                                          venues.name AS venue, show_times.show_time, shows.slug, show_times.time_alternative, shows.description,
                                          IF(shows.starting_at,shows.starting_at,MIN(tickets.retail_price+tickets.processing_fee)) AS price'))    
                        ->where('shows.venue_id',$venue->venue_id)->where('shows.is_active','>',0)->where('shows.is_featured','>',0)
                        ->where('images.image_type','=','Logo')->where('show_times.is_active','=',1)
                        ->where('show_times.show_time','>',\Carbon\Carbon::now())
                        ->whereNotNull('images.url')
                        ->orderBy('shows.name','ASC')->orderBy('show_times.show_time','ASC')
                        ->groupBy('shows.id')
                        ->distinct()->get();
            foreach ($venue->events as $s)
                if(!empty($s->url))
                    $s->url = Image::view_image($s->url);
            //return view
            return view('production.venues.view',compact('venue'));
        } catch (Exception $ex) {
            throw new Exception('Error Production Venue View: '.$ex->getMessage());
        }
    }
    
}
