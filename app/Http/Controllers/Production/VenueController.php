<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Image;
use App\Http\Models\Venue;
use App\Http\Models\Util;

class VenueController extends Controller
{
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
                        ->select(DB::raw('venues.id as venue_id, venues.slug, venues.description, venues.name,
                                          venues.facebook, venues.twitter, venues.googleplus, venues.yelpbadge, venues.youtube, venues.instagram,
                                          locations.*, images.url, images.caption'))
                        ->where('venues.is_featured','>',0)->where('images.image_type','=','Logo')->get();
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
                        ->select(DB::raw('venues.id as venue_id, venues.slug, venues.description, venues.name as venue,
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
            //return view
            return view('production.venues.view',compact('venue'));
        } catch (Exception $ex) {
            throw new Exception('Error Production Venue View: '.$ex->getMessage());
        }
    }
    
}
