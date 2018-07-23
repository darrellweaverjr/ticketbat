<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Purchase;

/**
 * Manage ACLs
 *
 * @author ivan
 */
class NGCBController extends Controller{
    /**
     * Return default view.
     *
     * @return view
     */
    public function index()
    {
        try {
            //init
            $input = Input::all();
            $data = $total = array();
            //conditions to search
            $data = Purchase::filter_options('REPORTS', $input, '-7');
            $where = $data['where'];
            $search = $data['search'];
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
 
            //return view
            return view('admin.ngcb.index',compact('data','total','search'));
        } catch (Exception $ex) {
            throw new Exception('Error NGCB Index: '.$ex->getMessage());
        }
    }
}
