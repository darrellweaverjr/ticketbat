<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use App\Http\Models\Image;
use App\Http\Models\Discount;
use App\Http\Models\Shoppingcart;

/**
 * Manage buy tickets for the app
 *
 * @author ivan
 */
class BuyController extends Controller{
    
    /*
     * get all items in the cart
     */
    public function get($raw=null)
    {
        try {
            $info = Input::all();
            if($raw) $info['s_token'] = $raw;
            if(!empty($info['s_token']))
            {
                //get all items
                $items = DB::table('shoppingcart')
                            ->join('show_times', 'show_times.id', '=' ,'shoppingcart.item_id')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->join('tickets', 'tickets.id', '=' ,'shoppingcart.ticket_id')
                            ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                            ->select('shoppingcart.id','shows.name','shows.restrictions','shoppingcart.product_type','shoppingcart.cost_per_product',
                                     'shoppingcart.number_of_items','shoppingcart.total_cost','shoppingcart.coupon','shoppingcart.ticket_id')
                            ->where('shoppingcart.session_id','=',$info['s_token'])->where('shoppingcart.status','=',0)
                            ->orderBy('shoppingcart.timestamp')->distinct()->get();
                if($raw) return $items;
                return Util::json(['success'=>true,'items'=>$items,'totals'=>$this->totals($info['s_token'])]);
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
                    $item->total_cost = Util::round($item->cost_per_product*$item->number_of_items);
                    $item->save();
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
                return Util::json(['success'=>true,'totals'=>$this->totals($info['s_token'])]);
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
                //check if add or remove coupon code
                if(empty($info['code']))
                {
                    Shoppingcart::where('session_id','=',$info['s_token'])->update(['coupon'=>null]);
                    return Util::json(['success'=>true,'totals'=>$this->totals($info['s_token'])]);
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
                                ->where('discount_tickets.discount_id','=',$discount->id)->where('shoppingcart.session_id','=',$info['s_token'])
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
                                ->where('discounts.code',$info['code'])->groupBy('discounts.id')->first();
                        if($coupon)
                        {
                            Shoppingcart::where('session_id','=',$info['s_token'])->update(['coupon'=>json_encode($coupon,true)]);
                            return Util::json(['success'=>true,'description'=>$coupon->description,'totals'=>$this->totals($info['s_token'])]);
                        }
                        return Util::json(['success'=>false, 'msg'=>'That coupon is not valid!']);
                    }
                    return Util::json(['success'=>false, 'msg'=>'That coupon is not valid for your items!']);
                }
            }
            return Util::json(['success'=>false, 'msg'=>'You must fill out correctly the form!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }   
    
    /*
     * calc totals of the cart
     */
    private function totals($session_id)
    {
        try {
            $price = 0;
            $fee = 0;
            $save = 0;
            $coupon = null;
            if(empty($session_id))
                return ['success'=>false,'retail_price'=>0,'processing_fee'=>0,'savings'=>0,'total'=>0];
            else
            {
                $items = $this->get($session_id);
                //check coupon for discounts
                if(count($items) && $items[0]->coupon)
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
                    $fee += $i->total_cost - $p;
                    //calculate discounts for each ticket the the coupon applies
                    if($coupon && in_array($i->ticket_id,$coupon['ticket_ids']))
                    {
                        $s = 0;
                        switch($coupon['discount_type'])
                        {
                            case 'Percent':
                                    $s = Util::round($i->total_cost * $coupon['start_num'] / 100);
                                    break;
                            case 'Dollar':
                                    $s = $coupon['start_num'] * $i->number_of_items;
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
                        (($coupon['discount_scope']=='Total' && $coupon['discount_type']=='Dollar'))? $save = $s/$i->number_of_items : $save += $s;
                    }
                }
            }    
            //check total values before return
            $total = $price + $fee - $save;
            if($total<0)
            {
                $save = $price + $fee;
                $total = 0;
            }
            return ['success'=>true,'retail_price'=>$price,'processing_fee'=>$fee,'savings'=>$save,'total'=>$total];
        } catch (Exception $ex) {
            return ['success'=>false,'retail_price'=>0,'processing_fee'=>0,'savings'=>0,'total'=>0];
        }
    }   
    
    /*
     * buy all items in the cart
     */
    public function buy()
    {
        try {
            $info = Input::all();
            if(!empty($info['show_time_id']) && !empty($info['ticket_id']) && !empty($info['qty']) && !empty($info['s_token']))
            {
                //create entry on table
                $contact = new Contact;
                $contact->name = $info['name'];
                $contact->email = $info['email'];
                $contact->phone = $info['phone'];
                $contact->show_name = $info['show_name'];
                $contact->system_info = $info['system_info'];
                $contact->message = $info['message'];
                $contact->save();
                //send email
                $html = '<b>Customer: </b>'.$info['name'].'<br><b>Email: </b>'.$info['email'].'</b><br><b>Phone: </b>'.$info['phone'];
                $html .= '<br><b>Show/Venue: </b>'.$info['show_name'];
                $html .= '<br><b>System Info: </b>'.$info['system_info'].'<br><b>Message: </b>'.$info['message'];
                $email = new EmailSG(null,env('MAIL_APP_ADMIN','debug@ticketbat.com'),'TicketBat App Contact');
                $email->html($html);
                //$email->reply($info['email']);
                if($email->send())
                    return Util::json(['success'=>true]);
                return Util::json(['success'=>false, 'msg'=>'There was an error sending the email. Please try later!']);
            }
            return Util::json(['success'=>false, 'msg'=>'You must fill out correctly the form!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }   
    
}
