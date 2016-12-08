<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Purchase;
use App\Mail\EmailSG;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Models\Util;

/**
 * Manage Purchases
 *
 * @author ivan
 */
class PurchaseController extends Controller{
    
    /**
     * List all purchases and return default view.
     *
     * @return view
     */
    public function index()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && isset($input['start_date']) && isset($input['end_date']))
            {
                //input dates 
                $start_date = date('Y-m-d H:i:s',strtotime($input['start_date']));
                $end_date = date('Y-m-d H:i:s',strtotime($input['end_date']));
            }
            else
            {
                //default dates 
                $start_date = date('Y-m-d H:i:s',getlastmod());
                $end_date = date('Y-m-d H:i:s');
            }
            //get all records  
            $purchases = DB::table('purchases')
                                ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                                ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                                ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                                ->join('shows', 'shows.id', '=', 'show_times.show_id')
                                ->join('venues', 'venues.id', '=', 'shows.venue_id')
                                ->leftJoin('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                                ->select('purchases.*', 'transactions.*', 'discounts.code', 'venues.name AS venue', 'customers.first_name', 'customers.last_name', 'customers.email', 'show_times.show_time', 'shows.name')
                                ->whereBetween('purchases.created', [$start_date,$end_date])
                                ->orderBy('purchases.created','purchases.session_id')
                                ->get();
            $status = Util::getEnumValues('purchases','status');
            return view('admin.purchases.index',compact('purchases','status','start_date','end_date'));
        } catch (Exception $ex) {
            throw new Exception('Error Purchases Index: '.$ex->getMessage());
        }
    }
    /**
     * Updated purchase.
     *
     * @void
     */
    public function save()
    {
        try {
            //init
            $input = Input::all();
            //save all record      
            if($input && isset($input['id']))
            {
                $current = date('Y-m-d H:i:s');
                $purchase = Purchase::find($input['id']);
                if(isset($input['status']))
                {
                    $purchase->status = $input['status'];
                    $purchase->updated = $current;
                    $purchase->save();
                    return ['success'=>true,'msg'=>'Purchase saved successfully!'];
                }                    
                else if(isset($input['note']))
                {                    
                    $purchase->note = $input['note'];
                    $purchase->updated = $current;
                    $purchase->save();
                    return ['success'=>true,'msg'=>'Purchase saved successfully!'];
                }               
                else return ['success'=>false,'msg'=>'There was an error saving the purchase.<br>Invalid data.'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the purchase.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Purchases Save: '.$ex->getMessage());
        }
    }
    /**
     * Updated purchase.
     *
     * @void
     */
    public function save()
    {
        try {
            //init
            $input = Input::all();
            //save all record      
            if($input && isset($input['id']))
            {
                $current = date('Y-m-d H:i:s');
                $purchase = Purchase::find($input['id']);
                if(isset($input['status']))
                {
                    $purchase->status = $input['status'];
                    $purchase->updated = $current;
                    $purchase->save();
                    return ['success'=>true,'msg'=>'Purchase saved successfully!'];
                }                    
                else if(isset($input['note']))
                {                    
                    $purchase->note = $input['note'];
                    $purchase->updated = $current;
                    $purchase->save();
                    return ['success'=>true,'msg'=>'Purchase saved successfully!'];
                }               
                else return ['success'=>false,'msg'=>'There was an error saving the purchase.<br>Invalid data.'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the purchase.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Purchases Email: '.$ex->getMessage());
        }
    }
}
