<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use App\Http\Models\User;
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
                $user = User::where('email',$info['email'])->where('password',$info['password'])->where('is_active','>',0)
                            ->first(['id','email','password','first_name','last_name','user_type_id']);
                if($user) 
                {
                    $a_token = $user->id.'.'.md5($user->email.$user->password);
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
