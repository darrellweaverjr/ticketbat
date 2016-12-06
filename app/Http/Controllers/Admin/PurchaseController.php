<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Http\Models\Purchase;
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
                                ->select('purchases.*', 'transactions.card_holder', 'transactions.authcode', 'transactions.refnum', 'transactions.last_4', 'discounts.code', 
                                        'venues.name AS venue', 'customers.first_name', 'customers.last_name', 'customers.email', 'show_times.show_time', 'shows.name')
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
                    $note = '&nbsp;<b>'.Auth::user()->first_name.' '.Auth::user()->last_name.' ('.$current.'): </b>'.$input['note'].'&nbsp;';
                    $purchase->note = $purchase->note.$note;
                    $purchase->updated = $current;
                    $purchase->save();
                    return ['success'=>true,'msg'=>'Purchase saved successfully!','note'=>$purchase->note];
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
    public function email()
    {
        try {
            //init
            $input = Input::all();
            //save all record      
            if($input && isset($input['id']))
            {
                $receipt = Purchase::find($input['id'])->get_receipt();
                $sent = Purchase::email_receipts('Re-sending: TicketBat Purchase',[$receipt],'receipt');
                if($sent)
                    return ['success'=>true,'msg'=>'Email sent successfully!'];
                return ['success'=>false,'msg'=>'There was an error sending the email.'];    
            }
            return ['success'=>false,'msg'=>'There was an error saving the purchase.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Purchases Email: '.$ex->getMessage());
        }
    }
    /**
     * View tickets of purchase.
     *
     * @void
     */
    public function tickets($type,$ids)
    {
        try {
            //check input values    
            if(in_array($type,['C','S']))
            {
                $tickets = [];
                $purchases_id = explode('-',$ids);
                foreach ($purchases_id as $id)
                {
                    $t = Purchase::find($id)->get_receipt()['tickets'];
                    $tickets = array_merge($tickets,$t);
                }
                //create pdf tickets
                $format = 'pdf';
                $pdf_receipt = View::make('command.report_sales_receipt_tickets', compact('tickets','type','format')); 
                if($type == 'S')
                    return PDF::loadHTML($pdf_receipt->render())->setPaper([0,0,396,144],'portrait')->setWarnings(false)->download('TicketBat Admin - tickets - '.$ids.'.pdf');
                return PDF::loadHTML($pdf_receipt->render())->setPaper('a4', 'portrait')->setWarnings(false)->download('TicketBat Admin - tickets - '.$ids.'.pdf');
            }
            else
            {
                $format='plain';
                $tickets = '<script>alert("The system could not load the information from the DB. These are not valid purchases.");window.close();</script>';
                return View::make('command.report_sales_receipt_tickets', compact('tickets','type','format'))->render();
            }
            
        } catch (Exception $ex) {                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
            throw new Exception('Error Purchases tickets: '.$ex->getMessage());
        }
    }
}                    