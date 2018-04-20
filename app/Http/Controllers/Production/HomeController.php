<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Slider;
use App\Http\Models\User;
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
            //get sliders
            $sliders = Slider::orderBy('n_order')->get();
            foreach ($sliders as $s) {
                $s->image_url = Image::view_image($s->image_url);
            }

            // Don't hide shows for Seller accounts hack
            if (Auth::check() && in_array(Auth::user()->user_type_id, explode(',', env('SELLER_OPTION_USER_TYPE')))) {
                $nowVar = Carbon::now()->subDay()->toDateTimeString();
                $venues_edit = Auth::user()->venues_check_ticket;
                $venues_check = (!empty($venues_edit))? explode(',',$venues_edit) : [];
                $link = 'pos/buy/';
            } else {
                $nowVar = Carbon::now()->toDateTimeString();
                $venues_check = null;
                $link = 'event/';
            }
            
            //get cities
            $cities = DB::table('venues')
                ->join('locations', 'locations.id', '=', 'venues.location_id')
                ->select('locations.city', 'locations.state', 'locations.country')
                ->where('venues.is_featured', '>', 0)
                ->whereNotNull('venues.logo_url');
            if(!is_null($venues_check))
                $cities = $cities->whereIn('venues.id',$venues_check);
            $cities = $cities->orderBy('locations.city')->groupBy('locations.city')->distinct()->get();
            
            //get venues
            $venues = DB::table('venues')
                ->join('shows', 'venues.id', '=', 'shows.venue_id')
                ->join('locations', 'locations.id', '=', 'venues.location_id')
                ->join('show_times', 'shows.id', '=', 'show_times.show_id')
                ->join('tickets', 'tickets.show_id', '=', 'shows.id')
                ->select('venues.id', 'venues.name', 'locations.city')
                ->where('venues.is_featured', '>', 0)
                ->where('shows.is_active', '>', 0)->where('shows.is_featured', '>', 0)
                ->where(function ($query) use ($nowVar) {
                    $query->whereNull('shows.on_featured')
                        ->orWhere('shows.on_featured', '<=', $nowVar);
                })
                ->where(function ($query) use ($nowVar) {
                    $query->where('show_times.show_time', '>=', $nowVar);
                })
                ->where('show_times.is_active', '=', 1)
                ->whereNotNull('venues.logo_url');
            if(!is_null($venues_check))
                $venues = $venues->whereIn('venues.id',$venues_check);
            $venues = $venues->orderBy('venues.name')->groupBy('venues.id')->distinct()->get();

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
                ->where(function ($query) use ($nowVar) {
                    $query->whereNull('shows.on_featured')
                        ->orWhere('shows.on_featured', '<=', $nowVar);
                })
                ->where(function ($query) use ($nowVar) {
                    $query->where('show_times.show_time', '>=', $nowVar);
                })
                ->where('show_times.is_active', '=', 1)
                ->whereNotNull('shows.logo_url');
            if(!is_null($venues_check))
                $shows = $shows->whereIn('venues.id',$venues_check);
            $shows = $shows->orderBy('shows.sequence', 'ASC')->orderBy('show_times.show_time', 'ASC')->groupBy('shows.id')->distinct()->get();

            foreach ($shows as $s) {
                //add link here
                $s->link = '/'.$link.$s->slug;
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

            // Don't hide shows for Seller accounts hack
            if (Auth::check() && in_array(Auth::user()->user_type_id, explode(',', env('SELLER_OPTION_USER_TYPE')))) {
                $nowVar = Carbon::now()->subDay()->toDateTimeString();
                $venues_edit = Auth::user()->venues_check_ticket;
                $venues_check = (!empty($venues_edit))? explode(',',$venues_edit) : [];
            } else {
                $nowVar = Carbon::now()->toDateTimeString();
                $venues_check = null;
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
                ->where(function ($query) use ($nowVar) {
                    $query->whereNull('shows.on_featured')
                        ->orWhere('shows.on_featured', '<=', $nowVar);
                })
                ->where(function ($query) use ($nowVar) {
                    $query->where('show_times.show_time', '>=', $nowVar);
                })
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
                ->when(!is_null($venues_check), function ($shows) use ($venues_check) {
                    return $shows->whereIn('venues.id',$venues_check);
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
