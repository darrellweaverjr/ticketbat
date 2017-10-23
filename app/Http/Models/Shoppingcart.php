<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

/**
 * Shoppingcart class
 *
 * @author ivan
 */
class Shoppingcart extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shoppingcart';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the show_time record associated with the shoppingcart.
     */
    public function item()
    {
        return $this->belongsTo('App\Http\Models\ShowTime','item_id');
    }
    /**
     * Get the ticket record associated with the shoppingcart.
     */
    public function ticket()
    {
        return $this->belongsTo('App\Http\Models\Ticket','ticket_id');
    }
    //PERSONALIZED FUNCTIONS
    /**
     * Calculate totals and/or calculate items.
     */
    public static function items_session($session_id)
    {
        $items = DB::table('shoppingcart')
                            ->join('show_times', 'show_times.id', '=' ,'shoppingcart.item_id')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->join('tickets', 'tickets.id', '=' ,'shoppingcart.ticket_id')
                            ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                            ->leftJoin('purchases', 'purchases.ticket_id', '=' ,'tickets.id')
                            ->select(DB::raw('shoppingcart.id, shows.name, IF(shows.restrictions="None","",shows.restrictions) AS restrictions, shoppingcart.ticket_id, shoppingcart.options,
                                              shoppingcart.product_type, shoppingcart.cost_per_product, DATE_FORMAT(show_times.show_time,"%m/%d/%Y %H:%i:%s") AS show_time, shoppingcart.number_of_items, shoppingcart.item_id,
                                              IF(packages.title="None","",packages.title) AS package, shoppingcart.total_cost, tickets.percent_commission AS c_percent, shows.slug, show_times.id AS show_time_id,
                                              (tickets.processing_fee*shoppingcart.number_of_items) AS processing_fee, tickets.fixed_commission AS c_fixed, shoppingcart.coupon, shows.amex_only_ticket_types,
                                              (CASE WHEN (show_times.is_active>0 AND tickets.is_active>0 AND shows.is_active>0) THEN 1 ELSE 0 END) AS available_event, shows.amex_only_start_date,
                                              (CASE WHEN NOW() > (show_times.show_time - INTERVAL shows.cutoff_hours HOUR) THEN 0 ELSE 1 END) AS available_time, shows.amex_only_end_date,
                                              (CASE WHEN (tickets.max_tickets > 0) THEN (tickets.max_tickets - COALESCE(SUM(purchases.quantity),0)) ELSE -1 END) AS available_qty'))
                            ->where('shoppingcart.session_id','=',$session_id)->where('shoppingcart.status','=',0)
                            ->orderBy('shoppingcart.timestamp')->groupBy('shoppingcart.id')->distinct()->get();
        //search for availables items
        foreach ($items as $i)
        {
            $i->unavailable = 0;    //availables by default
            if($i->available_event < 1) //available events
                $i->unavailable = 3;
            else if($i->available_time < 1) //available time to purchase
                $i->unavailable = 2;
            else if($i->available_qty!=-1 && $i->available_qty-$i->number_of_items<0)   //available qty of items to buy
            {
                if($i->available_qty>0)
                {
                    $fee = $i->processing_fee/$i->number_of_items;
                    $i->number_of_items = $i->available_qty;
                    $i->processing_fee = $fee*$i->number_of_items;
                    $i->total_cost = ($i->cost_per_product+$fee)*$i->number_of_items;
                    Shoppingcart::where('id', $i->id)->update(['number_of_items'=>$i->number_of_items,'total_cost'=>$i->total_cost]);
                }
                else
                    $i->unavailable = 1;
            }
            //if there is unavailable item remove it
            if($i->unavailable > 0)
                Shoppingcart::where('id', $i->id)->delete();
        }
        //return
        return $items;
    }
    /**
     * Calculate totals and/or calculate items.
     */
    public static function calculate_session($session_id)
    {
        try {
            $price = $qty = $fee = $save = $saveAll = $total = 0;
            $saveAllApplied = false;
            $coupon = $coupon_description = null; 
            $restrictions = [];
            $amex_only = 0;
            //get all items
            $items = Shoppingcart::items_session($session_id);
            if(count($items))
            {
                //check coupon for discounts
                if($items[0]->coupon)
                    $coupon = json_decode($items[0]->coupon,true);
                //loop for all items to calculate
                foreach ($items as $i)
                {
                    //get amex only for pay
                    if($amex_only!=1)
                    {
                        if(!empty($i->amex_only_start_date) && !empty($i->amex_only_end_date) && !empty($i->amex_only_ticket_types))
                        {
                            $s_d = strtotime($i->amex_only_start_date);
                            $e_d = strtotime($i->amex_only_end_date);
                            $t_t = explode(',',$i->amex_only_ticket_types);
                            if($s_d && $e_d && $s_d<strtotime('now') && $e_d>strtotime('now') && in_array($i->product_type, $t_t))
                                $amex_only = 1;
                        }
                    }
                    //get restrictions
                    if($i->restrictions!='None')
                        $restrictions[$i->name] = $i->restrictions;
                    //calculate totals for availables items only
                    if(!$i->unavailable)
                    {
                        //calculate price and fees
                        $p = $i->cost_per_product * $i->number_of_items;
                        $price += $p;
                        $fee += Util::round($i->processing_fee);
                        $qty += $i->number_of_items;
                        //others
                        $i->discount_id = ($coupon && $coupon['id'])? $coupon['id'] : 1;
                        $i->commission = ($i->c_fixed)? $i->c_fixed : Util::round($i->c_percent*$i->number_of_items*$p/100);
                        $i->retail_price = Util::round($p);  
                        
                        //calculate discounts for each ticket the the coupon applies
                        $s = 0;
                        if(!empty($coupon) && !empty($coupon['tickets']))
                        {
                            foreach ($coupon['tickets'] as $dt)
                            {
                                if($dt['ticket_id'] == $i->ticket_id)
                                {
                                    $couponObj = Discount::find($coupon['id']);
                                    $s = $couponObj->calculate_savings($i->number_of_items,$i->total_cost,$dt['start_num'],$dt['end_num']);
                                    //write savings or suming
                                    if(($coupon['discount_scope']=='Total' && $coupon['discount_type']=='Dollar'))
                                    {
                                        //add total savings if doesnt exist
                                        if($saveAll==0 && !$saveAllApplied)
                                        {
                                            $saveAllApplied = true;
                                            $saveAll = $s;
                                        }
                                        //discount savings for each item from total
                                        if($saveAll>0)
                                        {
                                            if($saveAll > $i->total_cost)
                                                $s = $i->total_cost;
                                            else
                                                $s = $saveAll;
                                            $saveAll -= $s;
                                            $save += $s;
                                        }
                                        else $s = 0;
                                    } 
                                    else
                                        $save += $s;
                                    break;
                                }
                            }
                        }
                        //calculate savings for item
                        $i->savings = Util::round($s);
                    }
                }                
            }   
            //calculate and return sum of all values of the shoppingcart
            $total = $price + $fee - $save;
            if($total<0)
            {
                $save = $price + $fee;
                $total = 0;
            }
            //coupons
            if(!empty($coupon))
            {
                $coupon_description = $coupon['description'];
                $coupon = $coupon['code'];
            }
            return ['success'=>true,'coupon'=>$coupon,'coupon_description'=>$coupon_description,'quantity'=>$qty,
                    'retail_price'=>Util::round($price),'processing_fee'=>Util::round($fee),'savings'=>Util::round($save),
                    'total'=>Util::round($total),'items'=>$items,'restrictions'=>$restrictions,'amex_only'=>$amex_only];
           
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
    
    /**
     * Applies coupon for the shoppingcart.
     */
    public static function apply_coupon($session_id,$code)
    {
        try {
            $current = date('Y-m-d');
            //check if add or remove coupon code
            if(empty($code) || $code=='0000')
            {
                $response = Shoppingcart::where('session_id','=',$session_id)->update(['coupon'=>null]);
                if($response || $response >= 0)
                    return ['success'=>true];
                return ['success'=>false,'msg'=>'There was an error trying to remove the coupon.'];
            } 
            else
            {
                //items to apply
                $items = DB::table('shoppingcart')
                            ->join('discount_tickets', 'discount_tickets.ticket_id', '=' ,'shoppingcart.ticket_id')
                            ->join('discounts', 'discounts.id', '=' ,'discount_tickets.discount_id')
                            ->join('show_times', 'show_times.id', '=' ,'shoppingcart.item_id')
                            ->select('shoppingcart.id')
                            ->where('discounts.code','=',$code)->where('shoppingcart.session_id','=',$session_id)
                            ->whereRaw('DATE(show_times.show_time) BETWEEN DATE(discounts.start_date) AND DATE(discounts.end_date)')
                            ->where('discounts.coupon_type','=','Normal')
                            ->where(function($query) use ($current)
                            {
                                $query->whereNull('discounts.effective_start_date')
                                      ->orWhereDate('discounts.effective_start_date','<=',$current);
                            })
                            ->where(function($query) use ($current)
                            {
                                $query->whereNull('discounts.effective_end_date')
                                      ->orWhereDate('discounts.effective_end_date','>=',$current);
                            })
                            ->count();
                if($items)
                {   
                    $coupon = DB::table('discounts')
                            ->join('discount_tickets', 'discount_tickets.discount_id', '=' ,'discounts.id')
                            ->join('tickets', 'discount_tickets.ticket_id', '=' ,'tickets.id')
                            ->select(DB::raw('discounts.id, discounts.code, discounts.description, discounts.start_num,
                                              discounts.discount_type, discounts.discount_scope, discounts.end_num'))
                            ->where('discounts.code',$code)->groupBy('discounts.id')->first();
                    if($coupon)
                    {
                        $coupon->tickets = DB::table('discount_tickets')
                                ->join('discounts', 'discount_tickets.discount_id', '=' ,'discounts.id')
                                ->select(DB::raw('discount_tickets.ticket_id, 
                                                  COALESCE(discount_tickets.fixed_commission,null) AS fixed_commission,
                                                  COALESCE(discount_tickets.start_num,discounts.start_num) AS start_num, 
                                                  COALESCE(discount_tickets.end_num,discounts.end_num,null) AS end_num'))
                                ->where('discounts.id',$coupon->id)->get();
                        $response = Shoppingcart::where('session_id','=',$session_id)->update(['coupon'=>json_encode($coupon,true)]);
                        if($response || $response >= 0)
                            return ['success'=>true];
                        return ['success'=>false,'msg'=>'There was an error trying to add the coupon.'];
                    }
                    return ['success'=>false, 'msg'=>'That coupon is not valid!'];
                }
                return ['success'=>false, 'msg'=>'That coupon is not valid for your items!'];
            }
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
    /**
     * Get tickets that coupon applies in shoppingcart.
     */
    public static function tickets_coupon($session_id)
    {
        try {
            $tickets = [];
            $coupon = Shoppingcart::where('session_id','=',$session_id)->get(['coupon']);
            foreach ($coupon as $c)
            {
                if(!empty($c->coupon) && Util::isJSON($c->coupon))
                {
                    $coup = json_decode($c->coupon,true);
                    if(!empty($coup['tickets']))
                    {
                        foreach ($coup['tickets'] as $dt)
                            if(!in_array($dt['ticket_id'], $tickets))
                                $tickets[] = $dt['ticket_id'];
                    }
                }
            }
            return $tickets;
        } catch (Exception $ex) {
            return [];
        }
    }
    /**
     * Add items to the shoppingcart.
     */
    public static function add_item($show_time_id,$ticket_id,$qty,$s_token,$seat_id=null)
    {
        try {
            //get pricing first
            if(empty($seat_id))
            {
                $ticket = DB::table('tickets')
                        ->select(DB::raw('id AS ticket_id, retail_price, processing_fee, ticket_type'))
                        ->where('id','=',$ticket_id)->where('is_active','>',0)->first();
            }
            else
            {
                $ticket = DB::table('seats')
                        ->join('tickets', 'seats.ticket_id', '=' ,'tickets.id')
                        ->select(DB::raw('tickets.id AS ticket_id, seats.id AS seat_id, seats.consignment_id, seats.seat, tickets.ticket_type,
                                          COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0)) AS retail_price, 
                                          COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0)) AS processing_fee'))
                        ->where('seats.id','=',$seat_id)->where('seats.status','=','Created')->first();
            }
            if(!$ticket)
                return ['success'=>false, 'msg'=>'That ticket is not longer available!'];
            //get valid showtime
            $show_time = ShowTime::where('id','=',$show_time_id)->where('is_active','>',0)->first();
            if(!$show_time)
                return ['success'=>false, 'msg'=>'That event is not longer available!'];
            //continue if valid
            $item = (!empty($seat_id))? null : Shoppingcart::where('item_id','=',$show_time->id)->where('ticket_id','=',$ticket->ticket_id)->where('session_id','=',$s_token)->first();
            if($item)
            {
                $item->number_of_items += $qty;
                $item->total_cost = round($item->cost_per_product*$item->number_of_items,2, PHP_ROUND_HALF_UP);
                $item->save();
            }
            else
            {
                $i = Shoppingcart::where('session_id','=',$s_token)->first();
                //save item into shoppingcart
                $item = new Shoppingcart;
                $item->item_id = $show_time->id;
                $item->ticket_id = $ticket->ticket_id;
                $item->session_id = $s_token;
                $item->user_id = (Auth::check())? Auth::user()->id : Session::get('guest_email',null);
                $item->number_of_items = $qty;
                $item->cost_per_product = $ticket->retail_price;
                $item->total_cost = Util::round(($item->cost_per_product+$ticket->processing_fee)*$item->number_of_items);
                $item->coupon = ($i && !empty($i->coupon))? $i->coupon : Session::get('coupon',null);
                $item->status = 0;
                $item->timestamp = date('Y-m-d H:i:s');
                if(!empty($seat_id))
                {
                    $item->item_name = $ticket->seat;
                    $item->product_type = $ticket->ticket_type.' - Seat: '.$ticket->seat;
                    $item->options = json_encode(['consignments'=>$ticket->consignment_id,'seats'=>$ticket->seat_id]);
                }
                else
                {
                    $item->item_name = null;
                    $item->product_type = $ticket->ticket_type;
                    $item->options = json_encode([]);
                }
                $item->save();
            }
            //overwrite default values for all items into shoppingcart
            Shoppingcart::where('session_id','=',$s_token)->update(['user_id'=>$item->user_id,'coupon'=>$item->coupon]);
            //return
            return ['success'=>true, 'msg'=>'Tickets added successfully!'];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'The system could not add the tickets!'];
        }
    }
    /**
     * Update qty items to the shoppingcart.
     */
    public static function update_item($shoppingcart_id,$qty,$s_token)
    {
        try {
            //get item to update
            $item = Shoppingcart::where('id','=',$shoppingcart_id)->where('session_id','=',$s_token)->first();
            if($item)
            {
                $item->number_of_items = $qty;
                $item->total_cost = Util::round(($item->cost_per_product+$item->ticket->processing_fee)*$item->number_of_items);
                $item->save();
                return ['success'=>true, 'msg'=>'Tickets updated successfully!'];
            }
            return ['success'=>false, 'msg'=>'That ticket is not longer available!'];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'The system could not update the tickets!'];
        }
    }
    /**
     * Remove items to the shoppingcart.
     */
    public static function remove_item($shoppingcart_id,$s_token)
    {
        try {
            Shoppingcart::where('id','=',$shoppingcart_id)->where('session_id','=',$s_token)->delete();
            return ['success'=>true, 'msg'=>'Tickets removed successfully!'];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'The system could not remove the tickets!'];
        }
    }
    /**
     * Get qty items to the shoppingcart.
     */
    public static function qty_items($s_token=null)
    {
        if(empty($s_token))
            $s_token = Util::s_token (false, true);
        $items = Shoppingcart::where('session_id','=',$s_token)->get();
        foreach ($items as $i)
        {
            if(!empty($i->options) && Util::isJSON($i->options))
            {
                $option = json_decode($i->options,true);
                if(!empty($option['consignments']))
                {
                    $consignment = Consignment::find($option['consignments']);
                    if(!($consignment && Auth::check() && Auth::user()->id==$consignment->seller_id))
                        Shoppingcart::where('id','=',$i->id)->delete();
                }
            }
        }
        return Shoppingcart::where('session_id','=',$s_token)->count();
    }
}
