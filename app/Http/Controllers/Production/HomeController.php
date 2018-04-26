<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Slider;
use App\Http\Models\User;
use App\Http\Models\Util;
use App\Http\Models\Image;
use App\Http\Models\Category;
use Carbon\Carbon;

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
            //get categories
            $categs = Category::get_categories();
            $cats = $categories = $cities = $venues = [];
            $current = date('Y-m-d H:i:s');
            $options = Util::display_options_by_user();
            
            //get sliders
            $sliders = Slider::orderBy('n_order')->get();
            foreach ($sliders as $s) {
                $s->image_url = Image::view_image($s->image_url);
            }
            
            //get shows
            $shows = DB::table('shows')
                ->join('venues', 'venues.id', '=', 'shows.venue_id')
                ->join('locations', 'locations.id', '=', 'venues.location_id')
                ->join('show_times', 'shows.id', '=', 'show_times.show_id')
                ->join('tickets', 'tickets.show_id', '=', 'shows.id')
                ->select(DB::raw('shows.id, shows.venue_id, shows.name, shows.logo_url, locations.city, locations.country, locations.state, shows.category_id,
                                          venues.name AS venue, MIN(show_times.show_time) AS show_time, shows.slug, show_times.time_alternative,
                                          MIN(tickets.retail_price+tickets.processing_fee) AS price, shows.starting_at, shows.regular_price'))
                ->where('venues.is_featured', '>', 0)
                ->where('shows.is_active', '>', 0)->where('shows.is_featured', '>', 0)
                ->where(function ($query) use ($current) {
                    $query->whereNull('shows.on_featured')
                        ->orWhere('shows.on_featured', '<=', $current);
                })
                ->where($options['where'])
                ->where('show_times.is_active', '>', 0)
                ->whereNotNull('venues.logo_url')
                ->whereNotNull('shows.logo_url')
                ->when(!is_null($options['venues']), function ($shows) use ($options) {
                    return $shows->whereIn('venues.id',$options['venues']);
                })
                ->orderBy('shows.sequence', 'ASC')->orderBy('show_times.show_time', 'ASC')->groupBy('shows.id')->distinct()->get();

            foreach ($shows as $s) {
                //venues
                if(!isset($venues[$s->venue_id]))
                    $venues[$s->venue_id] = ['id'=>$s->venue_id, 'name'=>$s->venue, 'city'=>$s->city];
                //cities
                if(!isset($cities[$s->city]))
                    $cities[$s->city] = ['city'=>$s->city, 'state'=>$s->state, 'country'=>$s->country];                
                //add link here
                $s->link = '/'.$options['link'].$s->slug;
                //set up url
                if (!empty($s->logo_url)) {
                    $s->logo_url = Image::view_image($s->logo_url);
                }
                //category filter 1
                if (!in_array($s->category_id, $cats)) {
                    $cats[] = $s->category_id;
                    $c = Category::find($s->category_id);
                    if ($c && $c->id_parent != 0) {
                        $father = $c->id_parent;
                        while (!in_array($father, $cats)) {
                            $cats[] = $father;
                            if ($father != 0) {
                                $c = $c->parent();
                            } else {
                                break;
                            }
                        }
                    }
                }
            }
            //category filter 2
            foreach ($categs as $c) {
                if (in_array($c->id, $cats)) {
                    $categories[] = $c;
                }
            }            

            //return view
            return view('production.home.index', compact('sliders', 'shows', 'categories', 'cities', 'venues'));

        } catch (Exception $ex) {
            throw new Exception('Error Production Home Index: ' . $ex->getMessage());
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
            $current = date('Y-m-d H:i:s');
            $options = Util::display_options_by_user();
            
            //calculate subcategories
            if (!empty($input['category']) && is_numeric($input['category'])) {
                $result = [];
                function subCategories($result, $category)
                {
                    $subCat = $category->children()->get();
                    if (count($subCat)) {
                        foreach ($subCat as $c) {
                            $result = subCategories($result, $c);
                        }
                    }
                    $result[] = $category->id;
                    return $result;
                }

                $category = Category::where('id', $input['category'])->first();
                if ($category) {
                    $input['category'] = subCategories($result, $category);
                } else {
                    unset($input['category']);
                }
            } else {
                unset($input['category']);
            }

            //get shows
            $shows = DB::table('shows')
                ->join('venues', 'venues.id', '=', 'shows.venue_id')
                ->join('locations', 'locations.id', '=', 'venues.location_id')
                ->join('show_times', 'shows.id', '=', 'show_times.show_id')
                ->select(DB::raw('shows.id, show_times.time_alternative,
                                          DATE_FORMAT(MIN(show_times.show_time),"%b %d, %Y @ %h:%i %p") AS date_venue_on'))
                ->where('venues.is_featured', '>', 0)
                ->where('shows.is_active', '>', 0)->where('shows.is_featured', '>', 0)
                ->where(function ($query) use ($current) {
                    $query->whereNull('shows.on_featured')
                        ->orWhere('shows.on_featured', '<=', $current);
                })
                ->where($options['where'])
                ->where('show_times.is_active', '=', 1)
                ->whereNotNull('shows.logo_url')
                //custom
                ->when(!empty($input['city']), function ($shows) use ($input) {
                    return $shows->where('locations.city', 'LIKE', $input['city']);
                })
                ->when(!empty($input['venue']), function ($shows) use ($input) {
                    return $shows->where('venues.id', '=', $input['venue']);
                })
                ->when(!empty($input['start_date']) && strtotime($input['start_date']), function ($shows) use ($input) {
                    return $shows->whereDate('show_times.show_time', '>=', $input['start_date']);
                })
                ->when(!empty($input['end_date']) && strtotime($input['end_date']), function ($shows) use ($input) {
                    return $shows->whereDate('show_times.show_time', '<=', $input['end_date']);
                })
                ->when(!empty($input['category']) && is_array($input['category']), function ($shows) use ($input) {
                    return $shows->whereIn('shows.category_id', $input['category']);
                })
                ->when(!is_null($options['venues']), function ($shows) use ($options) {
                    return $shows->whereIn('venues.id',$options['venues']);
                })
                //custom
                ->orderBy('shows.sequence', 'ASC')->orderBy('show_times.show_time', 'ASC')
                ->groupBy('shows.id')
                ->distinct()->get();
            //return view
            return ['success' => true, 'shows' => $shows];

        } catch (Exception $ex) {
            throw new Exception('Error Production Home Search: ' . $ex->getMessage());
        }
    }


}
