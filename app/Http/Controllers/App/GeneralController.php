<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use App\Http\Models\Image;
use App\Http\Models\Util;

/**
 * Manage General options for the app
 *
 * @author ivan
 */
class GeneralController extends Controller{
    
    
    /*
     * return arrays of all init values in json format
     */
    public function init()
    {
        try {
            return Util::json(['success'=>true,'cities'=>$this->cities(1),'shows'=>$this->shows(null,1),'venues'=>$this->venues(1),'x_token'=>csrf_field()]);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }    
    /*
     * return arrays of all cities in json format
     */
    public function cities($raw=null)
    {
        try {
            $cities = DB::table('venues')
                        ->join('venue_images', 'venue_images.venue_id', '=' ,'venues.id')
                        ->join('images', 'venue_images.image_id', '=' ,'images.id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->select('locations.city')
                        ->where('venues.is_featured','>',0)->where('images.image_type','=','Logo')
                        ->whereNotNull('images.url')
                        ->orderBy('locations.city')->groupBy('locations.city')
                        ->distinct()->get();
            if($raw) return $cities;
            return Util::json(['success'=>true, 'cities'=>$cities]);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }
    
    /*
     * return arrays of all shows (or by id, or by venue id) in json format
     */
    public function shows($id=null,$raw=null)
    {
        try {
            $current = date('Y-m-d');
            if(!empty($id) && is_numeric($id))
            {
                $shows = DB::table('shows')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                        ->select(DB::raw('shows.id, shows.name, shows.description, shows.slug, MIN(tickets.retail_price+tickets.processing_fee) AS retail_price,
                                          locations.address, locations.city, locations.state, locations.zip, locations.lat, locations.lng'))
                        ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('show_times.is_active','=',1)
                        ->where('show_times.show_time','>',\Carbon\Carbon::now())->where('shows.id','=',$id)
                        ->orderBy('shows.name')->groupBy('shows.id')
                        ->distinct()->get(); 
                $showtimes = DB::table('show_times')
                        ->select(DB::raw('DATE_FORMAT(show_time,"%Y-%m-%d") AS s_date'))
                        ->where('show_time','>',\Carbon\Carbon::now())->where('is_active','=',1)->where('show_id','=',$id)
                        ->orderBy('s_date')->groupBy('s_date')
                        ->distinct()->take(30)->get(); 
                foreach ($showtimes as $st)
                {
                    $times = DB::table('show_times')
                            ->select(DB::raw('id, DATE_FORMAT(show_time,"%h:%i %p") AS s_time'))
                            ->whereDate('show_time',$st->s_date)->where('show_id','=',$id)
                            ->distinct()->get(); 
                    $st->times = $times;
                }
                $videos = DB::table('videos')
                            ->join('show_videos', 'show_videos.video_id', '=' ,'videos.id')
                            ->select('videos.id','videos.embed_code')
                            ->where('show_videos.show_id','=',$id)
                            ->distinct()->get();
                foreach ($videos as $v)
                {
                    $part1 = explode('src="',$v->embed_code);
                    $part2 = explode('"',$part1[1]);
                    $v->embed_code = $part2[0];
                } 
                $images = DB::table('images')
                            ->join('show_images', 'show_images.image_id', '=' ,'images.id')
                            ->select('images.id','images.url','images.image_type')
                            ->where('show_images.show_id','=',$id)
                            ->whereIn('images.image_type',['Header','Image'])
                            ->distinct()->get();
                foreach ($images as $i)
                    $i->url = Image::view_image($i->url);
                foreach ($shows as $s)
                {
                    $s->showtimes = $showtimes;
                    $s->videos = $videos;
                    $s->images = $images;
                }                
            }  
            else
            {
                $shows = DB::table('shows')
                            ->join('show_images', 'show_images.show_id', '=' ,'shows.id')
                            ->join('images', 'show_images.image_id', '=' ,'images.id')
                            ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                            ->join('locations', 'locations.id', '=' ,'venues.location_id')
                            ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                            ->select('shows.id','shows.venue_id','shows.name','images.url','locations.city')
                            ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('images.image_type','=','Logo')
                            ->where('show_times.show_time','>',\Carbon\Carbon::now())->where('show_times.is_active','=',1)
                            ->whereNotNull('images.url')
                            ->orderBy('shows.sequence','ASC')->orderBy('show_times.show_time','ASC')
                            ->groupBy('shows.id')
                            ->distinct()->get();
            }    
            foreach ($shows as $s)
                if(!empty($s->url))
                    $s->url = Image::view_image($s->url);
            if($raw) return $shows;
            return Util::json(['success'=>true, 'shows'=>$shows]);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }
    
    /*
     * return arrays of all venues in json format
     */
    public function venues($raw=null)
    {
        try {
            $venues = DB::table('venues')
                        ->join('venue_images', 'venue_images.venue_id', '=' ,'venues.id')
                        ->join('images', 'venue_images.image_id', '=' ,'images.id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->select('venues.id','venues.name','images.url','locations.city')
                        ->where('venues.is_featured','>',0)->where('images.image_type','=','Logo')
                        ->where('show_times.show_time','>',\Carbon\Carbon::now())
                        ->whereNotNull('images.url')
                        ->orderBy('venues.name')->groupBy('venues.id')
                        ->distinct()->get();
            foreach ($venues as $v)
                $v->url = Image::view_image($v->url);
            if($raw) return $venues;
            return Util::json(['success'=>true, 'venues'=>$venues]);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }
    
    /*
     * return showtime details in json format
     */
    public function showtime($id)
    {
        try {
            $showtime = DB::table('show_times')
                        ->join('shows', 'show_times.show_id', '=' ,'shows.id')
                        ->join('venues', 'shows.venue_id', '=' ,'venues.id')
                        ->join('stages', 'shows.stage_id', '=' ,'stages.id')
                        ->select(DB::raw('show_times.id, shows.name, shows.slug, shows.on_sale, stages.image_url AS url,
                                         shows.amex_only_start_date, shows.amex_only_end_date, shows.amex_only_ticket_types,
                                         show_times.show_time, show_times.time_alternative, show_times.show_id,
                                         CASE WHEN NOW() > (show_times.show_time - INTERVAL shows.cutoff_hours HOUR) THEN 0 ELSE 1 END AS for_sale'))
                        ->where('show_times.show_time','>',\Carbon\Carbon::now())->where('show_times.is_active','=',1)
                        ->where('show_times.id','=',$id)
                        ->distinct()->get();
            if(count($showtime))
            {
                $showtime[0]->url = Image::view_image($showtime[0]->url);
                $types = [];
                $tickets = DB::table('tickets')
                            ->join('shows', 'tickets.show_id', '=' ,'shows.id')
                            ->join('packages', 'tickets.package_id', '=' ,'packages.id')
                            ->leftJoin('purchases', 'purchases.ticket_id', '=' ,'tickets.id')
                            ->select(DB::raw('tickets.id, (tickets.retail_price+tickets.processing_fee) AS amount, tickets.is_default,
                                             tickets.package_id, tickets.ticket_type, tickets.ticket_type_class,
                                             (CASE WHEN (packages.title != "None") THEN packages.title ELSE "" END) AS title,
                                             (CASE WHEN (packages.title != "None") THEN packages.description ELSE "" END) AS description,
                                             (CASE WHEN (tickets.max_tickets > 0) THEN (tickets.max_tickets - COALESCE(SUM(purchases.quantity),0)) ELSE 100 END) AS max_available'))
                            ->where('tickets.is_active','=',1)->where('shows.id','=',$showtime[0]->show_id)
                            ->whereNotIn('tickets.id', function($query) use ($id)
                            {
                                $query->select(DB::raw('ticket_id'))
                                      ->from('soldout_tickets')
                                      ->where('show_time_id','=',$id);
                            })
                            ->having('max_available','>',0)->groupBy('tickets.id')->get(); 
                foreach ($tickets as $t)
                {
                    if(isset($types[$t->ticket_type]))
                        $types[$t->ticket_type]['tickets'][] = $t;
                    else
                        $types[$t->ticket_type] = ['type'=>$t->ticket_type,'class'=>$t->ticket_type_class,'default'=>$t->is_default,'tickets'=>[$t]];
                }
                $showtime[0]->types = array_values($types);  
            } 
            return Util::json(['success'=>true, 'showtime'=>$showtime]);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    } 
    
    /*
     * send email for contact us
     */
    public function contact()
    {
        try {
            $info = Input::all();
            if(!empty($info['email']) && !empty($info['password']))
            {
                $user = User::where('email',$info['email'])->where('password',$info['password'])->where('is_active','>',0)
                            ->get(['id','email','first_name','last_name','user_type_id']);
                if($user) 
                    return Util::json(['success'=>true, 'user'=>$user]);
                return Util::json(['success'=>false, 'msg'=>'You must fill out correctly the form!']);
            }
            return Util::json(['success'=>false, 'msg'=>'You must enter a valid email and password!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }    
    
}
