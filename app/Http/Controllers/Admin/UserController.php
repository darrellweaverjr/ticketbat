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
use App\Http\Models\Transaction;

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
                $search = [];
                $where = [['users.id','>',0]];
                //search first_name
                if(isset($input) && isset($input['first_name']))
                {
                    $search['first_name'] = $input['first_name'];
                    if($search['first_name'] != '')
                        $where[] = ['users.first_name','like','%'.$input['first_name'].'%'];
                }
                else
                    $search['first_name'] = '';
                //search last_name
                if(isset($input) && isset($input['last_name']))
                {
                    $search['last_name'] = $input['last_name'];
                    if($search['last_name'] != '')
                        $where[] = ['users.last_name','like','%'.$input['last_name'].'%'];
                }
                else
                    $search['last_name'] = '';
                //search email
                if(isset($input) && isset($input['email']))
                {
                    $search['email'] = $input['email'];
                    if($search['email'] != '')
                        $where[] = ['users.email','like','%'.$input['email'].'%'];
                }
                else
                    $search['email'] = '';
                //search role
                if(isset($input) && !empty($input['user_type_id']))
                {
                    $search['user_type_id'] = $input['user_type_id'];
                    $where[] = ['users.user_type_id','=',$search['user_type_id']];
                }
                else
                    $search['user_type_id'] = 0;
               //search status
                if(isset($input) && !empty($input['is_active']))
                {
                    $search['is_active'] = $input['is_active'];
                    if($search['is_active'] > 0)
                        $where[] = ['users.is_active','>',0];
                    else
                        $where[] = ['users.is_active','=',0];
                }
                else
                    $search['is_active'] = 0;
                //if user has permission to view
                if(in_array('View',Auth::user()->user_type->getACLs()['USERS']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['USERS']['permission_scope'] != 'All')
                    {
                        //get audit user records
                        if(count($input))
                        $users = DB::table('users')
                                ->join('user_types', 'user_types.id', '=' ,'users.user_type_id')
                                ->select(DB::raw('users.id, users.email, users.first_name, users.last_name, users.phone, user_types.user_type, IF(users.is_active>0,"Active","Inactive") AS is_active, users.created'))
                                ->where($where)
                                ->where('users.audit_user_id','=',Auth::user()->id)
                                ->orderBy('users.last_name')
                                ->get();
                    }
                    else
                    {
                        //get all records
                        if(count($input))
                        $users = DB::table('users')
                                ->join('user_types', 'user_types.id', '=' ,'users.user_type_id')
                                ->select(DB::raw('users.id, users.email, users.first_name, users.last_name, users.phone, user_types.user_type, IF(users.is_active>0,"Active","Inactive") AS is_active, users.created'))
                                ->where($where)
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
                $modal = (count($input))? 0 : 1;
                return view('admin.users.index',compact('users','user_types','discounts','venues','countries','search','modal'));
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
                $customer = null;
                if(isset($input['id']) && $input['id'])
                {
                    if(User::where('email','=',$input['email'])->where('id','!=',$input['id'])->count())
                        return ['success'=>false,'msg'=>'There was an error saving the user.<br>That email is already in the system.','errors'=>'email'];
                    $user = User::find($input['id']);
                    $user->updated = $current;
                    $location = $user->location;
                    $location->updated = $current;
                    if(isset($input['password']) && $input['password'])
                        $user->password = md5($input['password']);
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
                    $customer = Customer::where('email',$input['email'])->first();
                }
                //save location
                $location->address = strip_tags($input['address']);
                $location->city = strip_tags($input['city']);
                $location->state = strip_tags(strtoupper($input['state']));
                $location->zip = $input['zip'];
                $location->country = $input['country'];
                $location->set_lng_lat();
                $location->save();
                //save user
                $user->location()->associate($location);
                $user->user_type_id = $input['user_type_id'];
                $user->email = trim($input['email']);
                $user->first_name = ucwords(trim(strip_tags($input['first_name'])));
                $user->last_name = ucwords(trim($input['last_name']));
                $user->phone = $input['phone'];
                $user->is_active = $input['is_active'];
                //remove these fields from DB
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
                if(!empty($input['update_customer']))
                    $user->update_customer($customer);
                //update table transactions customer_id
                if(!empty($input['update_transaction_customer']))
                {
                    if(!$customer)
                        $customer = Customer::where('email','=',$input['email'])->first();
                    if($customer)
                        Transaction::where('customer_id',$customer->id)->update(['card_holder'=>$user->first_name.' '.$user->last_name]);
                }
                //update table transactions user_id
                if(!empty($input['update_transaction_user']))
                {
                    Transaction::where('user_id',$user->id)->update(['card_holder'=>$user->first_name.' '.$user->last_name]);
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
    public function impersonate()
    {
        try {
            //init
            $input = Input::all();
            if(Auth::check() && Auth::user()->user_type_id == 1)
            {
                if(!empty($input['id']) && isset($input['option']))
                {
                    $user = User::find($input['id']);
                    //admin
                    if($input['option']==0)
                    {
                        if($user && in_array($user->user_type_id, explode(',',env('ADMIN_LOGIN_USER_TYPE'))))
                        {
                            Auth::logout();
                            Auth::login($user);
                            return ['success'=>true,'msg'=>'The system is going to enter into the '.$user->first_name.' '.$user->last_name.' session.'];
                        }
                        return ['success'=>false,'msg'=>'There was an error.<br>You must select a valid user allowed to enter to the Admin.'];
                    }
                    //public
                    else if($input['option']==1)
                    {
                        if($user)
                        {
                            Auth::logout();
                            Auth::login($user);
                            return ['success'=>true,'msg'=>'The system is going to enter into the '.$user->first_name.' '.$user->last_name.' session.'];
                        }
                        return ['success'=>false,'msg'=>'There was an error.<br>You must select a valid user allowed to enter to TicketBat.'];
                    }
                    else
                        return ['success'=>false,'msg'=>'There was an error.<br>You must select a valid site to redirect.'];
                }
                return ['success'=>false,'msg'=>'There was an error.<br>You must select a valid user to impersonate.'];
            }
            return ['success'=>false,'msg'=>'There was an error.<br>You must be logged as an administrator to use this option.'];

        } catch (Exception $ex) {
            throw new Exception('Error Users Impersonate: '.$ex->getMessage());
        }
    }
}
