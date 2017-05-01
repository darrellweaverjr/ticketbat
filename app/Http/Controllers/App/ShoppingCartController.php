<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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
                //get pricing first
                $ticket = DB::table('tickets')
                            ->select('id','retail_price','processing_fee','ticket_type','max_tickets')
                            ->where('id','=',$info['ticket_id'])->where('is_active','>',0)->first();
                if($ticket)
                {
                    $item = Shoppingcart::where('item_id','=',$info['show_time_id'])->where('ticket_id','=',$ticket->id)->where('session_id','=',$info['s_token'])->first();
                    if($item)
                    {
                        $item->number_of_items += $info['qty'];
                        $item->total_cost = round($item->cost_per_product*$item->number_of_items,2, PHP_ROUND_HALF_UP);
                        $item->save();
                    }
                    else
                    {
                        $i = Shoppingcart::where('session_id','=',$info['s_token'])->first();
                        $item = new Shoppingcart;
                        $item->item_id = $info['show_time_id'];
                        $item->ticket_id = $ticket->id;
                        $item->session_id = $info['s_token'];
                        $item->number_of_items = $info['qty'];
                        $item->product_type = $ticket->ticket_type;
                        $item->cost_per_product = $ticket->retail_price;
                        $item->total_cost = Util::round(($item->cost_per_product+$ticket->processing_fee)*$item->number_of_items);
                        $item->coupon = ($i)? $i->coupon : null;
                        $item->status = 0;
                        $item->options = json_encode([]);
                        $item->timestamp = date('Y-m-d h:i:s');
                        $item->save();
                    }
                    return Util::json(['success'=>true]);
                }
                return Util::json(['success'=>false, 'msg'=>'That ticket is not longer available!']);
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
                //get item to update
                $item = Shoppingcart::where('id','=',$info['shoppingcart_id'])->where('session_id','=',$info['s_token'])->first();
                if($item)
                {
                    $item->number_of_items = $info['qty'];
                    $item->total_cost = Util::round(($item->cost_per_product+$item->ticket->processing_fee)*$item->number_of_items);
                    $item->save();
                    $totals = Shoppingcart::calculate_session($info['s_token']);
                    if($totals['success'])
                        return Util::json(['success'=>true,'totals'=>$totals]);
                    return Util::json($totals); 
                }
                return Util::json(['success'=>false, 'msg'=>'That ticket is not longer available!']);
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
                Shoppingcart::where('id','=',$info['shoppingcart_id'])->where('session_id','=',$info['s_token'])->delete();
                $totals = Shoppingcart::calculate_session($info['s_token']);
                if($totals['success'])
                    return Util::json(['success'=>true,'totals'=>$totals]);
                return Util::json($totals); 
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
                $coupon = Shoppingcart::apply_coupon($info['s_token'], $info['code']);
                if($coupon['success'])
                {
                    $totals = Shoppingcart::calculate_session($info['s_token']);
                    if($totals['success'])
                    {
                        $coupon['totals'] = $totals;
                        return Util::json($coupon);
                    }
                    return Util::json($totals); 
                }
                return Util::json($coupon); 
            }
            return Util::json(['success'=>false, 'msg'=>'You must fill out correctly the form!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }   
    
}
