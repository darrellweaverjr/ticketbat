<?php

namespace App\Http\Middleware;

use Closure;

class Permissions 
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $code
     * @return mixed
     */
    public function handle($request, Closure $next, $code)
    {
        if(!$request->user() || !array_key_exists($code,$request->user()->user_type->getACLs()) || !isset($request->user()->user_type->getACLs()[$code]))
            \Illuminate\Support\Facades\Redirect::to(route('logout'))->send();
        return $next($request);
    }
}
