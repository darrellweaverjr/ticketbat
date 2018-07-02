<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Purchase;

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
        //load first and default module depending of permission, logout if none or not valid user
        if(Auth::check()
            && in_array(Auth::user()->user_type_id,explode(',',env('ADMIN_LOGIN_USER_TYPE')))
            && !empty(Auth::user()->user_type->getACLs()) )
        {
            $permits = Auth::user()->user_type->getACLs();
            if(!empty($permits['REPORTS']))
                return redirect('/admin/dashboard/ticket_sales');
            if(!empty($permits['NGCB']))
                return redirect('/admin/ngcb');
            if(!empty($permits['USERS']))
                return redirect('/admin/users');
            if(!empty($permits['BANDS']))
                return redirect('/admin/bands');
            if(!empty($permits['VENUES']))
                return redirect('/admin/venues');
            if(!empty($permits['SHOWS']))
                return redirect('/admin/shows');
            if(!empty($permits['TYPES']))
                return redirect('/admin/ticket_types');
            if(!empty($permits['CATEGORIES']))
                return redirect('/admin/categories');
            if(!empty($permits['COUPONS']))
                return redirect('/admin/coupons');
            if(!empty($permits['PACKAGES']))
                return redirect('/admin/packages');            
            if(!empty($permits['MANIFESTS']))
                return redirect('/admin/manifests');
            if(!empty($permits['CONTACTS']))
                return redirect('/admin/contacts');
            if(!empty($permits['PURCHASES']))
                return redirect('/admin/purchases');
            if(!empty($permits['SLIDERS']))
                return redirect('/admin/sliders');
            if(!empty($permits['CONSIGNMENTS']))
                return redirect('/admin/consignments');
            if(!empty($permits['ACLS']))
                return redirect('/admin/acls');
            if(!empty($permits['RESTAURANTS']))
                return redirect('/admin/restaurants');
            
        }
        return redirect()->route('logout');
    }
    
    //clear date sold for comparisons
    public static function clear_date_sold($where)
    {
        return array_filter($where, function($value){
            if (strstr($value[0], 'purchases.created') !== false)
               return false;
            return true;
        });
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
            $data = Purchase::filter_options('REPORTS', $input, '-7');
            $where = $data['where'];
            $search = $data['search'];
            //coupon's report
            if(!empty($search['coupon_report']))
                $coupons = $this->coupons($data);
            //credit
            if(!in_array($search['statu'], ['Refunded','Chargeback']))
            {
                $credit = DB::table('purchases')
                        ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                        ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                        ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                        ->join('users', 'users.id', '=' ,'purchases.user_id')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                        ->leftJoin('transactions', 'transactions.id', '=' ,'purchases.transaction_id')
                        ->leftJoin('transaction_refunds', function($join){
                            $join->on('transaction_refunds.purchase_id', '=', 'purchases.id')
                                 ->where('transaction_refunds.result','=','Approved');
                        })
                        ->select(DB::raw('purchases.id, CONCAT(customers.first_name," ",customers.last_name) as name, customers.email,  shows.name AS show_name, 
                                          purchases.created, show_times.show_time, discounts.code, venues.name AS venue_name, tickets.inclusive_fee,
                                          ( CASE WHEN (discounts.discount_type = "N for N") THEN "BOGO"
                                                 WHEN (purchases.payment_type="None") THEN "Comp."
                                                 ELSE purchases.payment_type END ) AS method, tickets.ticket_type, packages.title,
                                          transactions.card_holder, transactions.last_4, transactions.invoice_num,
                                          purchases.price_paid AS amount, 
                                          transactions.authcode, 
                                          transactions.refnum,
                                          COUNT(purchases.id) AS purchases, purchases.status, purchases.channel, purchases.note,
                                          SUM(purchases.quantity) AS tickets,  purchases.retail_price, purchases.printed_fee,
                                          SUM( IF(purchases.inclusive_fee>0, 0 , purchases.processing_fee) ) AS fees, purchases.savings*-1 AS savings,
                                          SUM( IF(purchases.inclusive_fee>0, 
                                            purchases.price_paid-purchases.retail_price-purchases.sales_taxes+purchases.savings-purchases.printed_fee,
                                            purchases.price_paid-purchases.processing_fee-purchases.retail_price-purchases.sales_taxes+purchases.savings-purchases.printed_fee )) AS other,
                                          purchases.sales_taxes, purchases.price_paid, purchases.cc_fees, 
                                          ROUND(purchases.price_paid-purchases.processing_fee-purchases.commission_percent-purchases.cc_fees-purchases.printed_fee,2) AS to_show,
                                          SUM( IF(purchases.inclusive_fee>0, purchases.processing_fee, 0) ) AS fees_incl,
                                          SUM( IF(purchases.inclusive_fee>0, 0, purchases.processing_fee) ) AS fees_over,
                                          purchases.commission_percent AS commissions,
                                          ROUND(purchases.processing_fee+purchases.commission_percent+purchases.printed_fee,2) AS profit, 1 AS display'))
                        ->where($where)
                        ->groupBy('purchases.id')->orderBy('purchases.id','DESC')->get();  
                $credit_ = $credit->toArray();
                $total_c = ['purchases'=>array_sum(array_column($credit_,'purchases')),
                        'tickets'=>array_sum(array_column($credit_,'tickets')),
                        'savings'=>array_sum(array_column($credit_,'savings')),
                        'sales_taxes'=>array_sum(array_column($credit_,'sales_taxes')),
                        'price_paid'=>array_sum(array_column($credit_,'amount')),
                        'cc_fees'=>array_sum(array_column($credit_,'cc_fees')),
                        'printed_fee'=>array_sum(array_column($credit_,'printed_fee')),
                        'to_show'=>array_sum(array_column($credit_,'to_show')),
                        'fees_incl'=>array_sum(array_column($credit_,'fees_incl')),
                        'fees_over'=>array_sum(array_column($credit_,'fees_over')),
                        'commissions'=>array_sum(array_column($credit_,'commissions')),
                        'profit'=>array_sum(array_column($credit_,'profit'))];   
            }
            else
                $total_c = [  'purchases'=>0,'tickets'=>0,'savings'=>0,'sales_taxes'=>0,'price_paid'=>0,'cc_fees'=>0,'printed_fee'=>0,'to_show'=>0,'fees_incl'=>0,'fees_over'=>0,'commissions'=>0,'profit'=>0 ];
            
            //debit
            if(in_array($search['statu'], ['','Refunded','Chargeback']))
            {
                $debit = DB::table('purchases')
                        ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                        ->join('packages', 'packages.id', '=' ,'tickets.package_id')
                        ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                        ->join('users', 'users.id', '=' ,'purchases.user_id')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                        ->join('transaction_refunds', function($join){
                            $join->on('transaction_refunds.purchase_id', '=', 'purchases.id')
                                 ->where('transaction_refunds.result','=','Approved');
                        })
                        ->leftJoin('transactions', 'transactions.id', '=' ,'purchases.transaction_id')
                        ->select(DB::raw('purchases.id, CONCAT(customers.first_name," ",customers.last_name) as name, customers.email,  shows.name AS show_name, 
                                          transaction_refunds.created, show_times.show_time, discounts.code, venues.name AS venue_name, tickets.inclusive_fee,
                                          ( CASE WHEN (discounts.discount_type = "N for N") THEN "BOGO"
                                                 WHEN (purchases.payment_type="None") THEN "Comp."
                                                 ELSE purchases.payment_type END ) AS method, tickets.ticket_type, packages.title,
                                          transactions.card_holder, transactions.last_4, transactions.invoice_num,
                                          transaction_refunds.amount*-1 AS amount, 
                                          transaction_refunds.authcode, 
                                          transaction_refunds.ref_num AS refnum,
                                          COUNT(transaction_refunds.id)*-1 AS purchases, purchases.status, purchases.channel, purchases.note,
                                          SUM(transaction_refunds.quantity)*-1 AS tickets,  transaction_refunds.retail_price*-1 AS retail_price, transaction_refunds.printed_fee*-1 AS printed_fee,
                                          SUM( IF(purchases.inclusive_fee>0, 0 , transaction_refunds.processing_fee*-1) ) AS fees, transaction_refunds.savings,
                                          SUM( IF(purchases.inclusive_fee>0, 
                                            (transaction_refunds.amount-transaction_refunds.retail_price-transaction_refunds.sales_taxes+transaction_refunds.savings-transaction_refunds.printed_fee)*-1,
                                            (transaction_refunds.amount-transaction_refunds.processing_fee-transaction_refunds.retail_price-transaction_refunds.sales_taxes+transaction_refunds.savings-transaction_refunds.printed_fee)*-1 )) AS other,
                                          transaction_refunds.sales_taxes*-1 AS sales_taxes, transaction_refunds.amount*-1 AS price_paid, purchases.cc_fees*-1 AS cc_fees, 
                                          ROUND(transaction_refunds.amount-transaction_refunds.processing_fee-transaction_refunds.commission_percent-purchases.cc_fees-transaction_refunds.printed_fee,2)*-1 AS to_show,
                                          SUM( IF(purchases.inclusive_fee>0, transaction_refunds.processing_fee*-1, 0) ) AS fees_incl,
                                          SUM( IF(purchases.inclusive_fee>0, 0, transaction_refunds.processing_fee*-1) ) AS fees_over,
                                          transaction_refunds.commission_percent*-1 AS commissions,
                                          ROUND(transaction_refunds.processing_fee+transaction_refunds.commission_percent+transaction_refunds.printed_fee,2)*-1 AS profit, -1 AS display'))
                        ->where(DashboardController::clear_date_sold($where))
                        ->where(function($query) use ($search) {
                            if(!empty($search['soldtime_start_date']) && !empty($search['soldtime_end_date']))
                            {
                                $start_date = date('Y-m-d H:i:s', strtotime($search['soldtime_start_date']));
                                $end_date = date('Y-m-d H:i:s', strtotime($search['soldtime_end_date']));
                                $query->whereBetween(DB::raw('DATE(transaction_refunds.created)'),[$start_date,$end_date]);
                            }
                        })
                        ->where(function($query) {
                            $query->where('purchases.status','=','Refunded')
                                  ->orWhere('purchases.status','=','Chargeback');
                        })
                        ->groupBy('purchases.id')->groupBy('transaction_refunds.id')
                        ->orderBy('purchases.id','DESC')->orderBy('transaction_refunds.id','DESC')->get(); 
                $debit_ = $debit->toArray(); 
                $total_d = ['purchases_'=>array_sum(array_column($debit_,'purchases')),
                        'tickets_'=>array_sum(array_column($debit_,'tickets')),
                        'savings_'=>array_sum(array_column($debit_,'savings')),
                        'sales_taxes_'=>array_sum(array_column($debit_,'sales_taxes')),
                        'price_paid_'=>array_sum(array_column($debit_,'amount')),
                        'cc_fees_'=>array_sum(array_column($debit_,'cc_fees')),
                        'printed_fee_'=>array_sum(array_column($debit_,'printed_fee')),
                        'to_show_'=>array_sum(array_column($debit_,'to_show')),
                        'fees_incl_'=>array_sum(array_column($debit_,'fees_incl')),
                        'fees_over_'=>array_sum(array_column($debit_,'fees_over')),
                        'commissions_'=>array_sum(array_column($debit_,'commissions')),
                        'profit_'=>array_sum(array_column($debit_,'profit')) ];  
            }
            else
                $total_d = [  'purchases_'=>0,'tickets_'=>0,'savings_'=>0,'sales_taxes_'=>0,'price_paid_'=>0,'cc_fees_'=>0,'printed_fee_'=>0,'to_show_'=>0,'fees_incl_'=>0,'fees_over_'=>0,'commissions_'=>0,'profit_'=>0 ]; 
                      
            if(isset($credit))
            {
                if(isset($debit))
                    $data = $credit->merge($debit)->toArray();
                else
                    $data = $credit->toArray();                    
            }
            else if(isset($debit))
                $data = $debit->toArray(); 
            else
                $data = [];            
            $total = array_merge($total_c,$total_d);   
            
            //calculate totals
            function calc_totals($data)
            {
                return [  'purchases'=>array_sum(array_column($data,'purchases')),
                        'tickets'=>array_sum(array_column($data,'tickets')),
                        'savings'=>array_sum(array_column($data,'savings')),
                        'sales_taxes'=>array_sum(array_column($data,'sales_taxes')),
                        'price_paid'=>array_sum(array_column($data,'price_paid')),
                        'cc_fees'=>array_sum(array_column($data,'cc_fees')),
                        'printed_fee'=>array_sum(array_column($data,'printed_fee')),
                        'to_show'=>array_sum(array_column($data,'to_show')),
                        'fees_incl'=>array_sum(array_column($data,'fees_incl')),
                        'fees_over'=>array_sum(array_column($data,'fees_over')),
                        'commissions'=>array_sum(array_column($data,'commissions')),
                        'refunds'=>array_sum(array_column($data,'refunds')),
                        'profit'=>array_sum(array_column($data,'profit')) ];
            }     
            
            //calculate summary table according to period
            function cal_summary($period,$where,$search,$type='previous')
            {
                //start date
                if(strtotime($search['soldtime_start_date']))
                {
                    $start_date = date('Y-m-d',strtotime($search['soldtime_start_date']));
                    $start_period = date('M jS Y',strtotime($start_date));
                }
                else
                {
                    $start_date = null;
                    $start_period = '&#8734;';
                }
                //end date
                if(strtotime($search['soldtime_end_date']))
                {
                    $end_date = date('Y-m-d',strtotime($search['soldtime_end_date']));
                    $end_period = date('M jS Y',strtotime($end_date));
                }
                else
                {
                    $end_date = null;
                    $end_period = '&#8734;';
                }
                //title
                $title = 'Current: <i>( '.$start_period.' - '.$end_period.' )</i>';
                if(!empty($period))
                {
                    if(!empty($start_date) && !empty($end_date))
                    {
                        if($type=='previous_period')
                        {
                            //calculate date range according to period
                            $start_date = strtotime($start_date);
                            $end_date = strtotime($end_date);
                            $diff_days = floor(($end_date-$start_date) / (60*60*24));
                            //if full month
                            if(  date('Y-m-d',strtotime('first day of this month',$start_date)) == $search['soldtime_start_date']
                              && date('Y-m-d',strtotime('last day of this month',$end_date)) == $search['soldtime_end_date'] )
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
                        }
                        else if($type=='previous_year')
                        {
                            //calculate date range according to yearly
                            $start_date = strtotime($start_date.' -'.$period.' year');
                            $end_date = strtotime($end_date.' -'.$period.' year');
                            $diff_days = floor(($end_date-$start_date) / (60*60*24));
                            //if full month
                            if(  date('Y-m-d',strtotime('first day of this month',$start_date)) ==$start_date
                              && date('Y-m-d',strtotime('last day of this month',$end_date)) == $end_date )
                            {
                                $start_date = date('Y-m-d',strtotime('first day of this month '.$period.' months ago',$start_date));
                                $end_date = date('Y-m-d',strtotime('last day of this month '.$period.' months ago',$end_date));
                            }
                            else
                            {
                                $start_date = date('Y-m-d',$start_date);
                                $end_date = date('Y-m-d',$end_date);
                            }
                        }
                        else return ['title'=>$title,'table'=>[]];

                        //set up new date period
                        $title = 'Period '.$period.': <i>( '.date('M jS Y',strtotime($start_date)).' - '.date('M jS Y',strtotime($end_date)).' )</i>';
                    }
                    else return ['title'=>$title,'table'=>[]];
                }
                
                $summary_table = [];
                $subtotals = $consignment = ['purchases'=>0,'tickets'=>0,'price_paid'=>0,'savings'=>0,'to_show'=>0,'commissions'=>0,'fees_incl'=>0,'fees_over'=>0,
                                             'profit'=>0,'sales_taxes'=>0,'cc_fees'=>0,'printed_fee'=>0];
                $summary_credit = DB::table('purchases')
                            ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                            ->join('users', 'users.id', '=' ,'purchases.user_id')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                            ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->leftJoin('transactions', 'transactions.id', '=' ,'purchases.transaction_id')
                            ->leftJoin('transaction_refunds', function($join){
                                $join->on('transaction_refunds.purchase_id', '=', 'purchases.id')
                                     ->where('transaction_refunds.result','=','Approved');
                            })
                            ->select(DB::raw('( CASE WHEN (discounts.discount_type = "N for N") THEN "BOGO"
                                                     WHEN (purchases.payment_type="None") THEN "Comp."
                                                     ELSE purchases.payment_type END ) AS method, purchases.channel,
                                              COUNT(purchases.id) AS purchases,
                                              SUM(purchases.quantity) AS tickets, 
                                              SUM(purchases.commission_percent+purchases.processing_fee+purchases.printed_fee) AS profit,
                                              SUM(purchases.price_paid) AS price_paid,
                                              SUM(purchases.savings) AS savings,
                                              SUM(purchases.cc_fees) AS cc_fees, SUM(purchases.printed_fee) AS printed_fee,
                                              SUM(purchases.sales_taxes) AS sales_taxes,
                                              IF(purchases.inclusive_fee>0, SUM(purchases.processing_fee) , 0) AS fees_incl,
                                              IF(purchases.inclusive_fee>0, 0, SUM(purchases.processing_fee) ) AS fees_over,
                                              SUM(purchases.price_paid-purchases.commission_percent-purchases.processing_fee-purchases.cc_fees-purchases.printed_fee) AS to_show,
                                              SUM(purchases.commission_percent) AS commissions'))
                            ->where(DashboardController::clear_date_sold($where));
                if(!empty($start_date) && !empty($end_date))
                    $summary_credit = $summary_credit->whereBetween(DB::raw('DATE(purchases.created)'),[$start_date,$end_date]);
                $summary_credit = $summary_credit->groupBy('channel','method')->orderBy('channel','method')->get()->toArray();
                
                foreach ($summary_credit as $d)
                {
                    $current = ['purchases'=>$d->purchases,'tickets'=>$d->tickets,'price_paid'=>$d->price_paid,'savings'=>$d->savings,'sales_taxes'=>$d->sales_taxes,
                                'cc_fees'=>$d->cc_fees,'to_show'=>$d->to_show,'commissions'=>$d->commissions,'fees_incl'=>$d->fees_incl,'fees_over'=>$d->fees_over,
                                'profit'=>$d->profit,'printed_fee'=>$d->printed_fee];
                    if($d->channel == 'Consignment')
                        $consignment = calc_totals([$consignment,$current]);
                    else
                    {
                        $summary_table[$d->channel.' - '.$d->method] = $current;
                        $subtotals = calc_totals([$subtotals,$current]);
                    }
                }
                $summary_table['Subtotals'] = $subtotals;
                $summary_table['Consignment'] = $consignment;
                $summary_table['Totals'] = calc_totals([$consignment,$subtotals]);
                $summary_debit = DB::table('purchases')
                            ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                            ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                            ->join('users', 'users.id', '=' ,'purchases.user_id')
                            ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                            ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                            ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                            ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                            ->join('transaction_refunds', function($join){
                                $join->on('transaction_refunds.purchase_id', '=', 'purchases.id')
                                     ->where('transaction_refunds.result','=','Approved');
                            })
                            ->leftJoin('transactions', 'transactions.id', '=' ,'purchases.transaction_id')
                            ->select(DB::raw('COUNT(transaction_refunds.id)*-1 AS purchases,
                                              COALESCE(SUM(transaction_refunds.quantity), 0)*-1 AS tickets, 
                                              COALESCE(SUM(transaction_refunds.commission_percent+transaction_refunds.processing_fee+transaction_refunds.printed_fee), 0)*-1 AS profit,
                                              COALESCE(SUM(transaction_refunds.amount), 0)*-1 AS price_paid,
                                              COALESCE(SUM(transaction_refunds.savings), 0)*-1 AS savings,
                                              COALESCE(SUM(purchases.cc_fees), 0)*-1 AS cc_fees, COALESCE(SUM(transaction_refunds.printed_fee), 0)*-1 AS printed_fee,
                                              COALESCE(SUM(transaction_refunds.sales_taxes), 0)*-1 AS sales_taxes,
                                              IF(purchases.inclusive_fee>0, COALESCE(SUM(transaction_refunds.processing_fee), 0), 0)*-1 AS fees_incl,
                                              IF(purchases.inclusive_fee>0, 0, COALESCE(SUM(transaction_refunds.processing_fee), 0) )*-1 AS fees_over,
                                              COALESCE(SUM(transaction_refunds.amount-transaction_refunds.commission_percent-transaction_refunds.processing_fee-purchases.cc_fees-transaction_refunds.printed_fee), 0)*-1 AS to_show,
                                              COALESCE(SUM(transaction_refunds.commission_percent), 0)*-1 AS commissions'))
                            ->where(DashboardController::clear_date_sold($where));
                if(!empty($start_date) && !empty($end_date))
                    $summary_debit = $summary_debit->whereBetween(DB::raw('DATE(transaction_refunds.created)'),[$start_date,$end_date]);
                $summary_debit = (array)$summary_debit->first();
                
                $summary_table['- Ref&Chargbk'] = $summary_debit;
                $summary_table['Grand Total'] = calc_totals([$summary_table['Totals'],$summary_debit]);
                
                return ['title'=>$title,'table'=>$summary_table];
            }
            for ($i=0;$i<=$search['mirror_period'];$i++)
                $summary[] = cal_summary($i,$where,$search,$search['mirror_type']);
            //remove conditios of date for the graph, to show 1 year ago
            $where = DashboardController::clear_date_sold($where);
            $start = date('Y-m-d', strtotime('-1 year'));
            $where[] = [DB::raw('DATE(purchases.created)'),'>=',$start];
            //info for the graph
            $graph_credit= DB::table('purchases')
                    ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                    ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                    ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                    ->join('users', 'users.id', '=' ,'purchases.user_id')
                    ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                    ->leftJoin('transactions', 'transactions.id', '=' ,'purchases.transaction_id')
                    ->leftJoin('transaction_refunds', function($join){
                        $join->on('transaction_refunds.purchase_id', '=', 'purchases.id')
                             ->where('transaction_refunds.result','=','Approved');
                    })
                    ->select(DB::raw('DATE_FORMAT(purchases.created,"%b %Y") AS purchased,
                                    COUNT(purchases.id) AS qty,
                                    SUM(purchases.commission_percent+purchases.processing_fee+purchases.printed_fee) AS amount'))
                    ->where($where)
                    ->whereRaw(DB::raw('DATE_FORMAT(purchases.created,"%Y%m") >= '.$start))
                    ->groupBy(DB::raw('DATE_FORMAT(purchases.created,"%Y%m")'))->get()->toJson(); 
            $graph_debit = DB::table('purchases')
                    ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                    ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                    ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                    ->join('users', 'users.id', '=' ,'purchases.user_id')
                    ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                    ->join('transaction_refunds', function($join){
                        $join->on('transaction_refunds.purchase_id', '=', 'purchases.id')
                             ->where('transaction_refunds.result','=','Approved');
                    })
                    ->leftJoin('transactions', 'transactions.id', '=' ,'purchases.transaction_id')
                    ->select(DB::raw('DATE_FORMAT(transaction_refunds.created,"%b %Y") AS purchased,
                                    COUNT(transaction_refunds.id) AS qty,
                                    SUM(transaction_refunds.commission_percent+transaction_refunds.processing_fee+transaction_refunds.printed_fee) AS amount'))
                    ->where($where)
                    ->whereRaw(DB::raw('DATE_FORMAT(transaction_refunds.created,"%Y%m") >= '.$start))
                    ->groupBy(DB::raw('DATE_FORMAT(transaction_refunds.created,"%Y%m")'))->get()->toJson();  
            //return view
                   // dd($graph_credit);
            return view('admin.dashboard.ticket_sales',compact('data','total','graph_credit','graph_debit','summary','coupons','search'));
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
            $data = (!empty($info))? $info : Purchase::filter_options('REPORTS', $input, '-7');
            $where = $data['where'];
            $where[] = ['discounts.id','!=',1];
            $search = $data['search'];
            //get all records
            $data = DB::table('discounts')
                    ->leftJoin('purchases', 'discounts.id', '=' ,'purchases.discount_id')
                    ->leftJoin('users', 'users.id', '=' ,'purchases.user_id')
                    ->leftJoin('customers', 'customers.id', '=' ,'purchases.customer_id')
                    ->leftJoin('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                    ->leftJoin('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                    ->leftJoin('shows', 'shows.id', '=' ,'show_times.show_id')
                    ->leftJoin('venues', 'venues.id', '=' ,'shows.venue_id')
                    ->leftJoin('transactions', 'transactions.id', '=' ,'purchases.transaction_id')
                    ->leftJoin('transaction_refunds', function($join){
                        $join->on('transaction_refunds.purchase_id', '=', 'purchases.id')
                             ->where('transaction_refunds.result','=','Approved');
                    })
                    ->select(DB::raw('COALESCE(shows.name,"-") AS show_name, COUNT(purchases.id)-COUNT(transaction_refunds.id) AS purchases,
                                    COALESCE(venues.name,"-") AS venue_name, discounts.code,
                                    discounts.distributed_at, discounts.description,discounts.start_date,discounts.end_date, purchases.id,
                                    COALESCE((SELECT SUM(pp.quantity) FROM purchases pp INNER JOIN show_times stt ON stt.id = pp.show_time_id
                                              WHERE stt.show_id = shows.id AND pp.discount_id = purchases.discount_id
                                              AND DATE(pp.created)>=DATE_SUB(CURDATE(),INTERVAL 1 DAY)),0) AS tickets_one,
                                    COALESCE((SELECT SUM(pp.quantity) FROM purchases pp INNER JOIN show_times stt ON stt.id = pp.show_time_id
                                              WHERE stt.show_id = shows.id AND pp.discount_id = purchases.discount_id
                                              AND DATE(pp.created)>=DATE_SUB(CURDATE(),INTERVAL 7 DAY)),0) AS tickets_seven,
                                    SUM(purchases.quantity)-SUM(COALESCE(transaction_refunds.quantity,0)) AS tickets, 
                                    SUM(purchases.price_paid)-SUM(COALESCE(transaction_refunds.amount,0)) AS price_paids,
                                    SUM(purchases.retail_price)-SUM(COALESCE(transaction_refunds.retail_price,0)) AS retail_prices,
                                    SUM(purchases.price_paid)-SUM(COALESCE(transaction_refunds.amount,0)) AS revenue,
                                    SUM(purchases.savings)-SUM(COALESCE(transaction_refunds.savings,0)) AS discounts,
                                    SUM(purchases.cc_fees) AS cc_fees,
                                    SUM(purchases.printed_fee)-SUM(COALESCE(transaction_refunds.printed_fee,0)) AS printed_fee,  
                                    SUM(purchases.sales_taxes)-SUM(COALESCE(transaction_refunds.sales_taxes,0)) AS sales_taxes,
                                    IF(purchases.inclusive_fee>0, SUM(purchases.processing_fee)-SUM(COALESCE(transaction_refunds.processing_fee,0)), 0) AS fees_incl,
                                    IF(purchases.inclusive_fee>0, 0, SUM(purchases.processing_fee)-SUM(COALESCE(transaction_refunds.processing_fee,0))) AS fees_over,
                                    SUM(purchases.price_paid-purchases.processing_fee-purchases.commission_percent-purchases.cc_fees-purchases.printed_fee) 
                                    -SUM(COALESCE(transaction_refunds.amount-transaction_refunds.commission_percent-transaction_refunds.processing_fee-purchases.cc_fees-transaction_refunds.printed_fee,0)) AS to_show,
                                    SUM(purchases.commission_percent)-SUM(COALESCE(transaction_refunds.commission_percent,0)) AS commissions'))
                    ->where(DashboardController::clear_date_sold($where));
            //conditions
            if(!empty($search['soldtime_start_date']) && !empty($search['soldtime_end_date']))
            {
                $data->where(DB::raw('DATE(discounts.end_date)'),'>=',date('Y-m-d',strtotime($search['soldtime_end_date'])));
                $data->where(function($query) use ($search) {
                    $query->where(DB::raw('DATE(purchases.created)'),'>=',date('Y-m-d',strtotime($search['soldtime_start_date'])))
                          ->orWhereNull('purchases.id');
                });
                $data->where(DB::raw('DATE(discounts.start_date)'),'<=',date('Y-m-d',strtotime($search['soldtime_start_date'])));
                $data->where(function($query) use ($search) {
                    $query->where(DB::raw('DATE(purchases.created)'),'<=',date('Y-m-d',strtotime($search['soldtime_end_date'])))
                          ->orWhereNull('purchases.id');
                });
            }
            $data = $data->groupBy('venues.id','shows.id','discounts.id')->orderBy('tickets','DESC')
                         ->orderBy('discounts.code','ASC')->orderBy('show_name','ASC')->get()->toArray();
            //calculate totals
            $total = array( 'purchases'=>array_sum(array_column($data,'purchases')),
                            'tickets'=>array_sum(array_column($data,'tickets')),
                            'price_paids'=>array_sum(array_column($data,'price_paids')),
                            'retail_prices'=>array_sum(array_column($data,'retail_prices')),
                            'revenue'=>array_sum(array_column($data,'revenue')),
                            'printed_fee'=>array_sum(array_column($data,'printed_fee')),
                            'discounts'=>array_sum(array_column($data,'discounts')),
                            'fees_incl'=>array_sum(array_column($data,'fees_incl')),
                            'fees_over'=>array_sum(array_column($data,'fees_over')),
                            'to_show'=>array_sum(array_column($data,'to_show')),
                            'cc_fees'=>array_sum(array_column($data,'cc_fees')),
                            'sales_taxes'=>array_sum(array_column($data,'sales_taxes')),
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
            $data = Purchase::filter_options('REPORTS', $input, 0);
            //enable only valid purchase status
            foreach ($data['search']['status'] as $k=>$v)
                if($v!='Active' && !(strpos($v,'Pending')===0))
                    unset($data['search']['status'][$k]);
            $where = $data['where'];
            $where[] = ['show_times.show_time','>',$current];
            $search = $data['search'];
            //get all records
            $data = DB::table('purchases')
                        ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                        ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                        ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                        ->join('users', 'users.id', '=' ,'purchases.user_id')
                        ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                        ->leftJoin('transactions', 'transactions.id', '=' ,'purchases.transaction_id')
                        ->leftJoin('transaction_refunds', function($join){
                            $join->on('transaction_refunds.purchase_id', '=', 'purchases.id')
                                 ->where('transaction_refunds.result','=','Approved');
                        })
                        ->select(DB::raw('shows.id, shows.name, COUNT(purchases.id)-COUNT(transaction_refunds.id) AS purchases, venues.name AS venue_name,
                                    SUM(purchases.quantity)-SUM(COALESCE(transaction_refunds.quantity,0)) AS tickets,
                                    SUM(purchases.price_paid)-SUM(COALESCE(transaction_refunds.amount,0)) AS price_paids,
                                    SUM(purchases.retail_price)-SUM(COALESCE(transaction_refunds.retail_price,0)) AS retail_prices,
                                    IF(purchases.inclusive_fee>0, 
                                        SUM(purchases.retail_price-purchases.savings)-SUM(COALESCE(transaction_refunds.retail_price,0))-SUM(COALESCE(transaction_refunds.savings,0)), 
                                        SUM(purchases.retail_price-purchases.savings+purchases.processing_fee)-SUM(COALESCE(transaction_refunds.retail_price,0))-SUM(COALESCE(transaction_refunds.savings,0))-SUM(COALESCE(transaction_refunds.processing_fee,0)) ) AS revenue,
                                    SUM(purchases.savings)-SUM(COALESCE(transaction_refunds.savings,0)) AS discounts,
                                    SUM(purchases.cc_fees) AS cc_fees,
                                    SUM(purchases.printed_fee)-SUM(COALESCE(transaction_refunds.printed_fee,0)) AS printed_fee,
                                    SUM(purchases.sales_taxes)-SUM(COALESCE(purchases.sales_taxes,0)) AS sales_taxes,
                                    IF(purchases.inclusive_fee>0, SUM(purchases.processing_fee)-SUM(COALESCE(transaction_refunds.processing_fee,0)), 0) AS fees_incl,
                                    IF(purchases.inclusive_fee>0, 0, SUM(purchases.processing_fee)-SUM(COALESCE(transaction_refunds.processing_fee,0))) AS fees_over,
                                    SUM(purchases.price_paid-purchases.commission_percent-purchases.processing_fee-purchases.cc_fees-purchases.printed_fee) 
                                    -SUM(COALESCE(transaction_refunds.amount-transaction_refunds.commission_percent-transaction_refunds.processing_fee-purchases.cc_fees-transaction_refunds.printed_fee,0)) AS to_show,
                                    SUM(purchases.commission_percent)-SUM(COALESCE(transaction_refunds.commission_percent,0)) AS commissions '))
                        ->where($where)
                        ->orderBy('shows.name')->groupBy('shows.id')->get()->toArray();
            //calculate totals
            $total = array( 'purchases'=>array_sum(array_column($data,'purchases')),
                            'tickets'=>array_sum(array_column($data,'tickets')),
                            'price_paids'=>array_sum(array_column($data,'price_paids')),
                            'retail_prices'=>array_sum(array_column($data,'retail_prices')),
                            'revenue'=>array_sum(array_column($data,'revenue')),
                            'printed_fee'=>array_sum(array_column($data,'printed_fee')),
                            'discounts'=>array_sum(array_column($data,'discounts')),
                            'fees_incl'=>array_sum(array_column($data,'fees_incl')),
                            'fees_over'=>array_sum(array_column($data,'fees_over')),
                            'to_show'=>array_sum(array_column($data,'to_show')),
                            'cc_fees'=>array_sum(array_column($data,'cc_fees')),
                            'sales_taxes'=>array_sum(array_column($data,'sales_taxes')),
                            'commissions'=>array_sum(array_column($data,'commissions')));
            //return view
            return view('admin.dashboard.future_liabilities',compact('data','total','search'));
        } catch (Exception $ex) {
            throw new Exception('Error Dashboard Future Liabilities: '.$ex->getMessage());
        }
    }

    /**
     * Show the Referrals report on the dashboard.
     *
     * @return view
     */
    public function channels()
    {
        try {
            //init
            $input = Input::all();
            $data = $total = array();
            //conditions to search
            $data = Purchase::filter_options('REPORTS', $input, '-30');
            $where = $data['where'];
            $search = $data['search'];
            //search arrange by order url or show
            if(isset($input) && isset($input['order']) && $input['order']=='channel')
            {
                $order = 'channel';
                $groupby = 'channel,show_name';
                $orderby = 'channel,show_name';
            }
            else
            {
                $order = 'show';
                $groupby = 'show_name,channel';
                $orderby = 'show_name,channel';
            }
            $search['order'] = $order;
            $data = DB::table('purchases')
                    ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                    ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                    ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                    ->join('venues', 'venues.id', '=' ,'shows.venue_id')
                    ->join('users', 'users.id', '=' ,'purchases.user_id')
                    ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                    ->leftJoin('transactions', 'transactions.id', '=' ,'purchases.transaction_id')
                    ->leftJoin('transaction_refunds', function($join){
                        $join->on('transaction_refunds.purchase_id', '=', 'purchases.id')
                             ->where('transaction_refunds.result','=','Approved');
                    })
                    ->select(DB::raw('shows.id, shows.name AS show_name, COUNT(purchases.id)-COUNT(transaction_refunds.id) AS purchases, venues.name AS venue_name, purchases.channel,
                                    SUM(purchases.quantity)-SUM(COALESCE(transaction_refunds.quantity,0)) AS tickets,
                                    SUM(purchases.price_paid)-SUM(COALESCE(transaction_refunds.amount,0)) AS price_paids,
                                    SUM(purchases.retail_price)-SUM(COALESCE(transaction_refunds.retail_price,0)) AS retail_prices,
                                    IF(purchases.inclusive_fee>0, 
                                        SUM(purchases.retail_price-purchases.savings)-SUM(COALESCE(transaction_refunds.retail_price,0))-SUM(COALESCE(transaction_refunds.savings,0)), 
                                        SUM(purchases.retail_price-purchases.savings+purchases.processing_fee)-SUM(COALESCE(transaction_refunds.retail_price,0))-SUM(COALESCE(transaction_refunds.savings,0))-SUM(COALESCE(transaction_refunds.processing_fee,0)) ) AS revenue,
                                    SUM(purchases.savings)-SUM(COALESCE(transaction_refunds.savings,0)) AS discounts,
                                    SUM(purchases.cc_fees) AS cc_fees,
                                    SUM(purchases.printed_fee)-SUM(COALESCE(transaction_refunds.printed_fee,0)) AS printed_fee,
                                    SUM(purchases.sales_taxes)-SUM(COALESCE(purchases.sales_taxes,0)) AS sales_taxes,
                                    IF(purchases.inclusive_fee>0, SUM(purchases.processing_fee)-SUM(COALESCE(transaction_refunds.processing_fee,0)), 0) AS fees_incl,
                                    IF(purchases.inclusive_fee>0, 0, SUM(purchases.processing_fee)-SUM(COALESCE(transaction_refunds.processing_fee,0))) AS fees_over,
                                    SUM(purchases.price_paid-purchases.commission_percent-purchases.processing_fee-purchases.cc_fees-purchases.printed_fee) 
                                    -SUM(COALESCE(transaction_refunds.amount-transaction_refunds.commission_percent-transaction_refunds.processing_fee-purchases.cc_fees-transaction_refunds.printed_fee,0)) AS to_show,
                                    SUM(purchases.commission_percent)-SUM(COALESCE(transaction_refunds.commission_percent,0)) AS commissions '))
                    ->where($where)
                    ->groupBy(DB::raw($groupby))->orderBy(DB::raw($orderby))->get()->toArray();
            //info for the graph
            if($order=='channel')
                $groupby = 'channel';
            else
                $groupby = 'shows.id';
            $graph['channel'] = DB::table('purchases')
                    ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                    ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                    ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                    ->join('users', 'users.id', '=' ,'purchases.user_id')
                    ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                    ->leftJoin('transactions', 'transactions.id', '=' ,'purchases.transaction_id')
                    ->leftJoin('transaction_refunds', function($join){
                        $join->on('transaction_refunds.purchase_id', '=', 'purchases.id')
                             ->where('transaction_refunds.result','=','Approved');
                    })
                    ->select(DB::raw('SUM(purchases.processing_fee+purchases.commission_percent+purchases.printed_fee)
                                     -SUM(COALESCE(transaction_refunds.commission_percent+transaction_refunds.processing_fee+transaction_refunds.printed_fee,0)) AS amount, purchases.channel'))
                    ->where($where)
                    ->groupBy('channel')->orderBy('amount','ASC')->distinct()->get()->toJson();
            $graph['show'] = DB::table('purchases')
                    ->join('tickets', 'tickets.id', '=' ,'purchases.ticket_id')
                    ->join('show_times', 'show_times.id', '=' ,'purchases.show_time_id')
                    ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                    ->join('users', 'users.id', '=' ,'purchases.user_id')
                    ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                    ->leftJoin('transactions', 'transactions.id', '=' ,'purchases.transaction_id')
                    ->leftJoin('transaction_refunds', function($join){
                        $join->on('transaction_refunds.purchase_id', '=', 'purchases.id')
                             ->where('transaction_refunds.result','=','Approved');
                    })
                    ->select(DB::raw('SUM(purchases.processing_fee+purchases.commission_percent+purchases.printed_fee)
                                     -SUM(COALESCE(transaction_refunds.commission_percent+transaction_refunds.processing_fee+transaction_refunds.printed_fee,0)) AS amount, shows.name AS show_name'))
                    ->where($where)
                    ->groupBy('show_name')->orderBy('amount','ASC')->distinct()->get()->toJson();
            //calculate totals
            $total = array( 'purchases'=>array_sum(array_column($data,'purchases')),
                            'tickets'=>array_sum(array_column($data,'tickets')),
                            'price_paids'=>array_sum(array_column($data,'price_paids')),
                            'retail_prices'=>array_sum(array_column($data,'retail_prices')),
                            'revenue'=>array_sum(array_column($data,'revenue')),
                            'printed_fee'=>array_sum(array_column($data,'printed_fee')),
                            'discounts'=>array_sum(array_column($data,'discounts')),
                            'fees_incl'=>array_sum(array_column($data,'fees_incl')),
                            'fees_over'=>array_sum(array_column($data,'fees_over')),
                            'to_show'=>array_sum(array_column($data,'to_show')),
                            'cc_fees'=>array_sum(array_column($data,'cc_fees')),
                            'sales_taxes'=>array_sum(array_column($data,'sales_taxes')),
                            'commissions'=>array_sum(array_column($data,'commissions')));
            //return view
            return view('admin.dashboard.channels',compact('data','total','graph','search'));
        } catch (Exception $ex) {
            throw new Exception('Error Dashboard Channels: '.$ex->getMessage());
        }
    }
    
    /**
     * Show the Seller report on the dashboard.
     *
     * @return view
     */
    public function sellers()
    {
        try {
            //init
            $input = Input::all();
            $data = $total = array();
            //conditions to search
            $data = Purchase::filter_options('REPORTS', $input, '-7');
            $where = $data['where'];
            $search = $data['search'];
            $data = DB::table('seller_tally')
                    ->join('users', 'users.id', '=' ,'seller_tally.user_id')
                    ->leftJoin('purchases', function($join){
                        $join->on('seller_tally.user_id', '=', 'purchases.user_id')
                             ->on('seller_tally.time_in','<=','purchases.created')
                             ->on('seller_tally.time_out','>=','purchases.created');
                    })
                    ->leftJoin('transaction_refunds', function($join){
                        $join->on('transaction_refunds.purchase_id', '=', 'purchases.id')
                             ->where('transaction_refunds.result','=','Approved');
                    })
                    ->select(DB::raw('users.email, seller_tally.*, 
                                     COUNT(purchases.id) AS t_trans, SUM(COALESCE(purchases.quantity,0)) AS t_tick, SUM(COALESCE(purchases.price_paid,0)) AS t_tot, 
                                     COUNT( IF(purchases.payment_type="Cash",purchases.id,null) ) AS s_trans, SUM(COALESCE(IF(purchases.payment_type="Cash",purchases.quantity,null),0)) AS s_tick, SUM(COALESCE(IF(purchases.payment_type="Cash",purchases.price_paid,null),0)) AS s_tot, 
                                     COUNT( IF(purchases.payment_type="Credit",purchases.id,null) ) AS c_trans, SUM(COALESCE(IF(purchases.payment_type="Credit",purchases.quantity,null),0)) AS c_tick, SUM(COALESCE(IF(purchases.payment_type="Credit",purchases.price_paid,null),0)) AS c_tot, 
                                     COUNT(transaction_refunds.id) AS r_trans, SUM(COALESCE(transaction_refunds.quantity,0)) AS r_tick, SUM(COALESCE(transaction_refunds.amount,0)) AS r_tot'))
                    ->where(DashboardController::clear_date_sold($where));
                    
            //conditions
            if(!empty($search['soldtime_start_date']) && !empty($search['soldtime_end_date']))
            {
                $data->where(DB::raw('DATE(seller_tally.time_in)'),'>=',date('Y-m-d',strtotime($search['soldtime_start_date'])))
                     ->where(DB::raw('DATE(seller_tally.time_in)'),'<=',date('Y-m-d',strtotime($search['soldtime_end_date'])));
            }                    
            $data = $data->groupBy('seller_tally.id')->orderBy('seller_tally.id','DESC')->get()->toArray();
            //calculate totals
            $total = array( 't_trans'=>array_sum(array_column($data,'t_trans')),
                            't_tick'=>array_sum(array_column($data,'t_tick')),
                            't_tot'=>array_sum(array_column($data,'t_tot')),
                            's_trans'=>array_sum(array_column($data,'s_trans')),
                            's_tick'=>array_sum(array_column($data,'s_tick')),
                            's_tot'=>array_sum(array_column($data,'s_tot')),
                            'c_trans'=>array_sum(array_column($data,'c_trans')),
                            'c_tick'=>array_sum(array_column($data,'c_tick')),
                            'c_tot'=>array_sum(array_column($data,'c_tot')),
                            'r_trans'=>array_sum(array_column($data,'r_trans')),
                            'r_tick'=>array_sum(array_column($data,'r_tick')),
                            'r_tot'=>array_sum(array_column($data,'r_tot')));
            //return view
            return view('admin.dashboard.sellers',compact('data','total','search'));
        } catch (Exception $ex) {
            throw new Exception('Error Dashboard Channels: '.$ex->getMessage());
        }
    }

}
