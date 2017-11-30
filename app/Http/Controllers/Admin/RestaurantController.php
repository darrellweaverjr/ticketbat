<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Venue;
use App\Http\Models\Restaurant;
use App\Http\Models\RestaurantMenu;
use App\Http\Models\RestaurantAlbums;
use App\Http\Models\RestaurantAwards;
use App\Http\Models\RestaurantComments;
use App\Http\Models\RestaurantItems;
use App\Http\Models\RestaurantReviews;
use App\Http\Models\RestaurantSpecials;
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
                $restaurant->reservations = DB::table('restaurant_reservations')
                                ->select('restaurant_reservations.*')
                                ->select(DB::raw('restaurant_reservations.*'))
                                ->where('restaurant_reservations.restaurants_id',$restaurant->id)
                                ->whereDate('restaurant_reservations.schedule','>=', date('Y-m-d',strtotime($this->start_reservations())) )
                                ->orderBy('restaurant_reservations.schedule','DESC')
                                ->get();
                //items
                $restaurant->items = DB::table('restaurant_items')
                                ->join('restaurant_menu', 'restaurant_menu.id', '=' ,'restaurant_items.restaurant_menu_id')
                                ->select('restaurant_items.*', 'restaurant_menu.name AS menu')
                                ->where('restaurant_items.restaurants_id',$restaurant->id)
                                ->orderBy('restaurant_menu.name')->orderBy('restaurant_items.order')
                                ->get();
                foreach($restaurant->items as $i)
                    $i->image_id = Image::view_image($i->image_id);
                //albums
                $restaurant->albums = DB::table('restaurant_albums')
                                ->leftJoin('restaurant_album_images', 'restaurant_album_images.restaurant_albums_id', '=' ,'restaurant_albums.id')
                                ->leftJoin('images', 'images.id', '=' ,'restaurant_album_images.image_id')
                                ->select('restaurant_albums.*')
                                ->select(DB::raw('restaurant_albums.*, COUNT(images.id) AS images'))
                                ->where('restaurant_albums.restaurants_id',$restaurant->id)
                                ->groupBy('restaurant_albums.id')->orderBy('restaurant_albums.title')
                                ->get();
                //awards
                $restaurant->awards = DB::table('restaurant_awards')
                                ->select('restaurant_awards.*')
                                ->where('restaurant_awards.restaurants_id',$restaurant->id)
                                ->orderBy('restaurant_awards.posted','DESC')
                                ->get();
                //comments
                $restaurant->comments = DB::table('restaurant_comments')
                                ->select('restaurant_comments.*')
                                ->where('restaurant_comments.restaurants_id',$restaurant->id)
                                ->orderBy('restaurant_comments.posted','DESC')
                                ->get();
                //reviews
                $restaurant->reviews = DB::table('restaurant_reviews')
                                ->leftJoin('images', 'images.id', '=' ,'restaurant_reviews.image_id')
                                ->select('restaurant_reviews.*','images.url')
                                ->where('restaurant_reviews.restaurants_id',$restaurant->id)
                                ->orderBy('restaurant_reviews.posted','DESC')
                                ->get();
                foreach($restaurant->reviews as $i)
                    $i->url = Image::view_image($i->url);
                //specials
                $restaurant->specials = DB::table('restaurant_specials')
                                ->leftJoin('images', 'images.id', '=' ,'restaurant_specials.image_id')
                                ->select('restaurant_specials.*','images.url')
                                ->where('restaurant_specials.restaurants_id',$restaurant->id)
                                ->orderBy('restaurant_specials.title')
                                ->get();
                foreach($restaurant->reviews as $i)
                    $i->url = Image::view_image($i->url);
                return ['success'=>true,'restaurant'=>$restaurant];
            }
            else
            {
                $restaurants = [];
                $menu = [];
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
                    $menu = $this->menus_formated();
                }
                //nomeclators
                $reservation_occasions = Util::getEnumValues('restaurant_reservations','occasion');
                $reservation_status = Util::getEnumValues('restaurant_reservations','status');
                //return view
                return view('admin.restaurants.index',compact('restaurants','venues','menu','reservation_occasions','reservation_status'));
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
    function menus_formated()
    {
        $menus = RestaurantMenu::all();
        $menu = [];
        foreach($menus as $m)
        {
            if($m->parent_id == 0)
            {
                $m->name = '-&emsp;'.$m->name;
                $menu[] = $m;
                foreach ($m->children()->get() as $c)
                {
                    $c->name = '-&emsp;-&emsp;'.$c->name;
                    $menu[] = $c;
                    foreach ($c->children()->get() as $n)
                    {
                        $n->name = '-&emsp;-&emsp;-&emsp;'.$n->name;
                        $menu[] = $n;
                    }  
                }
            }
        } 
        return $menu;
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
                    $menu = $this->menus_formated();   
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
                $menu = $this->menus_formated();   
                return ['success'=>true,'menu'=>$menu,'msg'=>'Menu saved successfully!'];
            }
            else //get all
            {
                $menu = $this->menus_formated();   
                return ['success'=>true,'menu'=>$menu];
            }
        } catch (Exception $ex) {
            throw new Exception('Error ShowTickets Index: '.$ex->getMessage());
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
    public function reservations()
    {
        try {  
            //init
            $input = Input::all(); 
            //get
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
//                $menu = RestaurantMenu::find($input['id']);
//                if($menu)
//                    return ['success'=>true,'menu'=>$menu];                
//                return ['success'=>false,'msg'=>'There is an error getting the menu.<br>Item not longer in the system.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
//                if(!empty($input['id']))
//                {
//                    $menu = RestaurantMenu::find($input['id']);
//                    if($menu)
//                    {
//                        function remove_children($m)
//                        {
//                            $children = $m->children();
//                            if(count($children))
//                            {
//                                foreach ($children as $c)
//                                    remove_children($m);
//                            }
//                            $m->delete();
//                        }
//                        remove_children($menu);
//                    }
//                    $menu = $this->menus_formated();   
//                    return ['success'=>true,'menu'=>$menu,'msg'=>'Menus and submenus removed successfully!'];
//                }
//                return ['success'=>false,'msg'=>'There was an error deleting the menu and submenus.<br>You must select a valid item.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
//                if(!empty($input['id']))
//                {
//                    $menu = RestaurantMenu::find($input['id']);
//                    if(!$menu)
//                        return ['success'=>false,'msg'=>'There was an error updating the menu.<br>The item is not longer in the system.'];
//                }
//                else
//                {
//                    $menu = new RestaurantMenu;
//                }
//                $menu->name = strip_tags(trim($input['name']));
//                $menu->notes = (!empty($input['notes']))? strip_tags(trim($input['notes'])) : null;
//                $menu->disabled = (!empty($input['disabled']))? 1 : 0;
//                $menu->parent_id = $input['parent_id'];
//                $menu->save();
//                //return
//                $menu = $this->menus_formated();   
//                return ['success'=>true,'menu'=>$menu,'msg'=>'Menu saved successfully!'];
            }
            else if(isset($input) && isset($input['restaurants_id'])) //get all
            {
                $reservations = DB::table('restaurant_reservations')
                                ->select('restaurant_reservations.*')
                                ->select(DB::raw('restaurant_reservations.*'))
                                ->where('restaurant_reservations.restaurants_id',$input['restaurants_id'])
                                ->whereDate('restaurant_reservations.schedule','>=', date('Y-m-d',strtotime($this->start_reservations())) )
                                ->orderBy('restaurant_reservations.schedule','DESC')
                                ->get(); 
                return ['success'=>true,'reservations'=>$reservations];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error ShowTickets Index: '.$ex->getMessage());
        }
    }
    /**
     * Get, Edit items for restaurants
     *
     * @return view
     */
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
                    $items = DB::table('restaurant_items')
                        ->join('restaurant_menu', 'restaurant_menu.id', '=' ,'restaurant_items.restaurant_menu_id')
                        ->select('restaurant_items.*', 'restaurant_menu.name AS menu')
                        ->where('restaurant_items.restaurants_id',$item->restaurants_id)
                        ->orderBy('restaurant_menu.name')->orderBy('restaurant_items.order')
                        ->get();
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
                else
                    $item->delete_image();
                //order
                $item->order = $input['order'];
                $item->save();
                $items = DB::table('restaurant_items')
                        ->join('restaurant_menu', 'restaurant_menu.id', '=' ,'restaurant_items.restaurant_menu_id')
                        ->select('restaurant_items.*', 'restaurant_menu.name AS menu')
                        ->where('restaurant_items.restaurants_id',$input['restaurants_id'])
                        ->orderBy('restaurant_menu.name')->orderBy('restaurant_items.order')
                        ->get();
                foreach ($items as $index=>$i)
                {
                    $i->image_id = Image::view_image($i->image_id);
                    $i->order = $index+1;
                    RestaurantItems::where('id',$i->id)->update(['order'=>$i->order]);
                }
                //return
                return ['success'=>true,'items'=>$items];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error ShowTickets Index: '.$ex->getMessage());
        }
    }
    
    /**
     * Get, Edit awards for restaurants
     *
     * @return view
     */
    public function awards()
    {
        try {  
            //init
            $input = Input::all(); 
            //get
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $award = DB::table('restaurant_awards')
                                ->leftJoin('images', 'images.id', '=' ,'restaurant_items.image_id')
                                ->select('restaurant_items.*', 'restaurant_menu.name AS menu', 'images.url')
                                ->where('restaurant_items.id',$input['id'])
                                ->orderBy('restaurant_menu.name')->orderBy('restaurant_items.order')
                                ->first();
                if($item)
                {
                    $item->url = Image::view_image($item->url);
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
                    $items = DB::table('restaurant_items')
                        ->join('restaurant_menu', 'restaurant_menu.id', '=' ,'restaurant_items.restaurant_menu_id')
                        ->select('restaurant_items.*', 'restaurant_menu.name AS menu')
                        ->where('restaurant_items.restaurants_id',$item->restaurants_id)
                        ->orderBy('restaurant_menu.name')->orderBy('restaurant_items.order')
                        ->get();
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
                if(!empty($input['url']))
                {
                    if(preg_match('/media\/preview/',$input['url'])) 
                    {
                        $item->delete_image();
                        $item->set_image($input['url']);
                    }
                }
                else
                    $item->delete_image();
                //order
                $item->order = $input['order'];
                $item->save();
                $items = DB::table('restaurant_items')
                        ->join('restaurant_menu', 'restaurant_menu.id', '=' ,'restaurant_items.restaurant_menu_id')
                        ->select('restaurant_items.*', 'restaurant_menu.name AS menu')
                        ->where('restaurant_items.restaurants_id',$input['restaurants_id'])
                        ->orderBy('restaurant_menu.name')->orderBy('restaurant_items.order')
                        ->get();
                foreach ($items as $index=>$i)
                {
                    $i->image_id = Image::view_image($i->image_id);
                    $i->order = $index+1;
                    RestaurantItems::where('id',$i->id)->update(['order'=>$i->order]);
                }
                //return
                return ['success'=>true,'items'=>$items];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error ShowTickets Index: '.$ex->getMessage());
        }
    }
    
}
