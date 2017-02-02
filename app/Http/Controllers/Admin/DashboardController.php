<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Venue;
use App\Http\Models\Show;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the default method on the dashboard.
     *
     * @return Method
     */
    public function index()
    {
        return $this->ticket_sales();
    }
    
    /**
     * Show the ticket sales report on the dashboard.
     *
     * @return view
     */
    public function ticket_sales()
    {
        try {
            //init
            $input = Input::all();
            $data = $total = array();
            //conditions to search
            $where = [['purchases.status','=','Active']];
            //search venue
            if(isset($input) && isset($input['venue']))
            {
                $venue = $input['venue'];
                if($venue != '')
                    $where[] = ['shows.venue_id','=',$venue];
            }
            else
                $venue = '';
            //search show
            if(isset($input) && isset($input['show']))
            {
                $show = $input['show'];
                if($show != '')
                    $where[] = ['shows.id','=',$show];
            }
            else
                $show = '';
            //search showtime
            if(isset($input) && isset($input['showtime_start_date']) && isset($input['showtime_end_date']))
            {
                $showtime_start_date = $input['showtime_start_date'];
                $showtime_end_date = $input['showtime_end_date'];
                if($showtime_start_date != '' && $showtime_end_date != '')
                {
                    $where[] = ['show_times.show_time','>=',$showtime_start_date];
                    $where[] = ['show_times.show_time','<=',$showtime_end_date];
                }    
            }
            else
            {
                $showtime_start_date = '';
                $showtime_end_date = '';
            }
            //search soldtime
            if(isset($input) && isset($input['soldtime_start_date']) && isset($input['soldtime_end_date']))
            {
                $soldtime_start_date = $input['soldtime_start_date'];
                $soldtime_end_date = $input['soldtime_end_date'];
                if($soldtime_start_date != '' && $soldtime_end_date != '')
                {
                    $where[] = ['purchases.created','>=',$soldtime_start_date];
                    $where[] = ['purchases.created','<=',$soldtime_end_date];
                }    
            }
            else
            {
                $soldtime_start_date = '';
                $soldtime_end_date = '';
            }
            //if 5(only his report), if 1 or 6(all reports), others check a 0 result query
            if(Auth::user()->user_type->id == 5)
                $where[] = ['shows.create_user_id','=',Auth::user()->id];
            else if(Auth::user()->user_type->id != 1 && Auth::user()->user_type->id != 6)
                $where[] = ['shows.create_user_id','=',0];   
            //get all records        
            $data = DB::table('purchases')
                        ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                        ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                        ->select(DB::raw('purchases.id, CONCAT(customers.first_name," ",customers.last_name) as name, shows.name AS show_name, 
                                          tickets.ticket_type, purchases.created, show_times.show_time, discounts.code,
                                          SUM(purchases.quantity) AS tickets, 
                                          SUM(ROUND(purchases.price_paid,2)) AS price_paids, 
                                          SUM(ROUND(purchases.retail_price,2)) AS retail_prices, 
                                          SUM(ROUND(purchases.savings,2)) AS discounts, 
                                          SUM(ROUND(purchases.processing_fee,2)) AS fees, 
                                          SUM(ROUND((purchases.retail_price-purchases.savings+purchases.processing_fee)-purchases.commission_percent-purchases.processing_fee,2)) AS to_show, 
                                          SUM(ROUND(purchases.commission_percent,2)) AS commissions'))
                        ->where($where)
                        ->orderBy('purchases.created','DESC')->groupBy('purchases.id')->get()->toArray();
            //calculate totals
            $total = array( 'tickets'=>array_sum(array_column($data,'tickets')),
                            'price_paids'=>array_sum(array_column($data,'price_paids')),
                            'retail_prices'=>array_sum(array_column($data,'retail_prices')),
                            'discounts'=>array_sum(array_column($data,'discounts')),
                            'fees'=>array_sum(array_column($data,'fees')),
                            'to_show'=>array_sum(array_column($data,'to_show')),
                            'commissions'=>array_sum(array_column($data,'commissions')));
            $venues = Venue::all('id','name');
            $shows = Show::all('id','name','venue_id');
            //return view
            return view('admin.dashboard.ticket_sales',compact('data','total','venues','shows','venue','show','showtime_start_date','showtime_end_date','soldtime_start_date','soldtime_end_date'));
        } catch (Exception $ex) {
            throw new Exception('Error Dashboard Ticket Sales: '.$ex->getMessage());
        }
    }
    
    /**
     * Show the chargeback report on the dashboard.
     *
     * @return view
     */
    public function chargebacks()
    {
        try {
            //init
            $input = Input::all();
            $data = $total = array();
            //conditions to search
            $where = [['purchases.status','=','Chargeback']];
            //search venue
            if(isset($input) && isset($input['venue']))
            {
                $venue = $input['venue'];
                if($venue != '')
                    $where[] = ['shows.venue_id','=',$venue];
            }
            else
                $venue = '';
            //search show
            if(isset($input) && isset($input['show']))
            {
                $show = $input['show'];
                if($show != '')
                    $where[] = ['shows.id','=',$show];
            }
            else
                $show = '';
            //search showtime
            if(isset($input) && isset($input['showtime_start_date']) && isset($input['showtime_end_date']))
            {
                $showtime_start_date = $input['showtime_start_date'];
                $showtime_end_date = $input['showtime_end_date'];
                if($showtime_start_date != '' && $showtime_end_date != '')
                {
                    $where[] = ['show_times.show_time','>=',$showtime_start_date];
                    $where[] = ['show_times.show_time','<=',$showtime_end_date];
                }    
            }
            else
            {
                $showtime_start_date = '';
                $showtime_end_date = '';
            }
            //search soldtime
            if(isset($input) && isset($input['soldtime_start_date']) && isset($input['soldtime_end_date']))
            {
                $soldtime_start_date = $input['soldtime_start_date'];
                $soldtime_end_date = $input['soldtime_end_date'];
                if($soldtime_start_date != '' && $soldtime_end_date != '')
                {
                    $where[] = ['purchases.created','>=',$soldtime_start_date];
                    $where[] = ['purchases.created','<=',$soldtime_end_date];
                }    
            }
            else
            {
                $soldtime_start_date = '';
                $soldtime_end_date = '';
            }
            //if 5(only his report), if 1 or 6(all reports), others check a 0 result query
            if(Auth::user()->user_type->id == 5)
                $where[] = ['shows.create_user_id','=',Auth::user()->id];
            else if(Auth::user()->user_type->id != 1 && Auth::user()->user_type->id != 6)
                $where[] = ['shows.create_user_id','=',0]; 
            //get all records        
            $data = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->join('transactions', 'transactions.id', '=' ,'purchases.transaction_id')
                        ->select(DB::raw('purchases.id, COALESCE(transactions.card_holder,CONCAT(customers.first_name," ",customers.last_name)) AS card_holder, 
                                          COALESCE(transactions.refnum,0) AS refnum, COALESCE(transactions.amount,0) AS amount, COALESCE(transactions.authcode,0) AS authcode, 
                                          shows.name AS show_name, show_times.show_time, purchases.status AS status,
                                          purchases.quantity AS tickets, purchases.transaction_id, purchases.ticket_type, purchases.created, purchases.note '))
                        ->where($where)
                        ->orderBy('purchases.created','DESC')->groupBy('purchases.id')->get()->toArray();
            //calculate totals
            $total = array( 'amount'=>array_sum(array_column($data,'amount')),
                            'tickets'=>array_sum(array_column($data,'tickets')));
            $venues = Venue::all('id','name');
            $shows = Show::all('id','name','venue_id');
            //return view
            return view('admin.dashboard.chargebacks',compact('data','total','venues','shows','venue','show','showtime_start_date','showtime_end_date','soldtime_start_date','soldtime_end_date'));
        } catch (Exception $ex) {
            throw new Exception('Error Dashboard Chargebacks: '.$ex->getMessage());
        }
    }
    
    /**
     * Show the future_liabilities report on the dashboard.
     *
     * @return view
     */
    public function future_liabilities()
    {
        try {
            //init
            $input = Input::all();
            $data = $total = array();
            $current = date('Y-m-d H:i:s');
            //conditions to search
            $where = [['purchases.status','=','Active']];
            $where = [['show_times.show_time','>',$current]];
            //search venue
            if(isset($input) && isset($input['venue']))
            {
                $venue = $input['venue'];
                if($venue != '')
                    $where[] = ['shows.venue_id','=',$venue];
            }
            else
                $venue = '';
            //search show
            if(isset($input) && isset($input['show']))
            {
                $show = $input['show'];
                if($show != '')
                    $where[] = ['shows.id','=',$show];
            }
            else
                $show = '';
            //search showtime
            if(isset($input) && isset($input['showtime_start_date']) && isset($input['showtime_end_date']))
            {
                $showtime_start_date = $input['showtime_start_date'];
                $showtime_end_date = $input['showtime_end_date'];
                if($showtime_start_date != '' && $showtime_end_date != '')
                {
                    $where[] = ['show_times.show_time','>=',$showtime_start_date];
                    $where[] = ['show_times.show_time','<=',$showtime_end_date];
                }    
            }
            else
            {
                $showtime_start_date = '';
                $showtime_end_date = '';
            }
            //search soldtime
            if(isset($input) && isset($input['soldtime_start_date']) && isset($input['soldtime_end_date']))
            {
                $soldtime_start_date = $input['soldtime_start_date'];
                $soldtime_end_date = $input['soldtime_end_date'];
                if($soldtime_start_date != '' && $soldtime_end_date != '')
                {
                    $where[] = ['purchases.created','>=',$soldtime_start_date];
                    $where[] = ['purchases.created','<=',$soldtime_end_date];
                }    
            }
            else
            {
                $soldtime_start_date = '';
                $soldtime_end_date = '';
            }
            //if 5(only his report), if 1 or 6(all reports), others check a 0 result query
            if(Auth::user()->user_type->id == 5)
                $where[] = ['shows.create_user_id','=',Auth::user()->id];
            else if(Auth::user()->user_type->id != 1 && Auth::user()->user_type->id != 6)
                $where[] = ['shows.create_user_id','=',0]; 
            //get all records        
            $data = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->select(DB::raw('shows.id, shows.name, COUNT(purchases.id) AS purchases, 
                                    SUM(purchases.quantity) AS tickets, 
                                    SUM(ROUND(purchases.price_paid,2)) AS price_paids, 
                                    SUM(ROUND(purchases.retail_price,2)) AS retail_prices, 
                                    SUM(ROUND(purchases.savings,2)) AS discounts, 
                                    SUM(ROUND(purchases.processing_fee,2)) AS fees, 
                                    SUM(ROUND((purchases.retail_price-purchases.savings+purchases.processing_fee)-purchases.commission_percent-purchases.processing_fee,2)) AS to_show,
                                    SUM(ROUND(purchases.commission_percent,2)) AS commissions '))
                        ->where($where)
                        ->orderBy('shows.name')->groupBy('shows.id')->get()->toArray();
            //calculate totals
            $total = array( 'purchases'=>array_sum(array_column($data,'purchases')),
                            'tickets'=>array_sum(array_column($data,'tickets')),
                            'price_paids'=>array_sum(array_column($data,'price_paids')),
                            'retail_prices'=>array_sum(array_column($data,'retail_prices')),
                            'discounts'=>array_sum(array_column($data,'discounts')),
                            'fees'=>array_sum(array_column($data,'fees')),
                            'to_show'=>array_sum(array_column($data,'to_show')),
                            'commissions'=>array_sum(array_column($data,'commissions')));
            $venues = Venue::all('id','name');
            $shows = Show::all('id','name','venue_id');
            //return view
            return view('admin.dashboard.future_liabilities',compact('data','total','venues','shows','venue','show','showtime_start_date','showtime_end_date','soldtime_start_date','soldtime_end_date'));
        } catch (Exception $ex) {
            throw new Exception('Error Dashboard Future Liabilities: '.$ex->getMessage());
        }
    }
    
    /**
     * Show the Trend and Pace report on the dashboard.
     *
     * @return view
     */
    public function trend_pace()
    {
        try {
            //init
            $input = Input::all();
            $data = $total = $graph = array();
            //conditions to search
            $where = [['purchases.status','=','Active']];
            //search venue
            if(isset($input) && isset($input['venue']))
            {
                $venue = $input['venue'];
                if($venue != '')
                    $where[] = ['shows.venue_id','=',$venue];
            }
            else
                $venue = '';
            //search show
            if(isset($input) && isset($input['show']))
            {
                $show = $input['show'];
                if($show != '')
                    $where[] = ['shows.id','=',$show];
            }
            else
                $show = '';
            //search showtime
            if(isset($input) && isset($input['showtime_start_date']) && isset($input['showtime_end_date']))
            {
                $showtime_start_date = $input['showtime_start_date'];
                $showtime_end_date = $input['showtime_end_date'];
                if($showtime_start_date != '' && $showtime_end_date != '')
                {
                    $where[] = ['show_times.show_time','>=',$showtime_start_date];
                    $where[] = ['show_times.show_time','<=',$showtime_end_date];
                }    
            }
            else
            {
                $showtime_start_date = '';
                $showtime_end_date = '';
            }
            //search soldtime
            if(isset($input) && isset($input['soldtime_start_date']) && isset($input['soldtime_end_date']))
            {
                $soldtime_start_date = $input['soldtime_start_date'];
                $soldtime_end_date = $input['soldtime_end_date'];
                if($soldtime_start_date != '' && $soldtime_end_date != '')
                {
                    $where[] = ['purchases.created','>=',$soldtime_start_date];
                    $where[] = ['purchases.created','<=',$soldtime_end_date];
                }    
            }
            else
            {
                $soldtime_start_date = '';
                $soldtime_end_date = '';
            }
            //if 5(only his report), if 1 or 6(all reports), others check a 0 result query
            if(Auth::user()->user_type->id == 1 || Auth::user()->user_type->id == 6)
            {   
                //get all records        
                $data = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->select(DB::raw('shows.name AS show_name, COUNT(purchases.id) AS purchases,
                                        COALESCE((SELECT SUM(pp.quantity) FROM purchases pp INNER JOIN show_times stt ON stt.id = pp.show_time_id 
                                                  WHERE stt.show_id = shows.id AND DATE(pp.created)=DATE_SUB(CURDATE(),INTERVAL 1 DAY)),0) AS tickets_one,
                                        COALESCE((SELECT SUM(pp.quantity) FROM purchases pp INNER JOIN show_times stt ON stt.id = pp.show_time_id 
                                                  WHERE stt.show_id = shows.id AND DATE(pp.created)=DATE_SUB(CURDATE(),INTERVAL 2 DAY)),0) AS tickets_two,
                                        SUM(purchases.quantity) AS tickets, 
                                        SUM(ROUND(purchases.price_paid,2)) AS price_paids, 
                                        SUM(ROUND(purchases.retail_price,2)) AS retail_prices, 
                                        SUM(ROUND(purchases.savings,2)) AS discounts, 
                                        SUM(ROUND(purchases.processing_fee,2)) AS fees, 
                                        SUM(ROUND((purchases.retail_price-purchases.savings+purchases.processing_fee)-purchases.commission_percent-purchases.processing_fee,2)) AS to_show,
                                        SUM(ROUND(purchases.commission_percent,2)) AS commissions'))
                        ->where($where)
                        ->orderBy('shows.name')->groupBy('shows.id')->get()->toArray();
                //info for the graph 
                $start = date('Y-m-d', strtotime('-1 year'));
                $where[] = ['purchases.created','>=',$start];
                $graph = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->select(DB::raw('DATE_FORMAT(purchases.created,"%m/%Y") AS purchased, 
                                        SUM(purchases.quantity) AS qty_tickets, COUNT(purchases.id) AS qty_purchases, SUM(purchases.price_paid) AS amount'))
                        ->where($where)
                        ->whereRaw(DB::raw('DATE_FORMAT(purchases.created,"%Y%m") >= '.$start))
                        ->groupBy(DB::raw('DATE_FORMAT(purchases.created,"%Y%m")'))->get()->toJson();
            }
            //calculate totals
            $total = array( 'purchases'=>array_sum(array_column($data,'purchases')),
                            'tickets'=>array_sum(array_column($data,'tickets')),
                            'price_paids'=>array_sum(array_column($data,'price_paids')),
                            'retail_prices'=>array_sum(array_column($data,'retail_prices')),
                            'discounts'=>array_sum(array_column($data,'discounts')),
                            'fees'=>array_sum(array_column($data,'fees')),
                            'to_show'=>array_sum(array_column($data,'to_show')),
                            'commissions'=>array_sum(array_column($data,'commissions')));
            $venues = Venue::all('id','name');
            $shows = Show::all('id','name','venue_id');
            //return view
            return view('admin.dashboard.trend_pace',compact('data','total','graph','venues','shows','venue','show','showtime_start_date','showtime_end_date','soldtime_start_date','soldtime_end_date'));
        } catch (Exception $ex) {
            throw new Exception('Error Dashboard Trend and Pace: '.$ex->getMessage());
        }
    }
    
    /**
     * Show the Referrals report on the dashboard.
     *
     * @return view
     */
    public function referrals()
    {
        try {
            //init
            $input = Input::all();
            $data = $total = array();
            //conditions to search
            $where = [['purchases.status','=','Active']];
            //search venue
            if(isset($input) && isset($input['venue']))
            {
                $venue = $input['venue'];
                if($venue != '')
                    $where[] = ['shows.venue_id','=',$venue];
            }
            else
                $venue = '';
            //search show
            if(isset($input) && isset($input['show']))
            {
                $show = $input['show'];
                if($show != '')
                    $where[] = ['shows.id','=',$show];
            }
            else
                $show = '';
            //search showtime
            if(isset($input) && isset($input['showtime_start_date']) && isset($input['showtime_end_date']))
            {
                $showtime_start_date = $input['showtime_start_date'];
                $showtime_end_date = $input['showtime_end_date'];
                if($showtime_start_date != '' && $showtime_end_date != '')
                {
                    $where[] = ['show_times.show_time','>=',$showtime_start_date];
                    $where[] = ['show_times.show_time','<=',$showtime_end_date];
                }    
            }
            else
            {
                $showtime_start_date = '';
                $showtime_end_date = '';
            }
            //search soldtime
            if(isset($input) && isset($input['soldtime_start_date']) && isset($input['soldtime_end_date']))
            {
                $soldtime_start_date = $input['soldtime_start_date'];
                $soldtime_end_date = $input['soldtime_end_date'];
                if($soldtime_start_date != '' && $soldtime_end_date != '')
                {
                    $where[] = ['purchases.created','>=',$soldtime_start_date];
                    $where[] = ['purchases.created','<=',$soldtime_end_date];
                }    
            }
            else
            {
                $soldtime_start_date = '';
                $soldtime_end_date = '';
            }
            //search arrange by order url or show
            if(isset($input) && isset($input['order']) && $input['order']=='url')
            {
                $order = 'url';
                $groupby = 'referral_url,show_name';
                $orderby = 'referral_url,show_name';
            }    
            else
            {
                $order = 'show';
                $groupby = 'show_name,referral_url';
                $orderby = 'show_name,referral_url';
            }
            //if 5(only his report), if 1 or 6(all reports), others check a 0 result query
            if(Auth::user()->user_type->id == 1 || Auth::user()->user_type->id == 6)
            {   
                
                $data = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->select(DB::raw('shows.name AS show_name, COUNT(purchases.id) AS purchases,
                                        COALESCE(SUBSTRING_INDEX(SUBSTRING_INDEX(purchases.referrer_url, "://", -1),"/", 1), "-Not Registered-") AS referral_url,
                                        SUM(purchases.quantity) AS tickets, 
                                        SUM(ROUND(purchases.price_paid,2)) AS price_paids, 
                                        SUM(ROUND(purchases.retail_price,2)) AS retail_prices, 
                                        SUM(ROUND(purchases.savings,2)) AS discounts, 
                                        SUM(ROUND(purchases.processing_fee,2)) AS fees, 
                                        SUM(ROUND((purchases.retail_price-purchases.savings+purchases.processing_fee)-purchases.commission_percent-purchases.processing_fee,2)) AS to_show,
                                        SUM(ROUND(purchases.commission_percent,2)) AS commissions'))
                        ->where($where)
                        ->whereNotNull('purchases.referrer_url')
                        ->groupBy(DB::raw($groupby))->orderBy(DB::raw($orderby))->get()->toArray();
                //info for the graph 
                if($order=='url')
                    $groupby = 'referral_url';
                else
                    $groupby = 'shows.id';
                $graph = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->select(DB::raw('COALESCE(SUBSTRING_INDEX(SUBSTRING_INDEX(purchases.referrer_url, "://", -1),"/", 1), "-Not Registered-") AS referral_url,
                                          SUM(purchases.quantity) AS qty_tickets, SUM(purchases.price_paid) AS amount, shows.name AS show_name'))
                        ->where($where)
                        ->whereNotNull('purchases.referrer_url')
                        ->groupBy($groupby)->distinct()->get()->toJson();
            }
            //calculate totals
            $total = array( 'purchases'=>array_sum(array_column($data,'purchases')),
                            'tickets'=>array_sum(array_column($data,'tickets')),
                            'price_paids'=>array_sum(array_column($data,'price_paids')),
                            'retail_prices'=>array_sum(array_column($data,'retail_prices')),
                            'discounts'=>array_sum(array_column($data,'discounts')),
                            'fees'=>array_sum(array_column($data,'fees')),
                            'to_show'=>array_sum(array_column($data,'to_show')),
                            'commissions'=>array_sum(array_column($data,'commissions')));
            $venues = Venue::all('id','name');
            $shows = Show::all('id','name','venue_id');
            //return view
            return view('admin.dashboard.referrals',compact('data','total','graph','venues','shows','venue','show','showtime_start_date','showtime_end_date','soldtime_start_date','soldtime_end_date','order'));
        } catch (Exception $ex) {
            throw new Exception('Error Dashboard Referrals: '.$ex->getMessage());
        }
    }
    
}
