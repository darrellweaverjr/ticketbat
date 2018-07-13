<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Image;
use App\Http\Models\Util;

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
            
            //checkings by user
            $options = Util::display_options_by_user();
            
            $_venues = DB::table('venues')
                        ->join('locations', 'locations.id', '=', 'venues.location_id')
                        ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                        ->select(DB::raw('venues.id as venue_id, venues.slug, venues.description, venues.name,
                                          venues.facebook, venues.twitter, venues.googleplus, venues.yelpbadge, venues.youtube, venues.instagram,
                                          locations.*, venues.logo_url'))
                        ->where('venues.is_featured','>',0)->where('shows.is_active','>',0)
                        ->where('show_times.is_active','>',0)
                        ->where(DB::raw($this->cutoff_date()),'>', \Carbon\Carbon::now())
                        ->where('tickets.is_active','>',0)
                        ->whereNotNull('venues.logo_url');
            if(!is_null($options['venues']))
                $_venues = $_venues->whereIn('venues.id',$options['venues']);
            $_venues = $_venues->groupBy('venues.id')->orderBy('venues.name','ASC')->distinct()->get();
            
            foreach ($_venues as $v)
            {
                $v->logo_url = Image::view_image($v->logo_url);
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
            
            //checkings by user
            $options = Util::display_options_by_user();
            
            //get all records
            $venue = DB::table('venues')
                        ->join('locations', 'locations.id', '=', 'venues.location_id')
                        ->select(DB::raw('venues.id as venue_id, venues.slug, venues.description, venues.name, venues.header_url,
                                          venues.facebook, venues.twitter, venues.googleplus, venues.yelpbadge, venues.youtube, venues.instagram,
                                          locations.*, IF(venues.restrictions!="None",venues.restrictions,"") AS restrictions'))
                        ->where('venues.is_featured','>',0)->where('venues.slug', $slug);
            if(!is_null($options['venues']))
                $venue = $venue->whereIn('venues.id',$options['venues']);
            $venue= $venue->first();
            if(!$venue)
                return redirect()->route('index');
            //get header
            $venue->header_url = Image::view_image($venue->header_url);
            if(empty($venue->header_url))
                return redirect()->route('index');
            //get events
            $venue->events = DB::table('shows')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                        ->join('categories', 'shows.category_id', '=' ,'categories.id')
                        ->select(DB::raw('shows.id AS show_id, shows.name, shows.logo_url, locations.city, categories.name AS category,
                                          venues.name AS venue, show_times.show_time, shows.slug, show_times.time_alternative, shows.description,
                                          IF(shows.starting_at,shows.starting_at,MIN(tickets.retail_price+tickets.processing_fee)) AS price'))
                        ->where('shows.venue_id',$venue->venue_id)->where('shows.is_active','>',0)
                        ->where($options['where'])
                        ->where('show_times.is_active','=',1)
                        ->whereNotNull('shows.logo_url');
            if(!is_null($options['venues']))
                $venue->events = $venue->events->whereIn('venues.id',$options['venues']);
            $venue->events = $venue->events->orderBy('show_times.show_time','ASC')->groupBy('shows.id')->distinct()->get();
            
            foreach ($venue->events as $s)
            {
                //add link here
                $s->link = '/'.$options['link'].$s->slug;
                if(!empty($s->logo_url))
                    $s->logo_url = Image::view_image($s->logo_url);
            }
            //return view
            return view('production.venues.view',compact('venue'));
        } catch (Exception $ex) {
            throw new Exception('Error Production Venue View: '.$ex->getMessage());
        }
    }

}
