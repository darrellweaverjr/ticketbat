<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
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
            $current = date('Y-m-d H:i:s');
            //update
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $image = Image::find($input['id']);
                if($image)
                {
                    $image->image_type = $input['image_type'];
                    $image->caption = ($input['caption']!='')? $input['caption'] : null;
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
                    DB::table('show_images')->where('show_id',$input['show_id'])->where('image_id',$image->id)->delete()                            ;
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
                $image->caption = ($input['caption']!='')? $input['caption'] : null;
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
            //get all
            else
            {
                $deals= Deal::all();
                $deals = DB::table('deals')
                                ->leftJoin('shows', 'shows.id', '=' ,'deals.show_id')
                                ->select('deals.*','shows.name')->get();
                foreach ($deals as $d)
                    $d->image_url = Image::view_image($d->image_url);
                $shows = Show::where('is_active','>',0)->orderBy('name')->get();
                $venues = Venue::where('is_featured','>',0)->orderBy('name')->get();
                $discounts = Discount::all();
                //return view
                return view('admin.apps.deals',compact('deals','shows','venues','discounts'));
            }   
        } catch (Exception $ex) {
            throw new Exception('Error AppDeals: '.$ex->getMessage());
        }
    }
    
    
}
