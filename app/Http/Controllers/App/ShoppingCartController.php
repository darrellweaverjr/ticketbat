<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Http\Models\Shoppingcart;
use App\Http\Models\Util;

/**
 * Manage buy tickets for the app
 *
 * @author ivan
 */
class ShoppingCartController extends Controller{
    
    /*
     * get all items in the cart
     */
    public function get()
    {
        try {
            $info = Input::all();
            if(!empty($info['s_token']))
            {
                //get all items
                return Util::json(['success'=>true,'totals'=>Shoppingcart::calculate_session($info['s_token'])]);
            }
            return Util::json(['success'=>false, 'msg'=>'You must fill out correctly the form!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }   
    
    /*
     * add items to the cart
     */
    public function add()
    {
        try {
            $info = Input::all();
            if(!empty($info['show_time_id']) && !empty($info['ticket_id']) && !empty($info['qty']) && !empty($info['s_token']))
            {
                $success = Shoppingcart::add($info['show_time_id'], $info['ticket_id'], $info['qty'], $info['s_token']);
                return Util::json($success);
            }
            return Util::json(['success'=>false, 'msg'=>'You must fill out correctly the form!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }  
    
    /*
     * update qty of items of the chart
     */
    public function update()
    {
        try {
            $info = Input::all();
            if(!empty($info['shoppingcart_id']) && !empty($info['qty']) && !empty($info['s_token']))
            {
                $success = Shoppingcart::update($info['shoppingcart_id'], $info['qty'], $info['s_token']);
                if($success['success'])
                {
                    $totals = Shoppingcart::calculate_session($info['s_token']);
                    if($totals['success'])
                        return Util::json(['success'=>true,'totals'=>$totals]);
                    return Util::json($totals); 
                }
                return Util::json($success);
            }
            return Util::json(['success'=>false, 'msg'=>'You must fill out correctly the form!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }  
    
    /*
     * remove items to the cart
     */
    public function remove()
    {
        try {
            $info = Input::all();
            if(!empty($info['shoppingcart_id']) && !empty($info['s_token']))
            {
                //find and remove item
                $success = Shoppingcart::remove($info['shoppingcart_id'], $info['s_token']);
                if($success['success'])
                {
                    $totals = Shoppingcart::calculate_session($info['s_token']);
                    if($totals['success'])
                        return Util::json(['success'=>true,'totals'=>$totals]);
                    return Util::json($totals); 
                }
                return Util::json($success);
            }
            return Util::json(['success'=>false, 'msg'=>'You must fill out correctly the form!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }   
    
    /*
     * apply coupon to the cart
     */
    public function coupon()
    {
        try {
            $info = Input::all();
            if(isset($info['code']) && !empty($info['s_token']))
            {
                $success = Shoppingcart::apply_coupon($info['s_token'], $info['code']);
                if($success['success'])
                {
                    $totals = Shoppingcart::calculate_session($info['s_token']);
                    if($totals['success'])
                    {
                        $success['totals'] = $totals;
                        return Util::json($success);
                    }
                    return Util::json($totals); 
                }
                return Util::json($success); 
            }
            return Util::json(['success'=>false, 'msg'=>'You must fill out correctly the form!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }   
    
}
