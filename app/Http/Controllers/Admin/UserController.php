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
use App\Http\Models\Customer;
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
                $user->venues_edit = explode(',',$user->venues_edit);
                //dont show these fields
                unset($user->password);
                unset($location->id);
                return ['success'=>true,'user'=>array_merge($user->getAttributes(),$location->getAttributes(),['discounts[]'=>$discounts],['venues_check_ticket[]'=>$user->venues_check_ticket],['venues_edit[]'=>$user->venues_edit])];
            }
            else
            {
                $user_types = [];
                $discounts = [];
                $venues = [];
                $countries = [];
                $users = [];
                //if user has permission to view
                if(in_array('View',Auth::user()->user_type->getACLs()['USERS']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['USERS']['permission_scope'] != 'All')
                    {
                        //get audit user records        
                        $users = DB::table('users')
                                ->join('user_types', 'user_types.id', '=' ,'users.user_type_id')
                                ->select('users.id','users.email','users.first_name','users.last_name','users.phone','users.is_active','users.user_type_id','user_types.user_type')
                                ->where('users.audit_user_id','=',Auth::user()->id)
                                ->orderBy('users.last_name')
                                ->get();
                    }  
                    else 
                    {
                        //get all records        
                        $users = DB::table('users')
                                ->join('user_types', 'user_types.id', '=' ,'users.user_type_id')
                                ->select('users.id','users.email','users.first_name','users.last_name','users.phone','users.is_active','users.user_type_id','user_types.user_type')
                                ->orderBy('users.last_name')
                                ->get();
                    }  
                    //other enum
                    $user_types = UserType::orderBy('user_type')->get(['id','user_type','description']);
                    $discounts = Discount::orderBy('code')->get(['id','code','description']);
                    $venues = Venue::orderBy('name')->get(['id','name']);
                    $countries = Country::orderBy('code')->get(['code','name']);
                }
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
            $customer=null;
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
                    //get customer
                    $customer = Customer::where('email',$user->email)->first();
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
                //save customer location
                if($customer)
                {
                    $location_c = $customer->location;
                    $location_c->updated = $current;
                    $location_c->address = $input['address'];
                    $location_c->city = $input['city'];
                    $location_c->state = strtoupper($input['state']);
                    $location_c->zip = $input['zip'];
                    $location_c->country = $input['country'];
                    $location_c->set_lng_lat();
                    $location_c->save();
                    $customer->location()->associate($location_c);
                }
                //save user
                $user->location()->associate($location);
                $user->user_type_id = $input['user_type_id'];
                $user->email = $input['email'];
                $user->first_name = $input['first_name'];
                $user->last_name = $input['last_name'];
                $user->phone = $input['phone'];
                $user->is_active = $input['is_active'];
                //remove these fields from DB
                //$user->commission_percent = $input['commission_percent'];
                //$user->percentage_processing_fee = $input['percentage_processing_fee'];
                //$user->fixed_processing_fee = $input['fixed_processing_fee'];
                $user->force_password_reset = $input['force_password_reset'];
                if(isset($input['venues_check_ticket']) && $input['venues_check_ticket'] && count($input['venues_check_ticket']))
                    $user->venues_check_ticket = implode(',',$input['venues_check_ticket']);
                else
                    $user->venues_check_ticket = null;
                if(isset($input['venues_edit']) && $input['venues_edit'] && count($input['venues_edit']))
                    $user->venues_edit = implode(',',$input['venues_edit']);
                else
                    $user->venues_edit = null;
                //$user->set_slug();
                $user->save();
                //update table customers
                if($customer)
                {
                    $customer->email = $input['email'];
                    $customer->first_name = $input['first_name'];
                    $customer->last_name = $input['last_name'];
                    $customer->phone = $input['phone'];
                    $customer->updated = $current;
                    $customer->save();
                }
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
    /**
     * Save logged user.
     *
     * @void
     */
    public function profile()
    {
        try {
            //init
            $input = Input::all();
            $current = date('Y-m-d H:i:s');
            //save all record      
            if($input)
            {
                $user = User::find(Auth::user()->id);
                $user->updated = $current;
                $location = $user->location;
                $location->updated = $current;
                if(isset($input['password']) && $input['password'])
                    $user->password = md5($input['password']);
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
                $user->first_name = $input['first_name'];
                $user->last_name = $input['last_name'];
                $user->phone = $input['phone'];
                $user->set_slug();
                $user->save();
                //return
                return ['success'=>true,'msg'=>'User saved successfully!'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the user.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Users Profile: '.$ex->getMessage());
        }
    }
    /**
     * Code for impersonate.
     *
     * @void
     */
    public function impersonate($user=null,$code=null)
    {
        try {
            //init
            $input = Input::all();
            $current = date('Y-m-d H:i');
            if($user && $code)
            {
                $user = User::find($user);
                if($user)
                {
                    $current0 = substr(md5(substr(md5($user->email),0,10).substr(md5($current),0,10)),0,20);
                    $current1 = substr(md5(substr(md5($user->email),0,10).substr(md5(date('Y-m-d H:i',strtotime($current.' -1 minutes'))),0,10)),0,20);
                    if($code == $current0 || $code == $current1)
                    {
                        if (Auth::attempt(['email' => $user->email, 'password' => $user->password])) 
                        {
                            if(Auth::user()->is_active > 0 && in_array(Auth::user()->user_type->id,explode(',',env('ADMIN_LOGIN_USER_TYPE'))))
                                return redirect()->route('home');
                            else
                                return redirect()->route('logout');
                        } 
                        else 
                            return redirect()->route('logout');
                    }
                    else 
                        return redirect()->route('home');
                }
                else 
                    return redirect()->route('home');
            }
            //save all record      
            else if($input)
            {
                if(isset($input['action']) && $input['action']==0)
                {
                    $user_types = UserType::orderBy('user_type')->pluck ('user_type')                            ;
                    $users = DB::table('users')
                                ->join('user_types', 'user_types.id', '=' ,'users.user_type_id')
                                ->select(DB::raw('users.id, user_types.user_type, CONCAT(users.first_name," ",users.last_name) AS name'))
                                ->orderBy('users.first_name')->get();
                    return ['success'=>true,'user_types'=>$user_types,'users'=>$users];
                }
                else if(isset($input['user_id']) && !empty($input['user_id']))
                {
                    $user = User::find($input['user_id']);
                    if($user)
                    {
                        $current = substr(md5(substr(md5($user->email),0,10).substr(md5(date('Y-m-d H:i')),0,10)),0,20);
                        $link = $input['user_id'].'/'.$current;
                        return ['success'=>true,'link'=>$link];
                    }
                    return ['success'=>false,'msg'=>'There was an error.<br>That user does not exist.'];
                }
                else
                    return ['success'=>false,'msg'=>'There was an error.<br>Option Invalid!.'];
            }
            return ['success'=>false,'msg'=>'There was an error.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Users Impersonate: '.$ex->getMessage());
        }
    }
}
