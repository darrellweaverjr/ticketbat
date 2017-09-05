<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Venue;
use App\Http\Models\Show;
use App\Http\Models\User;
use App\Http\Models\Util;

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
     * Makes where for queries in all report and search values.
     *
     * @return Method
     */
    private function search($input,$custom=null)
    {
        //init
        $data = ['where'=>[],'search'=>[]];
        $data['search']['venues'] = [];
        $data['search']['shows'] = [];
        $data['search']['payment_types'] = Util::getEnumValues('purchases','payment_type');
        $data['search']['users'] = User::get(['id','email']);
        //search venue
        if(isset($input) && isset($input['venue']))
        {
            $data['search']['venue'] = $input['venue'];
            if($data['search']['venue'] != '')
                $data['where'][] = ['shows.venue_id','=',$data['search']['venue']];
        }
        else
            $data['search']['venue'] = '';
        //search show
        if(isset($input) && isset($input['show']))
        {
            $data['search']['show'] = $input['show'];
            if($data['search']['show'] != '')
                $data['where'][] = ['shows.id','=',$data['search']['show']];
        }
        else
            $data['search']['show'] = '';
        //search showtime
        if(isset($input) && isset($input['showtime_start_date']) && isset($input['showtime_end_date']))
        {
            $data['search']['showtime_start_date'] = $input['showtime_start_date'];
            $data['search']['showtime_end_date'] = $input['showtime_end_date'];
        }
        else
        {
            $data['search']['showtime_start_date'] = $data['search']['showtime_end_date'] = '';
        }
        if($data['search']['showtime_start_date'] != '' && $data['search']['showtime_end_date'] != '')
        {
            $data['where'][] = [DB::raw('DATE(show_times.show_time)'),'>=',$data['search']['showtime_start_date']];
            $data['where'][] = [DB::raw('DATE(show_times.show_time)'),'<=',$data['search']['showtime_end_date']];
        } 
        //search soldtime
        if(isset($input) && isset($input['soldtime_start_date']) && isset($input['soldtime_end_date']))
        {
            $data['search']['soldtime_start_date'] = $input['soldtime_start_date'];
            $data['search']['soldtime_end_date'] = $input['soldtime_end_date'];
        }
        else
        {
            if($custom=='future')
            {
                $data['search']['soldtime_start_date'] = $data['search']['soldtime_end_date'] = '';           
            }
            else
            {
                $data['search']['soldtime_start_date'] = ($custom!='coupons')? date('Y-m-d', strtotime('-7 DAY')) : date('Y-m-d', strtotime('-7 DAY'));
                $data['search']['soldtime_end_date'] = date('Y-m-d');
            }
        }
        if($data['search']['soldtime_start_date'] != '' && $data['search']['soldtime_end_date'] != '' && $custom!='coupons')
        {
            $data['where'][] = [DB::raw('DATE(purchases.created)'),'>=',$data['search']['soldtime_start_date']];
            $data['where'][] = [DB::raw('DATE(purchases.created)'),'<=',$data['search']['soldtime_end_date']];
        }  
        //search payment types        
        if(isset($input) && isset($input['payment_type']) && !empty($input['payment_type']))
        {
            $data['search']['payment_type'] = $input['payment_type'];
        }
        else
        {
            $data['search']['payment_type'] = array_values($data['search']['payment_types']);
        }
        //search user      
        if(isset($input) && isset($input['payment_type']) && !empty($input['user']))
        {
            $data['search']['user'] = $input['user'];
            $data['where'][] = ['purchases.user_id','=',$data['search']['user']];
        }
        else
        {
            $data['search']['user'] = '';
        }
        //search printing
        if(isset($input) && isset($input['mirror_period']) && !empty($input['mirror_period']) && is_numeric($input['mirror_period']))
            $data['search']['mirror_period'] = $input['mirror_period'];
        else
            $data['search']['mirror_period'] = 0;
        
        if(isset($input) && isset($input['replace_chart']) && !empty($input['replace_chart']))
            $data['search']['replace_chart'] = 1;
        else
            $data['search']['replace_chart'] = 0;
        
        if(isset($input) && isset($input['coupon_report']) && !empty($input['coupon_report']))
            $data['search']['coupon_report'] = 1;
        else
            $data['search']['coupon_report'] = 0;
        //PERMISSIONS
        //if user has permission to view        
        if(in_array('View',Auth::user()->user_type->getACLs()['REPORTS']['permission_types']))
        {
            if(Auth::user()->user_type->getACLs()['REPORTS']['permission_scope'] != 'All')
            {
                if(!empty(Auth::user()->venues_edit) && count(explode(',',Auth::user()->venues_edit)))
                {
                    $data['where'][] = [DB::raw('shows.venue_id IN ('.Auth::user()->venues_edit.') OR shows.create_user_id'),'=',Auth::user()->id];
                    //add shows and venues for search
                    $data['search']['venues'] = Venue::whereIn('id',explode(',',Auth::user()->venues_edit))->orderBy('name')->get(['id','name']);
                    $data['search']['shows'] = Show::whereIn('venue_id',explode(',',Auth::user()->venues_edit))->orWhere('create_user_id',Auth::user()->id)->orderBy('name')->get(['id','name','venue_id']);
                } 
                else 
                    $data['where'][] = ['shows.create_user_id','=',Auth::user()->id];
            }  
            //all
            else 
            {
                //add shows and venues for search
                $data['search']['venues'] = Venue::orderBy('name')->get(['id','name']);
                $data['search']['shows'] = Show::orderBy('name')->get(['id','name','venue_id']);
            }  
        }
        else
            $data['where'][] = ['purchases.id','=',0];
        //return     
        return $data;
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
            $data = $total = $summary = $coupons = array();
            //conditions to search
            $data = $this->search($input);
            $where = $data['where'];
            $where[] = ['purchases.status','=','Active'];
            $search = $data['search'];
            //coupon's report
            if(!empty($search['coupon_report']))
                $coupons = $this->coupons($data);
            //get all records        
            $data = DB::table('purchases')
                        ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                        ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                        ->select(DB::raw('purchases.id, CONCAT(customers.first_name," ",customers.last_name) as name, shows.name AS show_name, 
                                          tickets.ticket_type, purchases.created, show_times.show_time, discounts.code, venues.name AS venue_name,
                                          (CASE WHEN (purchases.ticket_type = "Consignment") THEN purchases.ticket_type ELSE purchases.payment_type END) AS method,
                                          COUNT(purchases.id) AS purchases, 
                                          SUM(purchases.quantity) AS tickets, 
                                          SUM(ROUND(purchases.commission_percent+purchases.processing_fee,2)) AS profit, 
                                          SUM(ROUND(purchases.retail_price-purchases.savings+purchases.processing_fee,2)) AS revenue, 
                                          SUM(ROUND(purchases.savings,2)) AS discounts, 
                                          SUM(ROUND(purchases.processing_fee,2)) AS fees, 
                                          SUM(ROUND(purchases.retail_price-purchases.savings-purchases.commission_percent,2)) AS to_show, 
                                          SUM(ROUND(purchases.commission_percent,2)) AS commissions'))
                        ->where($where)
                        ->orderBy('purchases.created','DESC')->groupBy('purchases.id')
                        ->havingRaw('method IN ("'.implode('","',$search['payment_type']).'")')
                        ->get()->toArray();
            //calculate totals
            function calc_totals($data)
            {
                return array( 'purchases'=>array_sum(array_column($data,'purchases')),
                            'tickets'=>array_sum(array_column($data,'tickets')),
                            'profit'=>array_sum(array_column($data,'profit')),
                            'revenue'=>array_sum(array_column($data,'revenue')),
                            'discounts'=>array_sum(array_column($data,'discounts')),
                            'fees'=>array_sum(array_column($data,'fees')),
                            'to_show'=>array_sum(array_column($data,'to_show')),
                            'commissions'=>array_sum(array_column($data,'commissions')));
            }
            $total = calc_totals($data);
            //clear date sold for comparisons
            function clear_date_sold($where)
            {
                return array_filter($where, function($value){
                    if (strstr($value[0], 'purchases.created') !== false)
                       return false;
                    return true;
                });
            }
            //calculate summary table according to period
            function cal_summary($period,$where,$search)
            {
                $title = '';
                if(!empty($period))
                {
                    if(!empty($search['soldtime_start_date']) && !empty($search['soldtime_end_date']))
                    {
                        //calculate date range according to period
                        $start_date = strtotime($search['soldtime_start_date']);
                        $end_date = strtotime($search['soldtime_end_date']);
                        $diff_days = floor(($end_date-$start_date) / (60*60*24));
                        //if full month
                        if(  date('Y-m-d',strtotime('first day of this month',$start_date)) == $search['soldtime_start_date']
                          && date('Y-m-d',strtotime('last day of this month',$start_date)) == $search['soldtime_end_date'] )
                        {
                            $start_date = date('Y-m-d',strtotime('first day of this month '.$period.' months ago',$start_date));
                            $end_date = date('Y-m-d',strtotime('last day of this month '.$period.' months ago',$end_date));
                        }
                        else if(  date('Y-m-d',strtotime('first day of this year',$start_date)) == $search['soldtime_start_date']
                          && date('Y-m-d',strtotime('last day of this year',$end_date)) == $search['soldtime_end_date'] )
                        {
                            $start_date = date('Y-m-d',strtotime('first day of this year '.$period.' years ago',$start_date));
                            $end_date = date('Y-m-d',strtotime('last day of this year '.$period.' years ago',$end_date));
                        }
                        else
                        {
                            $diff_days = ($diff_days + 1) * $period;
                            $start_date = date('Y-m-d',strtotime('-'.$diff_days.' days',$start_date));
                            $end_date = date('Y-m-d',strtotime('-'.$diff_days.' days',$end_date));
                        }
                        //remove previous date comparison
                        $where = clear_date_sold($where);
                        //set up new date period
                        $where[] = [DB::raw('DATE(purchases.created)'),'>=',$start_date];
                        $where[] = [DB::raw('DATE(purchases.created)'),'<=',$end_date];
                        $title = 'Period <i>'.date('m/d/Y',strtotime($start_date)).' to '.date('m/d/Y',strtotime($end_date)).'</i>';
                    }
                    else return ['title'=>$title,'table'=>[]];
                } 
                else $title = 'Current <i>'.date('m/d/Y',strtotime($search['soldtime_start_date'])).' to '.date('m/d/Y',strtotime($search['soldtime_end_date'])).'</i>';
                
                $summary_table = [];
                $subtotals = ['purchases'=>0,'tickets'=>0,'revenue'=>0,'discounts'=>0,'to_show'=>0,'commissions'=>0,'fees'=>0,'profit'=>0];
                $consignment = ['purchases'=>0,'tickets'=>0,'revenue'=>0,'discounts'=>0,'to_show'=>0,'commissions'=>0,'fees'=>0,'profit'=>0];
                $summary_info = DB::table('purchases')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                            ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                            ->select(DB::raw('(CASE WHEN (purchases.ticket_type = "Consignment") THEN purchases.ticket_type ELSE purchases.payment_type END) AS method,
                                              COUNT(purchases.id) AS purchases, 
                                              SUM(purchases.quantity) AS tickets, 
                                              SUM(ROUND(purchases.commission_percent+purchases.processing_fee,2)) AS profit, 
                                              SUM(ROUND(purchases.retail_price-purchases.savings+purchases.processing_fee,2)) AS revenue, 
                                              SUM(ROUND(purchases.savings,2)) AS discounts, 
                                              SUM(ROUND(purchases.processing_fee,2)) AS fees, 
                                              SUM(ROUND(purchases.retail_price-purchases.savings-purchases.commission_percent,2)) AS to_show, 
                                              SUM(ROUND(purchases.commission_percent,2)) AS commissions'))
                            ->where($where)
                            ->orderBy('method')->groupBy('method')
                            ->havingRaw('method IN ("'.implode('","',$search['payment_type']).'")')
                            ->get()->toArray();            
                foreach ($summary_info as $d)
                {
                    $current = ['purchases'=>$d->purchases,'tickets'=>$d->tickets,'revenue'=>$d->revenue,'discounts'=>$d->discounts,
                                                'to_show'=>$d->to_show,'commissions'=>$d->commissions,'fees'=>$d->fees,'profit'=>$d->profit];
                    if($d->method == 'Consignment')
                        $consignment = calc_totals([$consignment,$current]);
                    else
                    {
                        $summary_table[$d->method] = $current;
                        $subtotals = calc_totals([$subtotals,$current]);
                    }
                }
                $summary_table['Subtotals'] = $subtotals;
                $summary_table['Consignment'] = $consignment;
                $summary_table['Totals'] = calc_totals([$consignment,$subtotals]);
                return ['title'=>$title,'table'=>$summary_table];
            }
            for ($i=0;$i<=$search['mirror_period'];$i++)
                $summary[] = cal_summary($i,$where,$search);
            //remove conditios of date for the graph, to show 1 year ago
            $where = clear_date_sold($where);
            $start = date('Y-m-d', strtotime('-1 year'));
            $where[] = ['purchases.created','>=',$start];
            //info for the graph
            $graph = DB::table('purchases')
                    ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                    ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                    ->select(DB::raw('DATE_FORMAT(purchases.created,"%b %Y") AS purchased, 
                                    SUM(purchases.quantity) AS qty, SUM(ROUND(purchases.commission_percent+purchases.processing_fee,2)) AS amount'))
                    ->where($where)
                    ->whereRaw(DB::raw('DATE_FORMAT(purchases.created,"%Y%m") >= '.$start))
                    ->groupBy(DB::raw('DATE_FORMAT(purchases.created,"%Y%m")'))->get()->toJson();            
            //return view
            return view('admin.dashboard.ticket_sales',compact('data','total','graph','summary','coupons','search'));
        } catch (Exception $ex) {
            throw new Exception('Error Dashboard Ticket Sales: '.$ex->getMessage());
        }
    }
    
    /**
     * Show the coupons report on the dashboard.
     *
     * @return view
     */
    public function coupons($info=null)
    {
        try {
            //init
            $input = Input::all();
            $data = $total = $graph = array();
            //conditions to search
            $data = (!empty($info))? $info : $this->search($input,'coupons');
            $where = $data['where'];
            $where[] = ['discounts.id','!=',1];
            $search = $data['search'];
            //get all records        
            $data = DB::table('discounts')
                    ->leftJoin('purchases', 'discounts.id', '=' ,'purchases.discount_id')
                    ->leftJoin('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                    ->leftJoin('shows', 'shows.id', '=' ,'show_times.show_id')
                    ->leftJoin('venues', 'venues.id', '=' ,'shows.venue_id')
                    ->select(DB::raw('COALESCE(shows.name,"-") AS show_name, COUNT(purchases.id) AS purchases, 
                                    COALESCE(venues.name,"-") AS venue_name, discounts.code,
                                    discounts.distributed_at, discounts.description,discounts.start_date,discounts.end_date, purchases.id,
                                    COALESCE((SELECT SUM(pp.quantity) FROM purchases pp INNER JOIN show_times stt ON stt.id = pp.show_time_id 
                                              WHERE stt.show_id = shows.id AND pp.discount_id = purchases.discount_id
                                              AND DATE(pp.created)>=DATE_SUB(CURDATE(),INTERVAL 1 DAY)),0) AS tickets_one,
                                    COALESCE((SELECT SUM(pp.quantity) FROM purchases pp INNER JOIN show_times stt ON stt.id = pp.show_time_id 
                                              WHERE stt.show_id = shows.id AND pp.discount_id = purchases.discount_id
                                              AND DATE(pp.created)>=DATE_SUB(CURDATE(),INTERVAL 7 DAY)),0) AS tickets_seven,
                                    SUM(purchases.quantity) AS tickets, 
                                    SUM(ROUND(purchases.price_paid,2)) AS price_paids, 
                                    SUM(ROUND(purchases.retail_price,2)) AS retail_prices, 
                                    SUM(ROUND(purchases.savings,2)) AS discounts, 
                                    SUM(ROUND(purchases.processing_fee,2)) AS fees, 
                                    SUM(ROUND(purchases.retail_price-purchases.savings-purchases.commission_percent,2)) AS to_show,
                                    SUM(ROUND(purchases.commission_percent,2)) AS commissions'))
                    ->where($where)
                    ->where(function($query) use ($search) {
                        $query->where('purchases.status','=','Active')
                              ->orWhereNull('purchases.id');
                    })
                    ->groupBy('venues.id','shows.id','discounts.id')->orderBy('tickets','DESC')->orderBy('discounts.code','ASC');
            //conditions            
            if(!empty($search['soldtime_start_date']) && !empty($search['soldtime_end_date']))
            {
                $data->where(DB::raw('DATE(discounts.end_date)'),'>=',$search['soldtime_start_date']);
                $data->where(function($query) use ($search) {
                    $query->where(DB::raw('DATE(purchases.created)'),'>=',$search['soldtime_start_date'])
                          ->orWhereNull('purchases.id');
                });
                $data->where(DB::raw('DATE(discounts.start_date)'),'<=',$search['soldtime_end_date']);
                $data->where(function($query) use ($search) {
                    $query->where(DB::raw('DATE(purchases.created)'),'<=',$search['soldtime_end_date'])
                          ->orWhereNull('purchases.id');
                });
            }
            $data = $data->get()->toArray();
            //calculate totals
            $total = array( 'purchases'=>array_sum(array_column($data,'purchases')),
                            'tickets'=>array_sum(array_column($data,'tickets')),
                            'price_paids'=>array_sum(array_column($data,'price_paids')),
                            'retail_prices'=>array_sum(array_column($data,'retail_prices')),
                            'discounts'=>array_sum(array_column($data,'discounts')),
                            'fees'=>array_sum(array_column($data,'fees')),
                            'to_show'=>array_sum(array_column($data,'to_show')),
                            'commissions'=>array_sum(array_column($data,'commissions')));
            //descriptions
            $descriptions = [];
            foreach ($data as $d)
                if(!isset($descriptions[$d->code]))
                    $descriptions[$d->code] = $d->description;
            //return view
            if(!empty($info))
                return compact('data','total','descriptions');
            return view('admin.dashboard.coupons',compact('data','total','descriptions','search'));
        } catch (Exception $ex) {
            throw new Exception('Error Dashboard Coupons: '.$ex->getMessage());
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
            $data = $this->search($input);
            $where = $data['where'];
            $where[] = ['purchases.status','=','Chargeback'];
            $search = $data['search'];
            //get all records        
            $data = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('transactions', 'transactions.id', '=' ,'purchases.transaction_id')
                        ->select(DB::raw('purchases.id, COALESCE(transactions.card_holder,CONCAT(customers.first_name," ",customers.last_name)) AS card_holder, 
                                          COALESCE(transactions.refnum,0) AS refnum, COALESCE(transactions.amount,0) AS amount, COALESCE(transactions.authcode,0) AS authcode, 
                                          shows.name AS show_name, show_times.show_time, purchases.status AS status, venues.name AS venue_name,
                                          purchases.quantity AS tickets, purchases.transaction_id, purchases.ticket_type, purchases.created, purchases.note '))
                        ->where($where)
                        ->orderBy('purchases.created','DESC')->groupBy('purchases.id')->get()->toArray();
            //calculate totals
            $total = array( 'amount'=>array_sum(array_column($data,'amount')),
                            'tickets'=>array_sum(array_column($data,'tickets')));
            //return view
            return view('admin.dashboard.chargebacks',compact('data','total','search'));
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
            $data = $this->search($input,'future');
            $where = $data['where'];
            $where[] = ['purchases.status','=','Active'];
            $where[] = ['show_times.show_time','>',$current];
            $search = $data['search'];
            //get all records        
            $data = DB::table('purchases')
                        ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->select(DB::raw('shows.id, shows.name, COUNT(purchases.id) AS purchases, venues.name AS venue_name,
                                    SUM(purchases.quantity) AS tickets, 
                                    SUM(ROUND(purchases.price_paid,2)) AS price_paids, 
                                    SUM(ROUND(purchases.retail_price,2)) AS retail_prices, 
                                    SUM(ROUND(purchases.savings,2)) AS discounts, 
                                    SUM(ROUND(purchases.processing_fee,2)) AS fees, 
                                    SUM(ROUND(purchases.retail_price-purchases.savings-purchases.commission_percent,2)) AS to_show,
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
            //return view
            return view('admin.dashboard.future_liabilities',compact('data','total','search'));
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
            $data = $this->search($input);
            $where = $data['where'];
            $where[] = ['purchases.status','=','Active'];
            $search = $data['search'];
            //get all records        
            $data = DB::table('purchases')
                    ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                    ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                    ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                    ->select(DB::raw('shows.name AS show_name, COUNT(purchases.id) AS purchases, show_times.show_time, venues.name AS venue_name,
                                    COALESCE((SELECT SUM(pp.quantity) FROM purchases pp INNER JOIN show_times stt ON stt.id = pp.show_time_id 
                                              WHERE stt.show_id = shows.id AND DATE(pp.created)=DATE_SUB(CURDATE(),INTERVAL 1 DAY)),0) AS tickets_one,
                                    COALESCE((SELECT SUM(pp.quantity) FROM purchases pp INNER JOIN show_times stt ON stt.id = pp.show_time_id 
                                              WHERE stt.show_id = shows.id AND DATE(pp.created)=DATE_SUB(CURDATE(),INTERVAL 2 DAY)),0) AS tickets_two,
                                    SUM(purchases.quantity) AS tickets, 
                                    SUM(ROUND(purchases.price_paid,2)) AS price_paids, 
                                    SUM(ROUND(purchases.retail_price,2)) AS retail_prices, 
                                    SUM(ROUND(purchases.savings,2)) AS discounts, 
                                    SUM(ROUND(purchases.processing_fee,2)) AS fees, 
                                    SUM(ROUND(purchases.retail_price-purchases.savings-purchases.commission_percent,2)) AS to_show,
                                    SUM(ROUND(purchases.commission_percent,2)) AS commissions'))
                    ->where($where)
                    ->orderBy('shows.name','show_times.show_time desc')->groupBy('show_times.id')->get()->toArray();
            //info for the graph 
            $start = date('Y-m-d', strtotime('-1 year'));
            $where[] = ['purchases.created','>=',$start];
            $graph = DB::table('purchases')
                    ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                    ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                    ->select(DB::raw('DATE_FORMAT(purchases.created,"%m/%Y") AS purchased, 
                                    SUM(purchases.quantity) AS qty_tickets, COUNT(purchases.id) AS qty_purchases, SUM(purchases.commission_percent+purchases.processing_fee) AS amount'))
                    ->where($where)
                    ->whereRaw(DB::raw('DATE_FORMAT(purchases.created,"%Y%m") >= '.$start))
                    ->groupBy(DB::raw('DATE_FORMAT(purchases.created,"%Y%m")'))->get()->toJson();
            //calculate totals
            $total = array( 'purchases'=>array_sum(array_column($data,'purchases')),
                            'tickets'=>array_sum(array_column($data,'tickets')),
                            'price_paids'=>array_sum(array_column($data,'price_paids')),
                            'retail_prices'=>array_sum(array_column($data,'retail_prices')),
                            'discounts'=>array_sum(array_column($data,'discounts')),
                            'fees'=>array_sum(array_column($data,'fees')),
                            'to_show'=>array_sum(array_column($data,'to_show')),
                            'commissions'=>array_sum(array_column($data,'commissions')));
            //return view
            return view('admin.dashboard.trend_pace',compact('data','total','graph','search'));
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
            $data = $this->search($input);
            $where = $data['where'];
            $where[] = ['purchases.status','=','Active'];
            $search = $data['search'];
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
            $search['order'] = $order;
            $data = DB::table('purchases')
                    ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                    ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                    ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                    ->select(DB::raw('shows.name AS show_name, COUNT(purchases.id) AS purchases, venues.name AS venue_name,
                                    COALESCE(SUBSTRING_INDEX(SUBSTRING_INDEX(purchases.referrer_url, "://", -1),"/", 1), "-Not Registered-") AS referral_url,
                                    SUM(purchases.quantity) AS tickets, 
                                    SUM(ROUND(purchases.price_paid,2)) AS price_paids, 
                                    SUM(ROUND(purchases.retail_price,2)) AS retail_prices, 
                                    SUM(ROUND(purchases.savings,2)) AS discounts, 
                                    SUM(ROUND(purchases.processing_fee,2)) AS fees, 
                                    SUM(ROUND(purchases.retail_price-purchases.savings-purchases.commission_percent,2)) AS to_show,
                                    SUM(ROUND(purchases.commission_percent,2)) AS commissions'))
                    ->where($where)
                    ->whereNotNull('purchases.referrer_url')
                    ->groupBy(DB::raw($groupby))->orderBy(DB::raw($orderby))->get()->toArray();
            //info for the graph 
            if($order=='url')
                $groupby = 'referral_url';
            else
                $groupby = 'shows.id';
            $graph['url'] = DB::table('purchases')
                    ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                    ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                    ->select(DB::raw('COALESCE(SUBSTRING_INDEX(SUBSTRING_INDEX(purchases.referrer_url, "://", -1),"/", 1), "-Not Registered-") AS referral_url,
                                      SUM(purchases.processing_fee+purchases.commission_percent) AS amount'))
                    ->where($where)
                    ->whereNotNull('purchases.referrer_url')
                    ->groupBy('referral_url')->orderBy('amount','ASC')->distinct()->get()->toJson();
            $graph['show'] = DB::table('purchases')
                    ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                    ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                    ->select(DB::raw('SUM(purchases.processing_fee+purchases.commission_percent) AS amount, shows.name AS show_name'))
                    ->where($where)
                    ->whereNotNull('purchases.referrer_url')
                    ->groupBy('show_name')->orderBy('amount','ASC')->distinct()->get()->toJson();
            //calculate totals
            $total = array( 'purchases'=>array_sum(array_column($data,'purchases')),
                            'tickets'=>array_sum(array_column($data,'tickets')),
                            'price_paids'=>array_sum(array_column($data,'price_paids')),
                            'retail_prices'=>array_sum(array_column($data,'retail_prices')),
                            'discounts'=>array_sum(array_column($data,'discounts')),
                            'fees'=>array_sum(array_column($data,'fees')),
                            'to_show'=>array_sum(array_column($data,'to_show')),
                            'commissions'=>array_sum(array_column($data,'commissions')));
            //return view
            return view('admin.dashboard.referrals',compact('data','total','graph','search'));
        } catch (Exception $ex) {
            throw new Exception('Error Dashboard Referrals: '.$ex->getMessage());
        }
    }
    
}
