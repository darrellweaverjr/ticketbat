<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
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
            $input = Input::all(); 
            if(isset($input) && isset($input['id']))
            {
                //get selected record
                $user = User::find($input['id']);  
                if(!$user)
                    return ['success'=>false,'msg'=>'There was an error getting the user.<br>Maybe it is not longer in the system.'];
                $location = Location::find($user->location_id);
                $discounts = [];
                foreach($user->user_discounts as $d)
                    $discounts[] = $d->pivot->discount_id;
                $user->venues_check_ticket = explode(',',$user->venues_check_ticket);
                //dont show these fields
                unset($user->password);
                unset($location->id);
                return ['success'=>true,'user'=>array_merge($user->getAttributes(),$location->getAttributes(),['discounts[]'=>$discounts],['venues_check_ticket[]'=>$user->venues_check_ticket])];
            }
            else
            {
                //get all records        
                $users = DB::table('users')
                                ->join('user_types', 'user_types.id', '=' ,'users.user_type_id')
                                ->select('users.*', 'user_types.user_type')
                                ->orderBy('users.last_name')
                                ->get();
                $user_types = UserType::all();
                $discounts = Discount::all();
                $venues = Venue::orderBy('name')->get();
                $countries = Country::orderBy('code')->get();
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
            $input = Input::all();
            //save all record      
            if($input)
            {
                $current = date('Y-m-d H:i:s');
                if(isset($input['id']) && $input['id'])
                {
                    $user = User::find($input['id']);
                    $user->updated = $current;
                    $location = $user->location;
                    $location->updated = $current;
                    if(isset($input['password']) && $input['password'])
                        $user->password = md5($input['password']);
                }                    
                else
                {                    
                    if(User::where('email','=',$input['email'])->count())
                        return ['success'=>false,'msg'=>'There was an error saving the user.<br>That email is already in the system.','errors'=>'email'];
                    $user = new User;
                    $location = new Location;
                    $location->created = $current;
                    $location->updated = $current;
                    $user->audit_user_id = Auth::user()->id;
                    if(isset($input['password']) && $input['password'])
                        $user->set_password();
                }
                //save location
                $location->address = $input['address'];
                $location->city = $input['city'];
                $location->state = strtoupper($input['state']);
                $location->zip = $input['zip'];
                $location->country = $input['country'];
                $location->set_lng_lat();
                $location->save();
                //save user
                $user->location()->associate($location);
                $user->user_type_id = $input['user_type_id'];
                $user->email = $input['email'];
                $user->first_name = $input['first_name'];
                $user->last_name = $input['last_name'];
                $user->phone = $input['phone'];
                $user->is_active = $input['is_active'];
                $user->commission_percent = $input['commission_percent'];
                $user->percentage_processing_fee = $input['percentage_processing_fee'];
                $user->fixed_processing_fee = $input['fixed_processing_fee'];
                $user->force_password_reset = $input['force_password_reset'];
                if(isset($input['venues_check_ticket']) && $input['venues_check_ticket'] && count($input['venues_check_ticket']))
                    $user->venues_check_ticket = implode(',',$input['venues_check_ticket']);
                $user->set_slug();
                $user->save();
                //update intermediate table with discounts
                if(isset($input['discounts']) && $input['discounts'] && count($input['discounts']))
                    $user->user_discounts()->sync($input['discounts']);
                else
                    $user->user_discounts()->detach();
                //return
                return ['success'=>true,'msg'=>'User saved successfully!'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the user.<br>The server could not retrieve the data.'];
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
            return ['success'=>false,'msg'=>'There was an error deleting the user(s)!<br>They might have some dependences.'];
        } catch (Exception $ex) {
            throw new Exception('Error Users Remove: '.$ex->getMessage());
        }
    }
}
