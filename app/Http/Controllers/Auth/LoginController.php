<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        //change input name from username to email
        $email = $request->input('username');
        $request->request->add(['email' => $email]);
        //$request->request->remove('username');
        $this->validateLogin($request);
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        $credentials = $this->credentials($request);
        $credentials['password'] = md5($credentials['password']);
        if ($this->guard()->attempt($credentials, $request->has('remember'))) {
            //check if the user is active and it has permission to enter to the site
            if(Auth::user()->is_active > 0 && in_array(Auth::user()->user_type->id,explode(',',env('ADMIN_LOGIN_USER_TYPE')))){
                return $this->sendLoginResponse($request);
            } else
                Auth::logout();            
        } 
        $this->incrementLoginAttempts($request);
        $this->sendFailedLoginResponse($request);
        return back();
    }
    /**
     * Log the user out of the application.
     *
     * @return redirect
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }
}
