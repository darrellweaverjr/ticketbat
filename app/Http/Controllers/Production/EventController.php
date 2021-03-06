<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Http\Models\Image;
use App\Http\Models\Shoppingcart;
use App\Http\Models\User;
use App\Http\Models\Util;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    private $style_url = 'styles/ticket_types.css';

    /**
     * Show the default method for the event page.
     *
     * @return Method
     */
    public function index($slug)
    {
        try {
            if (empty($slug)) {
                return redirect()->route('index');
            }
            $current = date('Y-m-d H:i:s');
            $options = Util::display_options_by_user();
            //get all records
            $event = DB::table('shows')
                ->join('venues', 'venues.id', '=', 'shows.venue_id')
                ->join('locations', 'locations.id', '=', 'venues.location_id')
                ->select(DB::raw('shows.id as show_id, shows.slug, shows.on_sale, shows.short_description, shows.description, shows.url, shows.header_url AS header,
                                          shows.facebook, shows.twitter,shows.googleplus, shows.yelpbadge, shows.youtube, shows.instagram, venues.header_url,
                                          venues.name as venue, shows.name, locations.*, shows.presented_by, shows.starting_at, shows.regular_price, shows.sponsor,
                                          shows.sponsor_logo_id, venues.cutoff_text, shows.restrictions, shows.venue_id, shows.ua_conversion_code, shows.ext_slug,
                                          IF(shows.restrictions!="None",shows.restrictions,venues.restrictions) AS restrictions'))
                ->where('shows.is_active', '>', 0)->where('venues.is_featured', '>', 0)
                ->where(function ($query) {
                    $query->whereNull('shows.on_featured')
                        ->orWhere('shows.on_featured', '<=', \Carbon\Carbon::now());
                })
                ->where('shows.slug', $slug)->first();
            if (!$event) {
                return redirect()->route('index');
            }
						//redirect if $slug
						if (!empty($event->ext_slug) && filter_var($event->ext_slug, FILTER_VALIDATE_URL)) {
						    return redirect()->to($event->ext_slug);
						}
            //funnel
            $input = Input::all();
            if (!empty($input['funnel']) && in_array($input['funnel'], [0, 1])) {
                Session::put('funnel', $input['funnel']);
                Session::put('slug', $event->slug . '?funnel=' . $input['funnel']);
                if (!empty($event->ua_conversion_code)) {
                    Session::put('ua_code', $event->ua_conversion_code);
                }
            } else {
                Session::forget('funnel');
                Session::forget('slug');
                Session::forget('ua_code');
            }
            //format sponsor pic
            $event->sponsor_logo_id = Image::view_image($event->sponsor_logo_id);
            //set header of venue if not show header or return home if none
            $event->header = (!empty($event->header))? Image::view_image($event->header) : Image::view_image($event->header_url);
            if(empty($event->header))
                return redirect()->route('index');
            //get mobile header
            $event->mobile_header = DB::table('images')
                ->join('show_images', 'show_images.image_id', '=', 'images.id')
                ->select(DB::raw('images.url, images.caption'))
                ->where('show_images.show_id', $event->show_id)->where('images.image_type', '=', 'Mobile Header')->first();
            if ($event->mobile_header) {
                $event->mobile_header->url = Image::view_image($event->mobile_header->url);
            }
            //get images
            $event->images = DB::table('images')
                ->join('show_images', 'show_images.image_id', '=', 'images.id')
                ->select(DB::raw('images.url, images.caption'))
                ->where('show_images.show_id', $event->show_id)->where('images.image_type', '=', 'Image')->get();
            foreach ($event->images as $i) {
                $i->url = Image::view_image($i->url);
            }
            //get banners
            $event->banners = DB::table('banners')
                ->select(DB::raw('banners.id, banners.url, banners.file'))
                ->where(function ($query) use ($event) {
                    $query->whereRaw('banners.parent_id = ' . $event->show_id . ' AND banners.belongto="show" ')
                        ->orWhereRaw('banners.parent_id = ' . $event->venue_id . ' AND banners.belongto="venue" ');
                })
                ->where('banners.type', 'like', '%Show Page%')->get();
            foreach ($event->banners as $b) {
                $b->file = Image::view_image($b->file);
            }
            //get videos
            $event->videos = DB::table('videos')
                ->join('show_videos', 'show_videos.video_id', '=', 'videos.id')
                ->select(DB::raw('videos.id, videos.embed_code, videos.description'))
                ->where('show_videos.show_id', $event->show_id)
                ->get();
            foreach ($event->videos as $v) {
                $part1 = explode('src="', $v->embed_code);
                $part2 = explode('"', $part1[1]);
                $v->embed_code = $part2[0];
            }
            //get bands
            $event->bands = DB::table('bands')
                ->join('categories', 'bands.category_id', '=', 'categories.id')
                ->join('show_bands', 'show_bands.band_id', '=', 'bands.id')
                ->select(DB::raw('bands.*, categories.name AS category'))
                ->where('show_bands.show_id', $event->show_id)->orderBy('show_bands.n_order')->get();
            foreach ($event->bands as $b) {
                $b->image_url = Image::view_image($b->image_url);
            }
            //showtimes
            $event->showtimes = DB::table('show_times')
                ->join('shows', 'show_times.show_id', '=', 'shows.id')
                ->join('tickets', 'tickets.show_id', '=', 'shows.id')
                ->select(DB::raw('show_times.id, show_times.time_alternative,
                                                 DATE_FORMAT(show_times.show_time,"%Y/%m/%d %H:%i") AS show_time,
                                                 DATE_FORMAT(show_times.show_time,"%a") AS show_day,
                                                 DATE_FORMAT(show_times.show_time,"%b %D") AS show_date,
                                                 DATE_FORMAT(show_times.show_time,"%l:%i %p") AS show_hour,
                                                 IF(show_times.slug, show_times.slug, shows.ext_slug) AS ext_slug,
                                                 IF(NOW()>DATE_SUB(show_times.show_time,INTERVAL shows.cutoff_hours HOUR), 1, 0) as presale'))
                ->where('show_times.show_id', $event->show_id)->where('show_times.is_active', '>', 0)
                ->where($options['where'])
                ->groupBy('show_times.id')->orderBy('show_times.show_time')->get();
            //get reviews
            $reviews = DB::table('show_reviews')
                ->select(DB::raw('COUNT(id) AS posts, AVG(rating) AS rating'))
                ->where('show_id', $event->show_id)->groupBy('show_id')->first();
            if ($reviews) {
                $event->reviews = ['posts' => $reviews->posts, 'rating' => $reviews->rating];
            } else {
                $event->reviews = ['posts' => 0, 'rating' => 0];
            }
            $event->reviews['comments'] = DB::table('show_reviews')
                ->join('users', 'show_reviews.user_id', '=', 'users.id')
                ->select(DB::raw('CONCAT(users.first_name," ",users.last_name) AS name, show_reviews.review, show_reviews.rating, show_reviews.created'))
                ->where('show_reviews.show_id', $event->show_id)->where('show_reviews.status', 'Approved')
                ->orderBy('show_reviews.created', 'DESC')->get();
            //fbq
            $fbq_events = json_encode([$event->name], JSON_UNESCAPED_SLASHES );
            //meta data
            $meta = ['description'=>$event->short_description];
            //return view
            return view('production.events.index', compact('event','meta','fbq_events'));
        } catch (Exception $ex) {
            throw new Exception('Error Production Event Index: ' . $ex->getMessage());
        }
    }

    /**
     * Show the default method for the buy page.
     *
     * @return Method
     */
    public function buy($slug, $product)
    {
        try {
            $current = date('Y-m-d H:i:s');
            $options = Util::display_options_by_user();
            $qty_tickets_sell = 20;
            $stock_avail_warning = 75;
            if(empty($slug) || empty($product))
                return redirect()->route('index');
            //get all records
            $event = DB::table('shows')
                ->join('venues', 'venues.id', '=', 'shows.venue_id')
                ->join('stages', 'stages.id', '=', 'shows.stage_id')
                ->join('show_times', 'show_times.show_id', '=', 'shows.id')
                ->join('tickets', 'tickets.show_id', '=', 'shows.id')
                ->select(DB::raw('shows.id as show_id, show_times.id AS show_time_id, shows.name, shows.ticket_limit, show_times.slug as ext_slug,
                                          venues.name AS venue, stages.image_url, DATE_FORMAT(show_times.show_time,"%W, %M %d, %Y @ %l:%i %p") AS show_time,
                                          show_times.time_alternative, shows.amex_only_ticket_types, stages.id AS stage_id, stages.ticket_order,
                                          CASE WHEN (NOW()>shows.amex_only_start_date) && NOW()<shows.amex_only_end_date THEN 1 ELSE 0 END AS amex_only,
                                          shows.on_sale, CASE WHEN NOW() > (show_times.show_time - INTERVAL shows.cutoff_hours HOUR) THEN 0 ELSE 1 END AS for_sale'))
                ->where('shows.is_active', '>', 0)->where('venues.is_featured', '>', 0)
                ->where(function ($query) use ($current) {
                    $query->whereNull('shows.on_sale')
                        ->orWhere('shows.on_sale', '<=', $current);
                })
                ->where(function ($query) use ($current) {
                    $query->whereNull('shows.on_featured')
                        ->orWhere('shows.on_featured', '<=', $current);
                })
                ->where($options['where'])
                ->where('shows.slug', $slug)->where('show_times.id', $product)
                ->where('show_times.is_active', '>', 0)->where('show_times.show_time', '>=', $current)
                ->first();
            if (!$event) {
                return redirect()->route('index');
            }
						//redirect if $slug
						if (!empty($event->ext_slug) && filter_var($event->ext_slug, FILTER_VALIDATE_URL)) {
						    return redirect()->to($event->ext_slug);
						}
            //formats
            $event->image_url = Image::view_image($event->image_url);
            $event->amex_only_ticket_types = (!empty($event->amex_only_ticket_types)) ? explode(',', $event->amex_only_ticket_types) : [];
            //get stage images
            $event->stage_images = DB::table('images')
                ->join('stage_image_ticket_type', 'stage_image_ticket_type.image_id', '=', 'images.id')
                ->select(DB::raw('images.url, stage_image_ticket_type.ticket_type'))
                ->where('stage_image_ticket_type.stage_id', $event->stage_id)->get();
            foreach ($event->stage_images as $i) {
                $i->url = Image::view_image($i->url);
            }
            //passwords
            $passwords = DB::table('show_passwords')
                ->select(DB::raw('show_passwords.ticket_types'))
                ->whereRaw(DB::raw('NOW()>show_passwords.start_date'))->whereRaw(DB::raw('NOW()<show_passwords.end_date'))
                ->where('show_passwords.show_id', $event->show_id)->groupBy('show_passwords.id')->orderBy('show_passwords.id', 'DESC')->get();
            //get tickets/coupon in shoppingcart and session
            $s_token = Util::s_token(false, true);
            $coupon = array_merge(Shoppingcart::tickets_coupon($s_token), Util::tickets_coupon());
            $has_coupon = 0;
            //checkings for qty if ticket limit by customer
            if (!empty($event->ticket_limit))
            {
                $event->ticket_left = $event->ticket_limit;
                $event->ticket_reserved = 0;
                $email_guest = Session::get('email_guest', null);
                $user_id = null;
                if (Auth::check()) {
                    $user_id = Auth::user()->id;
                } else {
                    if (!empty($email_guest)) {
                        $user = User::where('email', $email_guest)->first(['id']);
                        if ($user) {
                            $user_id = $user->id;
                        }
                    }
                }
                //get previous purchases by user
                if (!empty($user_id)) {
                    $purchases = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                        ->select(DB::raw('SUM(purchases.quantity) AS tickets'))
                        ->where('show_times.id', $event->show_time_id)->where('purchases.user_id', '=', $user_id)
                        ->groupBy('purchases.user_id')->first();
                    if ($purchases && !empty($purchases->tickets)) {
                        $event->ticket_left -= $purchases->tickets;
                        $event->ticket_reserved += $purchases->tickets;
                    }

                }
                //see in shoppingcart
                $cart = DB::table('shoppingcart')
                    ->join('show_times', 'show_times.id', '=', 'shoppingcart.item_id')
                    ->select(DB::raw('SUM(shoppingcart.number_of_items) AS tickets'))
                    ->where('show_times.id', $event->show_time_id)->where('shoppingcart.session_id', '=', $s_token)
                    ->groupBy('shoppingcart.session_id')->first();
                if ($cart && !empty($cart->tickets)) {
                    $event->ticket_left -= $cart->tickets;
                    $event->ticket_reserved += $cart->tickets;
                }
                //checking tickets left to buy
                $event->ticket_left = ($event->ticket_left < 0) ? 0 : $event->ticket_left;
            }

            //get tickets types
            $event->tickets = [];
            $tickets = DB::table('tickets')
                ->join('packages', 'packages.id', '=', 'tickets.package_id')
                ->select(DB::raw('tickets.id AS ticket_id, packages.title, tickets.ticket_type, tickets.ticket_type_class,
                                                  tickets.retail_price,
                                                  (CASE WHEN (tickets.max_tickets > 0) THEN (tickets.max_tickets-(SELECT COALESCE(SUM(p.quantity),0) FROM purchases p
                                                            WHERE p.ticket_id = tickets.id AND p.show_time_id = ' . $event->show_time_id . ')) ELSE null END) AS max_available'))
                ->where('tickets.show_id', $event->show_id)->where('tickets.is_active', '>', 0)
                ->where(function ($query) {
                    if (Auth::check() && in_array(Auth::user()->user_type_id, explode(',', env('SELLER_OPTION_USER_TYPE'))))
                        $query->where('tickets.only_pos', '>=', 0);
                    else
                        $query->where('tickets.only_pos', '=', 0);
                })
                ->whereRaw(DB::raw('tickets.id NOT IN (SELECT ticket_id FROM soldout_tickets WHERE show_time_id = ' . $event->show_time_id . ')'))
                ->where(function ($query) use ($event) {
                    $query->whereNull('tickets.max_tickets')
                          ->orWhere('tickets.max_tickets', '>', 0);
                })
                ->groupBy('tickets.id')->orderBy('tickets.is_default', 'DESC')->get();

            foreach ($tickets as $t) {
                //id
                $id = preg_replace("/[^A-Za-z0-9]/", '_', $t->ticket_type);

                //checking qty
                if(is_null($t->max_available))  //unlimited
                {
                    if (!empty($event->ticket_limit))   //event limit
                        $t->max_available = ($event->ticket_left < $qty_tickets_sell)? $event->ticket_left : $qty_tickets_sell;
                    else    //unlimited2
                        $t->max_available = $qty_tickets_sell;
                }
                else if($t->max_available > 0)  //availables
                {
                    if (!empty($event->ticket_limit))   //event limit
                    {
                        if($t->max_available <= $stock_avail_warning)
                            $t->in_stock = $t->max_available;
                        $t->max_available = ($event->ticket_left < $t->max_available - $event->ticket_reserved) ? $event->ticket_left : $t->max_available - $event->ticket_reserved;
                        $t->max_available = ($t->max_available < $qty_tickets_sell)? $t->max_available : $qty_tickets_sell;
                    }
                    else    //no-limit availables
                    {
                        if($t->max_available <= $stock_avail_warning)
                            $t->in_stock = $t->max_available;
                        if($t->max_available>$qty_tickets_sell)
                            $t->max_available=$qty_tickets_sell;
                    }
                }
                else //soldout
                    $t->max_available = 0;

                //if there is tickets availables
                if ($t->max_available >= 0) {
                    //amex
                    $amex_only = ($event->amex_only > 0 && in_array($t->ticket_type, $event->amex_only_ticket_types)) ? 1 : 0;
                    //password
                    $pass = 0;
                    foreach ($passwords as $p) {
                        if (in_array($t->ticket_type, explode(',', $p->ticket_types))) {
                            $pass = 1;
                            break;
                        }
                    }
                    //tickets/coupon
                    if (in_array($t->ticket_id, $coupon)) {
                        $t->coupon = 1;
                        $has_coupon = 1;
                    } else
                        $t->coupon = 0;

                    //fill out tickets
                    if (isset($event->tickets[$id]))
                        $event->tickets[$id]['tickets'][] = $t;
                    else
                        $event->tickets[$id] = ['type' => $t->ticket_type, 'class' => $t->ticket_type_class, 'amex_only' => $amex_only, 'password' => $pass, 'tickets' => [$t]];
                }
            }
            //order the ticket types according to the stage order
            if (!empty($event->ticket_order)) {
                $ticket_order = explode(',', $event->ticket_order);
                $new_order = [];
                foreach ($ticket_order as $o) {
                    $id = preg_replace("/[^A-Za-z0-9]/", '_', $o);
                    if (!empty($event->tickets[$id])) {
                        $new_order[$id] = $event->tickets[$id];
                        unset($event->tickets[$id]);
                    }
                }
                $event->tickets = array_merge($new_order, $event->tickets);
            }
            //checkings for sale
            if(empty($event->tickets) || ($event->for_sale && !empty($event->on_sale) && strtotime($event->on_sale) > strtotime('now')) )
                $event->for_sale = 0;
            //get styles from cloud
            $ticket_types_css = file_get_contents(env('IMAGE_URL_AMAZON_SERVER') . '/' . $this->style_url);
            //fbq
            $fbq_events = json_encode([$event->name], JSON_UNESCAPED_SLASHES );
            //return view
            return view('production.events.buy', compact('event', 'has_coupon', 'ticket_types_css','fbq_events'));
        } catch (Exception $ex) {
            throw new Exception('Error Production Buy Index: ' . $ex->getMessage());
        }
    }

    /**
     * Post reviews for the event
     *
     * @return Method
     */
    public function reviews()
    {
        try {
            //init
            $input = Input::all();
            $current = date('Y-m-d H:i:s');
            if (!empty($input['review']) && !empty($input['rating']) && !empty($input['show_id'])) {
                DB::table('show_reviews')->insert([
                    'show_id' => $input['show_id'],
                    'user_id' => Auth::user()->id,
                    'rating' => $input['rating'],
                    'review' => $input['review'],
                    'created' => $current,
                    'updated' => $current
                ]);
                $posts = DB::table('show_reviews')->where('show_id', $input['show_id'])->groupBy('show_id')->count();
                return ['success' => true, 'posts' => $posts, 'msg' => 'Review posted successfully!'];
            }
            return ['success' => false, 'msg' => 'You must fill out the form correctly.'];
        } catch (Exception $ex) {
            return ['success' => false, 'msg' => 'There is an error posting your reviews. Please, contact us.'];
        }
    }

}
