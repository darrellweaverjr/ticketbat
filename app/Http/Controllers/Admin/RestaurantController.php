<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Venue;
use App\Http\Models\Restaurant;
use App\Http\Models\RestaurantAlbums;
use App\Http\Models\RestaurantAwards;
use App\Http\Models\RestaurantComments;
use App\Http\Models\RestaurantItems;
use App\Http\Models\RestaurantReviews;
use App\Http\Models\RestaurantSpecials;
use App\Http\Models\Image;
/**
 * Manage Bands
 *
 * @author ivan
 */
class RestaurantController extends Controller{
    
    /**
     * List all bands and return default view.
     *
     * @return view
     */
    public function index($autopen=null)
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && isset($input['id']))
            {
                //get selected record
                $restaurant = Restaurant::find($input['id']);  
                if(!$restaurant)
                    return ['success'=>false,'msg'=>'There was an error getting the restaurant.<br>Maybe it is not longer in the system.'];
//                $shows = [];
//                foreach($band->show_bands as $s)
//                    $shows[] = [$s->name,$s->pivot->n_order];
//                // change relative url uploads for real one
//                $band->image_url = Image::view_image($band->image_url);
                return ['success'=>true,'restaurant'=>$restaurant];
            }
            else
            {
                //if user has permission to view
                if(in_array('View',Auth::user()->user_type->getACLs()['RESTAURANTS']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['RESTAURANTS']['permission_scope'] == 'All')
                        $venues = Venue::orderBy('name')->get(['id','name']);
                    else
                        $venues = Venue::whereIn('id',explode(',',Auth::user()->venues_edit))->orderBy('name')->get(['id','name']);
                    //get all records        
                    $restaurants = DB::table('restaurants')
                                    ->join('venues', 'venues.id', '=' ,'restaurants.venue_id')
                                    ->select('restaurants.*', 'venues.name AS venue')
                                    ->orderBy('venues.name')->orderBy('restaurants.name')
                                    ->get();
                }
                //return view
                return view('admin.restaurants.index',compact('restaurants','venues'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Restaurants Index: '.$ex->getMessage());
        }
    } 
    /**
     * Save new or updated band.
     *
     * @void
     */
    public function save()
    {
        try {
            //init
            $input = Input::all(); 
            //save all record      
            if($input)
            {
                if(isset($input['id']) && $input['id'])
                {
                    if(Restaurant::where('name','=',$input['name'])->where('id','!=',$input['id'])->count())
                        return ['success'=>false,'msg'=>'There was an error saving the restaurant.<br>That name is already in the system.','errors'=>'name'];
                    $restaurant = Restaurant::find($input['id']);
                }                    
                else
                {                    
                    if(Restaurant::where('name','=',$input['name'])->count())
                        return ['success'=>false,'msg'=>'There was an error saving the restaurant.<br>That name is already in the system.','errors'=>'name'];
                    $restaurant = new Restaurant;
                    $restaurant->venue()->associate(Venue::find($input['venue_id']));
                }
                //save restaurant
                $restaurant->name = strip_tags($input['name']);
                $phone = preg_replace('/[^0-9,.]/','',$input['phone']);
                $restaurant->phone = (!empty($phone))? $phone : null;
                $restaurant->description = (!empty($input['description']))? strip_tags($input['description'],'<p><a><br>') : null;
                $restaurant->save();
                //return
                return ['success'=>true,'msg'=>'Restaurant saved successfully!'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the restaurant.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Restaurants Save: '.$ex->getMessage());
        }
    }
    /**
     * Remove bands.
     *
     * @void
     */
    public function remove()
    {
        try {
            //init
            $input = Input::all();
            //delete all records   
            foreach ($input['id'] as $id)
            {
                //get restaurant
                $restaurant = Restaurant::find($id);
                if($restaurant)
                {
                    //albums
                    $albums = RestaurantAlbums::where('restaurants_id','=',$restaurant->id)->get();
                    foreach ($albums as $a)
                        DB::table('restaurant_album_images')->where('restaurant_albums_id','=',$a->id)->delete();
                    RestaurantAlbums::where('restaurants_id','=',$restaurant->id)->delete();
                    //awards
                    RestaurantAwards::where('restaurants_id','=',$restaurant->id)->delete();
                    //comments
                    RestaurantComments::where('restaurants_id','=',$restaurant->id)->delete();
                    //items
                    RestaurantItems::where('restaurants_id','=',$restaurant->id)->delete();
                    //reviews
                    RestaurantReviews::where('restaurants_id','=',$restaurant->id)->delete();
                    //specials
                    RestaurantSpecials::where('restaurants_id','=',$restaurant->id)->delete();
                    //restaurant
                    $restaurant->delete();
                }
            }
            return ['success'=>true,'msg'=>'All records deleted successfully!'];
        } catch (Exception $ex) {
            throw new Exception('Error Restaurants Remove: '.$ex->getMessage());
        }
    }
    
}
