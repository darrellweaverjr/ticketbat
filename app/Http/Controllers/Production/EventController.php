<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Http\Models\Image;
use App\Http\Models\Shoppingcart;
use App\Http\Models\Util;

class EventController extends Controller
{
    /**
     * Show the default method for the event page.
     *
     * @return Method
     */
    public function index($slug)
    {
        try {
            if(empty($slug))
                return redirect()->route('index');
            //get all records
            $event = DB::table('shows')
                        ->join('venues', 'venues.id', '=', 'shows.venue_id')
                        ->join('locations', 'locations.id', '=', 'venues.location_id')
                        ->select(DB::raw('shows.id as show_id, shows.slug, shows.on_sale, shows.short_description, shows.description, shows.url, 
                                          shows.facebook, shows.twitter,shows.googleplus, shows.yelpbadge, shows.youtube, shows.instagram,
                                          venues.name as venue, shows.name, locations.*, shows.presented_by, shows.sponsor, 
                                          shows.sponsor_logo_id, venues.cutoff_text, shows.restrictions, shows.venue_id, shows.ua_conversion_code,
                                          IF(shows.restrictions!="None",shows.restrictions,venues.restrictions) AS restrictions'))
                        ->where('shows.is_active','>',0)->where('venues.is_featured','>',0)
                        ->where('shows.slug', $slug)->first();
            if(!$event)
                return redirect()->route('index');
            //funnel
            $input = Input::all();  
            if(!empty($input['funnel']) && in_array($input['funnel'], [0,1]))
            {
                Session::put('funnel', $input['funnel']);
                Session::put('slug', $event->slug.'?funnel='.$input['funnel']);
                if(!empty($event->ua_conversion_code))
                    Session::put('ua_code', $event->ua_conversion_code);
            }
            else
            {
                Session::forget('funnel');
                Session::forget('slug');
                Session::forget('ua_code');
            }
            //format sponsor pic
            $event->sponsor_logo_id = Image::view_image($event->sponsor_logo_id);
            //get header
            $event->header = DB::table('images')
                                ->join('show_images', 'show_images.image_id', '=', 'images.id')
                                ->select(DB::raw('images.url, images.caption'))
                                ->where('show_images.show_id',$event->show_id)->where('images.image_type','=','Header')->first();
            //set header of venue if not show header
            if(!$event->header)
                $event->header = DB::table('images')
                                ->join('venue_images', 'venue_images.image_id', '=', 'images.id')
                                ->select(DB::raw('images.url, images.caption'))
                                ->where('venue_images.venue_id',$event->venue_id)->where('images.image_type','=','Header')->first();
            $event->header->url = Image::view_image($event->header->url);
            //get logo
            $event->logo = DB::table('images')
                                ->join('show_images', 'show_images.image_id', '=', 'images.id')
                                ->select(DB::raw('images.url, images.caption'))
                                ->where('show_images.show_id',$event->show_id)->where('images.image_type','=','Logo')->first();
            $event->logo->url = Image::view_image($event->logo->url);
            //get images
            $event->images = DB::table('images')
                                ->join('show_images', 'show_images.image_id', '=', 'images.id')
                                ->select(DB::raw('images.url, images.caption'))
                                ->where('show_images.show_id',$event->show_id)->where('images.image_type','=','Image')->get();
            foreach ($event->images as $i)
                $i->url = Image::view_image($i->url);
            //get banners
            $event->banners = DB::table('banners')
                                ->select(DB::raw('banners.id, banners.url, banners.file'))
                                ->where(function($query) use ($event) {
                                    $query->whereRaw('banners.parent_id = '.$event->show_id.' AND banners.belongto="show" ')
                                          ->orWhereRaw('banners.parent_id = '.$event->venue_id.' AND banners.belongto="venue" ');
                                })
                                ->where('banners.type','like','%Show Page%')->get();
            foreach ($event->banners as $b)
                $b->file = Image::view_image($b->file);
            //get videos
            $event->videos = DB::table('videos')
                                ->join('show_videos', 'show_videos.video_id', '=', 'videos.id')
                                ->select(DB::raw('videos.id, videos.embed_code, videos.description'))
                                ->where('show_videos.show_id',$event->show_id)/*->where('videos.video_type','=','Video')*/->get();
            foreach ($event->videos as $v)
            {
                $part1 = explode('src="',$v->embed_code);
                $part2 = explode('"',$part1[1]);
                $v->embed_code = $part2[0];
            } 
            //get bands
            $event->bands = DB::table('bands')
                                ->join('categories', 'bands.category_id', '=', 'categories.id')
                                ->join('show_bands', 'show_bands.band_id', '=', 'bands.id')
                                ->select(DB::raw('bands.*, categories.name AS category'))
                                ->where('show_bands.show_id',$event->show_id)->orderBy('show_bands.n_order')->get();
            foreach ($event->bands as $b)
                $b->image_url = Image::view_image($b->image_url);
            //get showtimes
            $event->showtimes = DB::table('show_times')
                                ->join('shows', 'show_times.show_id', '=', 'shows.id')
                                ->select(DB::raw('show_times.id, show_times.time_alternative,
                                                 DATE_FORMAT(show_times.show_time,"%Y/%m/%d %H:%i") AS show_time,
                                                 DATE_FORMAT(show_times.show_time,"%W") AS show_day,
                                                 DATE_FORMAT(show_times.show_time,"%M %D") AS show_date,
                                                 DATE_FORMAT(show_times.show_time,"%l:%i %p") AS show_hour,
                                                 IF(show_times.slug, show_times.slug, shows.ext_slug) AS ext_slug,
                                                 IF(NOW()>DATE_SUB(show_times.show_time,INTERVAL shows.cutoff_hours HOUR) AND NOW()<show_times.show_time, 1, 0) as presale'))
                                ->where('show_times.show_id',$event->show_id)->where('show_times.is_active','>',0)
                                ->whereRaw(DB::raw('show_times.show_time > NOW()'))
                                ->where(function($query) {
                                    if(Auth::check() && Auth::user()->user_type_id!=1)
                                        $query->whereRaw(DB::raw('DATE_SUB(show_times.show_time, INTERVAL shows.cutoff_hours HOUR) > NOW()'));
                                })
                                ->orderBy('show_times.show_time')->get();
            //return view
            return view('production.events.index',compact('event'));
        } catch (Exception $ex) {
            throw new Exception('Error Production Event Index: '.$ex->getMessage());
        }
    }
    
    /**
     * Show the default method for the buy page.
     *
     * @return Method
     */
    public function buy($slug,$product)
    {
        try {
            $qty_tickets_sell = 20;
            if(empty($slug) || empty($product))
                return redirect()->route('index');
            //get all records
            $event = DB::table('shows')
                        ->join('venues', 'venues.id', '=', 'shows.venue_id')
                        ->join('stages', 'stages.venue_id', '=', 'venues.id')
                        ->join('show_times', 'show_times.show_id', '=', 'shows.id')
                        ->select(DB::raw('shows.id as show_id, show_times.id AS show_time_id, shows.name, 
                                          venues.name AS venue, stages.image_url, DATE_FORMAT(show_times.show_time,"%W, %M %d, %Y @ %l:%i %p") AS show_time, 
                                          show_times.time_alternative, shows.amex_only_ticket_types, stages.id AS stage_id, stages.ticket_order,
                                          CASE WHEN (NOW()>shows.amex_only_start_date) && NOW()<shows.amex_only_end_date THEN 1 ELSE 0 END AS amex_only,
                                          shows.on_sale, CASE WHEN NOW() > (show_times.show_time - INTERVAL shows.cutoff_hours HOUR) THEN 0 ELSE 1 END AS for_sale'))
                        ->where('shows.is_active','>',0)->where('venues.is_featured','>',0)
                        ->where('shows.slug', $slug)->where('show_times.id', $product)->where('show_times.is_active','>',0)
                        ->whereRaw(DB::raw('show_times.show_time > NOW()'))
                        ->where(function($query) {
                            if(Auth::check() && Auth::user()->user_type_id!=1)
                                $query->whereRaw(DB::raw('DATE_SUB(show_times.show_time, INTERVAL shows.cutoff_hours HOUR) > NOW()'));
                        })
                        ->first();
            if(!$event)
                return redirect()->route('index');
            //formats
            $event->image_url = Image::view_image($event->image_url);
            $event->amex_only_ticket_types = (!empty($event->amex_only_ticket_types))? explode(',', $event->amex_only_ticket_types) : [];
            //get stage images
            $event->stage_images = DB::table('images')
                                ->join('stage_image_ticket_type', 'stage_image_ticket_type.image_id', '=', 'images.id')
                                ->select(DB::raw('images.url, stage_image_ticket_type.ticket_type'))
                                ->where('stage_image_ticket_type.stage_id',$event->stage_id)->get();
            foreach ($event->stage_images as $i)
                $i->url = Image::view_image($i->url);
            //passwords
            $passwords = DB::table('show_passwords')
                                ->select(DB::raw('show_passwords.ticket_types'))
                                ->whereRaw(DB::raw('NOW()>show_passwords.start_date'))->whereRaw(DB::raw('NOW()<show_passwords.end_date'))
                                ->where('show_passwords.show_id',$event->show_id)->groupBy('show_passwords.id')->orderBy('show_passwords.id','DESC')->get();
            //get tickets/coupon in shoppingcart and session
            $s_token = Util::s_token(false, true);
            $coupon = array_merge( Shoppingcart::tickets_coupon($s_token) , Util::tickets_coupon() );
            $has_coupon = 0;
            //get tickets types
            $event->tickets = [];
            $tickets = DB::table('tickets')
                                ->join('packages', 'packages.id', '=', 'tickets.package_id')
                                ->select(DB::raw('tickets.id AS ticket_id, packages.title, tickets.ticket_type, tickets.ticket_type_class,
                                                  tickets.retail_price,
                                                  (CASE WHEN (tickets.max_tickets > 0) THEN (tickets.max_tickets-(SELECT COALESCE(SUM(p.quantity),0) FROM purchases p WHERE p.ticket_id = tickets.id AND p.show_time_id = '.$event->show_time_id.')) ELSE '.$qty_tickets_sell.' END) AS max_available'))
                                ->where('tickets.show_id',$event->show_id)->where('tickets.is_active','>',0)
                                ->whereRaw(DB::raw('tickets.id NOT IN (SELECT ticket_id FROM soldout_tickets WHERE show_time_id = '.$event->show_time_id.')'))
                                ->where(function($query) use ($event) {
                                    $query->where('tickets.max_tickets', '<=', 0)
                                    ->orWhereRaw('tickets.max_tickets-(SELECT COALESCE(SUM(p.quantity),0) FROM purchases p WHERE p.ticket_id = tickets.id AND p.show_time_id = '.$event->show_time_id.')','>',0);
                                })
                                ->groupBy('tickets.id')->orderBy('tickets.is_default','DESC')->get();
            foreach ($tickets as $t)
            {
                //max available
                if($t->max_available > $qty_tickets_sell)
                    $t->max_available = $qty_tickets_sell;
                //id
                $id = preg_replace("/[^A-Za-z0-9]/", '_', $t->ticket_type);
                //amex
                $amex_only = ($event->amex_only>0 && in_array($t->ticket_type, $event->amex_only_ticket_types))? 1 : 0;
                //password
                $pass = 0;
                foreach ($passwords as $p)
                {
                    if(in_array($t->ticket_type, explode(',',$p->ticket_types)))
                    {
                        $pass = 1; 
                        break;
                    }
                }
                //tickets/coupon
                if(in_array($t->ticket_id, $coupon))
                {
                    $t->coupon = 1;
                    $has_coupon = 1;
                }
                else
                    $t->coupon = 0;
                //fill out tickets
                if(isset($event->tickets[$id]))
                    $event->tickets[$id]['tickets'][] = $t;
                else 
                    $event->tickets[$id] = ['type'=>$t->ticket_type,'class'=>$t->ticket_type_class,'amex_only'=>$amex_only,'password'=>$pass,'tickets'=>[$t]];
            }
            //order the ticket types according to the stage order
            if(!empty($event->ticket_order))
            {
                $ticket_order = explode(',',$event->ticket_order);
                $new_order = [];
                foreach ($ticket_order as $o)
                {
                    $id = preg_replace("/[^A-Za-z0-9]/", '_', $o);
                    if(!empty($event->tickets[$id]))
                    {
                        $new_order[$id] = $event->tickets[$id];
                        unset($event->tickets[$id]);
                    }
                }
                $event->tickets = array_merge($new_order,$event->tickets); 
            }
            //return view
            return view('production.events.buy',compact('event','has_coupon'));
        } catch (Exception $ex) {
            throw new Exception('Error Production Buy Index: '.$ex->getMessage());
        }
    }
       
}
