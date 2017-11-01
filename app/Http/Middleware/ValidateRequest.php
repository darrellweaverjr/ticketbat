<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use App\Http\Models\Shoppingcart;

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
            Shoppingcart::apply_coupon(null, $request->coup, true);
        return $next($request);
    }
}
