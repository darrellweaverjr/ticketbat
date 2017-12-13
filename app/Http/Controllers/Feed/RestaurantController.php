<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Util;
use App\Http\Models\Image;
use App\Http\Models\Restaurant;
use App\Http\Models\RestaurantMenu;

/**
 * Manage Restaurant API
 *
 * @author ivan
 */
class RestaurantController extends Controller{
    
    /*
     * return general info (by restaurant id) in json format
     */
    public function general($restaurant_id)
    {
        $restaurant = null;
        if(!empty($restaurant_id) && is_numeric($restaurant_id))
        {
            $restaurant = Restaurant::find($restaurant_id); 
            if($restaurant)
            {
                $restaurant->venue = DB::table('venues')
                                ->join('locations', 'locations.id', '=' ,'venues.location_id')
                                ->select('venues.name AS venue','locations.address','locations.city','locations.state','locations.zip','locations.country',
                                         'venues.description','venues.facebook','venues.twitter','venues.googleplus','venues.yelpbadge','venues.youtube','venues.instagram')
                                ->where('venues.id','=',$restaurant->venue_id)->first();
                if($restaurant->venue)
                {
                    $restaurant->venue->images = DB::table('images')->join('venue_images', 'venue_images.image_id', '=' ,'images.id')
                                ->select('images.url')->where('venue_images.venue_id','=',$restaurant->venue_id)->distinct()->get();
                    foreach ($restaurant->venue->images as $i)
                        $i->url = Image::view_image($i->url);
                }
            }
        }     
        return $restaurant->toJson();
    }
    
    /*
     * return menu (by restaurant id) in json format
     */
    public function menu($restaurant_id)
    {
        $menu = [];
        if(!empty($restaurant_id) && is_numeric($restaurant_id))
        {
            $menus = RestaurantMenu::all();
            //recursive method
            function sub_menu($m,$restaurant_id)
            {
                $children = $m->children()->get();
                $submenu = [];
                //recursive
                if(count($children))
                {
                    foreach ($children as $c)
                    {
                        $sub = sub_menu($c,$restaurant_id);
                        if(!empty($sub))
                            $submenu[] = $sub;
                    }
                }
                $m->submenu = $submenu;
                //get items
                $m->items = DB::table('restaurant_items')
                                ->join('restaurant_menu', 'restaurant_menu.id', '=' ,'restaurant_items.restaurant_menu_id')
                                ->select('restaurant_items.*', 'restaurant_menu.name AS menu')
                                ->where('restaurant_items.restaurants_id',$restaurant_id)
                                ->where('restaurant_items.restaurant_menu_id',$m->id)
                                ->where('restaurant_items.enabled','>',0)
                                ->orderBy('restaurant_items.order')
                                ->get();
                foreach ($m->items as $index=>$i)
                    $i->image_id = Image::view_image($i->image_id);
                //return
                if(count($m->items) || !empty($m->submenu))
                    return $m;
                return null;
            }
            //first call
            foreach($menus as $m)
            {
                if($m->parent_id == 0)
                {
                    $sub = sub_menu($m,$restaurant_id);
                    if(!empty($sub))
                        $menu[] = $sub;
                }
            } 
        }   
        return Util::json($menu);
    }
    
    /*
     * return awards (by restaurant id) in json format
     */
    public function awards($restaurant_id)
    {
        $awards = [];
        if(!empty($restaurant_id) && is_numeric($restaurant_id))
        {
            $awards = DB::table('restaurant_awards')
                        ->join('restaurant_media', 'restaurant_media.id', '=' ,'restaurant_awards.restaurant_media_id')
                        ->select('restaurant_awards.*','restaurant_media.name','restaurant_media.image_id')
                        ->where('restaurant_awards.restaurants_id',$restaurant_id)
                        ->orderBy('restaurant_awards.posted','DESC')
                        ->get();
            foreach($awards as $i)
                $i->image_id = Image::view_image($i->image_id);
        }     
        return $awards->toJson();
    }
    
    /*
     * return reviews (by restaurant id) in json format
     */
    public function reviews($restaurant_id)
    {
        $reviews = [];
        if(!empty($restaurant_id) && is_numeric($restaurant_id))
        {
            $reviews = DB::table('restaurant_reviews')
                        ->join('restaurant_media', 'restaurant_media.id', '=' ,'restaurant_reviews.restaurant_media_id')
                        ->select('restaurant_reviews.*','restaurant_media.name','restaurant_media.image_id')
                        ->where('restaurant_reviews.restaurants_id',$restaurant_id)
                        ->orderBy('restaurant_reviews.posted','DESC')
                        ->get();
            foreach($reviews as $i)
                $i->image_id = Image::view_image($i->image_id);
        }     
        return $reviews->toJson();
    }
    
    /*
     * return comments (by restaurant id) in json format
     */
    public function comments($restaurant_id)
    {
        $comments = [];
        if(!empty($restaurant_id) && is_numeric($restaurant_id))
        {
            $comments = DB::table('restaurant_comments')
                        ->select('restaurant_comments.*')
                        ->where('restaurant_comments.restaurants_id',$restaurant_id)->where('restaurant_comments.enabled','>',0)
                        ->orderBy('restaurant_comments.posted','DESC')
                        ->get();
        }     
        return $comments->toJson();
    }
    
    /*
     * return albums (by restaurant id) in json format
     */
    public function albums($restaurant_id)
    {
        $albums = [];
        if(!empty($restaurant_id) && is_numeric($restaurant_id))
        {
            $albums = DB::table('restaurant_albums')
                        ->join('restaurant_album_images', 'restaurant_album_images.restaurant_albums_id', '=' ,'restaurant_albums.id')
                        ->select(DB::raw('restaurant_albums.*, COUNT(restaurant_album_images.image_id) AS qty'))
                        ->where('restaurant_albums.restaurants_id',$restaurant_id)
                        ->groupBy('restaurant_albums.id')->orderBy('restaurant_albums.posted','DESC')
                        ->get();
            foreach ($albums as $a)
            {
                $a->images = DB::table('restaurant_album_images')
                            ->join('images', 'restaurant_album_images.image_id', '=' ,'images.id')
                            ->select(DB::raw('images.url'))
                            ->where('restaurant_album_images.restaurant_albums_id',$a->id)
                            ->groupBy('restaurant_album_images.image_id')->orderBy('images.created','DESC')
                            ->distinct()->get();
                foreach($a->images as $i)
                    $i->url = Image::view_image($i->url);
            }
        }     
        return $albums->toJson();
    }
    
    
}
