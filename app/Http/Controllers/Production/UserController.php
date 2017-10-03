<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Models\User;
use App\Http\Models\Location;
use App\Mail\EmailSG;

class UserController extends Controller
{
    /**
     * Login user.
     *
     * @return Method
     */
    public function login()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && !empty($input['username']) && !empty($input['password']))
            {
                if (Auth::attempt(['email' => $input['username'], 'password' => md5($input['password'])])) 
                {
                    if(Auth::user()->is_active>0)
                        return ['success'=>true,'msg'=>'User logged successfully!'];
                    else
                    {
                        Auth::logout();
                        return ['success'=>false,'msg'=>'Your user login is not active!'];
                    }
                }
                return ['success'=>false,'msg'=>'Your credentials are incorrect!'];
            }
            return ['success'=>false,'msg'=>'Please, enter a valid credentials'];
        } catch (Exception $ex) {
            throw new Exception('Error Production User Login: '.$ex->getMessage());
        }
    }
    /**
     * Logout user.
     *
     * @return Method
     */
    public function logout()
    {
        try {
            if(Auth::check())
                Auth::logout();
            return ['success'=>true,'msg'=>'User logout successfully!'];
        } catch (Exception $ex) {
            throw new Exception('Error Production User Logout: '.$ex->getMessage());
        }
    }
    /**
     * Register user.
     *
     * @return Method
     */
    public function register()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && !empty($input['email']))
            {
                $current = date('Y-m-d H:i:s');
                if(User::where('email',$input['email'])->count())
                    return ['success'=>false,'msg'=>'That email is already in our system.<br>Please, log in.'];
                $user = new User;
                $location = new Location;
                $location->created = $current;
                $location->updated = $current;
                $user->audit_user_id = 2; //website-account
                $user->set_password($input['password']);
                //save location
                $location->address = strip_tags($input['address']);
                $location->city = strip_tags($input['city']);
                $location->state = $input['state'];
                $location->zip = strip_tags($input['zip']);
                $location->country = $input['country'];
                $location->set_lng_lat();
                $location->save();
                //save user
                $user->location()->associate($location);
                $user->user_type_id = 3; //customer
                $user->email = $input['email'];
                $user->first_name = strip_tags($input['first_name']);
                $user->last_name = strip_tags($input['last_name']);
                $user->phone = $input['phone'];
                $user->is_active = 1;
                $user->force_password_reset = 0;
                $user->save();
                //authenticate user
                Auth::login($user);
                //return
                return ['success'=>true,'msg'=>'User created successfully!'];
            }
            return ['success'=>false,'msg'=>'There was an error creating the user.<br>The fields are not fill out correctly.'];
        } catch (Exception $ex) {
            throw new Exception('Error Production User Register: '.$ex->getMessage());
        }
    }
    /**
     * Recover password.
     *
     * @return Method
     */
    public function recover_password()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && !empty($input['email']))
            {
                $current = date('Y-m-d H:i:s');
                $user = User::where('email',$input['email'])->first();
                if(!$user)
                    return ['success'=>false,'msg'=>'That email is not in our system.<br>Please, register an account.'];
                $user->updated = $current;
                $user->set_password();
                $password = $user->slug;
                $user->force_password_reset = 1;
                $user->save();
                //send password by email
                $email = new EmailSG(null, $user->email, 'TicketBat Recover Password');
                $email->category('Custom');
                $email->body('custom',['body'=>'Your new temporary password is: <b>'.$password.'</b>']);
                $email->template('46388c48-5397-440d-8f67-48f82db301f7');
                $response = $email->send();
                //return
                if($response)
                    return ['success'=>true,'msg'=>'The new credentials have been sent to your email!'];
                return ['success'=>false,'msg'=>'There was an error sending the credentials to your email.'];
            }
            return ['success'=>false,'msg'=>'There was an error creating your new password.<br>The field is not fill out correctly.'];
        } catch (Exception $ex) {
            throw new Exception('Error Production User recover_password: '.$ex->getMessage());
        }
    }
    /**
     * Reset password.
     *
     * @return Method
     */
    public function reset_password()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && !empty($input['password']))
            {
                if(Auth::check())
                {
                    $user = User::find(Auth::user()->id);
                    $user->set_password($input['password']);
                    $user->set_slug();
                    $user->force_password_reset = 0;
                    $user->save();
                    Auth::login($user);
                    return ['success'=>true,'msg'=>'Password changed successfully!'];
                }
                return ['success'=>false,'msg'=>'Please, you must log in before change your password'];
            }
            return ['success'=>false,'msg'=>'Please, enter a valid credentials'];
        } catch (Exception $ex) {
            throw new Exception('Error Production User reset_password: '.$ex->getMessage());
        }
    }
       
}
