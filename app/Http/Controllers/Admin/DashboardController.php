<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;

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
            $data_conditions = '';        
            //check field search
            if(isset($input['venue_id']) && $input['venue_id'])
                $data_conditions .= ' AND s.venue_id = '.$input['venue_id'];
            if(isset($input['show_id']) && $input['show_id'])
                $data_conditions .= ' AND s.id = '.$input['show_id'];
            if(isset($info['start_date']) && $info['start_date'])
                $data_conditions .= ' AND st.show_time >= "'.date_format(date_create($info['start_date']),'Y-m-d H:i:s').'"';
            if(isset($info['end_date']) && $info['end_date'])
                $data_conditions .= ' AND st.show_time <= "'.date_format(date_create($info['end_date']),'Y-m-d H:i:s').'"';
            //if 5(only his report), if 1 or 6(all reports), others check a 0 result query
            if(Auth::user()->user_type->id == 5)
                $data_conditions .= ' AND s.create_user_id = '.Auth::user()->id;
            else if(Auth::user()->user_type->id != 1 && Auth::user()->user_type->id != 6)
                $data_conditions .= ' AND s.create_user_id = 0';    
            //get all records        
            $data = DB::select('SELECT p.id, CONCAT(c.first_name," ", c.last_name) as name, s.name AS show_name, t.ticket_type, p.created, st.show_time, d.code, 
                            SUM(p.quantity) AS tickets, SUM(ROUND(p.price_paid,2)) AS total, SUM(ROUND(p.processing_fee,2)) AS fees, SUM(ROUND(p.savings,2)) AS savings, 
                            SUM(ROUND(p.retail_price-p.savings,2)) AS tickets_price,
                            SUM(ROUND((p.price_paid-p.processing_fee)*(1-(p.commission_percent/100)),2)) AS show_earned, 
                            SUM(ROUND((p.price_paid-p.processing_fee)*(p.commission_percent/100),2)) AS commission_earned 
                            FROM purchases p 
                            INNER JOIN tickets t ON t.id = p.ticket_id 
                            INNER JOIN show_times st on st.id = p.show_time_id 
                            INNER JOIN customers c on p.customer_id = c.id 
                            INNER JOIN shows s on st.show_id = s.id 
                            INNER JOIN discounts d on p.discount_id = d.id 
                            WHERE p.status = "Active" '.$data_conditions.'
                            GROUP BY p.id ORDER BY p.created');
            //calculate totals
            $total = array( 'tickets'=>array_sum(array_column($data,'tickets')),
                            'total'=>array_sum(array_column($data,'total')),
                            'fees'=>array_sum(array_column($data,'fees')),
                            'savings'=>array_sum(array_column($data,'savings')),
                            'tickets_price'=>array_sum(array_column($data,'tickets_price')),
                            'show_earned'=>array_sum(array_column($data,'show_earned')),
                            'commission_earned'=>array_sum(array_column($data,'commission_earned')));
            //return view
            return view('admin.dashboard.ticket_sales',compact('data','total'));
        } catch (Exception $ex) {
            throw new Exception('Error Dashboard Ticket Sales: '.$ex->getMessage());
        }
    }
    
    /**
     * Show the chargeback report on the dashboard.
     *
     * @return view
     */
    public function chargeback()
    {
        try {
            //init
            $input = Input::all();
            $data = $total = array();
            $data_conditions = '';    
            //check field search
            if(isset($info['start_date']) && $info['start_date'])
                $data_conditions .= ' AND p.created >= "'.date_format(date_create($info['start_date']),'Y-m-d H:i:s').'"';
            if(isset($info['end_date']) && $info['end_date'])
                $data_conditions .= ' AND p.created <= "'.date_format(date_create($info['end_date']),'Y-m-d H:i:s').'"';
            //get all records        
            $data = DB::select('SELECT DISTINCTROW p.id, COALESCE(t.refnum,0) AS refnum, COALESCE(t.card_holder,CONCAT(c.first_name," ",c.last_name)) AS card_holder, 
                            COALESCE(t.amount,0) AS amount, COALESCE(t.authcode,0) AS authcode, s.name AS show_name, st.show_time, p.status AS status, 
                            p.quantity, p.transaction_id, p.ticket_type, p.created, p.note
                            FROM purchases p 
                            INNER JOIN show_times st ON st.id = p.show_time_id 
                            INNER JOIN customers c ON p.customer_id = c.id 
                            INNER JOIN shows s ON st.show_id = s.id 
                            INNER JOIN transactions t ON t.id = p.transaction_id 
                            WHERE p.status = "Chargeback" '.$data_conditions.'
                            ORDER BY p.created DESC');
            //calculate totals
            $total = array( 'amount'=>array_sum(array_column($data,'amount')),
                            'quantity'=>array_sum(array_column($data,'quantity')));
            //return view
            return view('admin.dashboard.chargeback',compact('data','total'));
        } catch (Exception $ex) {
            throw new Exception('Error Dashboard Chargeback: '.$ex->getMessage());
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
            $data_conditions = '';    
            //check field search
            if(isset($info['start_date']) && $info['start_date'])
                $data_conditions .= ' AND st.show_time >= "'.date_format(date_create($info['start_date']),'Y-m-d H:i:s').'"';
            if(isset($info['end_date']) && $info['end_date'])
                $data_conditions .= ' AND st.show_time <= "'.date_format(date_create($info['end_date']),'Y-m-d H:i:s').'"'; 
            //if 5(only his report), if 1 or 6(all reports), others check a 0 result query
            if(Auth::user()->user_type->id == 5)
                $data_conditions .= ' AND s.create_user_id = '.Auth::user()->id;
            else if(Auth::user()->user_type->id != 1 && Auth::user()->user_type->id != 6)
                $data_conditions .= ' AND s.create_user_id = 0';    
            //get all records        
            $data = DB::select('SELECT s.id, s.name, COUNT(p.id) AS num_purchases, SUM(ROUND(p.retail_price,2)) AS retail_price, SUM(ROUND(p.processing_fee,2)) AS processing_fee, 
                            SUM(p.quantity) AS num_tickets, SUM(ROUND(p.price_paid,2)) AS total,
                            SUM(ROUND((p.price_paid-p.processing_fee)*(1-(p.commission_percent/100)),2)) AS show_earned, 
                            SUM(ROUND((p.price_paid-p.processing_fee)*(p.commission_percent/100),2)) AS commission_earned
                            FROM purchases p 
                            INNER JOIN show_times st ON st.id = p.show_time_id 
                            INNER JOIN shows s ON st.show_id = s.id 
                            WHERE p.status = "Active" AND st.show_time > NOW() '.$data_conditions.'
                            GROUP BY s.id');
            //calculate totals
            $total = array( 'num_purchases'=>array_sum(array_column($data,'num_purchases')),
                            'retail_price'=>array_sum(array_column($data,'retail_price')),
                            'processing_fee'=>array_sum(array_column($data,'processing_fee')),
                            'num_tickets'=>array_sum(array_column($data,'num_tickets')),
                            'total'=>array_sum(array_column($data,'total')));
            //return view
            return view('admin.dashboard.future_liabilities',compact('data','total'));
        } catch (Exception $ex) {
            throw new Exception('Error Dashboard Future Liabilities: '.$ex->getMessage());
        }
    }
    
}
