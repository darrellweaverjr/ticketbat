<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Http\Models\Image;
use App\Http\Models\Shoppingcart;
use App\Http\Models\Purchase;
use App\Http\Models\Country;
use App\Http\Models\Region;
use App\Http\Models\User;
use App\Http\Models\Util;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class POSController extends Controller
{
    private $style_url = 'styles/ticket_types.css';
    /**
     * Show the default method for the buy page.
     *
     * @return Method
     */
    public function buy($slug)
    {
        try {
            //init
            $qty_tickets_sell = 100;
            $cutoff_hours = 10;
            $display_schedule = 3;
            $current = date('Y-m-d H:i:s');
            $s_token = Util::s_token(false, true);
            $input = Input::all();
            $show_time_id = (!empty($input['show_time_id']))? $input['show_time_id'] : '';
            //checkings
            if (empty($slug)) {
                return redirect()->route('index');
            }
            if (!(Auth::check() && in_array(Auth::user()->user_type_id, explode(',', env('SELLER_OPTION_USER_TYPE')))))
                return redirect('event/'.$slug);
            //get all records
            $event = DB::table('shows')
                ->join('venues', 'venues.id', '=', 'shows.venue_id')
                ->join('stages', 'stages.id', '=', 'shows.stage_id')
                ->join('show_times', 'show_times.show_id', '=', 'shows.id')
                ->select(DB::raw('shows.id as show_id, show_times.id AS show_time_id, shows.name, shows.logo_url, stages.id AS stage_id, stages.ticket_order,
                                          shows.on_sale, venues.name AS venue'))
                ->where('shows.is_active', '>', 0)->where('venues.is_featured', '>', 0)
                ->where(function ($query) use ($current) {
                    $query->whereNull('shows.on_sale')
                        ->orWhere('shows.on_sale', '<=', $current);
                })
                ->where(function ($query) use ($current) {
                    $query->whereNull('shows.on_featured')
                        ->orWhere('shows.on_featured', '<=', $current);
                })
                ->where('shows.slug', $slug)
                ->where('show_times.is_active', '>', 0)
                ->where(function ($query) use ($current) {
                    $query->whereRaw(DB::raw('DATE_SUB(show_times.show_time, INTERVAL shows.cutoff_hours HOUR)', '>=', $current ));
                })
                ->first();
            if (!$event) {
                return redirect()->route('index');
            }
            //formats
            $event->logo_url = Image::view_image($event->logo_url);
            //get shoppingcart items
            $cart = Shoppingcart::items_session($s_token);
            //show_times
            $event->showtimes = DB::table('show_times')
                ->join('shows', 'show_times.show_id', '=', 'shows.id')
                ->select(DB::raw('show_times.id, show_times.time_alternative, show_times.show_time,
                                                 DATE_FORMAT(show_times.show_time,"%a, %b %d,%Y") AS show_day,
                                                 DATE_FORMAT(show_times.show_time,"%l:%i %p") AS show_hour,
                                                 IF(show_times.slug, show_times.slug, shows.ext_slug) AS ext_slug,
                                                 IF(NOW()>DATE_SUB(show_times.show_time,INTERVAL shows.cutoff_hours HOUR), 1, 0) as presale'))
                ->where('show_times.show_id', $event->show_id)->where('show_times.is_active', '>', 0)
                ->where(function ($query) use ($current,$cutoff_hours) {
                    $query->where(DB::raw('DATE_SUB(show_times.show_time, INTERVAL '.$cutoff_hours.' HOUR)'), '>=', $current );
                })
                ->orderBy('show_times.show_time')->take($display_schedule)->get();
            $show_time_id = (!count($event->showtimes))? '' : ( (empty($show_time_id))? $event->showtimes[0]->id : $show_time_id );
            //get tickets types
            $event->tickets = [];
            $tickets = DB::table('tickets')
                ->join('packages', 'packages.id', '=', 'tickets.package_id')
                ->select(DB::raw('tickets.id AS ticket_id, packages.title, tickets.ticket_type, tickets.ticket_type_class,
                                                  tickets.retail_price,
                                                  (CASE WHEN (tickets.max_tickets > 0) THEN (tickets.max_tickets-(SELECT COALESCE(SUM(p.quantity),0) FROM purchases p WHERE p.ticket_id = tickets.id AND p.show_time_id = ' . $event->show_time_id . ')) ELSE ' . $qty_tickets_sell . ' END) AS max_available'))
                ->where('tickets.show_id', $event->show_id)->where('tickets.is_active', '>', 0)->where('tickets.only_pos', '>=', 0)
                ->whereRaw(DB::raw('tickets.id NOT IN (SELECT ticket_id FROM soldout_tickets WHERE show_time_id = ' . $event->show_time_id . ')'))
                ->where(function ($query) use ($event) {
                    $query->where('tickets.max_tickets', '<=', 0)
                        ->orWhereRaw('tickets.max_tickets-(SELECT COALESCE(SUM(p.quantity),0) FROM purchases p WHERE p.ticket_id = tickets.id AND p.show_time_id = ' . $event->show_time_id . ')', '>', 0);
                })
                ->groupBy('tickets.id')->orderBy('tickets.is_default', 'DESC')->get();
            if(empty($event->showtimes) || empty($tickets))
                Shoppingcart::where('session_id','=',$s_token)->delete();
            foreach ($tickets as $t) {
                //if there is tickets availables
                if ($t->max_available > 0) {
                    //max available
                    if ($t->max_available > $qty_tickets_sell) {
                        $t->max_available = $qty_tickets_sell;
                    }
                    //id
                    $id = preg_replace("/[^A-Za-z0-9]/", '_', $t->ticket_type);
                    //fill out tickets
                    if (isset($event->tickets[$id])) {
                        $event->tickets[$id]['tickets'][] = $t;
                    } else {
                        $event->tickets[$id] = ['type' => $t->ticket_type, 'class' => $t->ticket_type_class, 'tickets' => [$t]];
                    }
                } else {
                    unset($t);
                }
                //check with items in shoppingcart
                if(count($cart))
                {
                    $t_available = false;
                    foreach ($cart as $i)
                    {
                        if($i->ticket_id==$t->ticket_id)
                        {
                            $t_available = true;
                            $t->cart = $i->number_of_items;
                        }
                    }
                    if(!$t_available)
                        Shoppingcart::where('ticket_id','=',$t->ticket_id)->where('session_id','=',$s_token)->delete();
                    if(empty($t->cart))
                        $t->cart = 0;
                }
                else
                    $t->cart = 0;
            }
            //get shoppingcart items
            $cart = $this->items($s_token,$show_time_id);
            //order the ticket types according to the stage order
            if (!empty($event->ticket_order)) {
                $ticket_order = explode(',', $event->ticket_order);
                $new_order = [];
                foreach ($ticket_order as $o) {
                    $id = preg_replace("/[^A-Za-z0-9]/", '_', $o);
                    if (!empty($event->tickets[$id])) {
                        $new_order[$id] = $event->tickets[$id];
                        unset($event->tickets[$id]);
                    }
                }
                $event->tickets = array_merge($new_order, $event->tickets);
            }
            //get styles from cloud
            $ticket_types_css = file_get_contents(env('IMAGE_URL_AMAZON_SERVER') . '/' . $this->style_url);

            $cart['countries'] = Country::get(['code','name']);
            $cart['regions'] = Region::where('country','US')->get(['code','name']);
            //return view
            return view('production.pos.buy', compact('event', 'ticket_types_css', 'cart', 'show_time_id'));
        } catch (Exception $ex) {
            throw new Exception('Error Production POS Buy Index: ' . $ex->getMessage());
        }
    }

    /**
     * Watch viewcart items.
     *
     * @return Method
     */
    public function items($s_token,$show_time_id=0)
    {
        try {
            $cart = Shoppingcart::calculate_session($s_token);
            if($show_time_id)
            {
                $tally = DB::table('purchases')
                            ->select(DB::raw('COUNT(purchases.id) AS transactions, SUM(purchases.quantity) AS tickets,
                                              SUM( IF(purchases.payment_type="Cash",purchases.price_paid,0) ) AS cash,
                                              SUM(purchases.price_paid) AS total'))
                            ->where('purchases.status','=','Active')
                            ->where('purchases.show_time_id','=',$show_time_id)
                            ->where('purchases.user_id','=',Auth::user()->id)
                            ->where('purchases.channel','=','POS')
                            ->groupBy('purchases.show_time_id')->orderBy('purchases.show_time_id')->first();
                if($tally)
                    $cart['tally'] = ['transactions'=>$tally->transactions, 'tickets'=>$tally->tickets, 'cash'=>$tally->cash, 'total'=>$tally->total];
                else
                    $cart['tally'] = ['transactions'=>0, 'tickets'=>0, 'cash'=>0, 'total'=>0];
            }
            return $cart;
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }

    /*
     * update items in the cart
     */
    public function update()
    {
        try {
            $info = Input::all();
            $s_token = Util::s_token(false,true);
            if(!empty($info['update']) && !empty($info['show_time_id']))
            {
                $cart = $this->items($s_token,$info['show_time_id']);
                return ['success'=>true,'msg'=>'', 'cart'=>$cart];
            }
            else if(!empty($info['id']))
            {
                return $this->remove($info['id'],$s_token);
            }
            else if(!empty($info['show_time_id']) && !empty($info['ticket_id']) && !empty($info['show_time_id']))
            {
                $item = Shoppingcart::where('item_id',$info['show_time_id'])->where('ticket_id',$info['ticket_id'])->where('session_id',$s_token)->first();

                if($item)
                {
                    if(empty($info['qty']))
                        return $this->remove($item->id,$s_token);
                    else
                    {
                        $success = Shoppingcart::update_item($item->id, $info['qty'], $s_token);
                        if($success['success'])
                        {
                            $cart = $this->items($s_token);
                            if( !empty($cart) )
                                return ['success'=>true,'msg'=>$success['msg'], 'cart'=>$cart];
                            return ['success'=>false, 'msg'=>'There are no items in the shopping cart!', 'cart'=>null];
                        }
                        return $success;
                    }
                }
                else if(!empty($info['qty']))
                {
                    $success = Shoppingcart::add_item($info['show_time_id'], $info['ticket_id'], $info['qty'], $s_token);
                    if($success['success'])
                    {
                        $cart = $this->items($s_token);
                        if( !empty($cart) )
                            return ['success'=>true,'msg'=>$success['msg'], 'cart'=>$cart];
                        return ['success'=>false, 'msg'=>'There are no items in the shopping cart!', 'cart'=>null];
                    }
                    return $success;
                }
                return ['success'=>false, 'msg'=>'That item is not longer on the shopping cart!'];
            }
            return ['success'=>false, 'msg'=>'You must do a valid action in the form!'];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }

    /*
     * remove items in the cart
     */
    public function remove($id,$s_token)
    {
        try {
            if(!empty($id))
            {
                //find and remove item
                $success = Shoppingcart::remove_item($id, $s_token);
                if($success['success'])
                {
                    $cart = $this->items($s_token);
                    if( !empty($cart) )
                        return ['success'=>true,'msg'=>$success['msg'], 'cart'=>$cart];
                    return ['success'=>true, 'msg'=>'There are no items in the shopping cart!', 'cart'=>null];
                }
                return $success;
            }
            return ['success'=>false, 'msg'=>'You must select a valid item to remove!'];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
    
    /*
     * send the receipt of purchase by email
     */
    public function email_receipt()
    {
        try {
            $input = Input::all();
            $receipts = [];
            if(!empty($input['purchases']) && !empty($input['email']) && filter_var($input['email'], FILTER_VALIDATE_EMAIL))
            {
                $purchases = explode(',', $input['purchases']);
                //send receipts
                foreach ($purchases as $id)
                {
                    $p = Purchase::find($id);
                    if($p)
                        $receipts[] = $p->get_receipt();
                }
                //sent email
                $response = Purchase::email_receipts('TicketBat Purchase',$receipts,'receipt',null,false,false,$input['email'],true);
                if($response)
                    return ['success'=>true,'msg'=>'The email was sent successfully!'];
                return ['success'=>false,'msg'=>'The system could not sent the receipt to that email!'];
            }
            return ['success'=>false, 'msg'=>'You must enter a valid email in the form!'];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }


}
