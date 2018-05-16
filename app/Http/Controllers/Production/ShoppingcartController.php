<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Command\ReportSalesController;
use App\Http\Models\Image;
use App\Http\Models\Shoppingcart;
use App\Http\Models\Purchase;
use App\Http\Models\ShowTime;
use App\Http\Models\Country;
use App\Http\Models\Region;
use App\Http\Models\Show;
use App\Http\Models\User;
use App\Http\Models\Ticket;
use App\Http\Models\Util;

class ShoppingcartController extends Controller
{
    private $style_url = 'styles/ticket_types.css';
    /**
     * Watch viewcart view shoppingcart.
     *
     * @return Method
     */
    public function index()
    {
        try {
            //init
            $input = Input::all();
            //  Force use the POS system
            if (Auth::check() && in_array(Auth::user()->user_type_id, explode(',', env('POS_OPTION_USER_TYPE'))))
                return $this->pos($input);
            //recover session
            if(!empty($input['session']))
                $this->recover($input['session']);
            //if auth or guest continue
            $email_guest = Session::get('email_guest', ''); 
            if(!Auth::check() && empty($email_guest))
                return view('production.shoppingcart.credentials');
            else
            {
                $cart = $this->items();
                if( !empty($cart) )
                    return $this->viewcart($cart,$email_guest);
                return view('production.shoppingcart.empty');
            }
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
    
    /**
     * Watch viewcart view.
     *
     * @return Method
     */
    public function viewcart($cart,$email_guest)
    {
        try {
            //default email
            $cart['email'] = (Auth::check())? Auth::user()->email : $email_guest;
            //default enum
            $cart['countries'] = Country::get(['code','name']);  
            $cart['regions'] = Region::where('country','US')->get(['code','name']); 
            return view('production.shoppingcart.index',compact('cart'));
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
    
    /**
     * Watch viewcart items.
     *
     * @return Method
     */
    public function items($show_time_id=0)
    {
        try {
            $s_token = Util::s_token(false,true);
            $cart = Shoppingcart::calculate_session($s_token);
            //only for pos sytem tally
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
                return $cart;
            }
            if($cart['success'] && $cart['quantity']>0)
                return $cart;
            return null;
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
    
    /**
     * Recover previous session.
     *
     * @return Method
     */
    public function recover($s_token)
    {
        //if exists elements into session
        $item = Shoppingcart::where('session_id',$s_token)->first(['user_id']);
        if($item)
        {
            if(Auth::check())
            {
                if(Auth::user()->id!=$item->user_id && Auth::user()->email!=$item->user_id)
                    return view('production.shoppingcart.recover_error');
                else
                    Shoppingcart::where('session_id',$s_token)->update(['user_id'=>Auth::user()->id]);
            }
            else 
            {
                if(!empty($item->user_id))
                {
                    if(filter_var($item->user_id, FILTER_VALIDATE_EMAIL))
                        Session::set('email_guest', $item->user_id);
                    else 
                    {
                        $user = User::find($item->user_id);
                        if($user)
                            Session::set('email_guest', $user->email);
                    }
                }
                else
                {
                    $email_guest = Session::get('email_guest', NULL); 
                    if($email_guest && filter_var($email_guest, FILTER_VALIDATE_EMAIL))
                        Shoppingcart::where('session_id',$s_token)->update(['user_id'=>$email_guest]);
                    else 
                        Session::forget('email_guest'); 
                }
            }
            //set up the token to the new one
            Util::s_token(false,true,$s_token);
        }
        else
            return view('production.shoppingcart.recover_error');
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
        $init = '20:00';
        $input = Input::all();
        if(isset($input['status']))
        {
            switch ($input['status'])
            {
                case 1:
                    Session::forget('countdown');
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
      
    /*
     * add items to the cart
     */
    public function add()
    {
        try {
            $info = Input::all();
            if(!empty($info['show_time_id']) && !empty($info['ticket_id']) && !empty($info['qty']))
            {
                //check password
                $passwords = DB::table('show_passwords')
                                ->join('show_times', 'show_times.show_id', '=', 'show_passwords.show_id')
                                ->select(DB::raw('show_passwords.ticket_types, show_passwords.password'))
                                ->whereRaw(DB::raw('NOW()>show_passwords.start_date'))->whereRaw(DB::raw('NOW()<show_passwords.end_date'))
                                ->where('show_times.id',$info['show_time_id'])->groupBy('show_passwords.id')->orderBy('show_passwords.id','DESC')->get();
                $ticket = Ticket::find($info['ticket_id']);
                if($ticket && count($passwords))
                {
                    $pass = [];
                    foreach ($passwords as $p)
                    {
                        if(in_array($ticket->ticket_type, explode(',',$p->ticket_types)))
                            $pass[] = $p->password; 
                    }
                    //check password
                    if(count($pass))
                    {
                        if(empty($info['password']))
                            return ['success'=>false, 'msg'=>'You must enter a password for the event!'];
                        if(!in_array($info['password'], $pass))
                            return ['success'=>false, 'msg'=>'The password is not valid for the event!'];   
                    }
                }
                //continue adding
                $s_token = Util::s_token(false, true);
                return Shoppingcart::add_item($info['show_time_id'], $info['ticket_id'], $info['qty'], $s_token);
            }
            return ['success'=>false, 'msg'=>'You must fill out correctly the form!'];
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
            if(!empty($info['id']) && !empty($info['qty']))
            {
                $s_token = Util::s_token(false,true);
                $success = Shoppingcart::update_item($info['id'], $info['qty'], $s_token);
                if($success['success'])
                {
                    $cart = $this->items();
                    if( !empty($cart) )
                        return ['success'=>true,'msg'=>$success['msg'], 'cart'=>$cart];
                    return ['success'=>false, 'msg'=>'There are no items in the shopping cart!', 'cart'=>null]; 
                }
                return $success;
            }
            return ['success'=>false, 'msg'=>'You must enter a valid quantity for the item!'];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }  
    
    /*
     * remove items in the cart
     */
    public function remove()
    {
        try {
            $info = Input::all();
            if(!empty($info['id']))
            {
                //find and remove item
                $s_token = Util::s_token(false,true);
                $success = Shoppingcart::remove_item($info['id'], $s_token);
                if($success['success'])
                {
                    $cart = $this->items();
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
     * add coupon in the cart
     */
    public function coupon()
    {
        try {
            $info = Input::all();
            if(!empty($info['coupon']))
            {
                $s_token = Util::s_token(false,true);
                $success = Shoppingcart::apply_coupon($s_token, $info['coupon']);
                if($success['success'])
                {
                    $cart = $this->items();
                    if( !empty($cart) )
                        return ['success'=>true,'msg'=>$success['msg'], 'cart'=>$cart];
                    return ['success'=>false, 'msg'=>'There are no items in the shopping cart!', 'cart'=>null];
                }
                return $success; 
            }
            return ['success'=>false, 'msg'=>'Incorrect/Invalid Coupon: You must enter a valid coupon for you items.'];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }  
    
    /*
     * printed tickets in the cart
     */
    public function printed()
    {
        try {
            $info = Input::all();
            if(isset($info['option']))
            {
                Session::put('printed_tickets', $info['option']);
                $cart = $this->items();
                if( !empty($cart) )
                    return ['success'=>true, 'cart'=>$cart];
                return ['success'=>false, 'msg'=>'There are no items in the shopping cart!', 'cart'=>null];
            }
            return ['success'=>false, 'msg'=>'You must select a valid option!'];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }  
    
    /*
     * share tickets in the cart
     */
    public function share()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && !empty($input['id']))
            {
                $shoppingcart = Shoppingcart::find($input['id']);
                if($shoppingcart)
                {
                    $tickets = [];
                    if(!empty($shoppingcart->gifts) && Util::isJSON($shoppingcart->gifts))
                        $tickets = json_decode($shoppingcart->gifts,true);
                    return ['success'=>true,'tickets'=>$tickets];
                }
                return ['success'=>false,'msg'=> 'You cannot share tickets for this item.'];
            }
            else
            {
                $shared = [];
                if(!empty($input['email']) && !empty($input['first_name']) && !empty($input['last_name']) && !empty($input['qty']))
                {
                    $indexes = array_keys($input['email']);
                    foreach ($indexes as $id=>$i)
                        $shared[] = ['id'=>$id+1,'first_name'=>$input['first_name'][$i],'last_name'=>$input['last_name'][$i],'email'=>$input['email'][$i],
                                     'comment'=>(!empty($input['comment'][$i]))? $input['comment'][$i] : '','qty'=>$input['qty'][$i]];
                }
                $shoppingcart = Shoppingcart::find($input['purchases_id']);
                if($shoppingcart)
                {
                    $shoppingcart->gifts = json_encode($shared,true);
                    $shoppingcart->save();
                    return ['success'=>true,'msg'=> 'After your purchase is complete these tickets will be shared.'];
                } 
                return ['success'=>false,'msg'=> 'There was an error sharing the tickets.<br>Please contact us.'];
            }
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    } 
    
    /**
     * Show the default method for the buy page.
     *
     * @return Method
     */
    public function pos($input)
    {
        try {
            //init
            $display_schedule = 3;
            $current = date('Y-m-d H:i:s');
            $s_token = Util::s_token(false, true);       
            $venue_id = $show_id = $show_time_id = $venue_logo = $show_logo = $show_time = null;
            $venues = $shows = $showtimes = $tickets = [];
            //checkings by user
            $options = Util::display_options_by_user();
            //get shoppingcart items
            $cart = Shoppingcart::items_session($s_token);
            
            //get all records
            $events = DB::table('shows')
                ->join('venues', 'venues.id', '=', 'shows.venue_id')
                ->join('stages', 'stages.id', '=', 'shows.stage_id')
                ->join('show_times', 'show_times.show_id', '=', 'shows.id')
                ->join('tickets', 'tickets.show_id', '=', 'shows.id')
                ->select(DB::raw('venues.id AS venue_id, venues.name AS venue, venues.logo_url AS venue_url,
                                  shows.id as show_id, shows.name, shows.logo_url'))
                ->where('venues.is_featured', '>', 0)
                ->where('shows.is_active', '>', 0)
                ->where(function ($query) use ($current) {
                    $query->whereNull('shows.on_sale')
                        ->orWhere('shows.on_sale', '<=', $current);
                })
                ->where(function ($query) use ($current) {
                    $query->whereNull('shows.on_featured')
                        ->orWhere('shows.on_featured', '<=', $current);
                })
                ->where('show_times.is_active', '>', 0)
                ->where($options['where'])
                ->whereNotNull('venues.logo_url')
                ->whereNotNull('shows.logo_url')
                ->when(!is_null($options['venues']), function ($shows) use ($options) {
                    return $shows->whereIn('venues.id',$options['venues']);
                })
                ->groupBy('shows.id')
                ->get();
            //venues and events    
            foreach ($events as $e)
            {
                if(!isset($venues[$e->venue_id]))
                    $venues[$e->venue_id] = ['id'=>$e->venue_id,'name'=>$e->venue, 'logo'=>Image::view_image($e->venue_url), 'shows'=>[]];
                $shows[$e->show_id] = ['id'=>$e->show_id,'name'=>$e->name, 'logo'=>Image::view_image($e->logo_url), 'venue'=>$e->venue_id];
                $venues[$e->venue_id]['shows'][] = $shows[$e->show_id];
            }
            
            //if select slug
            if(!empty($input['slug']))
            {
                $event = Show::where('slug',$input['slug'])->first();
                if($event)
                    $input['show_id'] = $event->id;
            }
            
            //if select venue
            if(!empty($input['venue_id']))
            {
                if(isset($venues[$input['venue_id']]))
                {
                    $venue_id = $input['venue_id'];
                    $venue_logo = $venues[$venue_id]['logo'];
                    if(count($venues[$venue_id]['shows'])>0)
                    {
                        $shows = $venues[$venue_id]['shows'];
                        $show_id = $shows[0]['id'];
                        $show_logo = $shows[0]['logo'];
                        
                    }
                    else
                        $shows = [];
                }
                else
                    $shows = [];
            }
            //if not selected any show and only one venue
            else if(empty($input['show_id']) && count($venues)==1)
            {
                $venue_id = key( $venues );
                $venue_logo = $venues[$venue_id]['logo'];
                if(count($venues[$venue_id]['shows'])>0)
                {
                    $shows = $venues[$venue_id]['shows'];
                    $show_id = $shows[0]['id'];
                    $show_logo = $shows[0]['logo'];

                }
                else
                    $shows = [];
            }
            //if select show (default into)
            else if(!empty($input['show_id']))
            {
                if(isset($shows[$input['show_id']]))
                {
                    $venue_id = $shows[$input['show_id']]['venue'];
                    $venue_logo = $venues[$venue_id]['logo'];
                    $show_id = $input['show_id'];
                    $show_logo = $shows[$show_id]['logo'];
                    $shows = $venues[$venue_id]['shows'];                    
                }
            }
            //if select showtime 
            if(!empty($input['show_time_id']))
            {
                $show_time = ShowTime::find($input['show_time_id']);
                if($show_time && isset($shows[$show_time->show->id]))
                {
                    $show_time_id = $show_time->id;
                    $show_id = $show_time->show->id;
                    $show_logo = $shows[$show_id]['logo'];
                    $venue_id = $show_time->show->venue_id;
                    $venue_logo = $venues[$venue_id]['logo'];
                    $shows = $venues[$venue_id]['shows'];
                }
            }
            
            //show_times
            if(!empty($show_id))
            {
                $showtimes = DB::table('show_times')
                    ->join('shows', 'show_times.show_id', '=', 'shows.id')
                    ->join('venues', 'venues.id', '=', 'shows.venue_id')
                    ->select(DB::raw('show_times.id, show_times.time_alternative, DATE_FORMAT(show_times.show_time, "%a, %b %D, %Y @ %l:%i %p") AS show_time'))
                    ->where('show_times.show_id', $show_id)
                    ->where('show_times.is_active', '>', 0)
                    ->where($options['where'])
                    ->orderBy('show_times.show_time')->take($display_schedule)->get();
                $show_time_id = (!count($showtimes))? null : ( (empty($show_time_id))? $showtimes[0]->id : $show_time_id );
            }
            else
                $shows = [];
            
            //tickets
            if(!empty($show_time_id))
            {
                $show_time = ShowTime::find($show_time_id)->show_time;
                
                $tcks = DB::table('tickets')
                    ->join('packages', 'packages.id', '=', 'tickets.package_id')
                    ->select(DB::raw('tickets.id AS ticket_id, packages.title, tickets.ticket_type, tickets.ticket_type_class,
                                                      tickets.retail_price,
                                                      (CASE WHEN (tickets.max_tickets > 0) THEN (tickets.max_tickets-(SELECT COALESCE(SUM(p.quantity),0) FROM purchases p 
                                                       WHERE p.ticket_id = tickets.id AND p.show_time_id = '.$show_time_id.')) ELSE null END) AS max_available'))
                    ->where('tickets.show_id', $show_id)->where('tickets.is_active', '>', 0)
                    ->where('tickets.only_pos', '>=', 0)
                    ->whereRaw(DB::raw('tickets.id NOT IN (SELECT ticket_id FROM soldout_tickets WHERE show_time_id = '.$show_time_id.')'))
                    ->where(function ($query) use ($show_time_id) {
                        $query->whereNull('tickets.max_tickets')
                              ->orWhere('tickets.max_tickets', '>', 0);
                    })
                    ->groupBy('tickets.id')->orderBy('tickets.is_default', 'DESC')->get();
                    
                //checking tickets
                foreach ($tcks as $t) {                    
                    
                    if(!is_null($t->max_available) && $t->max_available<1)
                        $t->max_available = 0;
                    
                    //id
                    $id = preg_replace("/[^A-Za-z0-9]/", '_', $t->ticket_type);
                    //fill out tickets
                    if (isset($tickets[$id]))
                        $tickets[$id]['tickets'][] = $t;
                    else 
                        $tickets[$id] = ['type' => $t->ticket_type, 'class' => $t->ticket_type_class, 'tickets' => [$t]];
                   
                    //check with items in shoppingcart
                    if(count($cart))
                    {
                        $t_available = false;
                        foreach ($cart as $i)
                        {
                            if($i->ticket_id==$t->ticket_id)
                            {
                                $t_available = true;
                                if($i->item_id==$show_time_id)
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
                $cart = $this->items($show_time_id);
            }
            else
            {
                //get shoppingcart items
                $cart = ['success'=>true,'quantity'=>0,'seller'=>1,'total'=>0,'items'=>[],'amex_only'=>0,'tally'=>['transactions'=>0, 'tickets'=>0, 'cash'=>0, 'total'=>0]];
            }
            //get styles from cloud
            $ticket_types_css = file_get_contents(env('IMAGE_URL_AMAZON_SERVER') . '/' . $this->style_url);
            //enums
            $cart['countries'] = Country::get(['code','name']);
            $cart['regions'] = Region::where('country','US')->get(['code','name']);
            //return view
            return view('production.shoppingcart.pos', compact('ticket_types_css', 'cart', 'show_time_id', 'show_id', 'venue_id', 'tickets', 'showtimes', 'show_time', 'shows', 'venues','show_logo','venue_logo'));
        } catch (Exception $ex) {
            throw new Exception('Error Production POS Buy Index: ' . $ex->getMessage());
        }
    }
    
    /*
     * update items in the cart
     */
    public function pos_update()
    {
        try {
            $info = Input::all();
            $s_token = Util::s_token(false,true);
            if(!empty($info['id']))
            {
                return $this->pos_remove($info['id'],$s_token);
            }
            else if(!empty($info['show_time_id']) && !empty($info['ticket_id']))
            {
                $item = Shoppingcart::where('item_id',$info['show_time_id'])->where('ticket_id',$info['ticket_id'])->where('session_id',$s_token)->first();

                if($item)
                {
                    if(empty($info['qty']))
                        return $this->pos_remove($item->id,$s_token);
                    else
                    {
                        $success = Shoppingcart::update_item($item->id, $info['qty'], $s_token);
                        if($success['success'])
                        {
                            $cart = $this->items();
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
                        $cart = $this->items();
                        if( !empty($cart) )
                            return ['success'=>true,'msg'=>$success['msg'], 'cart'=>$cart];
                        return ['success'=>false, 'msg'=>'There are no items in the shopping cart!', 'cart'=>null];
                    }
                    return $success;
                }
                return ['success'=>false, 'msg'=>'That item is not longer on the shopping cart!'];
            }
            return ['success'=>false, 'msg'=>'You must select a valid event date/time and ticket at the form!'];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
    
    /*
     * remove items in the cart
     */
    public function pos_remove($id,$s_token)
    {
        try {
            if(!empty($id))
            {
                //find and remove item
                $success = Shoppingcart::remove_item($id, $s_token);
                if($success['success'])
                {
                    $cart = $this->items();
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
    public function pos_email_receipt()
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
    
    /*
     * send the report of event by email
     */
    public function pos_email_report()
    {
        try {
            $input = Input::all();
            $receipts = [];
            if(!empty($input['show_time_id']) && !empty($input['report']) && filter_var($input['report'], FILTER_VALIDATE_EMAIL))
            {
                $showtime = ShowTime::find($input['show_time_id']);
                if($showtime)
                {
                    $control = new ReportSalesController(0,0);
                    $response = $control->event($input['show_time_id'],$input['report']);
                    if($response)
                        return ['success'=>true,'msg'=>'The email was sent successfully!'];
                    return ['success'=>false,'msg'=>'The system could not sent the report to that email!'];
                }
                return ['success'=>false,'msg'=>'That event is not longer available in the system!'];
            }
            return ['success'=>false, 'msg'=>'You must enter a valid email in the form!'];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
    
}
