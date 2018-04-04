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
            return Util::json(['success'=>true,'countries'=>$this->countries(),'cities'=>$this->cities(),'shows'=>$this->shows(),'venues'=>$this->venues(),'s_token'=> Util::s_token(true)]);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }

    /*
     * return cutoff_date for checking the showtime
     */
    public function cutoff_date()
    {
        return 'DATE_FORMAT(show_times.show_time + INTERVAL 1 DAY,"%Y-%m-%d 04:00:00")';
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
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->select('locations.city')
                        ->where('venues.is_featured','>',0)
                        ->whereNotNull('venues.logo_url')
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
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                        ->select(DB::raw('shows.id, shows.venue_id, shows.name, shows.logo_url, locations.city, MIN(tickets.retail_price+tickets.processing_fee) AS price'))
                        ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)
                        ->where(function($query) {
                            $query->whereNull('shows.on_featured')
                                  ->orWhere('shows.on_featured','<=',\Carbon\Carbon::now());
                        })
                        ->where(DB::raw($this->cutoff_date()),'>', \Carbon\Carbon::now())
                        ->where('show_times.is_active','=',1)
                        ->whereNotNull('shows.logo_url')
                        ->orderBy('shows.sequence','ASC')->orderBy('show_times.show_time','ASC')
                        ->groupBy('shows.id')
                        ->distinct()->get();
            foreach ($shows as $s)
                $s->logo_url = Image::view_image($s->logo_url);
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
                        ->join('locations', 'locations.id', '=' ,'venues.location_id')
                        ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                        ->select('venues.id','venues.name','venues.logo_url','locations.city')
                        ->where('venues.is_featured','>',0)->where('shows.is_active','>',0)->where('shows.is_featured','>',0)
                        ->where(function($query) {
                            $query->whereNull('shows.on_featured')
                                  ->orWhere('shows.on_featured','<=',\Carbon\Carbon::now());
                        })
                        ->where('show_times.is_active','>',0)
                        ->where(DB::raw($this->cutoff_date()),'>', \Carbon\Carbon::now())
                        ->where('tickets.is_active','>',0)
                        ->whereNotNull('venues.logo_url')
                        ->orderBy('venues.name')->groupBy('venues.id')
                        ->distinct()->get();
            foreach ($venues as $v)
                $v->logo_url = Image::view_image($v->logo_url);
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
                        ->select(DB::raw('shows.id, shows.name, shows.description, shows.slug, venues.name AS venue, shows.restrictions, shows.header_url AS header, venues.header_url,
                                          locations.address, locations.city, locations.state, locations.zip, locations.lat, locations.lng'))
                        ->where('shows.is_active','>',0)->where('shows.is_featured','>',0)->where('shows.id','=',$info['show_id'])
                        ->where(function($query) {
                            $query->whereNull('shows.on_featured')
                                  ->orWhere('shows.on_featured','<=',\Carbon\Carbon::now());
                        })
                        ->where('show_times.is_active','>',0)
                        ->where(DB::raw($this->cutoff_date()),'>', \Carbon\Carbon::now())
                        ->orderBy('shows.name')->groupBy('shows.id')->first();
                if($show)
                {
                    //get show times
                    $show->showtimes = DB::table('show_times')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                            ->select(DB::raw('DATE_FORMAT(show_times.show_time,"%m/%d/%Y") AS s_date'))
                            ->where(DB::raw($this->cutoff_date()),'>', \Carbon\Carbon::now())
                            ->where('shows.id','=',$show->id)
                            ->where('tickets.is_active','>',0)->where('show_times.is_active','>',0)->where('shows.is_active','>',0)
                            ->orderBy('show_times.show_time')
                            ->distinct()->take(30)->get();
                    //get videos
                    $show->videos = DB::table('videos')
                                ->join('show_videos', 'show_videos.video_id', '=' ,'videos.id')
                                ->select('videos.id','videos.embed_code')
                                ->where('show_videos.show_id','=',$show->id)
                                ->distinct()->get();
                    foreach ($show->videos as $v)
                    {
                        $part1 = explode('src="',$v->embed_code);
                        $part2 = explode('"',$part1[1]);
                        $v->embed_code = $part2[0];
                    }
                    //get header
                    $show->header = (!empty($show->header))? Image::view_image($show->header) : Image::view_image($show->header_url);
                    //get images
                    $show->images = DB::table('images')
                                ->join('show_images', 'show_images.image_id', '=' ,'images.id')
                                ->select('images.id','images.url','images.image_type')
                                ->where('show_images.show_id','=',$show->id)
                                ->whereIn('images.image_type',['Image'])
                                ->distinct()->get();
                    foreach ($show->images as $i)
                        $i->url = Image::view_image($i->url);
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
     * return event details in json format
     */
    public function event()
    {
        try {
            $info = Input::all();
            if(!empty($info['show_id']) && is_numeric($info['show_id']) && !empty($info['date']) && strtotime($info['date']))
            {
                $id = $info['show_id'];
                $info['date'] = date('Y-m-d',strtotime($info['date']));
                $event = DB::table('shows')
                            ->join('venues', 'shows.venue_id', '=' ,'venues.id')
                            ->join('stages', 'shows.stage_id', '=' ,'stages.id')
                            ->select(DB::raw('shows.id, shows.name, shows.slug, DATE_FORMAT(shows.on_sale,"%m/%d/%Y %H:%i:%s") AS on_sale, stages.image_url AS url,
                                             DATE_FORMAT(shows.amex_only_start_date,"%m/%d/%Y %H:%i:%s") AS amex_only_start_date, DATE_FORMAT(shows.amex_only_end_date,"%m/%d/%Y %H:%i:%s") AS amex_only_end_date, shows.amex_only_ticket_types'))
                            ->where('shows.id','=',$id)->first();
                if($event)
                {
                    //get times
                    $times = DB::table('show_times')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->join('tickets', 'tickets.show_id', '=' ,'shows.id')
                        ->leftJoin('purchases', 'purchases.ticket_id', '=' ,'tickets.id')
                        ->select(DB::raw('show_times.id, DATE_FORMAT(show_times.show_time,"%l:%i%p") AS s_time'))
                        ->whereDate('show_times.show_time',$info['date'])->where('show_times.show_id','=',$id)
                        ->where('show_times.is_active','>',0)
                        ->where(DB::raw($this->cutoff_date()),'>', \Carbon\Carbon::now())
                        ->distinct()->get();
                    $event->times = $times;
                    //parse url image
                    $event->url = Image::view_image($event->url);
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
                                ->where('tickets.is_active','=',1)->where('shows.id','=',$id)
                                ->whereNotIn('tickets.id', function($query) use ($id)
                                {
                                    $query->select(DB::raw('ticket_id'))
                                          ->from('soldout_tickets')
                                          ->where('show_time_id','=',$id);
                                })
                                ->having('max_available','>',0)->groupBy('tickets.id')->orderBy('tickets.is_default','DESC')->get();
                    foreach ($tickets as $t)
                    {
                        if(isset($types[$t->ticket_type]))
                            $types[$t->ticket_type]['tickets'][] = $t;
                        else
                            $types[$t->ticket_type] = ['type'=>$t->ticket_type,'class'=>$t->ticket_type_class,'default'=>$t->is_default,'tickets'=>[$t]];
                    }
                    //asign tickets and return event
                    $event->types = array_values($types);
                    return Util::json(['success'=>true, 'event'=>$event]);
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
