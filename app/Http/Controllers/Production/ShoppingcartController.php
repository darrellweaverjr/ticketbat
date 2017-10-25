<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Shoppingcart;
use App\Http\Models\Country;
use App\Http\Models\Region;
use App\Http\Models\User;
use App\Http\Models\Ticket;
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
        try {
            //init
            $input = Input::all();
            if(!empty($input['session']))
            {
                $this->recover($input['session']);
            }
            //if auth or guest continue
            $email_guest = Session::get('email_guest', NULL); 
            if(!Auth::check() && empty($email_guest))
                return $this->credentials();
            else
            {
                $cart = $this->items();
                if( !empty($cart) )
                    return view('production.shoppingcart.index',compact('cart'));
                return view('production.shoppingcart.empty');
            }
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
    
    /**
     * Watch viewcart items.
     *
     * @return Method
     */
    public function items()
    {
        try {
            $s_token = Util::s_token(false,true);
            $cart = Shoppingcart::calculate_session($s_token);
            if($cart['success'] && $cart['quantity']>0)
            {
                //seller
                $cart['seller'] = (Auth::check() && in_array(Auth::user()->user_type_id,[1,7]))? 1 : 0;
                //default email
                $cart['email'] = (Auth::check())? Auth::user()->email : ((!empty($email_guest))? $email_guest : '');
                //default enum
                $cart['countries'] = Country::get(['code','name']);  
                $cart['regions'] = Region::where('country','US')->get(['code','name']); 
                //return 
                return $cart;
            }
            else
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
     * Prompt for login or guest email on go to viewcart.
     *
     * @return Method
     */
    public function credentials()
    {
        //return view
        return view('production.shoppingcart.credentials');
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
    
    /**
     * process items in the shoppingcart.
     *
     * @return Method
     */
    public function process()
    {
        try {
            //init
            $input = Input::all();
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
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
            if(!empty($info['show_time_id']) && !empty($info['ticket_id']) && !empty($info['qty']))
            {
                
            }
            return ['success'=>false, 'msg'=>'Invalid option!'];
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
            if(!empty($info['show_time_id']) && !empty($info['ticket_id']) && !empty($info['qty']))
            {
                
            }
            return ['success'=>false, 'msg'=>'Invalid option!'];
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
                    return ['success'=>false, 'msg'=>'There are no items in the shopping cart!'];
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
                return ['success'=>false, 'msg'=>'There are no items in the shopping cart!'];
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
                    return ['success'=>true,'msg'=> 'Tickets shared successfully!'];
                } 
                return ['success'=>false,'msg'=> 'There was an error sharing the tickets.<br>Please contact us.'];
            }
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }  
    
}
