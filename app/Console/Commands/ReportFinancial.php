<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail\EmailSG;

class ReportFinancial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Report:financial {weeks=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for sending information about financial report previous/current week (if a param is added will be X previous week)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $weeks_ago = $this->argument('weeks');
                        
            //INIT VARIABLES    
            $date_format = 'Y-m-d';
            $days_this_week = array();
            $week = date ('W')-$weeks_ago;     
            $empty_row = array('qty'=>0,'retail_price'=>0, 'processing_fee'=>0, 'savings'=>0, 'price_paid'=>0, 'commission'=>0, 'coupon'=>0, 'gross_profit'=>0, 'chargeback_qty'=>0, 'chargeback_amount'=>0);

            //ARRAYS WITH INFO
            $venues = array();
            $coupons = array();
            $charge_back = array();

            //INTERNAL FUNCTIONS
            function getPercent($div1,$div2,$sign)
            {
                if($div1 == 0 && $div2 == 0) return 0.00;   
                ($div2 == 0)? $div2 = 1 : $div2;
                ($div1 == 0)? $value = round(1/$div2*100,2) : $value = round($div1/$div2*100,2);            
                if($sign && ($div1<$div2)){
                    $value = 100-$value; $value *= -1;
                } 
                return $value;
            }
            
            //GROSS REVENUE FUNCTION
            function calculateGrossRevenue($date_format,$days_this_week,$venues)
            {
                //FILL OUT GROSS REVENUE
                $gross_revenue = array('week_this'=>array('week_total'=>array('gross_revenue'=>0,'qty'=>0)),'week_last'=>array('week_total'=>array('gross_revenue'=>0,'qty'=>0)), 'week_diff'=>array(), 'week_perc'=>array());

                foreach ($days_this_week as $day) 
                {
                    //INIT ALL CALCULATORS
                    $day_back = date($date_format, strtotime ('-7 day',strtotime($day)));
                    if(!isset($gross_revenue['week_this'][$day]))  
                    {
                        $gross_revenue['week_this'][$day] = array('gross_revenue'=>0,'qty'=>0);  
                        $gross_revenue['week_last'][$day] = array('gross_revenue'=>0,'qty'=>0);
                        $gross_revenue['week_diff'][$day] = array('gross_revenue'=>0,'qty'=>0);
                        $gross_revenue['week_perc'][$day] = array('gross_revenue'=>0,'qty'=>0);
                    }
                    foreach($venues as $venue)
                    {
                        $gross_revenue['week_this'][$day]['gross_revenue'] += $venue['this_week'][$day]['gross_revenue'];
                        $gross_revenue['week_this'][$day]['qty'] += $venue['this_week'][$day]['qty'];
                        $gross_revenue['week_this']['week_total']['gross_revenue'] += $venue['this_week'][$day]['gross_revenue'];
                        $gross_revenue['week_this']['week_total']['qty'] += $venue['this_week'][$day]['qty'];

                        $gross_revenue['week_last'][$day]['gross_revenue'] += $venue['last_week'][$day_back]['gross_revenue'];
                        $gross_revenue['week_last'][$day]['qty'] += $venue['last_week'][$day_back]['qty'];
                        $gross_revenue['week_last']['week_total']['gross_revenue'] += $venue['last_week'][$day_back]['gross_revenue'];
                        $gross_revenue['week_last']['week_total']['qty'] += $venue['last_week'][$day_back]['qty'];
                    }
                    $gross_revenue['week_diff'][$day]['gross_revenue'] = $gross_revenue['week_this'][$day]['gross_revenue']-$gross_revenue['week_last'][$day]['gross_revenue'];
                    $gross_revenue['week_diff'][$day]['qty'] = $gross_revenue['week_this'][$day]['qty']-$gross_revenue['week_last'][$day]['qty'];

                    $gross_revenue['week_perc'][$day]['gross_revenue'] = getPercent($gross_revenue['week_this'][$day]['gross_revenue'],$gross_revenue['week_last'][$day]['gross_revenue'],true);
                    $gross_revenue['week_perc'][$day]['qty'] = getPercent($gross_revenue['week_this'][$day]['qty'],$gross_revenue['week_last'][$day]['qty'],true);      
                }               
                $gross_revenue['week_diff']['week_total']['gross_revenue'] = $gross_revenue['week_this']['week_total']['gross_revenue']-$gross_revenue['week_last']['week_total']['gross_revenue']; 
                $gross_revenue['week_diff']['week_total']['qty'] = $gross_revenue['week_this']['week_total']['qty']-$gross_revenue['week_last']['week_total']['qty'];

                $gross_revenue['week_perc']['week_total']['gross_revenue'] = getPercent($gross_revenue['week_this']['week_total']['gross_revenue'],$gross_revenue['week_last']['week_total']['gross_revenue'],true);
                $gross_revenue['week_perc']['week_total']['qty'] = getPercent($gross_revenue['week_this']['week_total']['qty'],$gross_revenue['week_last']['week_total']['qty'],true);
                return $gross_revenue;
            }

