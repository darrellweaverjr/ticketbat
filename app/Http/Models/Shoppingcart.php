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
    public static function calculate_session($session_id,$list=false)
    {
        try {
            $price = $qty = $fee = $save = $saveAll = $total = 0;
            $saveAllApplied = false;
            $coupon = $coupon_description = null; 
            $cart = [];
            //get all items
            $items = DB::table('shoppingcart')
                        ->join('show_times', 'show_times.id', '=' ,'shoppingcart.item_id')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->join('tickets', 'tickets.id', '=' ,'shoppingcart.ticket_id')
                        ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                        ->select('shoppingcart.id','shoppingcart.product_type','shoppingcart.cost_per_product','shows.name','show_times.show_time',
                                 'shoppingcart.item_id','packages.title','tickets.percent_commission AS c_percent','tickets.fixed_commission AS c_fixed',
                                 'shoppingcart.number_of_items','shoppingcart.total_cost','shoppingcart.coupon','shoppingcart.ticket_id')
                        ->where('shoppingcart.session_id','=',$session_id)->where('shoppingcart.status','=',0)
                        ->orderBy('shoppingcart.timestamp')->distinct()->get();
            if(count($items))
            {
                //check coupon for discounts
                if($items[0]->coupon)
                {
                    $coupon = json_decode($items[0]->coupon,true);
                    $coupon['ticket_ids'] = explode(',',$coupon['ticket_ids']);
                } 
                //loop for all items to calculate
                foreach ($items as $i)
                {
                    //calculate price and fees
                    $p = $i->cost_per_product * $i->number_of_items;
                    $price += $p;
                    $f = $i->total_cost - $p;
                    $fee += $f;
                    $qty += $i->number_of_items;
                    //calculate discounts for each ticket the the coupon applies
                    $s = 0;
                    if(!empty($coupon) && in_array($i->ticket_id,$coupon['ticket_ids']))
                    {
                        switch($coupon['discount_type'])
                        {
                            case 'Percent':
                                    $s = Util::round($i->total_cost * $coupon['start_num'] / 100);
                                    break;
                            case 'Dollar':
                                    $s = ($coupon['discount_scope']=='Total')? $coupon['start_num'] : $coupon['start_num'] * $i->number_of_items;
                                    break;
                            case 'N for N':
                                    $maxFreeSets = floor($i->number_of_items / $coupon['start_num']);
                                    $free = $total = 0;
                                    while ($maxFreeSets > 0) 
                                    {
                                        $a = 0;
                                        while ($a < $coupon['start_num'] && $total < $i->number_of_items) {
                                            $total++; $a++;
                                        }
                                        $b = 0;
                                        while ($b < $coupon['end_num'] && $total < $i->number_of_items) {
                                            $free++; $total++; $b++;
                                        }
                                        $maxFreeSets--;
                                    }
                                    $s = Util::round($i->total_cost / $i->number_of_items * $free);
                                    break;
                            default:  
                                    break;
                        }
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
                    }
                    //calculate array for purchase when buy
                    if($list) 
                        $cart[$i->id] = ['discount_id'=>($coupon)? $coupon['id'] : 1,'show_time_id'=>$i->item_id,'show_time'=>$i->show_time,
                                     'product_type'=>$i->product_type.' '.$i->title, 'savings'=>Util::round($s),'name'=>$i->name,
                                     'commission_percent'=>($i->c_fixed)? $i->c_fixed : Util::round($i->c_percent*$i->number_of_items*$p/100),
                                     'quantity'=>$i->number_of_items,'retail_price'=>Util::round($p),'processing_fee'=>Util::round($f)];
                }
                //change coupon name after loop
                if(!empty($coupon))
                {
                    $coupon_description = $coupon['description'];
                    $coupon = $coupon['code'];
                }
            }   
            //calculate and return sum of all values of the shoppingcart
            $total = $price + $fee - $save;
            if($total<0)
            {
                $save = $price + $fee;
                $total = 0;
            }
            return ['success'=>true,'coupon'=>$coupon,'coupon_description'=>$coupon_description,'quantity'=>$qty,
                    'retail_price'=>Util::round($price),'processing_fee'=>Util::round($fee),'savings'=>Util::round($save),
                    'total'=>Util::round($total),'items'=>$cart];
           
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
                            ->select('shoppingcart.id','shows.name','shows.restrictions','shoppingcart.product_type','shoppingcart.cost_per_product',
                                     'shoppingcart.total_cost','shoppingcart.coupon')
                            ->where('discounts.code','=',$code)->where('shoppingcart.session_id','=',$session_id)
                            ->whereDate('show_times.show_time','>=','discounts.start_date')->whereDate('show_times.show_time','<=','discounts.end_date')
                            ->where('discounts.coupon_type','=','Normal')
                            ->where(function($query)
                            {
                                $query->whereNull('discounts.effective_start_date')
                                      ->orWhereDate('discounts.effective_start_date','<=', \Carbon\Carbon::today());
                            })
                            ->where(function($query)
                            {
                                $query->whereNull('discounts.effective_end_date')
                                      ->orWhereDate('discounts.effective_end_date','>=', \Carbon\Carbon::today());
                            })
                            ->count();
                if($items)
                {
                    $coupon = DB::table('discounts')
                            ->join('discount_tickets', 'discount_tickets.discount_id', '=' ,'discounts.id')
                            ->join('tickets', 'discount_tickets.ticket_id', '=' ,'tickets.id')
                            ->select(DB::raw('discounts.id, discounts.code, discounts.description, discounts.start_num,
                                              discounts.discount_type, discounts.discount_scope, discounts.end_num,
                                              GROUP_CONCAT(DISTINCT discount_tickets.ticket_id) AS ticket_ids'))
                            ->where('discounts.code',$code)->groupBy('discounts.id')->first();
                    if($coupon)
                    {
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
