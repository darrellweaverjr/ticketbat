<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use App\Http\Models\Shoppingcart;
use App\Http\Models\Util;

class ValidateRequest
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $code
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //checking if coupon into session
        if(!empty($request->coup))
        {
            $session_id = Util::s_token(false,true);
            Shoppingcart::apply_coupon($session_id, $request->coup, true);
        }  
        return $next($request);
    }
}
