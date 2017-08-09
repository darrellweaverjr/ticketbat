<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Image;

/**
 * Manage Users
 *
 * @author ivan
 */
class FeedController extends Controller{
    
    /*
     * return arrays of all events (by venue id) in json format
     */
    public function events($venue_id)
    {
        $events = [];
        $cutoff_date = 'DATE_FORMAT(st.show_time + INTERVAL 1 DAY,"%Y-%m-%d 04:00:00") > NOW()';
        if(!empty($venue_id) && is_numeric($venue_id))
        {
            $events = DB::table('shows')
                    ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                    ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                    ->join('show_images', 'show_images.show_id', '=' ,'shows.id')
                    ->join('images', 'show_images.image_id', '=' ,'images.id')
                    ->select(DB::raw('shows.id, shows.name, images.url, shows.short_description, COALESCE(show_times.slug, shows.ext_slug, CONCAT("'.env('IMAGE_URL_OLDTB_SERVER').'/event/",shows.slug)) AS slug, MIN(tickets.retail_price) AS price, MIN(show_times.show_time) AS show_time'))
                    ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('show_times.is_active','=',1)
                    ->where('images.image_type','=','Logo')
                    ->where(DB::raw($cutoff_date))
                    ->where('shows.venue_id','=',$venue_id)
                    ->orderBy('show_times.show_time','ASC')->groupBy('shows.id')
                    ->distinct()->get(); 
        }       
        foreach ($events as $e)
            if(!empty($e->url))
                $e->url = Image::view_image($e->url);
        return $events->toJson();
    }
    
}
