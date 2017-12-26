<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Venue;
use App\Http\Models\Restaurant;
use App\Http\Models\RestaurantMenu;
use App\Http\Models\RestaurantMedia;
use App\Http\Models\RestaurantAlbums;
use App\Http\Models\RestaurantAwards;
use App\Http\Models\RestaurantComments;
use App\Http\Models\RestaurantItems;
use App\Http\Models\RestaurantReviews;
use App\Http\Models\RestaurantSpecials;
use App\Http\Models\RestaurantReservations;
use App\Http\Models\Image;
use App\Http\Models\Util;
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
                //reservations
                $restaurant->reservations = $this->get_reservations($restaurant->id);
                //items
                $restaurant->items = $this->get_items($restaurant->id);
                //specials
                $restaurant->specials = $this->get_specials($restaurant->id);
                //albums
                $restaurant->albums = $this->get_albums($restaurant->id);
                //awards
                $restaurant->awards = $this->get_awards($restaurant->id);
                //reviews
                $restaurant->reviews = $this->get_reviews($restaurant->id);
                //return
                return ['success'=>true,'restaurant'=>$restaurant];
            }
            else
            {
                $restaurants = [];
                $menu = $media = [];
                $venues = [];
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
                    $menu = $this->get_menus();
                    $media = $this->get_media();
                }
                //nomeclators
                $reservation_occasions = Util::getEnumValues('restaurant_reservations','occasion');
                $reservation_status = Util::getEnumValues('restaurant_reservations','status');
                //return view
                return view('admin.restaurants.index',compact('restaurants','venues','menu','media','reservation_occasions','reservation_status'));
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
    public function remove()            //missing remove images from server
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
                    {
                        $images = DB::table('restaurant_album_images')->where('restaurant_albums_id','=',$a->id)->get();
                        foreach ($items as $a)
                        {
                            $image = Image::find($a->image_id);
                            if($image)
                            {
                                $image->delete_image_file();
                                $image->delete();
                            }
                        }
                        DB::table('restaurant_album_images')->where('restaurant_albums_id','=',$a->id)->delete();
                    }
                    RestaurantAlbums::where('restaurants_id','=',$restaurant->id)->delete();
                    //awards
                    $awards = RestaurantAwards::where('restaurants_id','=',$restaurant->id)->get();
                    foreach ($awards as $a)
                        RestaurantAwards::find($a->id)->delete_image();
                    RestaurantAwards::where('restaurants_id','=',$restaurant->id)->delete();
                    //comments
                    RestaurantComments::where('restaurants_id','=',$restaurant->id)->delete();
                    //items
                    $items = RestaurantItems::where('restaurants_id','=',$restaurant->id)->get();
                    foreach ($items as $a)
                        RestaurantItems::find($a->id)->delete_image();
                    RestaurantItems::where('restaurants_id','=',$restaurant->id)->delete();
                    //reviews
                    RestaurantReviews::where('restaurants_id','=',$restaurant->id)->delete();
                    //specials
                    $specials = RestaurantSpecials::where('restaurants_id','=',$restaurant->id)->get();
                    foreach ($specials as $a)
                        RestaurantSpecials::find($a->id)->delete_image();
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
    
    
    /**
     * Get, Edit menu for restaurants
     *
     * @return view
     */
    function get_media()
    {
        $medias = RestaurantMedia::orderBy('name')->get();
        foreach($medias as $m)
            $m->image_id = Image::view_image($m->image_id);
        return $medias;
    }
    public function media()
    {
        try {  
            //init
            $input = Input::all(); 
            //get
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $media = RestaurantMedia::find($input['id']);
                if($media)
                    return ['success'=>true,'media'=>$media];                
                return ['success'=>false,'msg'=>'There is an error getting the media.<br>Item not longer in the system.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                if(!empty($input['id']))
                {
                    $media = RestaurantMedia::find($input['id']);
                    if($media)
                    {
                        $media->delete_image();
                        $media->delete();
                    }
                    $medias = $this->get_media();   
                    return ['success'=>true,'medias'=>$medias,'msg'=>'Mediaremoved successfully!'];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the menu and submenus.<br>You must select a valid item.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
                if(!empty($input['id']))
                {
                    $media = RestaurantMedia::find($input['id']);
                    if(!$media)
                        return ['success'=>false,'msg'=>'There was an error updating the media.<br>The item is not longer in the system.'];
                }
                else
                {
                    $media = new RestaurantMedia;
                    $name = RestaurantMedia::where('name',trim($input['id']))->count();
                    if($name>0)
                        return ['success'=>false,'msg'=>'There was an error creating the media.<br>There is already an item with that name in the system.'];
                }
                $media->name = strip_tags(trim($input['name']));
                //image
                if(!empty($input['image_id']))
                {
                    if(preg_match('/media\/preview/',$input['image_id'])) 
                    {
                        $media->delete_image();
                        $media->set_image($input['image_id']);
                    }
                }
                else
                    $media->delete_image();
                $media->save();
                //return
                $medias = $this->get_media();   
                return ['success'=>true,'medias'=>$medias,'msg'=>'Media saved successfully!'];
            }
            else //get all
            {
                $media = $this->get_media();   
                return ['success'=>true,'media'=>$media];
            }
        } catch (Exception $ex) {
            throw new Exception('Error RestaurantMedia Index: '.$ex->getMessage());
        }
    }
    
    /**
     * Get, Edit menu for restaurants
     *
     * @return view
     */
    function get_menus()
    {
        return RestaurantMenu::get_menu('-&emsp;&emsp;');
    }
    public function menu()
    {
        try {  
            //init
            $input = Input::all(); 
            //get
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $menu = RestaurantMenu::find($input['id']);
                if($menu)
                    return ['success'=>true,'menu'=>$menu];                
                return ['success'=>false,'msg'=>'There is an error getting the menu.<br>Item not longer in the system.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                if(!empty($input['id']))
                {
                    $menu = RestaurantMenu::find($input['id']);
                    if($menu)
                    {
                        function remove_children($m)
                        {
                            $children = $m->children();
                            if(count($children))
                            {
                                foreach ($children as $c)
                                    remove_children($m);
                            }
                            $m->delete();
                        }
                        remove_children($menu);
                    }
                    $menu = $this->get_menus();   
                    return ['success'=>true,'menu'=>$menu,'msg'=>'Menus and submenus removed successfully!'];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the menu and submenus.<br>You must select a valid item.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
                if(!empty($input['id']))
                {
                    $menu = RestaurantMenu::find($input['id']);
                    if(!$menu)
                        return ['success'=>false,'msg'=>'There was an error updating the menu.<br>The item is not longer in the system.'];
                }
                else
                {
                    $menu = new RestaurantMenu;
                }
                $menu->name = strip_tags(trim($input['name']));
                $menu->notes = (!empty($input['notes']))? strip_tags(trim($input['notes'])) : null;
                $menu->disabled = (!empty($input['disabled']))? 1 : 0;
                $menu->parent_id = $input['parent_id'];
                $menu->save();
                //return
                $menu = $this->get_menus();   
                return ['success'=>true,'menu'=>$menu,'msg'=>'Menu saved successfully!'];
            }
            else //get all
            {
                $menu = $this->get_menus();   
                return ['success'=>true,'menu'=>$menu];
            }
        } catch (Exception $ex) {
            throw new Exception('Error RestaurantMenu Index: '.$ex->getMessage());
        }
    }
    /**
     * Get, Edit reservations for restaurants
     *
     * @return view
     */
    /*
     * return cutoff_date for checking the showtime
     */
    public function start_reservations()
    {
        return '-7 days';
    }  
    public function get_reservations($restaurant_id)
    {
        return RestaurantReservations::whereDate('schedule','>=',date('Y-m-d', strtotime($this->start_reservations())))
                        ->orderBy('schedule','DESC')->get();
    }
    public function reservations()
    {
        try {  
            //init
            $input = Input::all(); 
            //get
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $reservation = RestaurantReservations::find($input['id']);
                if($reservation)
                    return ['success'=>true,'reservation'=>$reservation];
                return ['success'=>false,'msg'=>'There is an error getting the reservation.<br>Item not longer in the system.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                if(!empty($input['id']))
                {
                    RestaurantReservations::where('id',$input['id'])->delete();
                    $reservations = $this->get_reservations($input['restaurants_id']);
                    return ['success'=>true,'reservations'=>$reservations,'msg'=>'Reservation removed successfully!'];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the reservation.<br>You must select a valid item.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
                if(!empty($input['id']))
                {
                    $reservation = RestaurantReservations::find($input['id']);
                    if(!$reservation)
                        return ['success'=>false,'msg'=>'There was an error updating the reservation.<br>The item is not longer in the system.'];
                    $reservation->status = $input['status'];
                }
                else
                {
                    $reservation = new RestaurantReservations;
                    $reservation->restaurants_id = $input['restaurants_id'];
                    $reservation->status = 'Requested';
                }
                $reservation->schedule = $input['schedule'];
                $reservation->people = $input['people'];
                $reservation->first_name = $input['first_name'];
                $reservation->last_name = $input['last_name'];
                $reservation->phone = (!empty($input['phone']))? preg_replace('/[^0-9]/','',$input['phone']): null;
                $reservation->email = (!empty($input['email']))? $input['email']: null;
                $reservation->occasion = $input['occasion'];
                $reservation->special_request = (!empty($input['special_request']))? strip_tags(trim($input['special_request'])) : null;
                $reservation->newsletter = (!empty($input['newsletter']))? 1 : 0;
                $reservation->save();
                //return
                $reservations = $this->get_reservations($reservation->restaurants_id);
                return ['success'=>true,'reservations'=>$reservations,'msg'=>'Reservation saved successfully!'];
            }
            else if(isset($input) && isset($input['restaurants_id'])) //get all
            {
                $reservations = $this->get_reservations($input['restaurants_id']);
                return ['success'=>true,'reservations'=>$reservations];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error RestaurantReservations Index: '.$ex->getMessage());
        }
    }
    /**
     * Get, Edit items for restaurants
     *
     * @return view
     */
    public function get_items($restaurant_id)
    {
        $items = DB::table('restaurant_items')
                        ->join('restaurant_menu', 'restaurant_menu.id', '=' ,'restaurant_items.restaurant_menu_id')
                        ->select('restaurant_items.*', 'restaurant_menu.name AS menu')
                        ->where('restaurant_items.restaurants_id',$restaurant_id)
                        ->orderBy('restaurant_menu.name')->orderBy('restaurant_items.order')
                        ->get();
        foreach ($items as $index=>$i)
            $i->image_id = Image::view_image($i->image_id);
        return $items;
    }
    public function items()
    {
        try {  
            //init
            $input = Input::all(); 
            //get
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $item = DB::table('restaurant_items')
                                ->join('restaurant_menu', 'restaurant_menu.id', '=' ,'restaurant_items.restaurant_menu_id')
                                ->select('restaurant_items.*', 'restaurant_menu.name AS menu')
                                ->where('restaurant_items.id',$input['id'])
                                ->orderBy('restaurant_menu.name')->orderBy('restaurant_items.order')
                                ->first();
                if($item)
                {
                    $item->image_id = Image::view_image($item->image_id);
                    return ['success'=>true,'item'=>$item];
                }
                return ['success'=>false,'msg'=>'There is an error getting the item.<br>Item not longer in the system.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                if(!empty($input['id']))
                {
                    $item = RestaurantItems::find($input['id']);
                    if($item)
                    {
                        RestaurantItems::where('restaurants_id',$item->restaurants_id)->where('restaurant_menu_id',$item->restaurant_menu_id)
                                            ->where('order','>',$item->order)->decrement('order');
                        $item->delete_image();
                        $item->delete();
                    }
                    $items = $this->get_items($input['restaurants_id']);
                    return ['success'=>true,'items'=>$items,'msg'=>'Item removed successfully!'];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the item.<br>You must select a valid item.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && !empty($input['restaurants_id']) && $input['action']==1)
            {
                if(!empty($input['id']))
                {
                    $item = RestaurantItems::find($input['id']);
                    if(!$item)
                        return ['success'=>false,'msg'=>'There was an error updating the item.<br>The item is not longer in the system.'];
                    //order
                    RestaurantItems::where('restaurants_id',$input['restaurants_id'])->where('restaurant_menu_id',$input['restaurant_menu_id'])
                                            ->where('order','>=',$input['order'])->where('id','!=',$input['id'])->increment('order');
                }
                else
                {
                    $item = new RestaurantItems;
                    $item->restaurants_id = $input['restaurants_id'];
                    //order
                    RestaurantItems::where('restaurants_id',$input['restaurants_id'])->where('restaurant_menu_id',$input['restaurant_menu_id'])
                                            ->where('order','>=',$input['order'])->increment('order');
                }
                $item->name = strip_tags(trim($input['name']));
                $item->notes = (!empty($input['notes']))? strip_tags(trim($input['notes'])) : null;
                $item->description = (!empty($input['description']))? strip_tags(trim($input['description'])) : null;
                $item->price = $input['price'];
                $item->enabled = (!empty($input['enabled']))? 1 : 0;
                $item->restaurant_menu_id = $input['restaurant_menu_id'];
                //image
                if(!empty($input['image_id']))
                {
                    if(preg_match('/media\/preview/',$input['image_id'])) 
                    {
                        $item->delete_image();
                        $item->set_image($input['image_id']);
                    }
                }
                /*else
                    $item->delete_image();*/
                //order
                $item->order = $input['order'];
                $item->save();
                $items = $this->get_items($item->restaurants_id);
                foreach ($items as $index=>$i)
                {
                    $i->order = $index+1;
                    RestaurantItems::where('id',$i->id)->update(['order'=>$i->order]);
                }
                //return
                return ['success'=>true,'items'=>$items,'msg'=>'Item saved successfully!'];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error RestaurantItems Index: '.$ex->getMessage());
        }
    }
    
    /**
     * Get, Edit specials for restaurants
     *
     * @return view
     */
    public function get_specials($restaurant_id)
    {
        $specials = DB::table('restaurant_specials')
                        ->select('restaurant_specials.*')
                        ->where('restaurant_specials.restaurants_id',$restaurant_id)
                        ->orderBy('restaurant_specials.order')
                        ->get();
        foreach ($specials as $index=>$i)
            $i->image_id = Image::view_image($i->image_id);
        return $specials;
    }
    public function specials()
    {
        try {  
            //init
            $input = Input::all(); 
            //get
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $special = DB::table('restaurant_specials')
                                ->select('restaurant_specials.*')
                                ->where('restaurant_specials.id',$input['id'])
                                ->first();
                if($special)
                {
                    $special->image_id = Image::view_image($special->image_id);
                    return ['success'=>true,'special'=>$special];
                }
                return ['success'=>false,'msg'=>'There is an error getting the special.<br>Item not longer in the system.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                if(!empty($input['id']))
                {
                    $special = RestaurantSpecials::find($input['id']);
                    if($special)
                    {
                        RestaurantSpecials::where('restaurants_id',$special->restaurants_id)
                                            ->where('order','>',$special->order)->decrement('order');
                        $special->delete_image();
                        $special->delete();
                    }
                    $specials = $this->get_specials($input['restaurants_id']);
                    return ['success'=>true,'specials'=>$specials,'msg'=>'Special removed successfully!'];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the special.<br>You must select a valid item.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && !empty($input['restaurants_id']) && $input['action']==1)
            {
                if(!empty($input['id']))
                {
                    $special = RestaurantSpecials::find($input['id']);
                    if(!$special)
                        return ['success'=>false,'msg'=>'There was an error updating the special.<br>The item is not longer in the system.'];
                    //order
                    RestaurantSpecials::where('restaurants_id',$input['restaurants_id'])
                                            ->where('order','>=',$input['order'])->where('id','!=',$input['id'])->increment('order');
                }
                else
                {
                    $exist = RestaurantSpecials::where('restaurants_id',$input['restaurants_id'])
                                            ->where('title',$input['title'])->count();
                    if($exist)
                        return ['success'=>false,'msg'=>'There was an error updating the special.<br>There is already one element with that title in the system.'];
                    $special = new RestaurantSpecials;
                    $special->restaurants_id = $input['restaurants_id'];
                    //order
                    RestaurantSpecials::where('restaurants_id',$input['restaurants_id'])
                                            ->where('order','>=',$input['order'])->increment('order');
                }
                $special->title = strip_tags(trim($input['title']));
                $special->description = (!empty($input['description']))? strip_tags(trim($input['description'])) : null;
                $special->enabled = (!empty($input['enabled']))? 1 : 0;
                //image
                if(!empty($input['image_id']))
                {
                    if(preg_match('/media\/preview/',$input['image_id'])) 
                    {
                        $special->delete_image();
                        $special->set_image($input['image_id']);
                    }
                }
                /*else
                    $special->delete_image();*/
                //order
                $special->order = $input['order'];
                $special->save();
                $specials = $this->get_specials($special->restaurants_id);
                foreach ($specials as $index=>$i)
                {
                    $i->order = $index+1;
                    RestaurantSpecials::where('id',$i->id)->update(['order'=>$i->order]);
                }
                //return
                return ['success'=>true,'specials'=>$specials,'msg'=>'Special saved successfully!'];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error RestaurantSpecials Index: '.$ex->getMessage());
        }
    }
    
    /**
     * Get, Edit awards for restaurants
     *
     * @return view
     */
    public function get_awards($restaurant_id)
    {
        $awards = DB::table('restaurant_awards')
                        ->join('restaurant_media', 'restaurant_media.id', '=' ,'restaurant_awards.restaurant_media_id')
                        ->select('restaurant_awards.*','restaurant_media.name','restaurant_media.image_id')
                        ->where('restaurant_awards.restaurants_id',$restaurant_id)
                        ->orderBy('restaurant_awards.posted','DESC')
                        ->get();
        foreach($awards as $i)
            $i->image_id = Image::view_image($i->image_id);
        return $awards;
    }
    public function awards()
    {
        try {  
            //init
            $input = Input::all(); 
            //get
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $award = RestaurantAwards::find($input['id']);
                if($award)
                {
                    $award->image_id = Image::view_image($award->image_id);
                    return ['success'=>true,'award'=>$award];
                }
                return ['success'=>false,'msg'=>'There is an error getting the award.<br>Item not longer in the system.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                if(!empty($input['id']))
                {
                    $award = RestaurantAwards::find($input['id']);
                    if($award)
                    {
                        $award->delete_image();
                        $award->delete();
                    }
                    $awards = $this->get_awards($input['restaurants_id']);
                    return ['success'=>true,'awards'=>$awards,'msg'=>'Award removed successfully!'];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the award.<br>You must select a valid item.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && !empty($input['restaurants_id']) && $input['action']==1)
            {
                if(!empty($input['id']))
                {
                    $award = RestaurantAwards::find($input['id']);
                    if(!$award)
                        return ['success'=>false,'msg'=>'There was an error updating the award.<br>The item is not longer in the system.'];
                }
                else
                {
                    $award = new RestaurantAwards;
                    $award->restaurants_id = $input['restaurants_id'];
                }
                $award->awarded = strip_tags(trim($input['awarded']));
                $award->description = (!empty($input['description']))? strip_tags(trim($input['description'])) : null;
                $award->posted = $input['posted'];
                //image
                if(!empty($input['image_id']))
                {
                    if(preg_match('/media\/preview/',$input['image_id'])) 
                    {
                        $award->delete_image();
                        $award->set_image($input['image_id']);
                    }
                }
                /*else
                    $award->delete_image();*/
                $award->save();
                $awards = $this->get_awards($input['restaurants_id']);
                //return
                return ['success'=>true,'awards'=>$awards, 'msg'=>'Award saved successfully!'];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error RestaurantAwards Index: '.$ex->getMessage());
        }
    }
    
    /**
     * Get, Edit reviews for restaurants
     *
     * @return view
     */
    public function get_reviews($restaurant_id)
    {
        $reviews = DB::table('restaurant_reviews')
                        ->join('restaurant_media', 'restaurant_media.id', '=' ,'restaurant_reviews.restaurant_media_id')
                        ->select('restaurant_reviews.*','restaurant_media.name','restaurant_media.image_id')
                        ->where('restaurant_reviews.restaurants_id',$restaurant_id)
                        ->orderBy('restaurant_reviews.posted','DESC')
                        ->get();
        foreach($reviews as $i)
            $i->image_id = Image::view_image($i->image_id);
        return $reviews;
    }
    public function reviews()
    {
        try {  
            //init
            $input = Input::all(); 
            //get
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $review = RestaurantReviews::find($input['id']);
                if($review)
                    return ['success'=>true,'review'=>$review];
                return ['success'=>false,'msg'=>'There is an error getting the award.<br>Item not longer in the system.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                if(!empty($input['id']))
                {
                    RestaurantReviews::where('id',$input['id'])->delete();
                    $reviews = $this->get_reviews($input['restaurants_id']);
                    return ['success'=>true,'reviews'=>$reviews,'msg'=>'Review removed successfully!'];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the reviews.<br>You must select a valid item.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && !empty($input['restaurants_id']) && $input['action']==1)
            {
                if(!empty($input['id']))
                {
                    $review = RestaurantReviews::find($input['id']);
                    if(!$review)
                        return ['success'=>false,'msg'=>'There was an error updating the review.<br>The item is not longer in the system.'];
                }
                else
                {
                    $review = new RestaurantReviews;
                    $review->restaurants_id = $input['restaurants_id'];
                }
                $review->title = strip_tags(trim($input['title']));
                $review->link = strip_tags(trim($input['link']));
                $review->notes = (!empty($input['notes']))? strip_tags(trim($input['notes'])) : null;
                $review->posted = $input['posted'];
                $review->restaurant_media_id = $input['restaurant_media_id'];
                $review->save();
                $reviews = $this->get_reviews($input['restaurants_id']);
                //return
                return ['success'=>true,'reviews'=>$reviews, 'msg'=>'Review saved successfully!'];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error RestaurantReviews Index: '.$ex->getMessage());
        }
    }
    
    /**
     * Get, Edit comments for restaurants
     *
     * @return view
     */
    public function get_comments($restaurant_id)
    {
        return DB::table('restaurant_comments')
                        ->select('restaurant_comments.*')
                        ->where('restaurant_comments.restaurants_id',$restaurant_id)
                        ->orderBy('restaurant_comments.posted','DESC')
                        ->get();
    }
    public function comments()
    {
        try {  
            //init
            $input = Input::all(); 
            //update
            if(isset($input) && isset($input['restaurants_id']) && isset($input['status']) && !empty($input['id']))
            {
                $status = (!empty($input['status']))? 1 : 0;
                DB::table('restaurant_comments')->whereIn('id', $input['id'])->update(['enabled'=>$status]);
                $comments = $this->get_comments($input['restaurants_id']);
                //return
                return ['success'=>true,'comments'=>$comments, 'msg'=>'Reviews saved successfully!'];
            }
            else if(isset($input) && isset($input['restaurants_id'])) //get all
            {
                $comments = $this->get_comments($input['restaurants_id']);
                return ['success'=>true,'comments'=>$comments];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error Restaurantcomments Index: '.$ex->getMessage());
        }
    }
    
    /**
     * Get, Edit albums for restaurants
     *
     * @return view
     */
    public function get_albums($restaurant_id)
    {
        return DB::table('restaurant_albums')
                        ->leftJoin('restaurant_album_images', 'restaurant_album_images.restaurant_albums_id', '=' ,'restaurant_albums.id')
                        ->select(DB::raw('restaurant_albums.*, COUNT(restaurant_album_images.image_id) AS images'))
                        ->where('restaurant_albums.restaurants_id',$restaurant_id)
                        ->groupBy('restaurant_albums.id')->orderBy('restaurant_albums.posted','DESC')
                        ->get();
    }
    public function get_album_images($album_id)
    {
        $images = DB::table('restaurant_album_images')
                        ->join('images', 'restaurant_album_images.image_id', '=' ,'images.id')
                        ->select(DB::raw('images.id, images.url'))
                        ->where('restaurant_album_images.restaurant_albums_id',$album_id)
                        ->groupBy('restaurant_album_images.image_id')->orderBy('images.created','DESC')
                        ->get();
        foreach($images as $i)
            $i->url = Image::view_image($i->url);
        return $images;
    }
    public function albums()
    {
        try {  
            //init
            $input = Input::all(); 
            //get images
            if(isset($input) && isset($input['action']) && !empty($input['restaurant_albums_id']) && $input['action']==0)
            {
                $album = RestaurantAlbums::find($input['restaurant_albums_id']);
                if(!$album)
                        return ['success'=>false,'msg'=>'There was an error getting the images for that album.<br>The item is not longer in the system.'];
                $images = $this->get_album_images($album->id);
                return ['success'=>true,'images'=>$images];
            }
            //add images
            else if(isset($input) && isset($input['action']) && !empty($input['restaurant_albums_id']) && !empty($input['url']) && $input['action']==1)
            {
                $album = RestaurantAlbums::find($input['restaurant_albums_id']);
                if(!$album)
                        return ['success'=>false,'msg'=>'There was an error adding images for that album.<br>The item is not longer in the system.'];
                if($album->add_image($input['url']))
                {
                    $images = $this->get_album_images($album->id);
                    return ['success'=>true,'images'=>$images,'msg'=>'Image added successfully!'];
                }
                return ['success'=>false,'msg'=>'There is an error adding the image.'];
            }
            //remove images
            else if(isset($input) && isset($input['action']) && !empty($input['restaurant_albums_id']) && !empty($input['id']) && $input['action']==-1)
            {
                $album = RestaurantAlbums::find($input['restaurant_albums_id']);
                if(!$album)
                        return ['success'=>false,'msg'=>'There was an error removing the image for that album.<br>The item is not longer in the system.'];
                if($album->delete_image($input['id']))
                {
                    $images = $this->get_album_images($album->id);
                    return ['success'=>true,'images'=>$images,'msg'=>'Image removed successfully!'];
                }
                return ['success'=>false,'msg'=>'There is an error removing the image.'];
            }
            //get albums
            else if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $album = DB::table('restaurant_albums')
                                ->leftJoin('restaurant_album_images', 'restaurant_album_images.restaurant_albums_id', '=' ,'restaurant_albums.id')
                                ->select(DB::raw('restaurant_albums.*, COUNT(restaurant_album_images.image_id) AS images'))
                                ->where('restaurant_albums.id',$input['id'])
                                ->groupBy('restaurant_albums.id')->orderBy('restaurant_albums.posted','DESC')
                                ->first();
                if($album)
                    return ['success'=>true,'album'=>$album];
                return ['success'=>false,'msg'=>'There is an error getting the album.<br>Item not longer in the system.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                if(!empty($input['id']))
                {
                    $album = RestaurantAlbums::find($input['id']);
                    if($album)
                    {
                        $images = $album->images();
                        foreach ($images as $i)
                        {
                            $image = Image::find($i->image_id);
                            if($image)
                                $image->delete_image_file();
                        }
                        $album->images()->detach();
                        $album->delete();
                    }
                    $albums = $this->get_albums($restaurant_id);
                    return ['success'=>true,'albums'=>$albums,'msg'=>'Album removed successfully!'];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the album.<br>You must select a valid item.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && !empty($input['restaurants_id']) && $input['action']==1)
            {
                if(!empty($input['id']))
                {
                    $album = RestaurantAlbums::find($input['id']);
                    if(!$album)
                        return ['success'=>false,'msg'=>'There was an error updating the album.<br>The item is not longer in the system.'];
                }
                else
                {
                    $album = new RestaurantAlbums;
                    $album->restaurants_id = $input['restaurants_id'];
                    $exist = RestaurantAlbums::where('restaurants_id',$input['restaurants_id'])->where('title',$input['title'])->count();
                    if($exist)
                        return ['success'=>false,'msg'=>'There was an error saving the album.<br>There is already an album with that name in the system.'];
                }
                $album->title = strip_tags(trim($input['title']));
                $album->posted = $input['posted'];
                $album->enabled = (!empty($input['enabled']))? 1 : 0;
                $album->save();
                $albums = $this->get_albums($album->restaurants_id);
                //return
                return ['success'=>true,'albums'=>$albums,'msg'=>'Album saved successfully!'];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error RestaurantAlbumsImages Index: '.$ex->getMessage());
        }
    }
    
}
