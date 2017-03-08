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
     * return arrays of all shows (or by venue id) in json format
     */
    public function shows()
    {
        $shows = DB::table('shows')
                    ->join('show_images', 'show_images.show_id', '=' ,'shows.id')
                    ->join('images', 'show_images.image_id', '=' ,'images.id')
                    ->select('shows.id','shows.venue_id','shows.name','images.url')
                    ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('images.image_type','=','Logo')
                    ->whereNotNull('images.url')
                    ->orderBy('shows.name')->groupBy('shows.id')
                    ->distinct()->get();
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
                    ->select('venues.id','venues.name','images.url')
                    ->where('venues.is_featured','>',0)->where('images.image_type','=','Logo')
                    ->whereNotNull('images.url')
                    ->orderBy('venues.name')->groupBy('venues.id')
                    ->distinct()->get();
        foreach ($venues as $v)
            $v->url = Image::view_image($v->url);
        return $venues->toJson();     
    }
    
}
