<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Shoppingcart;
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
            $email_guest = Session::get('email_guest', NULL); 
            if(!empty($input['session']))
            {
                //if exists elements into session
                $item = Shoppingcart::where('session_id',$input['session'])->first(['user_id']);
                if($item)
                {
                    if(Auth::check())
                    {
                        if(Auth::user()->id!=$item->user_id && Auth::user()->email!=$item->user_id)
                            return view('production.shoppingcart.recover_error');
                        else
                            Shoppingcart::where('session_id',$input['session'])->update(['user_id'=>Auth::user()->id]);
                    }
                    else 
                    {
                        if(!empty($item->user_id))
                        {
                            if(filter_var($item->user_id, FILTER_VALIDATE_EMAIL))
                            {
                                Session::set('email_guest', $item->user_id);
                                $email_guest = $item->user_id;
                            }
                            else 
                            {
                                $user = User::find($item->user_id);
                                if($user)
                                {
                                    Session::set('email_guest', $user->email);
                                    $email_guest = $user->email;
                                }
                            }
                        }
                        else
                        {
                            if($email_guest && filter_var($email_guest, FILTER_VALIDATE_EMAIL))
                            {
                                Shoppingcart::where('session_id',$input['session'])->update(['user_id'=>$email_guest]);
                            }
                            else 
                            {
                                Session::forget('email_guest'); 
                                $email_guest = null;
                            }
                        }
                    }
                    //set up the token to the new one
                    Util::s_token(false,true,$input['session']);
                }
                else
                    return view('production.shoppingcart.recover_error');
            }
            //if auth or guest continue
            if(!Auth::check() && empty($guest_email))
                return $this->credentials();
            else
            {
                $s_token = Util::s_token(false,true);

                //return view
                return view('production.shoppingcart.index');
            }
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
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
        Session::forget('countdown');
        $init = '20:00';
        $input = Input::all();
        if(isset($input['status']))
        {
            switch ($input['status'])
            {
                case 1:
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
                            return Util::json(['success'=>false, 'msg'=>'You must enter a password for the event!']);
                        if(!in_array($info['password'], $pass))
                            return Util::json(['success'=>false, 'msg'=>'The password is not valid for the event!']);   
                    }
                }
                //continue adding
                $s_token = Util::s_token(false, true);
                $success = Shoppingcart::add_item($info['show_time_id'], $info['ticket_id'], $info['qty'], $s_token);
                return Util::json($success);
            }
            return Util::json(['success'=>false, 'msg'=>'You must fill out correctly the form!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }  
    
}