            //GROSS PROFIT FUNCTION
            function calculateGrossProfit($date_format,$days_this_week,$venues)
            {
                $gross_profit = array('week_this'=>array('week_total'=>array('commissions'=>0,'processing_fees'=>0,'gross_profit'=>0)),'week_last'=>array('week_total'=>array('commissions'=>0,'processing_fees'=>0,'gross_profit'=>0)), 'week_diff'=>array(), 'week_perc'=>array());      
                foreach ($days_this_week as $day) 
                {
                    //INIT ALL CALCULATORS
                    $day_back = date($date_format, strtotime ('-7 day',strtotime($day)));
                    if(!isset($gross_profit['week_this'][$day]))  
                    {
                        $gross_profit['week_this'][$day] = array('commissions'=>0,'processing_fees'=>0,'gross_profit'=>0);  
                        $gross_profit['week_last'][$day] = array('commissions'=>0,'processing_fees'=>0,'gross_profit'=>0);
                        $gross_profit['week_diff'][$day] = array('commissions'=>0,'processing_fees'=>0,'gross_profit'=>0);
                        $gross_profit['week_perc'][$day] = array('commissions'=>0,'processing_fees'=>0,'gross_profit'=>0); 
                    }
                    foreach($venues as $venue)
                    {
                        $gross_profit['week_this'][$day]['commissions'] += $venue['this_week'][$day]['commissions'];
                        $gross_profit['week_this'][$day]['processing_fees'] += $venue['this_week'][$day]['processing_fees'];
                        $gross_profit['week_this'][$day]['gross_profit'] += $venue['this_week'][$day]['gross_profit'];
                        $gross_profit['week_this']['week_total']['commissions'] += $venue['this_week'][$day]['commissions'];
                        $gross_profit['week_this']['week_total']['processing_fees'] += $venue['this_week'][$day]['processing_fees'];

                        $gross_profit['week_last'][$day]['commissions'] += $venue['last_week'][$day_back]['commissions'];
                        $gross_profit['week_last'][$day]['processing_fees'] += $venue['last_week'][$day_back]['processing_fees'];
                        $gross_profit['week_last'][$day]['gross_profit'] += $venue['last_week'][$day_back]['gross_profit'];
                        $gross_profit['week_last']['week_total']['commissions'] += $venue['last_week'][$day_back]['commissions'];
                        $gross_profit['week_last']['week_total']['processing_fees'] += $venue['last_week'][$day_back]['processing_fees'];           
                    }
                    $gross_profit['week_diff'][$day]['commissions'] = $gross_profit['week_this'][$day]['commissions']-$gross_profit['week_last'][$day]['commissions'];
                    $gross_profit['week_diff'][$day]['processing_fees'] = $gross_profit['week_this'][$day]['processing_fees']-$gross_profit['week_last'][$day]['processing_fees'];
                    $gross_profit['week_diff'][$day]['gross_profit'] = $gross_profit['week_this'][$day]['gross_profit']-$gross_profit['week_last'][$day]['gross_profit'];

                    $gross_profit['week_perc'][$day]['commissions'] = getPercent($gross_profit['week_this'][$day]['commissions'],$gross_profit['week_last'][$day]['commissions'],true);
                    $gross_profit['week_perc'][$day]['processing_fees'] = getPercent($gross_profit['week_this'][$day]['processing_fees'],$gross_profit['week_last'][$day]['processing_fees'],true);
                    $gross_profit['week_perc'][$day]['gross_profit'] = getPercent($gross_profit['week_this'][$day]['gross_profit'],$gross_profit['week_last'][$day]['gross_profit'],true);

                }               
                $gross_profit['week_diff']['week_total']['commissions'] = $gross_profit['week_this']['week_total']['commissions']-$gross_profit['week_last']['week_total']['commissions']; 
                $gross_profit['week_diff']['week_total']['processing_fees'] = $gross_profit['week_this']['week_total']['processing_fees']-$gross_profit['week_last']['week_total']['processing_fees'];
                $gross_profit['week_diff']['week_total']['gross_profit'] = $gross_profit['week_this']['week_total']['gross_profit']-$gross_profit['week_last']['week_total']['gross_profit'];


                $gross_profit['week_perc']['week_total']['commissions'] = getPercent($gross_profit['week_this']['week_total']['commissions'],$gross_profit['week_last']['week_total']['commissions'],true);
                $gross_profit['week_perc']['week_total']['processing_fees'] = getPercent($gross_profit['week_this']['week_total']['processing_fees'],$gross_profit['week_last']['week_total']['processing_fees'],true);
                $gross_profit['week_perc']['week_total']['gross_profit'] = getPercent($gross_profit['week_this']['week_total']['gross_profit'],$gross_profit['week_last']['week_total']['gross_profit'],true);
                return $gross_profit;                
            }

