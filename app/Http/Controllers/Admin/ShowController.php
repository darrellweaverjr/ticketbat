<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Category;
use App\Http\Models\Venue;
use App\Http\Models\Ticket;
use App\Http\Models\ShowTime;
use App\Http\Models\Banner;
use App\Http\Models\Show;
use App\Http\Models\Stage;
use App\Http\Models\Band;
use App\Http\Models\Util;
use App\Http\Models\Package;

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
                //get selected record
                $show = Show::find($input['id']);  
                if(!$show)
                    return ['success'=>false,'msg'=>'There was an error getting the show.<br>Maybe it is not longer in the system.'];
                // change relative url uploads for real one
                if(preg_match('/\/uploads\//',$show->sponsor_logo_id)) 
                    $show->sponsor_logo_id = env('IMAGE_URL_OLDTB_SERVER').$show->sponsor_logo_id;
                // change relative url s3 for real one
                if(preg_match('/\/s3\//',$show->sponsor_logo_id)) 
                    $show->sponsor_logo_id = env('IMAGE_URL_AMAZON_SERVER').str_replace('/s3/','/',$show->sponsor_logo_id);
                //search sub elements
                $tickets = DB::table('tickets')->join('packages', 'tickets.package_id', '=' ,'packages.id')
                                ->select('tickets.*','packages.title')->where('tickets.show_id','=',$show->id)->distinct()->get();
                $tt_inactive = DB::table('ticket_types_inactive')->select('ticket_types_inactive.*')->distinct()->implode('ticket_types_inactive.ticket_type',',')                            ;
                $show_times = ShowTime::where('show_id','=',$show->id)->distinct()->get();
                $passwords = DB::table('show_passwords')->select('show_passwords.*')
                                ->where('show_passwords.show_id','=',$show->id)->distinct()->get();
                $bands = DB::table('bands')->join('show_bands', 'show_bands.band_id', '=' ,'bands.id')
                                ->select('bands.name','show_bands.*')->where('show_bands.show_id','=',$show->id)
                                ->orderBy('show_bands.n_order')->distinct()->get();
                $images = DB::table('images')->join('show_images', 'show_images.image_id', '=' ,'images.id')
                                ->select('images.*')->where('show_images.show_id','=',$show->id)->distinct()->get();
                $banners = Banner::where('parent_id','=',$show->id)->where('belongto','=','show')->distinct()->get();
                $videos = DB::table('videos')->join('show_videos', 'show_videos.video_id', '=' ,'videos.id')
                                ->select('videos.*')->where('show_videos.show_id','=',$show->id)->distinct()->get();
                return ['success'=>true,'show'=>array_merge($show->getAttributes()),'tickets'=>$tickets,'ticket_types_inactive'=>$tt_inactive,'show_times'=>$show_times,'passwords'=>$passwords,'bands'=>$bands,'images'=>$images,'banners'=>$banners,'videos'=>$videos];
            }
            if(isset($input) && isset($input['venue_id']))
            {
                //search sub elements
                $stages = Stage::where('venue_id','=',$input['venue_id'])->distinct()->get();
                return ['success'=>true,'stages'=>$stages];
            }
            else
            {           
                $current = date('Y-m-d H:i:s');
                //conditions to search
                $where = [['shows.id','>',0]];
                //$where = [['images.image_type','=','Header']];
                //search venue
                if(isset($input) && isset($input['venue']))
                {
                    $venue = $input['venue'];
                    if($venue != '')
                        $where[] = ['venues.id','=',$venue];
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
                    $status = $input['status'];
                else
                    $status = 1;
                $where[] = ['shows.is_active','=',$status];    
                //search with error
                if(isset($input) && isset($input['onlyerrors']))
                {
                    $onlyerrors = $input['onlyerrors'];
                     if($onlyerrors == 1)
                     {
                         
                     }
                }
                else
                    $onlyerrors = 0;
                //get all records        
                $shows = DB::table('shows')
                                ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                                ->join('categories', 'categories.id', '=' ,'shows.category_id')
                                ->join('stages', 'stages.id', '=' ,'shows.stage_id')
                                ->leftJoin('show_times', 'show_times.show_id', '=' ,'shows.id')
                                ->leftJoin('show_images', 'show_images.show_id', '=' ,'shows.id')
                                ->leftJoin('images', 'show_images.image_id', '=' ,'images.id')
                                ->select('shows.*','categories.name AS category','images.url AS image_url','venues.name AS venue_name','stages.name AS stage_name')
                                ->where($where)
                                ->orderBy('shows.name')->groupBy('shows.id')
                                ->distinct()->get();
                $categories = Category::all();
                $venues = Venue::all();
                $stages = Stage::all();
                $bands = Band::orderBy('name')->get();
                $restrictions = Util::getEnumValues('shows','restrictions');
                $banner_types = [];//Util::getEnumValues('banners','type');
                $ticket_types = Util::getEnumValues('tickets','ticket_type');
                $tt_inactive = DB::table('ticket_types_inactive')->select('ticket_types_inactive.ticket_type')->distinct()->get();
                foreach ($tt_inactive as $tt)
                    unset($ticket_types[$tt->ticket_type]);
                $packages = Package::all();
                //return view
                return view('admin.shows.index',compact('shows','categories','venues','stages','bands','restrictions','banner_types','ticket_types','packages','venue','showtime','status','onlyerrors'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Shows Index: '.$ex->getMessage());
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
                    $show = Show::find($input['id']);
                    $show->updated = $current;
                    if(preg_match('/media\/preview/',$input['sponsor_logo_id'])) 
                        $show->delete_image_file();
                }                    
                else
                {                    
                    $show = new Show;
                    $show->audit_user_id = Auth::user()->id;
                    $show->created = $current;
                }
                //save show
                $show->venue_id = $input['venue_id'];
                $show->stage_id = $input['stage_id'];
                $show->category_id = $input['category_id'];
                $show->name = $input['name'];
                $show->slug = $input['slug'];
                $show->presented_by = $input['presented_by'];
                $show->sponsor = $input['sponsor'];
                $show->short_description = $input['short_description'];
                $show->description = $input['description'];
                $show->emails = $input['emails'];
                $show->accounting_email = $input['accounting_email'];
                $show->url = $input['url'];
                $show->restrictions = $input['restrictions'];
                $show->is_featured = $input['is_featured'];
                $show->cutoff_hours = $input['cutoff_hours'];
                $show->is_active = $input['is_active'];
                $show->facebook = $input['facebook'];
                $show->twitter = $input['twitter'];
                $show->youtube = $input['youtube'];
                $show->instagram = $input['instagram'];
                $show->yelpbadge = $input['yelpbadge'];
                $show->on_sale = $input['on_sale'];
                $show->printed_tickets = $input['printed_tickets'];
                $show->individual_emails = $input['individual_emails'];
                $show->manifest_emails = $input['manifest_emails'];
                $show->daily_sales_emails = $input['daily_sales_emails'];
                $show->financial_report_emails = $input['financial_report_emails'];
                if(isset($input['amex_only_start_date']) && $input['amex_only_start_date']!='' && isset($input['amex_only_end_date']) && $input['amex_only_end_date']!=''
                        && isset($input['ticket_types']) && count($input['ticket_types']))
                {
                    $show->amex_only_ticket_types = Ticket::where('show_id','=',$input['id'])->whereIn('id',$input['ticket_types']) ->distinct()->get()->implode('ticket_type',',')                            ;
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
                $show->save();
                //return
                return ['success'=>true,'msg'=>'Show saved successfully!'];
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
            if($input && isset($input['name']) && isset($input['venue_id']) && isset($input['show_id']))
                return Util::generate_slug($input['name'], $input['venue_id'], $input['show_id']);
            return '';
        } catch (Exception $ex) {
            return '';
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
                $tickets = Ticket::where('show_id','=',$passwords->show_id)->whereIn('ticket_type', explode(',',$passwords->ticket_types)) ->distinct()->pluck ('id')                            ;
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
                $tt = Ticket::where('show_id','=',$input['show_id'])->whereIn('id',$input['ticket_types']) ->distinct()->get()->implode('ticket_type',',')                            ;
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
                    $password = DB::table('show_passwords')->insertGetId ($show_password);
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
            throw new Exception('Error Shows Index: '.$ex->getMessage());
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
            //save
            else if(isset($input))
            {;
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
                    //no default
                    Ticket::where('show_id','=',$input['show_id'])->update(['is_default'=>0]);
                    //if not put first active as default
                    if(!$input['is_default'])
                    {
                        if(isset($input['id']) && isset($input['id']))
                            $t = Ticket::where('show_id','=',$input['show_id'])->where('is_active','=',1)->where('id','<>',$input['id'])->first();
                        else
                            $t = Ticket::where('show_id','=',$input['show_id'])->where('is_active','=',1)->first();
                        if($t)
                        {
                            $t->is_default = 1;
                            $t->save();
                        }
                        else 
                            $input['is_default'] = 1;
                    }
                }
                //create/update
                $ticket->show_id = $input['show_id'];
                $ticket->package_id = $input['package_id'];
                $ticket->ticket_type = $input['ticket_type'];
                $ticket->retail_price = $input['retail_price'];
                $ticket->processing_fee = $input['processing_fee'];
                $ticket->percent_commission = $input['percent_commission'];
                $ticket->percent_pf = $input['percent_pf'];
                $ticket->max_tickets = $input['max_tickets'];
                $ticket->is_default = $input['is_default'];
                $ticket->is_active = $input['is_active'];
                $ticket->save();
                //return
                $tickets = DB::table('tickets')->join('packages', 'tickets.package_id', '=' ,'packages.id')
                                ->select('tickets.*','packages.title')->where('tickets.show_id','=',$input['show_id'])->distinct()->get();
                return ['success'=>true,'tickets'=>$tickets];
            }
            else
                return ['success'=>false,'msg'=>'There was an error saving the ticket.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Tickets Index: '.$ex->getMessage());
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
            else
                return ['success'=>false,'msg'=>'Invalid Option.'];
        } catch (Exception $ex) {
            throw new Exception('Error Bands Index: '.$ex->getMessage());
        }
    } 
    
}
