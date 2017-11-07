<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Slider;
use App\Http\Models\Image;
use App\Http\Models\Category;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the default method for the website.
     *
     * @return Method
     */
    public function index()
    {
        return $this->home();
    }
    
    /**
     * List all acls and return default view.
     *
     * @return view
     */
    public function home()
    {
        try {
            //get sliders
            $sliders = Slider::orderBy('n_order')->get();
            foreach ($sliders as $s)
                $s->image_url = Image::view_image($s->image_url);
            //get shows
            $shows = DB::table('shows')
                        ->join('show_images', 'show_images.show_id', '=' ,'shows.id')
                        ->join('images', 'show_images.image_id', '=' ,'images.id')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                        ->select(DB::raw('shows.id, shows.venue_id, shows.name, images.url, locations.city, 
                                          venues.name AS venue, MIN(show_times.show_time) AS show_time, shows.slug, show_times.time_alternative,
                                          MIN(tickets.retail_price+tickets.processing_fee) AS price, shows.starting_at'))    
                        ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('images.image_type','=','Logo')
                        ->where('show_times.show_time','>',\Carbon\Carbon::now())->where('show_times.is_active','=',1)
                        ->whereNotNull('images.url')
                        ->orderBy('shows.sequence','ASC')->orderBy('show_times.show_time','ASC')
                        ->groupBy('shows.id')
                        ->distinct()->get();
            foreach ($shows as $s)
            if(!empty($s->url))
                $s->url = Image::view_image($s->url);
            //get categories
            $categories = Category::all();
            //get cities
            $cities = DB::table('venues')
                        ->join('venue_images', 'venue_images.venue_id', '=' ,'venues.id')
                        ->join('images', 'venue_images.image_id', '=' ,'images.id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->select('locations.city','locations.state','locations.country')
                        ->where('venues.is_featured','>',0)->where('images.image_type','=','Logo')
                        ->whereNotNull('images.url')
                        ->orderBy('locations.city')->groupBy('locations.city')
                        ->distinct()->get();
            //return view
            return view('production.home.index',compact('sliders','shows','categories','cities'));
           
        } catch (Exception $ex) {
            throw new Exception('Error Production Home Index: '.$ex->getMessage());
        }
    }
    
    /**
     * Search shows according to params in home index.
     *
     * @return view
     */
    public function search()
    {
        try {
            //init
            $input = Input::all(); 
            //calculate subcategories 
            if(!empty($input['category']) && is_numeric($input['category']))
            {
                $result = [];
                function subCategories($result,$category)
                {
                    $subCat = $category->children()->get();
                    if(count($subCat))
                        foreach ($subCat as $c)
                            $result = subCategories($result,$c);
                    $result[] = $category->id;
                    return $result;
                }
                $category = Category::where('id',$input['category'])->first();
                if($category)
                    $input['category'] = subCategories($result,$category);
                else 
                    unset($input['category']);
            }
            else 
                unset($input['category']);
            //get shows
            $shows = DB::table('shows')
                        ->join('show_images', 'show_images.show_id', '=' ,'shows.id')
                        ->join('images', 'show_images.image_id', '=' ,'images.id')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->select(DB::raw('shows.id, DATE_FORMAT(MIN(show_times.show_time),"%m/%d/%Y") AS date_next_on,
                                          DATE_FORMAT(MIN(show_times.show_time),"%M %d, %Y @ %h:%i %p") AS date_venue_on, show_times.time_alternative'))    
                        ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('images.image_type','=','Logo')
                        ->where('show_times.show_time','>',\Carbon\Carbon::now())->where('show_times.is_active','=',1)
                        ->whereNotNull('images.url')
                    //custom
                        ->when(!empty($input['city']), function($shows) use ($input){
                            return $shows->where('locations.city','LIKE',$input['city']);
                        })
                        ->when(!empty($input['start_date']) && strtotime($input['start_date']), function($shows) use ($input){
                            return $shows->whereDate('show_times.show_time','>=',$input['start_date']);
                        })
                        ->when(!empty($input['end_date']) && strtotime($input['end_date']), function($shows) use ($input){
                            return $shows->whereDate('show_times.show_time','<=',$input['end_date']);
                        })
                        ->when(!empty($input['category']) && is_array($input['category']), function($shows) use ($input){
                            return $shows->whereIn('shows.category_id',$input['category']);
                        })
                    //custom
                        ->orderBy('shows.sequence','ASC')->orderBy('show_times.show_time','ASC')
                        ->groupBy('shows.id')
                        ->distinct()->get();
            //return view
            return ['success'=>true,'shows'=>$shows];
           
        } catch (Exception $ex) {
            throw new Exception('Error Production Home Search: '.$ex->getMessage());
        }
    }
    
    
}
