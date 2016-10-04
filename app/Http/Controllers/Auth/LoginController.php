<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
    /*
    private function attempt($credentials)
    {
        if ( ! isset( $credentials['password'] ) or ! isset( $credentials['email'] )) {
            return false;
        }

        $user = User::whereEmail($credentials['email'])
                    ->wherePassword(md5($credentials['password']))
                    ->first();

        if ($user) {
            Auth::login($user);
        }

        return $user;
    }*/
}
