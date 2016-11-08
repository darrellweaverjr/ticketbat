<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\User;
use App\Http\Models\UserType;
use App\Http\Models\Discount;
use App\Http\Models\Venue;
use App\Http\Models\Country;
use App\Http\Models\Location;

/**
 * Manage Users
 *
 * @author ivan
 */
class UserController extends Controller{
        
    /**
     * List all users and return default view.
     *
     * @return view
     */
    public function index()
    {
        try {
            //init
            $input = Input::all();  //$input['id'] = 3078;
            if(isset($input) && isset($input['id']))
            {
                //get selected record
                $user = User::find($input['id']);                
                $location = Location::find($user->location_id);
                $discounts = [];
                foreach($user->user_discounts as $d)
                    $discounts[] = $d->pivot->discount_id;
                $venues = explode(',',$user->venues_check_ticket);
                //dont show these fields
                unset($user->password);
                unset($location->id);
                return ['success'=>true,'user'=>array_merge($user->getAttributes(),$location->getAttributes(),['discounts'=>$discounts],['venues'=>$venues])];
            }
            else
            {
                //get all records        
                $users = User::all();
                $user_types = UserType::all();
                $discounts = Discount::all();
                $venues = Venue::all();
                $countries = Country::all();
                //$locations = Location::all();
                //return view
                return view('admin.users.index',compact('users','user_types','discounts','venues','countries'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Users Index: '.$ex->getMessage());
        }
    }
    /**
     * Save new or updated user.
     *
     * @void
     */
    public function save()
    {
        try {
            //init
            //$input = Input::all();
            //get all records        
            $users = User::all();
            $user_types = UserType::all();
            $discounts = Discount::all();
            $venues = Venue::all();
            $countries = Country::all();
            //$locations = Location::all();
            //return view
            return view('admin.users.index',compact('users','user_types','discounts','venues','countries'));
        } catch (Exception $ex) {
            throw new Exception('Error Users Save: '.$ex->getMessage());
        }
    }
    /**
     * Remove users.
     *
     * @void
     */
    public function remove()
    {
        try {
            //init
            $input = Input::all();
            //delete all records   
            if(User::destroy($input['id']))
                return ['success'=>true,'msg'=>'All records deleted successfully!'];
            return ['success'=>false,'msg'=>'There was an error deleting the user(s)! They might have some dependences.'];
        } catch (Exception $ex) {
            throw new Exception('Error Users Remove: '.$ex->getMessage());
        }
    }
    
}
