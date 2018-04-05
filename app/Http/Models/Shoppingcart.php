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
                            ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                            ->join('tickets', 'tickets.id', '=' ,'shoppingcart.ticket_id')
                            ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                            ->leftJoin('purchases', function ($join) {
                                $join->on('purchases.ticket_id', '=', 'tickets.id')
                                     ->where('purchases.show_time_id', '=', 'show_times.id');
                            })
                            ->select(DB::raw('shoppingcart.id, shows.name, IF(shows.restrictions="None","",shows.restrictions) AS restrictions, shoppingcart.ticket_id, shoppingcart.options, shows.printed_tickets,
                                              shoppingcart.product_type, shoppingcart.cost_per_product, DATE_FORMAT(show_times.show_time,"%m/%d/%Y %H:%i:%s") AS show_time, shoppingcart.number_of_items, shoppingcart.item_id,
                                              IF(packages.title="None","",packages.title) AS package, shoppingcart.total_cost, tickets.percent_commission AS c_percent, shows.slug, show_times.id AS show_time_id,
                                              tickets.processing_fee AS processing_fee, COALESCE(shows.pos_fee,venues.pos_fee) AS pos_fee, tickets.fixed_commission AS c_fixed, shoppingcart.coupon, shows.amex_only_ticket_types,
                                              (CASE WHEN (show_times.is_active>0 AND tickets.is_active>0 AND shows.is_active>0) THEN 1 ELSE 0 END) AS available_event, shows.amex_only_start_date, shows.id AS show_id,
                                              (CASE WHEN NOW() > (show_times.show_time - INTERVAL shows.cutoff_hours HOUR) THEN 0 ELSE 1 END) AS available_time, shows.amex_only_end_date, shows.venue_id, tickets.inclusive_fee,
                                              (CASE WHEN (tickets.max_tickets > 0) THEN (tickets.max_tickets - COALESCE(SUM(purchases.quantity),0)) ELSE -1 END) AS available_qty, shows.ticket_limit, tickets.max_tickets,
                                              venues.disable_cash_breakdown'))
                            ->where('shoppingcart.session_id','=',$session_id)->where('shoppingcart.status','=',0)
                            ->orderBy('shoppingcart.timestamp')->groupBy('shoppingcart.id')->distinct()->get();
        //limit tickets by show
        $ticket_limit = [];
        //search for availables items
        foreach ($items as $key=>$i)
        {
            //POS system
            if(Auth::check() && in_array(Auth::user()->user_type_id,explode(',',env('SELLER_OPTION_USER_TYPE'))) && !empty($i->pos_fee))
                $i->processing_fee = $i->pos_fee;
            //recalculate availables tickets
            if($i->max_tickets>0 && $i->available_qty<-1)
                $i->available_qty = 0;
            //checkings for qty if ticket limit by customer
            if(!empty($i->ticket_limit) && !empty($i->available_qty))
            {
                //set up available qty by ticket limit
                if(!isset($ticket_limit[$i->item_id]))
                {
                    $ticket_limit[$i->item_id] = ['c'=>$i->number_of_items, 'p'=>0];
                    $email_guest = Session::get('email_guest', null);
                    $user_id = null;
                    if(Auth::check())
                        $user_id = Auth::user()->id;
                    else if(!empty($email_guest))
                    {
                        $user = User::where('email',$email_guest)->first(['id']);
                        if($user)
                            $user_id = $user->id;
                    }
                    //get previous purchases by user
                    if(!empty($user_id))
                    {
                        $purchases = DB::table('purchases')
                                    ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                                    ->select(DB::raw('SUM(purchases.quantity) AS tickets'))
                                    ->where('show_times.id',$i->item_id)->where('purchases.user_id','=', $user_id)
                                    ->groupBy('purchases.user_id')->first();
                        if(($purchases && !empty($purchases->tickets)))
                            $ticket_limit[$i->item_id]['p'] = $purchases->tickets;
                    }
                }
                //calc
                $max_available = ($i->available_qty!=-1 && $i->available_qty<$i->ticket_limit)? $i->available_qty : $i->ticket_limit;
                $qty_current = $ticket_limit[$i->item_id]['c'] + $ticket_limit[$i->item_id]['p'];
                //if no available for show
                if($qty_current > $max_available)
                    $i->available_qty = 0;
                else
                    $i->available_qty = $max_available;
            }
            //continue checking availables
            $i->unavailable = 0;    //availables by default
            if($i->available_event < 1 || $i->available_time < 1 || $i->available_qty==0) //available events and time
                $i->unavailable = 1;
            else if($i->available_qty!=-1 && $i->available_qty-$i->number_of_items<0)   //available qty of items to buy
            {
                if($i->available_qty>0 && $i->number_of_items>0)
                {
                    $qty_item_pay = $i->number_of_items;
                    $coupon = json_decode($i->coupon,true);
                    if(!empty($coupon) && !empty($coupon->id))
                    {
                        $couponObj = Discount::find($coupon->id);
                        if($couponObj)
                            $qty_item_pay -= $couponObj->free_tickets($i->number_of_items);
                    }
                    $i->total_cost = $i->cost_per_product*$i->number_of_items;
                    if($i->inclusive_fee>0)
                        $i->total_cost += $i->processing_fee*$qty_item_pay;
                    Shoppingcart::where('id', $i->id)->update(['number_of_items'=>$i->number_of_items,'total_cost'=>$i->total_cost]);
                }
                else
                    $i->unavailable = 1;
            }
            //if there is unavailable item remove it
            if($i->unavailable > 0 || $i->number_of_items < 1)
            {
                Shoppingcart::where('id', $i->id)->delete();
                $items->forget($key);
            }
            else
            {
                //get seats for consignments
                if(!empty($i->options) && Util::isJSON($i->options))
                {
                    $opt = json_decode($i->options,true);
                    if(!empty($opt['consignments']) && !empty($opt['seats']))
                    {
                        $i->consignment = $opt['consignments'];
                        $i->seat = $opt['seats'];
                    }
                }
            }
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
            $saveAllApplied = $disable_cash_breakdown = false;
            $coupon = $coupon_description = null;
            $restrictions = $banners = [];
            $amex_only = 0;
            $printed_tickets = ['details'=>0,'shows'=>[],'select'=>0];
            //get all items
            $items = Shoppingcart::items_session($session_id);
            if(count($items))
            {
                //check coupon for discounts
                if(!empty($items[0]) && !empty($items[0]->coupon))
                    $coupon = json_decode($items[0]->coupon,true);
                //loop for all items to calculate
                foreach ($items as $i)
                {
                    //pre-conditions cash fill out
                    if(!$disable_cash_breakdown && $i->disable_cash_breakdown>0)
                        $disable_cash_breakdown = true;
                    //printed tickets
                    if($i->printed_tickets>0)
                        $printed_tickets['shows'][] = $i->name;
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
                        $restrictions[$i->name] = preg_replace('/[^0-9]/','',$i->restrictions);
                    //get banners
                    $banner = DB::table('banners')
                                ->select(DB::raw('banners.id, banners.url, banners.file'))
                                ->where(function($query) use ($i) {
                                    $query->whereRaw('banners.parent_id = '.$i->show_id.' AND banners.belongto="show" ')
                                          ->orWhereRaw('banners.parent_id = '.$i->venue_id.' AND banners.belongto="venue" ');
                                })
                                ->where('banners.type','like','%Cart Page%')->get()->toArray();
                    foreach ($banner as $b)
                        $b->file = Image::view_image($b->file);
                    $banners = array_merge($banners,$banner);
                    //calculate totals for availables items only
                    if(!$i->unavailable)
                    {
                        //calculate price and fees
                        $p = $i->cost_per_product * $i->number_of_items;
                        $price += $p;
                        $qty += $i->number_of_items;
                        $i->discount_id = 1;
                        $i->retail_price = Util::round($p);
                        //calculate discounts for each ticket the the coupon applies
                        $s = $free = 0;
                        if(!empty($coupon) && !empty($coupon['tickets']))
                        {
                            //if valid showtimes for coupon
                            if(empty($coupon['showtimes']) || (!empty($coupon['showtimes']) && in_array($i->item_id, explode(',', $coupon['showtimes']))) )
                            {
                                //loop tickets
                                foreach ($coupon['tickets'] as $dt)
                                {
                                    if($dt['ticket_id'] == $i->ticket_id)
                                    {
                                        $couponObj = Discount::find($coupon['id']);
                                        $free = $couponObj->free_tickets($i->number_of_items,$dt['start_num'],$dt['end_num']);
                                        if($coupon['discount_type']=='N for N' && $free>0)
                                            $i->total_cost = $i->cost_per_product * $i->number_of_items;
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
                                        //set up coupon id for this item
                                        $i->discount_id = $coupon['id'];
                                        break;
                                    }
                                }
                            }
                        }
                        $qty_item_pay = $i->number_of_items-$free;
                        //calculate savings for item
                        $i->savings = Util::round($s);
                        //calculate fee for item/total
                        $i->processing_fee= Util::round($i->processing_fee*$qty_item_pay);
                        $fee += (!($i->inclusive_fee>0))? Util::round($i->processing_fee) : 0;
                        //calculate commission for item
                        $i->commission = ($i->c_fixed)? Util::round($i->c_fixed*$qty_item_pay) : Util::round($i->c_percent*$i->cost_per_product/100*$qty_item_pay);
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
            //printed tickets
            $printed_tickets['select'] = Session::get('printed_tickets',0);
            $total += $printed_tickets['select'];
            $printed_tickets['details'] = count($items)-count($printed_tickets['shows']);
            $seller = (Auth::check() && in_array(Auth::user()->user_type_id,explode(',',env('SELLER_OPTION_USER_TYPE'))))? 1 : 0;
            //return
            return ['success'=>true,'coupon'=>$coupon,'coupon_description'=>$coupon_description,'quantity'=>$qty,'seller'=>$seller,'banners'=>$banners,
                    'retail_price'=>Util::round($price),'processing_fee'=>Util::round($fee),'savings'=>Util::round($save),'printed'=>$printed_tickets['select'],
                    'total'=>Util::round($total),'items'=>$items,'restrictions'=>$restrictions,'amex_only'=>$amex_only,'printed_tickets'=>$printed_tickets,
                    'cash_breakdown'=>(!empty($cash_breakdown))? 1 : 0];

        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }

    /**
     * Applies coupon for the shoppingcart.
     */
    public static function apply_coupon($session_id,$code,$force=null)
    {
        try {
            $current = date('Y-m-d');
            //check if add or remove coupon code
            if(empty($code) || $code=='0000')
            {
                $response = Shoppingcart::where('session_id','=',$session_id)->update(['coupon'=>null]);
                if($response || $response >= 0)
                {
                    Session::forget('coup');
                    return ['success'=>true, 'msg'=>'Coupon removed successfully!'];
                }
                return ['success'=>false,'msg'=>'There was an error trying to remove the coupon.'];
            }
            else
            {
                //get coupon
                $coupon = Discount::get_coupon($code);
                //forced to add a coupon
                if(!empty($force))
                {
                    //if valid coupon
                    if($coupon)
                    {
                        //check if coupon is for admin only
                        if($coupon->coupon_type=='Admin' && (!Auth::check() || Auth::user()->user_type_id!=1))
                        {
                            Session::forget('coup');
                            Shoppingcart::where('session_id','=',$session_id)->update(['coupon'=>null]);
                            return ['success'=>false];
                        }
                        //continue loading coupon
                        $coupon->tickets = DB::table('discount_tickets')
                                ->join('discounts', 'discount_tickets.discount_id', '=' ,'discounts.id')
                                ->select(DB::raw('discount_tickets.ticket_id,
                                                  COALESCE(discount_tickets.fixed_commission,null) AS fixed_commission,
                                                  COALESCE(discount_tickets.start_num,discounts.start_num) AS start_num,
                                                  COALESCE(discount_tickets.end_num,discounts.end_num,null) AS end_num'))
                                ->where('discounts.id',$coupon->id)->get();
                        //object value into session
                        $coup = json_encode($coupon,true);
                        Session::put('coup', $coup);
                        //added to shoppingcart if there is values
                        $items = DB::table('shoppingcart')
                                ->join('discount_tickets', 'discount_tickets.ticket_id', '=' ,'shoppingcart.ticket_id')
                                ->join('discounts', 'discounts.id', '=' ,'discount_tickets.discount_id')
                                ->join('show_times', 'show_times.id', '=' ,'shoppingcart.item_id')
                                ->select('shoppingcart.id')
                                ->where('discounts.code','=',$code)->where('shoppingcart.session_id','=',$session_id)
                                ->whereRaw('DATE(show_times.show_time) BETWEEN DATE(discounts.start_date) AND DATE(discounts.end_date)')
                                ->whereIn('discounts.coupon_type', ['Normal','Admin'])
                                ->where(function($query) use ($current)
                                {
                                    $query->whereNull('discounts.effective_start_date')
                                          ->orWhereDate('discounts.effective_start_date','<=',$current);
                                })
                                ->where(function($query) use ($current)
                                {
                                    $query->whereNull('discounts.effective_end_date')
                                          ->orWhereDate('discounts.effective_end_date','>=',$current);
                                });
                        if(!empty($coupon->showtimes))
                            $items->whereIn('shoppingcart.item_id', explode(',', $coupon->showtimes));
                        if($items->count())
                            Shoppingcart::where('session_id','=',$session_id)->update(['coupon'=>$coup]);
                        return ['success'=>true];
                    }
                    else
                    {
                        Session::forget('coup');
                        Shoppingcart::where('session_id','=',$session_id)->update(['coupon'=>null]);
                        return ['success'=>false];
                    }
                }
                else
                {
                    if($coupon)
                    {
                        //check if coupon is for admin only
                        if($coupon->coupon_type=='Admin' && (!Auth::check() || Auth::user()->user_type_id!=1))
                            return ['success'=>false, 'msg'=>'You are now allowed to use this coupon.'];
                        //check if valid for shoppingcart and showtimes
                        $items = DB::table('shoppingcart')
                                ->join('discount_tickets', 'discount_tickets.ticket_id', '=' ,'shoppingcart.ticket_id')
                                ->join('discounts', 'discounts.id', '=' ,'discount_tickets.discount_id')
                                ->join('show_times', 'show_times.id', '=' ,'shoppingcart.item_id')
                                ->join('shows', 'show_times.show_id', '=' ,'shows.id')
                                ->select('shoppingcart.id','shows.name','shoppingcart.product_type')
                                ->where('discounts.code','=',$code)->where('shoppingcart.session_id','=',$session_id)
                                ->whereRaw('DATE(show_times.show_time) BETWEEN DATE(discounts.start_date) AND DATE(discounts.end_date)')
                                ->whereIn('discounts.coupon_type', ['Normal','Admin'])
                                ->where(function($query) use ($current)
                                {
                                    $query->whereNull('discounts.effective_start_date')
                                          ->orWhereDate('discounts.effective_start_date','<=',$current);
                                })
                                ->where(function($query) use ($current)
                                {
                                    $query->whereNull('discounts.effective_end_date')
                                          ->orWhereDate('discounts.effective_end_date','>=',$current);
                                });
                        if(!empty($coupon->showtimes))
                            $items->whereIn('shoppingcart.item_id', explode(',', $coupon->showtimes));
                        if(!count($items->get()))
                            return ['success'=>false, 'msg'=>'That coupon is not valid for your items!'];
                        //continue loading coupon
                        $coupon->tickets = DB::table('discount_tickets')
                                ->join('discounts', 'discount_tickets.discount_id', '=' ,'discounts.id')
                                ->select(DB::raw('discount_tickets.ticket_id,
                                                  COALESCE(discount_tickets.fixed_commission,null) AS fixed_commission,
                                                  COALESCE(discount_tickets.start_num,discounts.start_num) AS start_num,
                                                  COALESCE(discount_tickets.end_num,discounts.end_num,null) AS end_num'))
                                ->where('discounts.id',$coupon->id)->get();
                        $couponx = json_encode($coupon,true);
                        $response = Shoppingcart::where('session_id','=',$session_id)->update(['coupon'=>$couponx]);
                        if($response || $response >= 0)
                        {
                            $coup = Session::get('coup', null);
                            if(!empty($coup))
                            {
                                if(json_decode($coup, true)['code'] != $coupon->code)
                                    Session::forget('coup');
                            }
                            return ['success'=>true, 'msg'=> Discount::find($coupon->id)->full_description($items->get()) ];
                        }
                        return ['success'=>false, 'msg'=>'There was an error trying to add the coupon.'];
                    }
                    return ['success'=>false, 'msg'=>'That coupon is not valid!'];
                }
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
            $coupon = Shoppingcart::where('session_id','=',$session_id)->get(['item_id','coupon']);
            foreach ($coupon as $c)
            {
                if(!empty($c->coupon) && Util::isJSON($c->coupon))
                {
                    $coup = json_decode($c->coupon,true);
                    if(empty($coup['showtimes']) || (!empty($coup['showtimes']) && in_array($c->item_id, explode(',', $coup['showtimes']))) )
                    {
                        if(!empty($coup['tickets']))
                        {
                            foreach ($coup['tickets'] as $dt)
                                if(!in_array($dt['ticket_id'], $tickets))
                                    $tickets[] = $dt['ticket_id'];
                        }
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
                        ->select(DB::raw('id AS ticket_id, retail_price, processing_fee, ticket_type, inclusive_fee'))
                        ->where('id','=',$ticket_id)->where('is_active','>',0)->first();
            }
            else
            {
                $ticket = DB::table('seats')
                        ->join('tickets', 'seats.ticket_id', '=' ,'tickets.id')
                        ->select(DB::raw('tickets.id AS ticket_id, seats.id AS seat_id, seats.consignment_id, seats.seat, tickets.ticket_type, inclusive_fee,
                                          COALESCE(seats.retail_price,tickets.retail_price,0) AS retail_price,
                                          COALESCE(seats.processing_fee,tickets.processing_fee,0) AS processing_fee'))
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
            $i = Shoppingcart::where('session_id','=',$s_token)->first();
            if($item)
            {
                $item->number_of_items += $qty;
                $item->save();
            }
            else
            {
                //save item into shoppingcart
                $item = new Shoppingcart;
                $item->item_id = $show_time->id;
                $item->ticket_id = $ticket->ticket_id;
                $item->session_id = $s_token;
                $item->user_id = (Auth::check())? Auth::user()->id : Session::get('guest_email',null);
                $item->number_of_items = $qty;
                $item->cost_per_product = $ticket->retail_price;
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

            }
            $item->coupon = ($i && !empty($i->coupon))? $i->coupon : Session::get('coup',null);
            $qty_item_pay = $item->number_of_items;
            $coupon = json_decode($item->coupon,true);
            if(!empty($coupon) && !empty($coupon->id))
            {
                $couponObj = Discount::find($coupon->id);
                if($couponObj)
                    $qty_item_pay -= $couponObj->free_tickets($i->number_of_items);
            }
            $item->total_cost = Util::round($item->cost_per_product*$item->number_of_items);
            if(!($ticket->inclusive_fee>0))
                $item->total_cost += Util::round($ticket->processing_fee*$qty_item_pay);
            $item->save();
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
                //check if the share tickets has more than new value
                $new_gifts = [];
                if($item->number_of_items > $qty && !empty($item->gifts) && Util::isJSON($item->gifts))
                {
                    $new_qty = $qty;
                    $gifts = json_decode($item->gifts,true);
                    foreach ($gifts as $g)
                    {
                        if($new_qty>0)
                        {
                            if($g['qty']<=$new_qty)
                            {
                                $new_gifts[] = $g;
                                $new_qty -= $g['qty'];
                            }
                            else
                            {
                                $g['qty']=$new_qty;
                                $new_gifts[] = $g;
                                $new_qty=0;
                            }
                        }
                    }
                }
                //asign new qty
                $item->gifts = (count($new_gifts))? json_encode($new_gifts,true) : null;
                $item->number_of_items = $qty;
                $qty_item_pay = $item->number_of_items;
                $coupon = json_decode($item->coupon,true);
                if(!empty($coupon) && !empty($coupon->id))
                {
                    $couponObj = Discount::find($coupon->id);
                    if($couponObj)
                        $qty_item_pay -= $couponObj->free_tickets($item->number_of_items);
                }
                $item->total_cost = Util::round($item->cost_per_product*$item->number_of_items);
                if(!(Ticket::find($item->ticket_id)->inclusive_fee > 0))
                    $item->total_cost += Util::round($item->ticket->processing_fee*$qty_item_pay);
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
