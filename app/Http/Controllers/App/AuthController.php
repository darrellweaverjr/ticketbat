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
                    $a_token = $user->id.'.'.md5($user->email.$user->password.env('APP_KEY'));
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
    
}
