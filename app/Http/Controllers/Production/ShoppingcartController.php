<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Http\Models\Shoppingcart;
use App\Http\Models\Util;

class ShoppingcartController extends Controller
{
    /**
     * Watch viewcart view shoppingcart.
     *
     * @return Method
     */
    public function index()
    {
        return ['success'=>true,'qty_items'=> Shoppingcart::qty_items()];
    }
    
    /**
     * Count qty of items in the shoppingcart.
     *
     * @return Method
     */
    public function count()
    {
        return ['success'=>true,'qty_items'=> Shoppingcart::qty_items()];
    }
    /**
     * Countdown  shoppingcart.
     *
     * @return Method
     */
    public function countdown()
    {
        //init
        Session::forget('countdown');
        $init = '20:00';
        $input = Input::all();
        if(isset($input['status']))
        {
            switch ($input['status'])
            {
                case 1:
                    Session::put('countdown', $init);
                    return ['success'=>true,'init'=>$init];
                    break;
                case 0:
                    $countdown = (Session::get('countdown',null))? Session::get('countdown') : $init;
                    if($countdown!='00:00')
                    {
                        if(strtotime('2017-01-01 12:'.$countdown) != false)
                            $countdown = date('i:s',strtotime('2017-01-01 12:'.$countdown.' -1 second'));
                        else
                            $countdown = $init;
                        Session::put('countdown', $countdown);
                    }
                    break;
                case -1:
                    Session::forget('countdown');
                    break;
                case -2:
                    Session::forget('countdown');
                    $s_token = Util::s_token(false, true);
                    Shoppingcart::where('session_id',$s_token)->delete();
                    break;
                default:
                    Session::forget('countdown');
                    break;
            }
        }
        return ['success'=>true];
    }
      
    
    
}
