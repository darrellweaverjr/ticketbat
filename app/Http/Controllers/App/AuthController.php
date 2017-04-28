<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Util;

/**
 * Manage auth options
 *
 * @author ivan
 */
class AuthController extends Controller{
    
    /*
     * login user
     */
    public function login()
    {
        try {
            $info = Input::all();
            if(!empty($info['email']) && !empty($info['password']))
            {
                $user = DB::table('users')
                            ->join('locations', 'locations.id', '=' ,'users.location_id')
                            ->select('users.id','users.email','users.password','users.first_name','users.last_name','users.user_type_id',
                                     'users.phone','locations.address','locations.city','locations.country','locations.state','locations.zip')
                            ->where('users.email','=',$info['email'])->where('users.password','=',$info['password'])
                            ->where('users.is_active','>',0)->first();
                if($user) 
                {
                    $a_token = $this->create_a_token($user);
                    $user->password = null;
                    return Util::json(['success'=>true,'user'=>$user,'a_token'=>$a_token]);
                } 
                return Util::json(['success'=>false, 'msg'=>'Credentials Invalid!']);
            }
            return Util::json(['success'=>false, 'msg'=>'You must enter a valid email and password!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }  
    
    /*
     * register user
     */
    public function register()
    {
        try {
            $info = Input::all();
            $current = date('Y-m-d h:i:s');
            if(!empty($info['email']) && !empty($info['password']) && !empty($info['first_name']) && !empty($info['last_name']) && !empty($info['phone']) 
            && !empty($info['address']) && !empty($info['city']) && !empty($info['region']) && !empty($info['country']) && !empty($info['zip']))
            {
                //check password
                if(!(strlen($info['password'])>=8 && preg_match('/[A-Z]+[a-z]+[0-9]+/',$info['password']))) 
                    return Util::json(['success'=>false, 'msg'=>'The new password must have at least 8 characters, a lower case character, an upper case character, and a number']);
                //check user
                $user = User::where('email','=',$info['email'])->first();
                if($user)
                    return ['success'=>false, 'msg'=>'That email is already in the system.'];
                //save location
                $location = new Location;
                $location->created = $current;                
                $location->address = $info['address'];
                $location->city = $info['city'];
                $location->state = strtoupper($info['region']);
                $location->zip = $info['zip'];
                $location->country = $info['country'];
                $location->set_lng_lat();
                $location->save();
                //save user
                $user = new User;
                $user->created = $current;
                $user->user_type_id = 2;
                $user->is_active = 1;
                $user->force_password_reset = 0;
                $user->location()->associate($location);
                $user->first_name = $info['first_name'];
                $user->last_name = $info['last_name'];
                $user->phone = (!empty($info['phone']))? $info['phone'] : null;
                $user->slug = $info['password'];
                $user->set_password($info['password']);
                $user->save();
                //send email welcome
                $user->welcome_email();
                //erase temp pass
                $user->set_slug();
                //set customer
                $user->update_customer();  
                //return
                return Util::json(['success'=>true, 'msg'=>'Your credentials were successfully created! We sent you an email with your login information.']);
            }
            return Util::json(['success'=>false, 'msg'=>'You must fill the form out correctly!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }  
    
    /*
     * recover password
     */
    public function recover()
    {
        try {
            $info = Input::all();
            if(!empty($info['email']))
            {
                $user = User::where('email','=',$info['email'])->first();
                if(!$user)
                    return ['success'=>false, 'msg'=>'That email is not in the system.'];
                //create new password
                $user->set_password();
                //send email welcome
                $user->welcome_email();
                //erase temp pass
                $user->set_slug();
                $user->save();
                return Util::json(['success'=>true]);
            }
            return Util::json(['success'=>false, 'msg'=>'You must enter a valid email!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }  
    
    /*
     * change password
     */
    public function change()
    {
        try {
            $info = Input::all();
            if(!empty($info['a_token']) && $info['old_pass'] && !empty($info['new_pass']))
            {
                if(!(strlen($info['new_pass'])>=8 && preg_match('/[A-Z]+[a-z]+[0-9]+/',$info['new_pass']))) 
                    return Util::json(['success'=>false, 'msg'=>'The new password must have at least 8 characters, a lower case character, an upper case character, and a number']);
                $id = explode('.',$info['a_token']);
                $user = User::where('id','=',$id[0])->where('password','=',md5($info['old_pass']))->first();
                if(!$user)
                    return Util::json(['success'=>true,'msg'=>'The current password entered is not valid!']);
                //update password
                $user->set_password($info['new_pass']);
                $user->save();
                return Util::json(['success'=>true, 'a_token'=>$this->create_a_token($user)]);
            }
            return Util::json(['success'=>false, 'msg'=>'You must be logged and enter a valid passwords!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }  
    
    /*
     * create auth token
     */
    public function create_a_token($user)
    {
        try {
            if(!empty($user))
                return $user->id.'.'.md5($user->email.$user->password.env('APP_KEY'));
            return '';
        } catch (Exception $ex) {
            return '';
        }
    }  
    
}
