<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Deal;
use App\Http\Models\Show;
use App\Http\Models\Venue;
use App\Http\Models\Discount;
use App\Http\Models\Image;

class AppController extends Controller
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
     * Show the default method on the app.
     *
     * @return Method
     */
    public function index()
    {
        return $this->deals();
    }
    
    /**
     * Show the deals for the app.
     *
     * @return view
     */
    public function deals()
    {
        try {   
            //init
            $input = Input::all();
            //update
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $deal = Deal::find($input['id']);
                if($deal)
                {
                    if(preg_match('/media\/preview/',$input['image_url'])) 
                        $deal->set_image_url($input['image_url']);
                    if($input['type']=='url')
                    {
                        if(empty($input['url']))
                            return ['success'=>false,'msg'=>'There was an error adding the deal.<br>The URL is empty.'];
                        $deal->url = $input['url'];
                        $deal->show_id = null;
                        $deal->discount_id = null;
                    }
                    else
                    {
                        if(empty($input['show_id'])|| empty($input['discount_id']))
                            return ['success'=>false,'msg'=>'There was an error updating the deal.<br>You must select a valid show and coupon.'];
                        $deal->url = null;
                        $deal->show_id = $input['show_id'];
                        $deal->discount_id = $input['discount_id'];
                    }
                    if(isset($input['displayToShows']) && is_array($input['displayToShows']) && count($input['displayToShows']))
                        $deal->displayToShows = implode(',',$input['displayToShows']);
                    else $deal->displayToShows = null;
                    if(isset($input['displayToVenues']) && is_array($input['displayToVenues']) && count($input['displayToVenues']))
                        $deal->displayToVenues = implode(',',$input['displayToVenues']);
                    else $deal->displayToVenues = null;
                    $deal->save();
                    if($deal)
                        return ['success'=>true,'msg'=>'Deal saved successfully!'];
                    return ['success'=>false,'msg'=>'There was an error updating the deal.<br>The server could not retrieve the data.'];
                }
                return ['success'=>false,'msg'=>'There was an error updating the deal.<br>The server could not retrieve the data.'];
            }
            //remove
            else if(isset($input) && isset($input['action']) && $input['action']==-1)
            {
                foreach ($input['id'] as $id)
                {
                    $deal = Deal::find($id);
                    if($deal)
                    {
                        $deal->delete_image_file();
                        $deal->delete();
                    }
                }
                return ['success'=>true,'msg'=>'All records deleted successfully!'];
            }
            //save
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
                $deal = new Deal;
                if(preg_match('/media\/preview/',$input['image_url'])) 
                    $deal->set_image_url($input['image_url']);
                if($input['type']=='url')
                {
                    if(empty($input['url']))
                        return ['success'=>false,'msg'=>'There was an error adding the deal.<br>The URL is empty.'];
                    $deal->url = $input['url'];
                    $deal->show_id = null;
                    $deal->discount_id = null;
                }
                else 
                {
                    if(empty($input['show_id'])|| empty($input['discount_id']))
                        return ['success'=>false,'msg'=>'There was an error adding the deal.<br>You must select a valid show and coupon.'];
                    $deal->url = null;
                    $deal->show_id = $input['show_id'];
                    $deal->discount_id = $input['discount_id'];
                }
                if(isset($input['displayToShows']) && is_array($input['displayToShows']) && count($input['displayToShows']))
                    $deal->displayToShows = implode(',',$input['displayToShows']);
                if(isset($input['displayToVenues']) && is_array($input['displayToVenues']) && count($input['displayToVenues']))
                    $deal->displayToVenues = implode(',',$input['displayToVenues']);
                $deal->save();
                if($deal)
                    return ['success'=>true,'msg'=>'Deal saved successfully!'];
                return ['success'=>false,'msg'=>'There was an error adding the deal.<br>The server could not retrieve the data.'];
            }
            //get
            else if(isset($input) && isset($input['id']))
            {
                $deal = Deal::find($input['id']);
                if($deal)
                {   
                    $deal->image_url = Image::view_image($deal->image_url);
                    $deal->displayToShows = explode(',',$deal->displayToShows);
                    $deal->displayToVenues = explode(',',$deal->displayToVenues);
                    return ['success'=>true,'deal'=>array_merge($deal->getAttributes(),['displayToShows[]'=>$deal->displayToShows],['displayToVenues[]'=>$deal->displayToVenues])];
                }  
                return ['success'=>false,'msg'=>'There was an error getting the deal.<br>The server could not retrieve the data.'];
            }
            //get all
            else
            {
                //if user has permission to view
                $shows = [];
                $venues = [];
                $discounts = [];
                $deals = [];
                if(in_array('View',Auth::user()->user_type->getACLs()['APPS']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['APPS']['permission_scope'] != 'All')
                    {
                        $deals = DB::table('deals')
                                ->leftJoin('shows', 'shows.id', '=' ,'deals.show_id')
                                ->leftJoin('discounts', 'discounts.id', '=' ,'deals.discount_id')
                                ->select('deals.*','shows.name','discounts.code')
                                ->where(DB::raw('shows.venue_id IN ('.Auth::user()->venues_edit.') OR shows.audit_user_id'),'=',Auth::user()->id)
                                ->get();
                        foreach ($deals as $d)
                            $d->image_url = Image::view_image($d->image_url);
                        $shows = Show::where('is_active','>',0)->whereIn('venue_id',explode(',',Auth::user()->venues_edit))->orWhere('audit_user_id',Auth::user()->id)->orderBy('name')->get(['id','name','venue_id']);
                        $venues = Venue::where('is_featured','>',0)->whereIn('id',explode(',',Auth::user()->venues_edit))->orderBy('name')->get(['id','name']);
                    }//all
                    else
                    {
                        $deals = DB::table('deals')
                                ->leftJoin('shows', 'shows.id', '=' ,'deals.show_id')
                                ->leftJoin('discounts', 'discounts.id', '=' ,'deals.discount_id')
                                ->select('deals.*','shows.name','discounts.code')->get();
                        foreach ($deals as $d)
                            $d->image_url = Image::view_image($d->image_url);
                        $shows = Show::where('is_active','>',0)->orderBy('name')->get();
                        $venues = Venue::where('is_featured','>',0)->orderBy('name')->get();
                    }
                    $discounts = Discount::orderBy('code')->get(['id','code','description']);
                }
                //return view
                return view('admin.apps.deals',compact('deals','shows','venues','discounts'));
            }   
        } catch (Exception $ex) {
            throw new Exception('Error AppDeals: '.$ex->getMessage());
        }
    }
    
    
}
