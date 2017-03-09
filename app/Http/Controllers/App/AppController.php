<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Image;
use App\Http\Models\Show;
use App\Http\Models\Venue;

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
        if(!empty($id) && is_numeric($id))
        {
            $shows = DB::table('shows')
                    ->join('show_images', 'show_images.show_id', '=' ,'shows.id')
                    ->join('images', 'show_images.image_id', '=' ,'images.id')
                    ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                    ->join('locations', 'locations.id', '=' ,'venues.location_id')
                    ->select('shows.id','shows.venue_id','shows.name','images.url','locations.city')
                    ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('images.image_type','=','Logo')
                    ->whereNotNull('images.url')->where('shows.id','=',$id)
                    ->distinct()->get(); 
        }         
        else if(!empty($venue_id) && is_numeric($venue_id))
        {
            $shows = DB::table('shows')
                        ->join('show_images', 'show_images.show_id', '=' ,'shows.id')
                        ->join('images', 'show_images.image_id', '=' ,'images.id')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->select('shows.id','shows.venue_id','shows.name','images.url','locations.city')
                        ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('images.image_type','=','Logo')
                        ->whereNotNull('images.url')->where('venues.id','=',$venue_id)
                        ->orderBy('shows.name')->groupBy('shows.id')
                        ->distinct()->get();
        }
        else
        {
            $shows = DB::table('shows')
                    ->join('show_images', 'show_images.show_id', '=' ,'shows.id')
                    ->join('images', 'show_images.image_id', '=' ,'images.id')
                    ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                    ->join('locations', 'locations.id', '=' ,'venues.location_id')
                    ->select('shows.id','shows.venue_id','shows.name','images.url','locations.city')
                    ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('images.image_type','=','Logo')
                    ->whereNotNull('images.url')
                    ->orderBy('shows.name')->groupBy('shows.id')
                    ->distinct()->get();
        }    
        foreach ($shows as $s)
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
                    ->select('venues.id','venues.name','images.url','locations.city')
                    ->where('venues.is_featured','>',0)->where('images.image_type','=','Logo')
                    ->whereNotNull('images.url')
                    ->orderBy('venues.name')->groupBy('venues.id')
                    ->distinct()->get();
        foreach ($venues as $v)
            $v->url = Image::view_image($v->url);
        return $venues->toJson();     
    }
    
}
