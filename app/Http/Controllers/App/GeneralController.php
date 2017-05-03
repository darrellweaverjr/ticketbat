<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Models\Image;
use App\Http\Models\Contact;
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
            return Util::json(['success'=>true,'countries'=>$this->countries(),'cities'=>$this->cities(),'shows'=>$this->shows(),'venues'=>$this->venues(),'s_token'=>uniqid()]);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }    
    
    /*
     * return arrays of all countries 
     */
    private function countries()
    {
        try {
            $countries = DB::table('countries')
                        ->select('code','name')
                        ->distinct()->get();
            return $countries;
        } catch (Exception $ex) {
            return [];
        }
    }
    
    /*
     * return arrays of all cities 
     */
    private function cities()
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
            return $cities;
        } catch (Exception $ex) {
            return [];
        }
    }
    
    /*
     * return arrays of all shows 
     */
    private function shows()
    {
        try {
            $shows = DB::table('shows')
                        ->join('show_images', 'show_images.show_id', '=' ,'shows.id')
                        ->join('images', 'show_images.image_id', '=' ,'images.id')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                        ->select(DB::raw('shows.id, shows.venue_id, shows.name, images.url, locations.city, MIN(tickets.retail_price+tickets.processing_fee) AS price'))    
                        ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('images.image_type','=','Logo')
                        ->where('show_times.show_time','>',\Carbon\Carbon::now())->where('show_times.is_active','=',1)
                        ->whereNotNull('images.url')
                        ->orderBy('shows.sequence','ASC')->orderBy('show_times.show_time','ASC')
                        ->groupBy('shows.id')
                        ->distinct()->get();
            foreach ($shows as $s)
                if(!empty($s->url))
                    $s->url = Image::view_image($s->url);
            return $shows;
        } catch (Exception $ex) {
            return [];
        }
    }
    
    /*
     * return arrays of all venues 
     */
    public function venues()
    {
        try {
            $venues = DB::table('venues')
                        ->join('venue_images', 'venue_images.venue_id', '=' ,'venues.id')
                        ->join('images', 'venue_images.image_id', '=' ,'images.id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                        ->select('venues.id','venues.name','images.url','locations.city')
                        ->where('venues.is_featured','>',0)->where('shows.is_active','>',0)->where('shows.is_featured','>',0)
                        ->where('show_times.is_active','>',0)->whereRaw('NOW() < show_times.show_time - INTERVAL shows.cutoff_hours HOUR')
                        ->where('images.image_type','=','Logo')->where('tickets.is_active','>',0)
                        ->whereNotNull('images.url')
                        ->orderBy('venues.name')->groupBy('venues.id')
                        ->distinct()->get();
            foreach ($venues as $v)
                $v->url = Image::view_image($v->url);
            return $venues;
        } catch (Exception $ex) {
            return [];
        }
    }
    
    /*
     * return arrays of all shows (or by id, or by venue id) in json format
     */
    public function show()
    {
        try {
            $info = Input::all();  
            if(!empty($info['show_id']) && is_numeric($info['show_id']))
            {
                //get show
                $show = DB::table('shows')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->select(DB::raw('shows.id, shows.name, shows.description, shows.slug, venues.name AS venue,
                                          shows.restrictions, locations.address, locations.city, locations.state, locations.zip, locations.lat, locations.lng'))
                        ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('shows.id','=',$info['show_id'])
                        ->where('show_times.is_active','>',0)->whereRaw('NOW() < show_times.show_time - INTERVAL shows.cutoff_hours HOUR')
                        ->orderBy('shows.name')->groupBy('shows.id')->first(); 
                if($show)
                {
                    //get show times
                    $showtimes = DB::table('show_times')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                            ->select(DB::raw('DATE_FORMAT(show_times.show_time,"%Y-%m-%d") AS s_date'))
                            ->whereRaw('NOW() < show_times.show_time - INTERVAL shows.cutoff_hours HOUR')->where('shows.id','=',$show->id)
                            ->where('tickets.is_active','>',0)->where('show_times.is_active','>',0)->where('shows.is_active','>',0)
                            ->orderBy('s_date')->groupBy('s_date')
                            ->distinct()->take(30)->get(); 
                    foreach ($showtimes as $st)
                    {
                        //get date for only first element, speed issues
                        $times = $this->showtimes($show->id,$st->s_date);
                        $st->times = $times;
                        break;  
                    }
                    //get videos
                    $videos = DB::table('videos')
                                ->join('show_videos', 'show_videos.video_id', '=' ,'videos.id')
                                ->select('videos.id','videos.embed_code')
                                ->where('show_videos.show_id','=',$show->id)
                                ->distinct()->get();
                    foreach ($videos as $v)
                    {
                        $part1 = explode('src="',$v->embed_code);
                        $part2 = explode('"',$part1[1]);
                        $v->embed_code = $part2[0];
                    } 
                    //get images
                    $images = DB::table('images')
                                ->join('show_images', 'show_images.image_id', '=' ,'images.id')
                                ->select('images.id','images.url','images.image_type')
                                ->where('show_images.show_id','=',$show->id)
                                ->whereIn('images.image_type',['Header','Image'])
                                ->distinct()->get();
                    foreach ($images as $i)
                        $i->url = Image::view_image($i->url);
                    //asign values to show and return 
                    $show->showtimes = $showtimes;
                    $show->videos = $videos;
                    $show->images = $images;
                    return Util::json(['success'=>true, 'show'=>$show]);
                }
                return Util::json(['success'=>false, 'msg'=>'That show does not exist on the system!']);             
            } 
            return Util::json(['success'=>false, 'msg'=>'You must fill out correctly the form!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }
    
    /*
     * return showtime details in json format
     */
    public function showtimes($show_id=null,$date=null)
    {
        try {
            $info = Input::all();  
            if($show_id && $date)
            {
                $info['show_id'] = $show_id;
                $info['date'] = $date;
            }
            if(!empty($info['show_id']) && is_numeric($info['show_id']) && !empty($info['date']) && strtotime($info['date'])!=false 
            && strtotime($info['date']) >= strtotime('today') )
            {
                $times = DB::table('show_times')        
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                        ->leftJoin('purchases', 'purchases.ticket_id', '=' ,'tickets.id')
                        ->select(DB::raw('show_times.id, DATE_FORMAT(show_times.show_time,"%h:%i %p") AS s_time,
                                          MIN(tickets.retail_price+tickets.processing_fee) AS price,
                                          (CASE WHEN (tickets.max_tickets > 0) THEN (tickets.max_tickets - COALESCE(SUM(purchases.quantity),0)) ELSE 100 END) AS availables'))
                        ->whereDate('show_times.show_time',$info['date'])->where('show_times.show_id','=',$info['show_id'])
                        ->distinct()->get(); 
                if($show_id && $date)
                    return $times;
                return Util::json(['success'=>true, 'times'=>$times]);
            }
            return Util::json(['success'=>false, 'msg'=>'You must fill out correctly the form!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    } 
    
    /*
     * return event details in json format
     */
    public function event()
    {
        try {
            $info = Input::all();  
            if(!empty($info['show_time_id']) && is_numeric($info['show_time_id']))
            {
                $id = $info['show_time_id'];
                $showtime = DB::table('show_times')
                            ->join('shows', 'show_times.show_id', '=' ,'shows.id')
                            ->join('venues', 'shows.venue_id', '=' ,'venues.id')
                            ->join('stages', 'shows.stage_id', '=' ,'stages.id')
                            ->select(DB::raw('show_times.id, shows.name, shows.slug, shows.on_sale, stages.image_url AS url,
                                             shows.amex_only_start_date, shows.amex_only_end_date, shows.amex_only_ticket_types,
                                             show_times.show_time, show_times.time_alternative, show_times.show_id,
                                             CASE WHEN NOW() > (show_times.show_time - INTERVAL shows.cutoff_hours HOUR) THEN 0 ELSE 1 END AS for_sale'))
                            ->where('show_times.show_time','>',\Carbon\Carbon::now())->where('show_times.is_active','=',1)
                            ->where('show_times.id','=',$id)->first();
                if($showtime)
                {
                    //parse url image
                    $showtime->url = Image::view_image($showtime->url);
                    //get tickets
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
                                ->where('tickets.is_active','=',1)->where('shows.id','=',$showtime->show_id)
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
                    //asign tickets and return event
                    $showtime->types = array_values($types);  
                    return Util::json(['success'=>true, 'showtime'=>$showtime]);
                } 
                return Util::json(['success'=>false, 'msg'=>'That show does not exist on the system!']); 
            }
            return Util::json(['success'=>false, 'msg'=>'You must fill out correctly the form!']);
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
            if(!empty($info['name']) && !empty($info['email']) && !empty($info['phone']) 
            && !empty($info['show_name']) && !empty($info['message']) && !empty($info['system_info']))
            {
                //create entry on table
                $contact = new Contact;
                $contact->name = $info['name'];
                $contact->email = $info['email'];
                $contact->phone = $info['phone'];
                $contact->show_name = $info['show_name'];
                $contact->system_info = $info['system_info'];
                $contact->message = $info['message'];
                $contact->save();
                if($contact->email_us())
                    return Util::json(['success'=>true]);
                return Util::json(['success'=>false, 'msg'=>'There was an error sending the email. Please try later!']);
            }
            return Util::json(['success'=>false, 'msg'=>'You must fill out correctly the form!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }    
    
}
