<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ShowTime;
use App\Http\Models\UserSeller;
use App\Http\Models\SellerTally;
use App\Http\Models\Util;
use App\Http\Controllers\Command\ReportSalesController;

class UserSellerController extends Controller
{
    
    /**
     * Seller, status drawer.
     *
     * @return Method
     */
    public static function drawer_status()
    {
        try {
            $s_token = Util::s_token(false, true);
            $user_id = Auth::user()->id;
            $drawer = UserSeller::where('user_id',$user_id)->first(); 
            if(!$drawer)
            {
                $drawer = new UserSeller;
                $drawer->user_id = $user_id;
                $drawer->open_drawer = 0;
                $drawer->cash_in = 0;
                $drawer->session_id = $s_token;
                $drawer->save();
            }
            if($drawer->open_drawer == 0)
                return 0;
            else
            {
                if($drawer->session_id != $s_token)
                    return 2;
                else
                    return 1;
            }           
        } catch (Exception $ex) {
            throw new Exception('Error Production User drawer status: '.$ex->getMessage());
        }
    }
    
    /**
     * Seller, open drawer.
     *
     * @return Method
     */
    public function drawer_open()
    {
        try {
            //init
            $input = Input::all(); 
            $s_token = Util::s_token(false, true);
            $current = date('Y-m-d H:i:s');
            if(isset($input) && !empty($input['user_id']) && isset($input['open_drawer']) && isset($input['cash_in']))
            {
                //get drawer
                $drawer = UserSeller::where('user_id',$input['user_id'])->first(); 
                if(!$drawer)
                {
                    DB::table('user_seller')->insert(
                        ['user_id'=>$input['user_id'], 'cash_in'=>$input['cash_in'], 'time_in'=>$current, 'open_drawer'=>0, 'session_id'=>$s_token]
                    );
                    $drawer = UserSeller::where('user_id',$input['user_id'])->first(); 
                }
                if(!$drawer)
                    return ['success'=>false,'status'=>0,'msg'=>'The system could not create the drawer for that user!'];
                //check drawer status
                if($drawer->open_drawer>0)
                {
                    if($drawer->session_id != $s_token)
                    {
                        UserSeller::where('user_id',$input['user_id'])->update(['session_id'=>$s_token]); 
                        return ['success'=>true,'status'=>2,'msg'=>'The drawer was already open in a another session!<br>The system moved that session to the current one.'];
                    }
                    return ['success'=>false,'status'=>1,'msg'=>'The drawer is already open!'];
                }
                //get tally
                $tally = SellerTally::where('user_id', '=', $input['user_id'])->orderBy('time_in', 'DESC')->first();
                if($tally && $tally->time_out==null && $tally->cash_out==null)
                    return ['success'=>false,'status'=>2,'msg'=>'There is a tally already opened. You must close it first.'];                    
                //create tally
                $tally = new SellerTally;
                $tally->user_id = $input['user_id'];
                $tally->cash_in = $input['cash_in'];
                $tally->time_in = $current;
                $tally->cash_out = null;
                $tally->time_out = null;
                $tally->save();
                if(!$tally || empty($tally->id))
                    return ['success'=>false,'status'=>1,'msg'=>'The system could not create the tally for this seller.']; 
                //update drawer status
                UserSeller::where('user_id',$input['user_id'])->update(['cash_in'=>$input['cash_in'], 'time_in'=>$current, 'open_drawer'=>$tally->id, 'session_id'=>$s_token]); 
                return ['success'=>true,'status'=>1,'msg'=>'Drawer opened successfully!'];
            }
            return ['success'=>false,'msg'=>'Form invalid!'];
        } catch (Exception $ex) {
            throw new Exception('Error Production User drawer open: '.$ex->getMessage());
        }
    }
    
    /**
     * Seller, continue drawer.
     *
     * @return Method
     */
    public function drawer_continue()
    {
        try {
            //init
            $input = Input::all(); 
            $s_token = Util::s_token(false, true);
            if(isset($input) && !empty($input['user_id']))
            {
                $drawer = UserSeller::where('user_id',$input['user_id'])->first(); 
                if(!$drawer || $drawer->open_drawer==0)
                    return ['success'=>false,'msg'=>'There is not open session to keep!'];
                else 
                {
                    UserSeller::where('user_id',$input['user_id'])->update(['session_id'=>$s_token]);
                    return ['success'=>true,'msg'=>'Drawer updated successfully!'];
                }
            }
            return ['success'=>false,'msg'=>'Form invalid!'];
        } catch (Exception $ex) {
            throw new Exception('Error Production User drawer open: '.$ex->getMessage());
        }
    }    
    
