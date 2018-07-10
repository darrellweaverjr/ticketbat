<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Models\User;
use App\Http\Models\Util;

class AppSecurity 
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $check_login)
    {
        $response = response()->json(['success'=>false, 'msg'=>'Unsecure connection']);
        if($request->headers->has('X-TOKEN'))
        {   
            $x_token = $request->headers->get('X-TOKEN');
            if(!empty($x_token))
            {   
                $x_token = explode('.',$x_token);
                if($x_token[1] == md5($x_token[0].env('APP_KEY')))
                {
                    if($check_login)
                    {
                        $response = response()->json(['success'=>false, 'msg'=>'Bad login connection']);
                        $a_token = $request->headers->get('A-TOKEN');
                        if(!empty($a_token))
                        {
                            $a_token = explode('.',$a_token);
                            $user = User::where('id',$a_token[0])->where('is_active','>',0)->first(['id','email','password']);
                            if($user && $a_token[1] == md5($user->email.$user->password.env('APP_KEY')))
                                $response = $next($request);
                        }
                    }
                    else
                        $response = $next($request);
                }  
            }            
        }  
        return $response->header('Access-Control-Allow-Credentials', 'true')
                        ->header('Access-Control-Expose-Headers', 'Access-Control-*')
                        ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, X-TOKEN, S-TOKEN, A-TOKEN')
                        ->header('Access-Control-Allow-Origin', '*')
                        ->header('X-Frame-Options', 'SAMEORIGIN')
                        ->header('X-Content-Type-Options', 'nosniff')
                        ->header('X-XSS-Protection', '1; mode=block');
    }
}
