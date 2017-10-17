<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Shoppingcart;
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
        return ['success'=>true,'qty_items'=> Shoppingcart::qty_items()];
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
