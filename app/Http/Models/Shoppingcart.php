<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
                            ->select(DB::raw('shoppingcart.id, shows.name, IF(shows.restrictions="None","",shows.restrictions) AS restrictions, shoppingcart.ticket_id,
                                              shoppingcart.product_type, shoppingcart.cost_per_product, show_times.show_time, shoppingcart.number_of_items, shoppingcart.item_id,
                                              IF(packages.title="None","",packages.title) AS package, shoppingcart.total_cost, tickets.percent_commission AS c_percent,
                                              (tickets.processing_fee*shoppingcart.number_of_items) AS processing_fee, tickets.fixed_commission AS c_fixed, shoppingcart.coupon,
                                              (CASE WHEN (show_times.is_active>0 AND tickets.is_active>0 AND shows.is_active>0) THEN 1 ELSE 0 END) AS available_event,
                                              (CASE WHEN NOW() > (show_times.show_time - INTERVAL shows.cutoff_hours HOUR) THEN 0 ELSE 1 END) AS available_time, 
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
                    Shoppingcart::where('id','=',$i->id)->update(['number_of_items'=>$i->number_of_items,'total_cost'=>$i->total_cost]);
                }
                else
                    $i->unavailable = 1;
            }
            //if there is unavailable item remove it
            if($i->unavailable > 0)
                Shoppingcart::where('id','=',$i->id)->delete();
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
                    'total'=>Util::round($total),'items'=>$items];
           
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
                $response = DB::table('shoppingcart')->where('session_id','=',$session_id)->update(['coupon'=>null]);
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
                        $response = DB::table('shoppingcart')->where('session_id','=',$session_id)->update(['coupon'=>json_encode($coupon,true)]);
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
}