            //GET ALL DAYS TO SEARCH
            for($i=2; $i<9; $i++){
                $day = date($date_format, strtotime('01/01 +' . ($week-1) . ' weeks first day +' . $i . ' day')) ;
                $week_day = date("l", strtotime($day)) ;
                $days_this_week[$week_day] = $day;       
            }

            //GET ALL VENUES AND SHOW   
            $results = DB::select("SELECT v.id AS v_id, v.name AS v_name, v.accounting_email AS v_email, v.financial_report_emails AS financial_report, s.id AS s_id, s.name AS s_name, s.accounting_email AS s_email
                             FROM shows s INNER JOIN venues v ON s.venue_id = v.id INNER JOIN show_times st ON st.show_id = s.id
                             WHERE DATE_FORMAT(st.show_time,'%Y-%m-%d') >= ? AND st.is_active = 1
                             group by v.name,s.name Order by v.name,s.name",array(date($date_format, strtotime ('-7 day',strtotime($days_this_week['Monday'])))));  
            //create progress bar
            $progressbar = $this->output->createProgressBar(count($results));

            //FETCH ALL RESULTS                  
            foreach ($results as $result) 
            {
                //CREATE MAIN KEY AT INFO WITH VENUE ID                                                 
                if(!isset($venues[$result->v_id]))
                {
                    //INIT SUB TOTALS BY VENUE  
                    $venue_total_this_week = array();   
                    $venue_total_last_week = array();
                    foreach ($days_this_week as$day) 
                    {
                        $venue_total_this_week[$day] = array('qty'=>0, 'gross_revenue'=>0, 'commissions'=>0, 'processing_fees'=>0, 'gross_profit'=>0, 'chargeback_qty'=>0, 'chargeback_amount'=>0);  
                        $day_back = date($date_format, strtotime ('-7 day',strtotime($day)));
                        $venue_total_last_week[$day_back] = array('qty'=>0, 'gross_revenue'=>0, 'commissions'=>0, 'processing_fees'=>0, 'gross_profit'=>0, 'chargeback_qty'=>0, 'chargeback_amount'=>0);
                    }
                    $venue_total_this_week['week_total'] = array('qty'=>0, 'gross_revenue'=>0, 'commissions'=>0, 'processing_fees'=>0, 'gross_profit'=>0, 'chargeback_qty'=>0, 'chargeback_amount'=>0);
                    $venue_total_last_week['week_total'] = array('qty'=>0, 'gross_revenue'=>0, 'commissions'=>0, 'processing_fees'=>0, 'gross_profit'=>0, 'chargeback_qty'=>0, 'chargeback_amount'=>0);

                    $venues[$result->v_id] = array('name'=>$result->v_name, 'email'=>$result->v_email, 'financial_report'=>$result->financial_report, 'this_week'=>$venue_total_this_week, 'last_week'=>$venue_total_last_week, 'shows'=>array());

                    $venues[$result->v_id]['total_total']=$empty_row;
                }   

                //SEARCH EACH ELEMENT BY RESULT
                $venues[$result->v_id]['shows'][$result->s_id] = array('name'=>$result->s_name, 'email'=>$result->s_email, 'this_week'=>array('week_total'=>array('price_paid'=>0,'qty'=>0)), 'last_week'=>array('week_total'=>array('price_paid'=>0,'qty'=>0)));   

                $query = "SELECT
                               (SELECT COUNT(*) FROM purchases p 
                                               INNER JOIN show_times st ON st.id = p.show_time_id
                                               INNER JOIN shows s ON s.id = st.show_id 
                                               WHERE s.id = ? AND DATE_FORMAT(p.created,'%Y-%m-%d') = ? AND p.status = 'Chargeback') AS chargeback_qty,
                               (SELECT SUM(p.price_paid) FROM purchases p 
                                               INNER JOIN show_times st ON st.id = p.show_time_id
                                               INNER JOIN shows s ON s.id = st.show_id 
                                               WHERE s.id = ? AND DATE_FORMAT(p.created,'%Y-%m-%d') = ? AND p.status = 'Chargeback') AS chargeback_amount, 
                               SUM(p.quantity) AS qty, SUM(p.retail_price) AS retail_price, SUM(p.processing_fee) AS processing_fee, SUM(p.savings) AS savings, 
                               SUM(p.price_paid) AS price_paid, SUM(p.commission) AS commission, d.id
                          FROM 
                              (SELECT *, price_paid * commission_percent/100 AS commission 
                              FROM purchases 
                              WHERE DATE_FORMAT(created,'%Y-%m-%d') = ? AND status = 'Active') as p
                          INNER JOIN discounts d ON d.id = p.discount_id      
                          INNER JOIN show_times st ON st.id = p.show_time_id
                          INNER JOIN shows s ON s.id = st.show_id WHERE s.id = ? GROUP BY s.name";    

                //GETTING ALL VALUES BY SHOW AND DATE WEEK
                foreach ($days_this_week as $key_day => $day) 
                {
                    //THIS WEEK                     
                    $daily_this_week = DB::selectOne($query,array($result->s_id,$day,$result->s_id,$day,$day,$result->s_id));                
                    if($daily_this_week)
                    {
                        $venues[$result->v_id]['shows'][$result->s_id]['this_week'][$day]=(array)$daily_this_week;
                        //GET SHOW DETAILS BY SHOW_TIME ********    BEGIN                   
                        $show_times_day = DB::select("SELECT SUM(p.quantity) AS qty, SUM(p.price_paid) AS price_paid, DATE_FORMAT(st.show_time,'%h:%i %p') AS show_time
                                      FROM purchases p
                                      INNER JOIN show_times st ON st.id = p.show_time_id
                                      INNER JOIN shows s ON s.id = st.show_id 
                                      WHERE DATE_FORMAT(p.created,'%Y-%m-%d') = ? AND p.status = 'Active' AND s.id = ? 
                                      GROUP BY DATE_FORMAT(st.show_time,'%h:%i %p')",
                                      array($day,$result->s_id));
                        foreach ($show_times_day as $st) 
                        {
                            $venues[$result->v_id]['shows'][$result->s_id]['this_week']['show_time'][$st->show_time][$day] = array('price_paid'=>$st->price_paid, 'qty'=>$st->qty);
                            if(!isset($venues[$result->v_id]['shows'][$result->s_id]['this_week']['show_time'][$st->show_time]['week_total']))
                            {
                                $venues[$result->v_id]['shows'][$result->s_id]['this_week']['show_time'][$st->show_time]['week_total'] = array('price_paid'=>0, 'qty'=>0);
                                $show_times_total = DB::selectOne("SELECT SUM(p.quantity) AS qty, ROUND(SUM(p.price_paid),2) AS price_paid
                                      FROM purchases p
                                      INNER JOIN show_times st ON st.id = p.show_time_id
                                      INNER JOIN shows s ON s.id = st.show_id 
                                      WHERE s.id = ? AND DATE_FORMAT(st.show_time,'%h:%i %p') = ? 
                                      GROUP BY DATE_FORMAT(st.show_time,'%h:%i %p')",
                                      array($result->s_id,$st->show_time));
                                $venues[$result->v_id]['shows'][$result->s_id]['this_week']['show_time'][$st->show_time]['total_total'] = array('price_paid'=>$show_times_total->price_paid, 'qty'=>$show_times_total->qty);
                            }

                            $venues[$result->v_id]['shows'][$result->s_id]['this_week']['show_time'][$st->show_time]['week_total']['price_paid'] += $st->price_paid;
                            $venues[$result->v_id]['shows'][$result->s_id]['this_week']['show_time'][$st->show_time]['week_total']['qty'] += $st->qty;
                        }                   
                        //GET SHOW DETAILS BY SHOW_TIME ********    END

                        //THIS WEEK SUMMARY - FOR EVERY SHOW WEEK TOTAL
                        $venues[$result->v_id]['shows'][$result->s_id]['this_week']['week_total']['price_paid']+=$daily_this_week->price_paid;
                        $venues[$result->v_id]['shows'][$result->s_id]['this_week']['week_total']['qty']+=$daily_this_week->qty;

                        //THIS WEEK TOTALS SUMMARY - TOTALS FOR VENUE
                        $venues[$result->v_id]['this_week'][$day]['gross_revenue']+=$daily_this_week->price_paid;
                        $venues[$result->v_id]['this_week'][$day]['commissions']+=$daily_this_week->commission;
                        $venues[$result->v_id]['this_week'][$day]['processing_fees']+=$daily_this_week->processing_fee;
                        $venues[$result->v_id]['this_week'][$day]['gross_profit']+=$daily_this_week->commission+$daily_this_week->processing_fee;
                        $venues[$result->v_id]['this_week'][$day]['qty']+=$daily_this_week->qty;
                        $venues[$result->v_id]['this_week'][$day]['chargeback_amount']+=$daily_this_week->chargeback_amount;
                        $venues[$result->v_id]['this_week'][$day]['chargeback_qty']+=$daily_this_week->chargeback_qty;

                        //THIS WEEK TOTALS-TOTALS VENUE
                        $venues[$result->v_id]['this_week']['week_total']['gross_revenue']+=$daily_this_week->price_paid;
                        $venues[$result->v_id]['this_week']['week_total']['commissions']+=$daily_this_week->commission;
                        $venues[$result->v_id]['this_week']['week_total']['processing_fees']+=$daily_this_week->processing_fee;
                        $venues[$result->v_id]['this_week']['week_total']['gross_profit']+=$daily_this_week->commission+$daily_this_week->processing_fee;
                        $venues[$result->v_id]['this_week']['week_total']['qty']+=$daily_this_week->qty;
                        $venues[$result->v_id]['this_week']['week_total']['chargeback_amount']+=$daily_this_week->chargeback_amount;
                        $venues[$result->v_id]['this_week']['week_total']['chargeback_qty']+=$daily_this_week->chargeback_qty;
                    }
                    else $venues[$result->v_id]['shows'][$result->s_id]['this_week'][$day]=$empty_row; 

                    //LAST WEEK 
                    $day_back = date($date_format, strtotime ('-7 day',strtotime($day)));
                    $daily_last_week = DB::selectOne($query,array($result->s_id,$day_back,$result->s_id,$day_back,$day_back,$result->s_id));
                    if($daily_last_week)
                    {
                        $venues[$result->v_id]['shows'][$result->s_id]['last_week'][$day_back]=(array)$daily_last_week;

                        //LAST WEEK SUMMARY - FOR EVERY SHOW WEEK TOTAL
                        $venues[$result->v_id]['shows'][$result->s_id]['last_week']['week_total']['price_paid']+=$daily_last_week->price_paid;
                        $venues[$result->v_id]['shows'][$result->s_id]['last_week']['week_total']['qty']+=$daily_last_week->qty;    

                        //last WEEK TOTALS SUMMARY - TOTALS FOR VENUE
                        $venues[$result->v_id]['last_week'][$day_back]['gross_revenue']+=$daily_last_week->price_paid;
                        $venues[$result->v_id]['last_week'][$day_back]['commissions']+=$daily_last_week->commission;
                        $venues[$result->v_id]['last_week'][$day_back]['processing_fees']+=$daily_last_week->processing_fee;
                        $venues[$result->v_id]['last_week'][$day_back]['gross_profit']+=$daily_last_week->commission+$daily_last_week->processing_fee;
                        $venues[$result->v_id]['last_week'][$day_back]['qty']+=$daily_last_week->qty;
                        $venues[$result->v_id]['last_week'][$day_back]['chargeback_amount']+=$daily_last_week->chargeback_amount;
                        $venues[$result->v_id]['last_week'][$day_back]['chargeback_qty']+=$daily_last_week->chargeback_qty;

                        //LAST WEEK TOTALS-TOTALS VENUE
                        $venues[$result->v_id]['last_week']['week_total']['gross_revenue']+=$daily_last_week->price_paid;
                        $venues[$result->v_id]['last_week']['week_total']['commissions']+=$daily_last_week->commission;
                        $venues[$result->v_id]['last_week']['week_total']['processing_fees']+=$daily_last_week->processing_fee;
                        $venues[$result->v_id]['last_week']['week_total']['gross_profit']+=$daily_last_week->commission+$daily_last_week->processing_fee;
                        $venues[$result->v_id]['last_week']['week_total']['qty']+=$daily_last_week->qty;
                        $venues[$result->v_id]['last_week']['week_total']['chargeback_amount']+=$daily_last_week->chargeback_amount;
                        $venues[$result->v_id]['last_week']['week_total']['chargeback_qty']+=$daily_last_week->chargeback_qty;
                    }
                    else $venues[$result->v_id]['shows'][$result->s_id]['last_week'][$day_back]=$empty_row;                                   
                }
                //CALCULATE SUPER TOTAL ALL TIME            
                $super_total_show = DB::selectOne("SELECT SUM(p.quantity) AS qty, SUM(p.retail_price) AS retail_price, SUM(p.processing_fee) AS processing_fee, 
                                               SUM(p.savings) AS savings, SUM(p.price_paid) AS price_paid, SUM(p.commission) AS commission, d.id
                                               FROM 
                                                  (SELECT *, price_paid * commission_percent/100 AS commission 
                                                  FROM purchases) as p
                                               INNER JOIN discounts d ON d.id = p.discount_id     
                                               INNER JOIN show_times st ON st.id = p.show_time_id
                                               INNER JOIN shows s ON s.id = st.show_id WHERE s.id = ? GROUP BY s.name",array($result->s_id));

                if($super_total_show) $venues[$result->v_id]['shows'][$result->s_id]['total_total']=(array)$super_total_show;
                else $venues[$result->v_id]['shows'][$result->s_id]['total_total']=$empty_row; 
                //advance progress bar
                $progressbar->advance();
            }
         
            //finish progress bar
            $progressbar->finish();
            
            //create progress bar
            $progressbar = $this->output->createProgressBar(10);
            //CHARGE BACK
            foreach ($days_this_week as $day) 
            {
                $charge_back_day = DB::selectOne("SELECT COUNT(p.quantity) AS qty, SUM(p.price_paid) AS price_paid
                                                  FROM purchases p WHERE DATE_FORMAT(p.created,'%Y-%m-%d') = ? AND p.status = 'Chargeback' GROUP BY p.created",array($day));
                $charge_back[$day] = (array)$charge_back_day;
            }
            //advance progress bar
            $progressbar->advance(); 
            
            //CHARGE BACK WEEK TOTAL
            $charge_back_week = DB::selectOne("SELECT COUNT(p.quantity) AS qty, SUM(p.price_paid) AS price_paid
                                                  FROM purchases p WHERE DATE_FORMAT(p.created,'%Y-%m-%d') >= ? AND p.status = 'Chargeback' GROUP BY p.created",array($days_this_week['Monday']));
            $charge_back['week_total'] = (array)$charge_back_week;
            //advance progress bar
            $progressbar->advance(); 
            
            //CHARGE BACK TOTAL TOTAL
            $charge_back_total = DB::selectOne("SELECT COUNT(p.quantity) AS qty, SUM(p.price_paid) AS price_paid FROM purchases p WHERE p.status = 'Chargeback'");
            $charge_back['total_total'] = (array)$charge_back_total; 
            //advance progress bar
            $progressbar->advance(); 

            //FILL OUT GROSS REVENUE
            $gross_revenue = calculateGrossRevenue($date_format,$days_this_week,$venues);
            //advance progress bar
            $progressbar->advance(); 

            //FILL OUT GROSS PROFIT
            $gross_profit = calculateGrossProfit($date_format,$days_this_week,$venues);
            //advance progress bar
            $progressbar->advance(); 

            //GET ALL DISCOUNTS
            $coupons_id = '0';
            foreach ($days_this_week as $day)
            {
                $coupons['total'][$day]['savings'] = 0; 
                $coupons['total'][$day]['qty'] = 0;
                $savings = DB::select("SELECT DISTINCT d.id, d.code, d.description, ROUND(SUM(p.savings),2) AS savings, COUNT(*) AS qty
                                    FROM discounts d 
                                    INNER JOIN purchases p ON d.id = p.discount_id
                                    INNER JOIN show_times st ON st.id = p.show_time_id
                                    INNER JOIN shows s ON s.id = st.show_id 
                                    WHERE DATE_FORMAT(p.created,'%Y-%m-%d') = ? AND p.status = 'Active' AND p.savings > 0
                                    GROUP BY d.code",array($day));
                foreach ($savings as $coupon) 
                {
                    $c_id = $coupon->code.' - '.$coupon->description;   
                    $coupons[$c_id][$day]['savings'] = $coupon->savings;
                    $coupons['total'][$day]['savings'] += $coupon->savings;
                    $coupons[$c_id][$day]['qty'] = $coupon->qty;
                    $coupons['total'][$day]['qty'] += $coupon->qty;
                    $coupons_id .= ','.$coupon->id;
                }           
            }       
            foreach ($coupons as $code => $coupon)
            {
                $coupons[$code] = array('week_total'=>array('savings'=>0,'qty'=>0),'total_total'=>array('savings'=>0,'qty'=>0)); 
                foreach ($days_this_week as $day)
                {
                    $coupons[$code]['week_total']['savings'] += (isset($coupon[$day]['savings']))? $coupon[$day]['savings'] : 0;
                    $coupons[$code]['week_total']['qty'] += (isset($coupon[$day]['qty']))? $coupon[$day]['qty'] : 0;
                }           
            } 
            //advance progress bar
            $progressbar->advance(); 
            
            //CALCULATE SUPER TOTAL GENERAL VENUE WITH SHOWED SHOWS AND WITHOUT     
            foreach ($venues as $v_id => $venue) 
            {
                $super_total_show = DB::selectOne("SELECT 
                                                        (SELECT COUNT(*) FROM purchases p 
                                                                       INNER JOIN show_times st ON st.id = p.show_time_id
                                                                       INNER JOIN shows s ON s.id = st.show_id 
                                                                       WHERE s.venue_id = ? AND p.status = 'Chargeback') AS chargeback_qty,
                                                        (SELECT SUM(p.price_paid) FROM purchases p 
                                                                       INNER JOIN show_times st ON st.id = p.show_time_id
                                                                       INNER JOIN shows s ON s.id = st.show_id 
                                                                       WHERE s.venue_id = ? AND p.status = 'Chargeback') AS chargeback_amount,
                                                        SUM(p.quantity) AS qty, ROUND(SUM(p.retail_price),2) AS retail_price, ROUND(SUM(p.processing_fee),2) AS processing_fee, 
                                                        ROUND(SUM(p.savings),2) AS savings, ROUND(SUM(p.price_paid),2) AS price_paid, ROUND(SUM(p.commission),2) AS commission,
                                                        ROUND(SUM(p.commission)+SUM(p.processing_fee),2) AS gross_profit
                                                   FROM 
                                                      (SELECT *, price_paid * commission_percent/100 AS commission 
                                                      FROM purchases WHERE status = 'Active') as p  
                                                   INNER JOIN show_times st ON st.id = p.show_time_id
                                                   INNER JOIN shows s ON s.id = st.show_id WHERE s.venue_id = ? GROUP BY s.venue_id",array($v_id,$v_id,$v_id));

                if($super_total_show) $venues[$v_id]['total_total'] = (array)$super_total_show;  
                else $venues[$v_id]['total_total']=$empty_row;  
            }
            //advance progress bar
            $progressbar->advance(); 
            
            //CALCULATE SUPER TOTAL GENERAL WHOLE TICKETBAT
            $super_total = DB::selectOne("SELECT SUM(p.quantity) AS qty, ROUND(SUM(p.retail_price),2) AS retail_price, ROUND(SUM(p.processing_fee),2) AS processing_fee, 
                                       ROUND(SUM(p.savings),2) AS savings, ROUND(SUM(p.price_paid),2) AS price_paid, ROUND(SUM(p.commission),2) AS commission,
                                       ROUND(SUM(p.commission)+SUM(p.processing_fee),2) AS gross_profit
                                       FROM 
                                          (SELECT *, price_paid * commission_percent/100 AS commission 
                                          FROM purchases WHERE status = 'Active') as p ");
            if($super_total)
            { 
                $gross_revenue['total_total']['gross_revenue'] = $super_total->price_paid; 
                $gross_revenue['total_total']['qty'] = $super_total->qty; 

                $gross_profit['total_total']['commissions'] = $super_total->commission; 
                $gross_profit['total_total']['processing_fees'] = $super_total->processing_fee;
                $gross_profit['total_total']['gross_profit'] = $super_total->gross_profit;  
            }
            else $gross_revenue['total_total'] = array('gross_revenue'=>0,'qty'=>0,'commissions'=>0,'processing_fees'=>0,'gross_profit'=>0); 
            //advance progress bar
            $progressbar->advance(); 

            //SUPER TOTAL COUPONS
            $super_total_savings = DB::select("SELECT DISTINCT d.id, d.code, d.description, ROUND(SUM(p.savings),2) AS savings, COUNT(*) AS qty
                                    FROM discounts d 
                                    INNER JOIN purchases p ON d.id = p.discount_id
                                    WHERE d.id IN (".$coupons_id.") AND p.status = 'Active' GROUP BY d.id");
            $coupons['total']['total_total']['savings'] = 0;
            $coupons['total']['total_total']['qty'] = 0;
            if(count($super_total_savings))
            {
                foreach ($super_total_savings as $totals)
                {
                    $t_id = $totals->code.' - '.$totals->description;
                    $coupons[$t_id]['total_total'] = (array)$totals;
                    $coupons['total']['total_total'] = (array)$totals;
                }
            }
            //advance progress bar
            $progressbar->advance(); 

            //  PROCESS SENDING EMAILS ACCORDING TO CONDITIONS 
            $data = array('gross_revenue'=>$gross_revenue,'coupons'=>$coupons,'gross_profit'=>$gross_profit,'charge_back'=>$charge_back,'venues'=>$venues,'days_this_week'=>$days_this_week);
            //advance progress bar
            $progressbar->advance(); 
            //finish progress bar
            $progressbar->finish();
            
            function sendEmail($data,$filter,$to,$name)
            {
                $data['filter'] = $filter;
                $financial_report = View::make('command.report_financial', $data);
                
                
                print_r($financial_report->render());
                exit();
                /*
                $pdf = PDF::load($financial_report->render(), 'tabloid', 'landscape')->output();
                $pdf_url = '/tmp/ReportFinancial_'.$filter.'_'.date('Y-m-d').'_'.date('U').'.pdf';                
                $file_pdf = fopen($pdf_url, "w");fwrite($file_pdf, $pdf);fclose($file_pdf);PDF::reinit();
                */
                $email = new EmailSG(env('MAIL_REPORT_FROM'), $to ,'TicketBat Financial Report to '.$name);
                $email->category('Reports');
                $email->text('TicketBat Financial Report. Created at '.date('Y-m-d'));
                //$email->attachment($pdf_url);
                $email->send();
                unlink($pdf_url);   
            }

            //create progress bar
            $progressbar = $this->output->createProgressBar(count($venues)+1);
            sendEmail($data,0,env('MAIL_REPORT_TO'),'Admin');  //admin report
            //advance progress bar
            $progressbar->advance(); 
            foreach ($venues as $type => $venue)
                if($venue['financial_report'] == 1 && $venue['email']) 
                {
                    sendEmail($data,$type,$venue['email'],$venue['name']);                     
                    //advance progress bar
                    $progressbar->advance(); 
                }
            //finish progress bar
            $progressbar->finish();         
        } catch (Exception $ex) {
            throw new Exception('Error creating report with ReportFinancial Command: '.$ex->getMessage());
        }
    }
}