    /**
     * Seller, close drawer.
     *
     * @return Method
     */
    public function drawer_close()
    {
        try {
            //init
            $input = Input::all(); 
            $current = date('Y-m-d H:i:s');
            if(isset($input) && !empty($input['user_id']) && isset($input['open_drawer']))
            {
                $drawer = UserSeller::where('user_id',$input['user_id'])->first(); 
                if(!$drawer || $drawer->open_drawer==0)
                    return ['success'=>false,'msg'=>'There is not open session to close!'];
                else 
                {
                    $cash_out = $drawer->cash_in;
                    $tally = SellerTally::where('id', '=', $drawer->open_drawer)->first();
                    $purchases = DB::table('purchases')
                            ->select(DB::raw('SUM(price_paid) AS cash_get, GROUP_CONCAT(show_time_id) AS events'))
                            ->where('user_id', '=', $input['user_id'])->where('payment_type', '=', 'Cash')
                            ->where('created', '>=', $drawer->time_in)->where('created', '<=', $current )
                            ->groupBy('user_id')->first();
                    
                    if($tally && $tally->time_out==null && $tally->cash_out==null)
                    {
                        if($purchases)
                            $cash_out += $purchases->cash_get;
                        //update tally
                        $tally->time_out = $current;
                        $tally->cash_out = $cash_out;
                        $tally->save();
                        //return
                        $response = ['success'=>true,'cash_out'=>$cash_out,'msg'=>'Session closed successfully!'];
                    }
                    else
                        $response = ['success'=>false,'msg'=>'That session was already closed before!'];
                    //close drawer
                    UserSeller::where('user_id',$input['user_id'])->update(['open_drawer'=>0]); 
                    return $response;
                }
            }
            return ['success'=>false,'msg'=>'Form invalid!'];
        } catch (Exception $ex) {
            throw new Exception('Error Production User drawer open: '.$ex->getMessage());
        }
    }
    
    /**
     * Seller, close drawer.
     *
     * @return Method
     */
    public function seller_tally()
    {
        try {
            //init
            $current = date('Y-m-d H:i:s');
            if(!Auth::check())
                return ['success'=>false,'msg'=>'You must be logged as a seller to see this option!'];
            else
            {
                $entries = 5;
                $tally = DB::table('seller_tally')
                            ->leftJoin('purchases', function($join){
                                $join->on('seller_tally.user_id', '=', 'purchases.user_id')
                                     ->on('seller_tally.time_in','<=','purchases.created')
                                     ->on('seller_tally.time_out','>=','purchases.created');
                            })
                            ->select(DB::raw('seller_tally.*, COUNT(purchases.id) AS transactions, SUM(COALESCE(purchases.quantity,0)) AS tickets, SUM(COALESCE(purchases.price_paid,0)) AS total'))
                            ->where('seller_tally.user_id', '=',Auth::user()->id)
                            ->groupBy('seller_tally.id')->orderBy('seller_tally.id','DESC')->take($entries)->get();
                return ['success'=>true,'tally'=>$tally];
            }
        } catch (Exception $ex) {
            throw new Exception('Error Production User drawer open: '.$ex->getMessage());
        }
    }
        
    /*
     * send the report of event by email
     */
    public function seller_report()
    {
        try {   
            $input = Input::all();
            //get elements
            if(empty($input['action'])) 
            {
                $venues_edit = Auth::user()->venues_check_ticket;
                $venues_edit = (!empty($venues_edit))? explode(',',$venues_edit) : [0];
                $start_date = date('Y-m-d', strtotime('yesterday'));
                $end_date = date('Y-m-d', strtotime('now'));
                $events = DB::table('show_times')
                            ->join('shows', 'shows.id', '=', 'show_times.show_id')
                            ->join('venues', 'venues.id', '=', 'shows.venue_id')
                            ->leftJoin('purchases', 'show_times.id', '=', 'purchases.show_time_id')
                            ->select(DB::raw('show_times.id, shows.name, show_times.show_time, COUNT(purchases.id) AS purchases'))
                            ->whereIn('venues.id', $venues_edit)
                            ->whereDate('show_times.show_time','>=', $start_date)
                            ->whereDate('show_times.show_time','<=', $end_date)
                            ->where('venues.is_featured', '>', 0)
                            ->where('shows.is_active', '>', 0)
                            ->where('show_times.is_active', '>', 0)
                            ->groupBy('show_times.id')->orderBy('show_times.show_time','DESC')->get();
                return ['success'=>true,'events'=>$events];
            }
            //send report
            else
            {  
                if(!empty($input['show_times']) && !empty($input['email']))
                {
                    $msg = '';
                    foreach ($input['show_times'] as $e)
                    {
                        $showtime = ShowTime::find($e);
                        if($showtime)
                        {
                            //email to sent to
                            if($input['email']=='v')
                                $email = $showtime->show->venue->weekly_email;
                            else if($input['email']=='v')
                                $email = $showtime->show->emails;
                            else
                                $email = null;
                            //checkings
                            if(empty($email))
                                $msg .= '<br><b>Event #'.$e.'</b> has no email to sent the report to.';
                            else
                            {
                                $control = new ReportSalesController(0,0);
                                $response = $control->event($e,$email);
                                if($response)
                                    $msg .= '<br><b>Event #'.$e.'</b> report was sent successfully.';
                                else
                                    $msg .= '<br><b>Event #'.$e.'</b> there was an error sending the report by email.';
                            }
                        }
                        else
                            $msg .= '<br><b>Event #'.$e.'</b> is not longer available in the system.';
                    }
                    return ['success'=>true,'msg'=>$msg];
                }
                return ['success'=>false,'msg'=>'You must select some events to create the report!'];
            }
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
       
}
