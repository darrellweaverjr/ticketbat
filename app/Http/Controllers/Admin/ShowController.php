<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Category;
use App\Http\Models\Venue;
use App\Http\Models\Ticket;
use App\Http\Models\Image;
use App\Http\Models\ShowTime;
use App\Http\Models\Consignment;
use App\Http\Models\Transaction;
use App\Http\Models\Purchase;
use App\Http\Models\ShowContract;
use App\Http\Models\Banner;
use App\Http\Models\Show;
use App\Http\Models\Stage;
use App\Http\Models\Band;
use App\Http\Models\Video;
use App\Http\Models\Util;
use App\Http\Models\Package;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Manage Shows
 *
 * @author ivan
 */
class ShowController extends Controller{
    
    /**
     * List all shows and return default view.
     *
     * @return view
     */
    public function index()
    {
        try {  
            //init
            $input = Input::all(); 
            if(isset($input) && isset($input['id']))
            {
                return $this->get($input['id']);
            }
            if(isset($input) && isset($input['venue_id']))
            {
                //search default values for that venue
                $default = DB::table('venues')
                                ->select('venues.accounting_email','venues.weekly_email','venues.daily_sales_emails','venues.financial_report_emails')
                                ->where('venues.id','=',$input['venue_id'])->first();
                if($default)
                    return ['success'=>true,'default'=>$default];
                return ['success'=>false];
            }
            else
            {      
                $current = date('Y-m-d H:i:s');
                //conditions to search
                $where = [['shows.id','>',0]];
                //search venue
                if(isset($input) && isset($input['venue']))
                {
                    $venue = $input['venue'];
                    if($venue != '')
                        $where[] = ['shows.venue_id','=',$venue];
                }
                else
                    $venue = '';
                //search showtime
                if(isset($input) && isset($input['showtime']))
                {
                    $showtime = $input['showtime'];
                    if($showtime == 'P')
                        $where[] = ['show_times.show_time','<',$current];
                    if($showtime == 'U')
                        $where[] = ['show_times.show_time','>',$current];
                }
                else
                    $showtime = 'A';
                //search status
                if(isset($input) && isset($input['status']))
                {
                    $status = $input['status'];
                    if($status != '')
                        $where[] = ['shows.is_active','=',$status];
                }
                else
                    $status = '';                
                //SEARCH
                $categories = [];
                $venues = [];
                $stages = [];
                $restrictions = [];
                $ticket_types = [];
                $ticket_types_classes = [];
                $image_types = [];
                $banner_types = [];
                $video_types = [];
                $tt_inactive = [];
                $packages = [];
                $onlyerrors = 0;
                $shows = [];
                //if user has permission to view
                if(in_array('View',Auth::user()->user_type->getACLs()['SHOWS']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['SHOWS']['permission_scope'] != 'All')
                    {
                        //search with error
                        if(isset($input) && isset($input['onlyerrors']) && $input['onlyerrors'] == 1)
                        {
                            $onlyerrors = 1;
                            $shows = DB::table('shows')
                                        ->join('categories', 'categories.id', '=' ,'shows.category_id')
                                        ->leftJoin('show_times', 'show_times.show_id', '=' ,'shows.id')
                                        ->leftJoin('tickets', 'tickets.show_id', '=' ,'shows.id')
                                        ->leftJoin(DB::raw('(SELECT si.show_id, i.url 
                                                             FROM show_images si 
                                                             LEFT JOIN images i ON si.image_id = i.id 
                                                             WHERE i.image_type = "Logo") as images'),
                                        function($join){
                                            $join->on('shows.id','=','images.show_id');
                                        })
                                        ->select('shows.id','shows.name','shows.slug','shows.short_description','shows.url','shows.is_active','shows.is_featured',
                                                 'shows.facebook','shows.twitter','shows.googleplus','shows.youtube','shows.instagram','shows.yelpbadge','shows.conversion_code',
                                                 'categories.name AS category','images.url AS image_url')
                                        ->where($where)
                                        ->where('tickets.is_active','>',0)->where('tickets.is_default','>',0)->where('show_times.is_active','>',0)
                                        ->where(function($query)
                                        {
                                            $query->whereIn('shows.venue_id',[Auth::user()->venues_edit])
                                                  ->orWhere('shows.audit_user_id','=',Auth::user()->id);
                                        })
                                        ->whereNull('images.url')
                                        ->orWhereNull('tickets.id')
                                        ->orWhereNull('show_times.id')
                                        ->orderBy('shows.name')->groupBy('shows.id')
                                        ->distinct()->get();
                        }
                        else
                        {
                            $onlyerrors = 0;
                            //get all records        
                            $shows = DB::table('shows')
                                        ->join('categories', 'categories.id', '=' ,'shows.category_id')
                                        ->leftJoin('show_times', 'show_times.show_id', '=' ,'shows.id')
                                        ->leftJoin(DB::raw('(SELECT si.show_id, i.url 
                                                             FROM show_images si 
                                                             LEFT JOIN images i ON si.image_id = i.id 
                                                             WHERE i.image_type = "Logo") as images'),
                                        function($join){
                                            $join->on('shows.id','=','images.show_id');
                                        })
                                        ->select('shows.id','shows.name','shows.slug','shows.short_description','shows.url','shows.is_active','shows.is_featured',
                                                 'shows.facebook','shows.twitter','shows.googleplus','shows.youtube','shows.instagram','shows.yelpbadge','shows.conversion_code',
                                                 'categories.name AS category','images.url AS image_url')
                                        ->where($where)
                                        ->where(function($query)
                                        {
                                            $query->whereIn('shows.venue_id',[Auth::user()->venues_edit])
                                                  ->orWhere('shows.audit_user_id','=',Auth::user()->id);
                                        })
                                        ->orderBy('shows.name')->groupBy('shows.id')
                                        ->distinct()->get();
                        }
                        $venues = Venue::whereIn('id',explode(',',Auth::user()->venues_edit))->orderBy('name')->get(['id','name']);
                    }  
                    else 
                    {
                        //search with error
                        if(isset($input) && isset($input['onlyerrors']) && $input['onlyerrors'] == 1)
                        {
                            $onlyerrors = 1;
                            $shows = DB::table('shows')
                                        ->join('categories', 'categories.id', '=' ,'shows.category_id')
                                        ->leftJoin('show_times', 'show_times.show_id', '=' ,'shows.id')
                                        ->leftJoin('tickets', 'tickets.show_id', '=' ,'shows.id')
                                        ->leftJoin(DB::raw('(SELECT si.show_id, i.url 
                                                             FROM show_images si 
                                                             LEFT JOIN images i ON si.image_id = i.id 
                                                             WHERE i.image_type = "Logo") as images'),
                                        function($join){
                                            $join->on('shows.id','=','images.show_id');
                                        })                                     
                                        ->select('shows.id','shows.name','shows.slug','shows.short_description','shows.url','shows.is_active','shows.is_featured',
                                                 'shows.facebook','shows.twitter','shows.googleplus','shows.youtube','shows.instagram','shows.yelpbadge','shows.conversion_code',
                                                 'categories.name AS category','images.url AS image_url')
                                        ->where($where)
                                        ->where('tickets.is_active','>',0)->where('tickets.is_default','>',0)->where('show_times.is_active','>',0)
                                        ->whereNull('images.url')
                                        ->orWhereNull('tickets.id')
                                        ->orWhereNull('show_times.id')
                                        ->orderBy('shows.name')->groupBy('shows.id')
                                        ->distinct()->get();
                        }
                        else
                        {
                            $onlyerrors = 0;
                            //get all records        
                            $shows = DB::table('shows')
                                        ->join('categories', 'categories.id', '=' ,'shows.category_id')
                                        ->leftJoin('show_times', 'show_times.show_id', '=' ,'shows.id')
                                        ->leftJoin(DB::raw('(SELECT si.show_id, i.url 
                                                             FROM show_images si 
                                                             LEFT JOIN images i ON si.image_id = i.id 
                                                             WHERE i.image_type = "Logo") as images'),
                                        function($join){
                                            $join->on('shows.id','=','images.show_id');
                                        })
                                        ->select('shows.id','shows.name','shows.slug','shows.short_description','shows.url','shows.is_active','shows.is_featured',
                                                 'shows.facebook','shows.twitter','shows.googleplus','shows.youtube','shows.instagram','shows.yelpbadge','shows.conversion_code',
                                                 'categories.name AS category','images.url AS image_url')
                                        ->where($where)
                                        ->orderBy('shows.name')->groupBy('shows.id')
                                        ->distinct()->get();
                        }
                        $venues = Venue::orderBy('name')->get(['id','name','restrictions']);
                    }  
                    //other enum
                    $categories = Category::all();
                    $stages = Stage::all('id','name','venue_id');
                    $restrictions = Util::getEnumValues('shows','restrictions');
                    $ticket_types_aux = Util::getEnumValues('tickets','ticket_type');
                    foreach ($ticket_types_aux as $tt)
                    {
                        $t = Ticket::where('ticket_type',$tt)->first();
                        $ticket_types[] = ['type'=>$tt,'class'=>($t && $t->ticket_type_class)? $t->ticket_type_class : 'btn-primary'];
                    }
                    $ticket_types_classes = Util::getEnumValues('tickets','ticket_type_class');
                    $image_types = Util::getEnumValues('images','image_type');
                    $banner_types = Util::getEnumValues('banners','type');
                    $video_types = Util::getEnumValues('videos','video_type');
                    $tt_inactive = DB::table('ticket_types_inactive')->select('ticket_types_inactive.ticket_type')->distinct()->get();
                    foreach ($tt_inactive as $tt)
                        unset($ticket_types[$tt->ticket_type]);
                    $packages = Package::all();
                }
                //return view
                return view('admin.shows.index',compact('shows','categories','venues','stages','restrictions','ticket_types','ticket_types_classes','image_types','banner_types','video_types','packages','venue','showtime','status','onlyerrors'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Shows Index: '.$ex->getMessage());
        }
    } 
    /**
     * Get show by id.
     *
     * @return view
     */
    private function get($id)
    {
        try {   
            //init
            if(!empty($id) && is_numeric($id))
            {
                $current = date('Y-m-d');
                //get selected record
                $show = Show::find($id);  
                if(!$show)
                    return ['success'=>false,'msg'=>'There was an error getting the show.<br>Maybe it is not longer in the system.'];
                // change relative url uploads for real one
                $show->sponsor_logo_id = Image::view_image($show->sponsor_logo_id);
                //search sub elements
                $tickets = DB::table('tickets')->join('packages', 'tickets.package_id', '=' ,'packages.id')
                                ->select('tickets.*','packages.title')->where('tickets.show_id','=',$show->id)->distinct()->get();
                $tt_inactive = DB::table('ticket_types_inactive')->select('ticket_types_inactive.*')->distinct()->implode('ticket_types_inactive.ticket_type',',');
                $show_times = ShowTime::where('show_id','=',$show->id)->where('show_time','>=',$current)->distinct()->get();
                $passwords = DB::table('show_passwords')->select('show_passwords.*')
                                ->where('show_passwords.show_id','=',$show->id)->distinct()->get();
                $bands = DB::table('bands')->join('show_bands', 'show_bands.band_id', '=' ,'bands.id')
                                ->select('bands.name','show_bands.*')->where('show_bands.show_id','=',$show->id)
                                ->orderBy('show_bands.n_order')->distinct()->get();
                $sweepstakes = DB::table('users')
                                ->join('show_sweepstakes', 'show_sweepstakes.user_id', '=' ,'users.id')
                                ->join('locations', 'locations.id', '=' ,'users.location_id')
                                ->select(DB::raw('show_sweepstakes.*, CONCAT(users.first_name," ",users.last_name) AS name, users.email,
                                                  CONCAT(locations.address,", ",locations.city,", ",locations.state,", ",locations.zip) AS address'))
                                ->where('show_sweepstakes.show_id','=',$show->id)
                                ->orderBy('show_sweepstakes.created','DESC')->distinct()->get();
                $contracts = ShowContract::where('show_id','=',$show->id)->orderBy('updated','desc')->get();
                $images = DB::table('images')->join('show_images', 'show_images.image_id', '=' ,'images.id')
                                ->select('images.*')->where('show_images.show_id','=',$show->id)->distinct()->get();
                foreach ($images as $i)
                    $i->url = Image::view_image($i->url);
                $banners = Banner::where('parent_id','=',$show->id)->where('belongto','=','show')->distinct()->get();
                foreach ($banners as $b)
                    $b->file = Image::view_image($b->file);
                $videos = DB::table('videos')->join('show_videos', 'show_videos.video_id', '=' ,'videos.id')
                                ->select('videos.*')->where('show_videos.show_id','=',$show->id)->distinct()->get();
                return ['success'=>true,'show'=>array_merge($show->getAttributes()),'tickets'=>$tickets,'ticket_types_inactive'=>$tt_inactive,'show_times'=>$show_times,'passwords'=>$passwords,'bands'=>$bands,'sweepstakes'=>$sweepstakes,'contracts'=>$contracts,'images'=>$images,'banners'=>$banners,'videos'=>$videos];
            }
        } catch (Exception $ex) {
            throw new Exception('Error Shows Get: '.$ex->getMessage());
        }
    } 
    /**
     * Save new or updated show or subtable related with show.
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
                $current = date('Y-m-d H:i:s');
                if(isset($input['id']) && $input['id'])
                {
                    if(Show::where('slug','=',$input['slug'])->where('id','!=',$input['id'])->count())
                        return ['success'=>false,'msg'=>'There was an error saving the show.<br>That slug is already in the system.','errors'=>'slug'];
                    $show = Show::find($input['id']);
                    $show->updated = $current;
                    if(preg_match('/media\/preview/',$input['sponsor_logo_id'])) 
                        $show->delete_image_file();
                }                    
                else
                {                    
                    if(Show::where('slug','=',$input['slug'])->count())
                        return ['success'=>false,'msg'=>'There was an error saving the show.<br>That slug is already in the system.','errors'=>'slug'];
                    $show = new Show;
                    $show->audit_user_id = Auth::user()->id;
                    $show->created = $current;
                }
                //save show
                $show->venue_id = $input['venue_id'];
                $show->stage_id = $input['stage_id'];
                $show->category_id = $input['category_id'];
                $show->name = strip_tags($input['name']);
                $show->slug = strip_tags($input['slug']);
                $show->presented_by = strip_tags($input['presented_by']);
                $show->sponsor = strip_tags($input['sponsor']);
                $show->short_description = strip_tags($input['short_description']);
                $show->description = strip_tags($input['description'],'<p><a><br>');
                $show->emails = strip_tags($input['emails']);
                $show->accounting_email = strip_tags($input['accounting_email']);
                $show->url = strip_tags($input['url']);
                $show->restrictions = $input['restrictions'];
                $show->is_featured = $input['is_featured'];
                $show->cutoff_hours = $input['cutoff_hours'];
                $show->sequence = $input['sequence'];
                $show->is_active = $input['is_active'];
                $show->facebook = strip_tags($input['facebook']);
                $show->twitter = strip_tags($input['twitter']);
                $show->youtube = strip_tags($input['youtube']);
                $show->instagram = strip_tags($input['instagram']);
                $show->yelpbadge = strip_tags($input['yelpbadge']);
                $show->on_sale = $input['on_sale'];
                $show->printed_tickets = $input['printed_tickets'];
                $show->individual_emails = $input['individual_emails'];
                $show->manifest_emails = $input['manifest_emails'];
                $show->daily_sales_emails = $input['daily_sales_emails'];
                $show->financial_report_emails = $input['financial_report_emails'];
                $show->starting_at = (!empty($input['starting_at']))? $input['starting_at'] : null;
                $show->conversion_code = (!empty($input['conversion_code']))? $input['conversion_code'] : null;
                if(isset($input['amex_only_start_date']) && $input['amex_only_start_date']!='' && isset($input['amex_only_end_date']) && $input['amex_only_end_date']!=''
                        && isset($input['ticket_types']) && count($input['ticket_types']))
                {
                    $show->amex_only_ticket_types = Ticket::where('show_id','=',$input['id'])->whereIn('id',$input['ticket_types'])->distinct()->get()->implode('ticket_type',',');
                    $show->amex_only_start_date = $input['amex_only_start_date'];
                    $show->amex_only_end_date = $input['amex_only_end_date'];
                }
                else
                {
                    $show->amex_only_ticket_types = null;
                    $show->amex_only_start_date = null;
                    $show->amex_only_end_date = null;
                }
                if(preg_match('/media\/preview/',$input['sponsor_logo_id'])) 
                    $show->set_sponsor_logo_id($input['sponsor_logo_id']);
                if(!empty($input['ext_slug']) && preg_match('/^http(s)?:\/\/[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i',$input['ext_slug']))
                    $show->ext_slug = $input['ext_slug'];
                else
                    $show->ext_slug = null;
                $show->save();
                //order shows
                $shows = Show::where('sequence','<',10000)->orderBy('sequence')->get(['id']);
                foreach($shows as $key=>$s) 
                    Show::where('id',$s->id)->update(['sequence'=>$key+1]);
                //return
                if(isset($input['id']) && $input['id'])
                    return ['success'=>true,'msg'=>'Show saved successfully!'];
                return $this->get($show->id);
            }
            return ['success'=>false,'msg'=>'There was an error saving the show.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Shows Save: '.$ex->getMessage());
        }
    }
    /**
     * Get slug for show.
     *
     * @void
     */
    public function slug()
    {
        try {
            //init
            $input = Input::all(); 
            //get all record      
            if($input && !empty(strip_tags($input['name'])) && isset($input['venue_id']) && isset($input['show_id']))
                return Util::generate_slug(strip_tags($input['name']), $input['venue_id'], $input['show_id']);
            return '';
        } catch (Exception $ex) {
            return '';
        }
    }
    /**
     * Remove shows.
     *
     * @void
     */
    public function remove()
    {
        try {
            //init
            $input = Input::all();
            $msg = $msg1 = ''; 
            //delete all records   
            foreach ($input['id'] as $id)
            {
                //get show
                $show = Show::find($id);
                if($show)
                {
                    $dependences = false;
                    //showtimes
                    $showtimes = ShowTime::where('show_times.show_id','=',$show->id)->get();
                    foreach ($showtimes as $st)
                    {
                        if(!$dependences)
                        {
                            $depend = DB::table('show_times')
                                        ->leftJoin('purchases', 'purchases.show_time_id', '=', 'show_times.id')
                                        ->leftJoin('transactions', 'transactions.show_time_id', '=', 'show_times.id')
                                        ->leftJoin('consignments', 'consignments.show_time_id', '=', 'show_times.id')
                                        ->select(DB::raw('count(*) as dependences'))
                                        ->where('show_times.id','=',$st->id)->first();
                            if($depend->dependences > 1)
                            {
                                $dependences = true;
                                if($msg=='')
                                    $msg = 'The following shows have dependences (purchases, transactions and/or consignments) and the system cannot delete them:<br><br><ol style="max-height:200px;overflow:auto;text-align:left;">';
                                $msg .= '<li style="color:red;">'.$show->name.'</li>';
                            }
                        }
                    }
                    //if has no dependences delete all subtables
                    if(!$dependences)
                    {
                        //tickets(soldout_tickets,discount_tickets)
                        $tickets = Ticket::where('show_id',$show->id)->get();
                        foreach ($tickets as $t)
                        {
                            DB::table('soldout_tickets')->where('ticket_id',$t->id)->delete();
                            DB::table('discount_tickets')->where('ticket_id',$t->id)->delete();
                        }
                        $tickets = Ticket::where('show_id',$show->id)->delete();
                        //banners
                        $banners = Banner::where('parent_id',$show->id)->where('belongto','show')->delete();
                        //discount_shows
                        $discount_shows = DB::table('discount_shows')->where('show_id',$show->id)->delete();
                        //merchandise(merchandise_photos)
                        $merchandise = DB::table('merchandise')->where('show_id',$show->id)->get();
                        foreach ($merchandise as $m)
                            DB::table('merchandise_photos')->where('ticket_id',$m->id)->delete();
                        $merchandise = DB::table('merchandise')->where('show_id',$show->id)->delete();
                        //show_awards
                        $show_awards = DB::table('show_awards')->where('show_id',$show->id)->delete();
                        //show_bands
                        $show_bands = DB::table('show_bands')->where('show_id',$show->id)->delete();
                        //show_images(images)
                        $show_images = DB::table('show_images')->where('show_id',$show->id)->get();
                        foreach ($show_images as $i)
                        {
                            $image = Image::find($i->image_id);
                            Image::remove_image($image->url);
                            $image->delete();
                        }
                        $show_images = DB::table('show_images')->where('show_id',$show->id)->delete();
                        //show_passwords
                        $show_passwords = DB::table('show_passwords')->where('show_id',$show->id)->delete();
                        //show_reviews
                        $show_reviews = DB::table('show_reviews')->where('show_id',$show->id)->delete();
                        //show_videos(videos)
                        $show_videos = DB::table('show_videos')->where('show_id',$show->id)->get();
                        foreach ($show_videos as $v)
                            DB::table('videos')->where('id',$v->video_id)->delete();
                        $show_videos = DB::table('show_videos')->where('show_id',$show->id)->delete();
                        //show_times
                        $show_times = DB::table('show_times')->where('show_id',$show->id)->delete();
                        //try to delete final show if it has not dependences
                        if(!$show->delete())
                        {
                            if($msg1=='')
                                $msg1 = 'The following shows have problems deleting them:<br><br><ol style="max-height:200px;overflow:auto;text-align:left;">';
                            $msg1 .= '<li style="color:red;">'.$show->name.'</li>';
                        } 
                    }
                }
            }
            if($msg != '' || $msg1 != '')
            {
                if($msg!='') $msg .= '</ol><br> Please, contact an administrator if you want a force delete.';
                if($msg1!='') $msg1 .= '</ol><br> Please, contact an administrator.';
                return ['success'=>false,'msg'=>$msg.$msg1];
            }  
            return ['success'=>true,'msg'=>'All records deleted successfully!'];
        } catch (Exception $ex) {
            throw new Exception('Error Shows Remove: '.$ex->getMessage());
        }
    }
    /**
     * Get, Edit, Remove passwords for show
     *
     * @return view
     */
    public function passwords()
    {
        try {   
            //init
            $input = Input::all(); 
            //get
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $passwords = DB::table('show_passwords')->select('show_passwords.*')
                                ->where('show_passwords.id','=',$input['id'])->distinct()->first();
                $tickets = Ticket::where('show_id','=',$passwords->show_id)->whereIn('ticket_type', explode(',',$passwords->ticket_types))->distinct()->pluck('id');
                $passwords->ticket_types = $tickets;
                return ['success'=>true,'password'=>$passwords];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                $password = DB::table('show_passwords')->where('id','=',$input['id'])->delete();
                if($password)
                    return ['success'=>true];
                else
                    return ['success'=>false,'msg'=>'There was an error deleting the password.<br>The server could not retrieve the data.'];
            }
            //save
            else if(isset($input))
            {
                $tt = Ticket::where('show_id','=',$input['show_id'])->whereIn('id',$input['ticket_types'])->distinct()->get()->implode('ticket_type',',');
                $show_password = ['show_id'=>$input['show_id'],'start_date'=>$input['start_date'],'end_date'=>$input['end_date'],'password'=>$input['password'],'ticket_types'=>$tt];
               
                //update
                if(isset($input['id']) && $input['id'])
                {
                    $password = DB::table('show_passwords')->where('id','=',$input['id'])->update($show_password);
                    if($password>=0) 
                        $password = DB::table('show_passwords')->where('id','=',$input['id'])->first();
                } 
                //add
                else
                {
                    $password = DB::table('show_passwords')->insertGetId($show_password);
                    if($password) 
                        $password = DB::table('show_passwords')->where('id','=',$password)->first();
                }
                if($password)
                    return ['success'=>true,'password'=>$password];
                else
                    return ['success'=>false,'msg'=>'There was an error. Invalid Option.<br>The server could not retrieve the data.'];
            }
            else
                return ['success'=>false,'msg'=>'There was an error saving the password.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error ShowPasswords Index: '.$ex->getMessage());
        }
    } 
    /**
     * Get, Edit ticket for show
     *
     * @return view
     */
    public function tickets()
    {
        try {   
            //init
            $input = Input::all(); 
            //get
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $tickets = Ticket::find($input['id']);
                return ['success'=>true,'ticket'=>$tickets];
            }
            else if(isset($input['venue_defaults']) && isset($input['show_id']))
            {
                $default = DB::table('venues')
                                ->join('shows', 'shows.venue_id', '=' ,'venues.id')
                                ->select('venues.default_processing_fee','venues.default_percent_pfee','venues.default_percent_commission','venues.default_fixed_commission')
                                ->where('shows.id','=',$input['show_id'])->first();
                if($default)
                    return ['success'=>true,'default'=>$default];
                return ['success'=>false];
            }
            //save
            else if(isset($input))
            {
                //update/add
                if(isset($input['id']) && $input['id'])
                {
                    $ticket = Ticket::find($input['id']);
                }                    
                else
                {                    
                    $ticket = new Ticket;
                    $ticket->audit_user_id = Auth::user()->id;
                }
                //update default
                if(isset($input['is_default']) && isset($input['show_id']))
                {
                    //make another not default
                    if(isset($input['id']) && isset($input['id']))
                        Ticket::where('show_id','=',$input['show_id'])->where('id','<>',$input['id'])->update(['is_default'=>0]);
                    else
                        Ticket::where('show_id','=',$input['show_id'])->update(['is_default'=>0]);
                    //if not put first active as default
                    if($input['is_default']==0)
                    {
                        if(isset($input['id']) && isset($input['id']))
                            $t = Ticket::where('show_id','=',$input['show_id'])->where('is_active','=',1)->where('id','<>',$input['id'])->first();
                        else
                            $t = Ticket::where('show_id','=',$input['show_id'])->where('is_active','=',1)->first();
                        if($t)
                        {
                            $t->is_default = 1;
                            $t->save();
                            $ticket->is_default = 0;
                        }
                        else 
                            $ticket->is_default = 1;
                    }
                    //mark active
                    else
                        $ticket->is_default = 1;
                }
                //create/update
                $ticket->show_id = $input['show_id'];
                $ticket->package_id = $input['package_id'];
                $ticket->ticket_type = $input['ticket_type'];
                $ticket->ticket_type_class = $input['ticket_type_class'];
                $ticket->retail_price = $input['retail_price'];
                $ticket->processing_fee = $input['processing_fee'];
                $ticket->percent_pf = $input['percent_pf'];
                $ticket->max_tickets = $input['max_tickets'];
                $ticket->is_active = $input['is_active'];
                if(isset($input['fixed_commission']) && $input['fixed_commission'] != 0.00 && $input['fixed_commission'] != '0.00')
                {
                    $ticket->percent_commission = 0.00;
                    $ticket->fixed_commission = $input['fixed_commission'];
                }
                else
                {
                    $ticket->percent_commission = $input['percent_commission'];
                    $ticket->fixed_commission = null;
                }
                $ticket->save();
                //return
                $tickets = DB::table('tickets')->join('packages', 'tickets.package_id', '=' ,'packages.id')
                                ->select('tickets.*','packages.title')->where('tickets.show_id','=',$input['show_id'])->distinct()->get();
                return ['success'=>true,'tickets'=>$tickets];
            }
            else
                return ['success'=>false,'msg'=>'There was an error saving the ticket.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error ShowTickets Index: '.$ex->getMessage());
        }
    }
    /**
     * Get, Edit, Remove bands for show
     *
     * @return view
     */
    public function bands()
    {
        try {   
            //init
            $input = Input::all(); 
            //update
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                if(isset($input['show_id']) && isset($input['order']))
                {
                    $order = $input['order']; 
                    $bands = [];
                    for ($i=0, $pos = min($order); $i<count($order); $i++, $pos++)
                    {
                        $band = DB::table('show_bands')->where([ ['show_id','=',$input['show_id']],['n_order','=',$order[$i]] ])->first();
                        $bands[$pos] = $band->band_id;
                    }
                    foreach ($bands as $pos=>$b)
                        DB::table('show_bands')->where('show_id',$input['show_id'])->where('n_order',$pos)->update(['band_id'=>$b]);
                    return ['success'=>true];
                }
                else
                    return ['success'=>false,'msg'=>'There was an error updating the bands.<br>The server could not retrieve the data.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                if(isset($input['show_id']) && isset($input['order']))
                {
                    $band = DB::table('show_bands')->where([ ['show_id','=',$input['show_id']],['n_order','=',$input['order']] ])->delete();
                    if($band)
                    {
                        $bands = DB::table('show_bands')->where([ ['show_id','=',$input['show_id']],['n_order','>',$input['order']] ])->get();
                        foreach ($bands as $b)
                        {
                            DB::table('show_bands')->where('show_id',$b->show_id)->where('n_order',$b->n_order)->update(['n_order'=>$input['order']]);
                            $input['order']++;
                        } 
                        $bands = DB::table('bands')->join('show_bands', 'show_bands.band_id', '=' ,'bands.id')
                                ->select('bands.name','show_bands.*')->where('show_bands.show_id','=',$input['show_id'])
                                ->orderBy('show_bands.n_order')->distinct()->get();
                        return ['success'=>true,'bands'=>$bands];
                    } 
                    else
                        return ['success'=>false,'msg'=>'There was an error deleting the band.<br>The server could not retrieve the data.'];
                }
                else
                    return ['success'=>false,'msg'=>'There was an error deleting the band.<br>The server could not retrieve the data.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
                if(isset($input['show_id']) && isset($input['band_id']))
                {
                    $order = DB::table('show_bands')->where('show_id',$input['show_id'])->count() + 1;
                    $band = DB::table('show_bands')->insert( ['show_id' => $input['show_id'], 'band_id' => $input['band_id'], 'n_order' => $order] );
                    if($band)
                    {
                        $band = DB::table('bands')->join('show_bands', 'show_bands.band_id', '=' ,'bands.id')
                                ->select('bands.name','show_bands.*')
                                ->where('show_bands.show_id','=',$input['show_id'])
                                ->where('show_bands.n_order','=',$order)->first();
                        return ['success'=>true,'band'=>$band];
                    } 
                    else
                        return ['success'=>false,'msg'=>'There was an error adding the band.<br>The server could not retrieve the data.'];
                }
                else
                    return ['success'=>false,'msg'=>'There was an error adding the band.<br>The server could not retrieve the data.'];
            }
            //get
            else if(isset($input) && isset($input['action']) && $input['action']==2)
            {
                $bands = Band::orderBy('name')->get(['id','name']);
                if($bands)
                    return ['success'=>true,'bands'=>$bands];
                else
                    return ['success'=>false,'msg'=>'There are no bands in the system.<br>Please create one.'];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error ShowBands Index: '.$ex->getMessage());
        }
    } 
    /**
     * Get, Edit, Remove showtimes for show
     *
     * @return view
     */
    public function showtimes()
    {
        try {   
            //init
            $input = Input::all();                  
            $current = date('Y-m-d H:i:s');
            //get events for change/cancel
            if(isset($input) && isset($input['action']) && in_array($input['action'],['cc_show_times','cc_show_time_info','change','move']))
            {
                if($input['action']=='cc_show_times')
                {
                    $showtimes = ShowTime::where('show_id',$input['show_id'])
                                    ->where('is_active','>',0)->where('show_time','>',$current)
                                    ->orderBy('show_time','asc')->get(['id','show_time']);
                    return ['success'=>true,'showtimes'=>$showtimes];
                }
                else if($input['action']=='cc_show_time_info')
                {
                    $purchases = Purchase::where('show_time_id',$input['show_time_id'])->get(['id','created']);
                    $consignments = Consignment::where('show_time_id',$input['show_time_id'])->get(['id','created']);
                    return ['success'=>true,'purchases'=>$purchases,'consignments'=>$consignments];
                }
                else if($input['action']=='move')
                {
                    $showtime = ShowTime::where('id',$input['show_time_id'])->first();
                    if($showtime)
                    {
                        $date_from = $showtime->show_time;
                        $purchases = DB::table('purchases')
                                        ->join('tickets', 'purchases.ticket_id','=','tickets.id')
                                        ->select('purchases.id')
                                        ->where('purchases.show_time_id','=',$showtime->id)
                                        ->where('purchases.status','=','Active')
                                        ->where('tickets.is_active','>',0)
                                        ->distinct()->get();
                        $showtime_to = ShowTime::where('show_time',$input['show_time_to'])->where('show_id',$input['show_id'])->first();
                        if(!$showtime_to)
                        {
                            $showtime->show_time = $input['show_time_to'];
                            $showtime->save();
                            $showtime_to = $showtime;
                        }
                        else
                        {
                            //update dependences
                            Transaction::where('show_time_id',$showtime->id)->update(['show_time_id'=>$showtime_to->id]);
                            Purchase::where('show_time_id',$showtime->id)->update(['show_time_id'=>$showtime_to->id]);
                            Consignment::where('show_time_id',$showtime->id)->update(['show_time_id'=>$showtime_to->id]);
                            //remove old showtime
                            $showtime->delete();
                        }
                        //send email after edit everything
                        if($input['send_email'] == 1 && $purchases && count($purchases))
                        {
                            foreach ($purchases as $p)
                            {
                                $pur = Purchase::find($p->id);
                                if($pur)
                                {
                                    $receipt = $pur->get_receipt();
                                    Purchase::email_receipts('Updated show information: TicketBat Purchase', [$receipt], 'changed', $date_from);
                                }
                            }
                        }
                        return ['success'=>true,'id'=>$input['show_time_id'],'showtime'=>$showtime_to];
                    }
                    return ['success'=>false,'msg'=>'There was an error.<br>That event not longer exists!'];
                }
                else
                    return ['success'=>false,'msg'=>'There was an error.<br>Invalid Option!'];
            }
            //get availables dates
            else if(isset($input) && isset($input['action']) && in_array($input['action'],['add','update','delete']))
            {
                $dates = [];
                //calculate dates
                if(strtotime($input['time']))
                {
                    //change time format to compare
                    $input['time'] = date('H:i',strtotime($input['time']));
                    //get all date/time
                    if($input['start_date'] == $input['end_date'])
                    {
                        if(strtotime($input['start_date'])>strtotime($current))
                            $dates[] = $input['start_date'].' '.$input['time'];
                    }    
                    else if(strtotime($input['start_date']) < strtotime($input['end_date']))
                    {
                        $period = new \DatePeriod(
                            \DateTime::createFromFormat('Y-m-d H:i',$input['start_date'].$input['time']),
                            new \DateInterval('P1D'),
                            \DateTime::createFromFormat('Y-m-d H:i',$input['end_date'].'23:59')
                        );
                        foreach($period as $date){
                            if(in_array($date->format('w'),$input['weekdays']))
                                $dates[] = $date->format('Y-m-d H:i:s');
                        }
                    }
                    else
                        return ['success'=>false,'msg'=>'There was an error checking the dates.<br>Invalid Date Range.'];
                    //add
                    if($input['action']=='add')
                    {
                        //if exists event in that date/time
                        $showtimes = ShowTime::where('show_id',$input['show_id'])->whereIn('show_time',$dates)->pluck('show_time')->all();
                        //return data
                        foreach ($dates as $key=>$d)
                            $dates[$key] = ['showtime'=>$d,'available'=>!in_array($d, $showtimes)];
                        return ['success'=>true,'dates'=>$dates];
                    }
                    //update
                    else if($input['action']=='update')
                    {
                        //return data
                        foreach ($dates as $key=>$d)
                        {
                            $showtime = ShowTime::where('show_id',$input['show_id'])->where('show_time',$d)->first();
                            if($showtime)
                                $dates[$key] = ['showtime'=>$d,'available'=>true];
                            else
                                unset($dates[$key]);
                        }
                        return ['success'=>true,'dates'=>$dates];
                    }
                    //delete
                    else if($input['action']=='delete')
                    {
                        foreach ($dates as $key=>$d)
                        {
                            //search showtime
                            $showtime = ShowTime::where('show_id','=',$input['show_id'])->where('show_times.show_time','=',$d.':00')->first();
                            if($showtime)
                            {
                                //check dependences
                                $dependences = DB::table('purchases')
                                                    ->leftJoin('transactions', 'transactions.show_time_id','=','purchases.show_time_id')
                                                    ->leftJoin('consignments', 'consignments.show_time_id','=','purchases.show_time_id')
                                                    ->select(DB::raw('count(*) as dependences'))
                                                    ->where('purchases.show_time_id','=',$showtime->id)->first();
                                //cases
                                if($dependences->dependences < 1)
                                    $dates[$key] = ['showtime'=>$d,'available'=>true];
                                else
                                    $dates[$key] = ['showtime'=>$d,'available'=>false];
                            } 
                            else
                                unset($dates[$key]);
                        }    
                        return ['success'=>true,'dates'=>$dates];
                    }
                    else
                        return ['success'=>false,'msg'=>'There was an error checking the dates.<br>Invalid Option.'];
                }
                return ['success'=>false,'msg'=>'There was an error checking the dates.<br>The time has not a valid format.'];
            }
            //update
            else if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                if(isset($input['showtime']) && count($input['showtime']))
                {
                    $showtimes = [];
                    foreach ($input['showtime'] as $st)
                    {
                        $showtime = ShowTime::where('show_id',$input['show_id'])->where('show_time',$st)->first();
                        if($showtime)
                        {
                            //update status
                            $showtime->is_active = $input['is_active'];
                            $showtime->updated = $current;
                            $showtime->save();
                            //delete all dependences that are not marked
                            if(isset($input['ticket_types']) && count($input['ticket_types']))
                            {
                                DB::table('soldout_tickets')->where('show_time_id',$showtime->id)->whereNotIn('ticket_id',$input['ticket_types'])->delete();
                                //update dependences
                                foreach ($input['ticket_types'] as $tt)
                                {
                                    $exists = DB::table('soldout_tickets')->where('show_time_id',$showtime->id)->where('ticket_id',$tt)->count();
                                    if(!$exists)
                                        DB::table('soldout_tickets')->insert(['show_time_id'=>$showtime->id,'ticket_id'=>$tt,'created'=>$current]);
                                }
                            }  
                            else DB::table('soldout_tickets')->where('show_time_id',$showtime->id)->delete();
                            //return showtime
                            $showtimes[] = $showtime;
                        }
                    }
                    return ['success'=>true,'action'=>0,'showtimes'=>$showtimes];
                }
                return ['success'=>false,'msg'=>'There was an error adding the showtimes.<br>The are no dates to add.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                if(isset($input['showtime']) && count($input['showtime']))
                {
                    $showtimes = [];
                    foreach ($input['showtime'] as $d)
                    {
                        //search showtime
                        $showtime = ShowTime::where('show_id','=',$input['show_id'])->where('show_times.show_time','=',$d.':00')->first();
                        if($showtime)
                        {
                            //check dependences
                            $dependences = DB::table('purchases')
                                                ->leftJoin('transactions', 'transactions.show_time_id','=','purchases.show_time_id')
                                                ->leftJoin('consignments', 'consignments.show_time_id','=','purchases.show_time_id')
                                                ->select(DB::raw('count(*) as dependences'))
                                                ->where('purchases.show_time_id','=',$showtime->id)->first();
                            //return
                            if($dependences->dependences < 1)
                            {
                                $showtimes[] = $showtime->id;
                                DB::table('soldout_tickets')->where('show_time_id',$showtime->id)->delete();
                                $showtime->delete();
                            }
                        }
                    }
                    return ['success'=>true,'action'=>-1,'showtimes'=>$showtimes];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the showtimes.<br>The are no dates to delete.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
                if(isset($input['showtime']) && count($input['showtime']))
                {
                    $showtimes = [];
                    foreach ($input['showtime'] as $st)
                    {
                        $showtime = ShowTime::where('show_id',$input['show_id'])->where('show_time',$st)->count();
                        if(!$showtime)
                        {
                            $showtime = new ShowTime;
                            $showtime->show_id = $input['show_id'];
                            $showtime->show_time = $st;
                            $showtime->time_alternative = strip_tags($input['time_alternative']);
                            $showtime->is_active = 1;
                            $showtime->created = $current;
                            $showtime->save();
                            $showtimes[] = $showtime;
                        }
                    }
                    return ['success'=>true,'action'=>1,'showtimes'=>$showtimes];
                }
                return ['success'=>false,'msg'=>'There was an error adding the showtimes.<br>The are no dates to add.'];
            }
            //save one
            else if(isset($input) && isset($input['id']) && isset($input['is_active']))
            {
                $showtime = ShowTime::find($input['id']);
                if($showtime)
                {
                    $showtime->is_active = $input['is_active'];
                    if(!empty($input['slug']) && preg_match('/^http(s)?:\/\/[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i',$input['slug']))
                        $showtime->slug = $input['slug'];
                    else
                        $showtime->slug = null;
                    $showtime->save();
                    //delete all dependences that are not marked
                    if(isset($input['ticket_types']) && count($input['ticket_types']))
                    {
                        DB::table('soldout_tickets')->where('show_time_id',$showtime->id)->whereNotIn('ticket_id',$input['ticket_types'])->delete();
                        //update dependences
                        foreach ($input['ticket_types'] as $tt)
                        {
                            $exists = DB::table('soldout_tickets')->where('show_time_id',$showtime->id)->where('ticket_id',$tt)->count();
                            if(!$exists)
                                DB::table('soldout_tickets')->insert(['show_time_id'=>$showtime->id,'ticket_id'=>$tt,'created'=>$current]);
                        }
                    }  
                    else DB::table('soldout_tickets')->where('show_time_id',$showtime->id)->delete();
                    return ['success'=>true,'showtime'=>$showtime];
                }
                return ['success'=>false,'msg'=>'There was an error updating the event.<br>The server could not retrieve the data.'];
            }
            //get
            else if(isset($input) && isset($input['id']))
            {
                $showtime = ShowTime::find($input['id']);
                if($showtime)
                {
                    $tt_inactives = DB::table('soldout_tickets')->where('show_time_id',$showtime->id)->distinct()->pluck('ticket_id');
                    return ['success'=>true,'showtime'=>$showtime,'tickets'=>$tt_inactives];
                }
                return ['success'=>false,'msg'=>'There was an error getting the event.<br>The server could not retrieve the data.'];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error ShowTimes Index: '.$ex->getMessage());
        }
    } 
    /**
     * Edit, Remove sweepstakes for show
     *
     * @return view
     */
    public function sweepstakes()
    {
        try {   
            //init
            $input = Input::all(); 
            $resp = DB::table('show_sweepstakes')->where('show_id',$input['show_id'])->update(['selected' => 0]);
            if($resp>=0)
            {
                if(count($input['user_id']))
                {
                    $resp = DB::table('show_sweepstakes')->where('show_id',$input['show_id'])->whereIn('user_id',$input['user_id'])->update(['selected' => 1]);
                    if($resp>=0)
                        return ['success'=>true];
                    return ['success'=>false,'msg'=>'Error updating all sweepstakes. Check the user input value.'];
                }
                return ['success'=>true];
            }   
            else
                return ['success'=>false,'msg'=>'Error updating all sweepstakes. Check the show input value.'];
        } 
        catch (Exception $ex) {
            throw new Exception('Error ShowSweepstakes Index: '.$ex->getMessage());
        }
    } 
    /**
     * Get, Edit, Remove contracts for show
     *
     * @return view
     */
    public function contracts($format=null,$id=null)
    {
        try {   
            //init
            $input = Input::all(); 
            $file = null;
            if(Input::hasFile('file'))
                $file = Input::file('file');
            $current = date('Y-m-d H:i:s');
            //save
            if($format && $id)
            {
                $contract = DB::table('show_contracts')->where('id',$id)->first();
                //check agreement data sent
                if($contract && isset($contract->file))
                {
                    //check format
                    if($format==='file')
                    {
                        $file = str_replace('/s3/','',$contract->file);
                        $exists = Storage::disk('s3')->exists($file);
                        if($exists)
                        {
                            $file = Storage::disk('s3')->get($file); 
                            return Response::make($file, 200, [
                                'Content-Type' => 'application/pdf',
                                'Content-Disposition' => 'inline; filename="Contract_'.$id.'" filename*="Contract_'.$id.'"'
                            ]);
                        }
                        else 
                            return '<script>alert("The system could not load the information from the DB. It does not exists.");window.close();</script>';
                    }
                    else
                        return '<script>alert("The system could not load the information from the DB. It has not a valid format.");window.close();</script>';
                }
                else
                    return '<script>alert("The system could not load the information from the DB. There is not that contract.");window.close();</script>';
            }
            //tickets info
            else if(isset($input['ticket_id']) && count($input)==1)
            {
                $ticket = Ticket::find($input['ticket_id']);
                if($ticket)
                    return ['success'=>true,'ticket'=>$ticket];
                else
                    return ['success'=>false,'msg'=>'There was an error getting the ticket.<br>The server could not retrieve the data.'];
            }
            //remove
            else if(isset($input['action']) && $input['action']==-1)
            {
                $contract = ShowContract::find($input['id']);
                if($contract)
                {
                    Util::remove_file ($contract->file);
                    if($contract->delete())
                        return ['success'=>true];
                    return ['success'=>false,'msg'=>'There was an error deleting the contract.<br>The server could not retrieve the data.'];
                }    
                else
                    return ['success'=>true];
            }
            //add
            else if(isset($input))
            {
                $contract = new ShowContract;
                if($file)
                    $contract->set_file($file);
                else 
                    return ['success'=>false,'msg'=>'There was an error saving the contract.<br>There is no file to upload.'];
                $contract->show_id = $input['show_id'];
                $contract->effective_date = $input['effective_date'];
                $contract->data = (isset($input['tickets']) && is_array($input['tickets']) && count($input['tickets']))? '['.implode(',',$input['tickets']).']' : null;
                $contract->updated = $current;
                $contract->save();
                if($contract)
                    return ['success'=>true,'contract'=>$contract];
                else
                    return ['success'=>false,'msg'=>'There was an error saving the contract.<br>The server could not retrieve the data.'];
            }
            else
                return ['success'=>false,'msg'=>'There was an error saving the contract.Invalid Option.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error ShowPasswords Index: '.$ex->getMessage());
        }
    } 
    /**
     * Get, Edit, Remove images for show
     *
     * @return view
     */
    public function images()
    {
        try {   
            //init
            $input = Input::all();
            $current = date('Y-m-d H:i:s');
            //update
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $image = Image::find($input['id']);
                if($image)
                {
                    $image->image_type = $input['image_type'];
                    $image->caption = (!empty(strip_tags($input['caption'])))? strip_tags($input['caption']) : null;
                    $image->updated = $current;
                    $image->save();
                    $image->url = Image::view_image($image->url);
                    return ['success'=>true,'action'=>0,'image'=>$image];
                }
                return ['success'=>false,'msg'=>'There was an error updating the image.<br>The server could not retrieve the data.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                $image = Image::find($input['id']);
                if($image)
                {
                    DB::table('show_images')->where('show_id',$input['show_id'])->where('image_id',$image->id)->delete();
                    $image->delete_image_file();
                    $image->delete();
                    return ['success'=>true,'action'=>-1];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the image.<br>The server could not retrieve the data.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
                $image = new Image;
                $image->created = $current;
                if(preg_match('/media\/preview/',$input['url'])) 
                    $image->set_url($input['url']);
                $image->image_type = $input['image_type'];
                $image->caption = (!empty(strip_tags($input['caption'])))? strip_tags($input['caption']) : null;
                $image->save();
                if($image)
                {
                    DB::table('show_images')->insert(['show_id'=>$input['show_id'],'image_id'=>$image->id]);
                    $image->url = Image::view_image($image->url);
                    return ['success'=>true,'action'=>1,'image'=>$image];
                } 
                return ['success'=>false,'msg'=>'There was an error adding the image.<br>The server could not retrieve the data.'];
            }
            //get
            else if(isset($input) && isset($input['id']))
            {
                $image = Image::find($input['id']);
                if($image)
                {   
                    $image->url = Image::view_image($image->url);
                    return ['success'=>true,'image'=>$image];
                }  
                return ['success'=>false,'msg'=>'There was an error getting the image.<br>The server could not retrieve the data.'];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error ShowImages Index: '.$ex->getMessage());
        }
    } 
    /**
     * Get, Edit, Remove banners for show
     *
     * @return view
     */
    public function banners()
    {
        try {   
            //init
            $input = Input::all();
            //update
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $banner = Banner::find($input['id']);
                if($banner)
                {
                    $banner->url = strip_tags($input['url']);
                    $banner->type = (isset($input['type']) && count($input['type']))? implode($input['type'],',') : null;
                    $banner->save();
                    $banner->file = Image::view_image($banner->file);
                    return ['success'=>true,'action'=>0,'banner'=>$banner];
                }
                return ['success'=>false,'msg'=>'There was an error updating the banner.<br>The server could not retrieve the data.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                $banner = Banner::find($input['id']);
                if($banner)
                {
                    $banner->delete_image_file();
                    $banner->delete();
                    return ['success'=>true,'action'=>-1];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the banner.<br>The server could not retrieve the data.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
                $banner = new Banner;
                if(preg_match('/media\/preview/',$input['file'])) 
                    $banner->set_file($input['file']);
                $banner->type = (isset($input['type']) && count($input['type']))? implode($input['type'],',') : null;
                $banner->url = strip_tags($input['url']);
                $banner->parent_id = $input['parent_id'];
                $banner->belongto = 'show';
                $banner->save();
                if($banner)
                {
                    $banner->file = Image::view_image($banner->file);
                    return ['success'=>true,'action'=>1,'banner'=>$banner];
                } 
                return ['success'=>false,'msg'=>'There was an error adding the banner.<br>The server could not retrieve the data.'];
            }
            //get
            else if(isset($input) && isset($input['id']))
            {
                $banner = Banner::find($input['id']);
                if($banner)
                {   
                    $banner->file = Image::view_image($banner->file);
                    return ['success'=>true,'banner'=>$banner];
                }  
                return ['success'=>false,'msg'=>'There was an error getting the banner.<br>The server could not retrieve the data.'];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error ShowBanners Index: '.$ex->getMessage());
        }
    } 
    /**
     * Get, Edit, Remove videos for show
     *
     * @return view
     */
    public function videos()
    {
        try {   
            //init
            $input = Input::all();
            $current = date('Y-m-d H:i:s');
            //update
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $video = Video::find($input['id']);
                if($video)
                {
                    $video->video_type = $input['video_type'];
                    $video->embed_code = strip_tags($input['embed_code'],'<iframe>');
                    $video->description = (!empty(strip_tags($input['description'])))? strip_tags($input['description']) : null;
                    $video->updated = $current;
                    $video->save();
                    return ['success'=>true,'action'=>0,'video'=>$video];
                }
                return ['success'=>false,'msg'=>'There was an error updating the video.<br>The server could not retrieve the data.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                $video = Video::find($input['id']);
                if($video)
                {
                    DB::table('show_videos')->where('show_id',$input['show_id'])->where('video_id',$video->id)->delete();
                    $video->delete();
                    return ['success'=>true,'action'=>-1];
                }
                return ['success'=>false,'msg'=>'There was an error deleting the video.<br>The server could not retrieve the data.'];
            }
            //save
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
                $video = new Video;
                $video->created = $current;
                $video->video_type = $input['video_type'];
                $video->embed_code = strip_tags($input['embed_code'],'<iframe>');
                $video->description = (!empty(strip_tags($input['description'])))? strip_tags($input['description']) : null;
                $video->audit_user_id = Auth::user()->id;
                $video->save();
                if($video)
                {
                    DB::table('show_videos')->insert(['show_id'=>$input['show_id'],'video_id'=>$video->id]);
                    return ['success'=>true,'action'=>1,'video'=>$video];
                } 
                return ['success'=>false,'msg'=>'There was an error adding the video.<br>The server could not retrieve the data.'];
            }
            //get
            else if(isset($input) && isset($input['id']))
            {
                $video = Video::find($input['id']);
                if($video)
                    return ['success'=>true,'video'=>$video];
                return ['success'=>false,'msg'=>'There was an error getting the video.<br>The server could not retrieve the data.'];
            }
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error ShowVideos Index: '.$ex->getMessage());
        }
    } 
}
