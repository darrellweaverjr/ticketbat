<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Models\Venue;
use App\Http\Models\Show;
use App\Http\Models\Purchase;
use App\Http\Models\Util;

/**
 * Manage ReportSales options for the commands
 *
 * @author ivan
 */
class ReportSalesController extends Controller{
    
    protected $days = 1;
    protected $only_admin = 0;
    protected $start_date;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($days, $only_admin)
    {
        $this->days = $days;
        $this->only_admin = $only_admin;
        $this->start_date = date('Y-m-d',strtotime('-'.$this->days.' days'));
    }
    
    /*
     * get all venues data for report
     */
    public function venues()
    {
        try {
            //get all records 
            $venues = DB::table('venues')
                        ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->select('venues.id','venues.name','venues.accounting_email','venues.daily_sales_emails')
                        ->where('purchases.status','=','Active')
                        ->whereDate('purchases.created','>=',$this->start_date)
                        ->groupBy('venues.id')->orderBy('venues.name')
                        ->distinct()->get();
        } catch (Exception $ex) {
            return false;
        }
    }    
    
    /*
     * table_sales_types
     */
    public function table_sales_types()
    {
        try {
            //get all records 
            $venues = DB::table('venues')
                        ->join('shows', 'venues.id', '=' ,'shows.venue_id')
                        ->join('show_times', 'shows.id', '=' ,'show_times.show_id')
                        ->join('purchases', 'show_times.id', '=' ,'purchases.show_time_id')
                        ->select('venues.id','venues.name','venues.accounting_email','venues.daily_sales_emails')
                        ->where('purchases.status','=','Active')
                        ->whereDate('purchases.created','>=',$this->start_date)
                        ->groupBy('venues.id')->orderBy('venues.name')
                        ->distinct()->get();
        } catch (Exception $ex) {
            return false;
        }
    }    
    
}
