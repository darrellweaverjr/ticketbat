<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Http\Models\Purchase;
use App\Http\Models\Venue;
use App\Http\Models\Show;
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
            }
            else
            {
                $showtime_start_date = '';
                $showtime_end_date = '';
            }
            if($showtime_start_date != '' && $showtime_end_date != '')
            {
                $where[] = ['show_times.show_time','>=',$showtime_start_date];
                $where[] = ['show_times.show_time','<=',$showtime_end_date.' 11:59:59'];
            } 
            //search soldtime
            if(isset($input) && isset($input['soldtime_start_date']) && isset($input['soldtime_end_date']))
            {
                $soldtime_start_date = $input['soldtime_start_date'];
                $soldtime_end_date = $input['soldtime_end_date'];
            }
            else
            {
                $soldtime_start_date = date('Y-m-d', strtotime('-30 DAY'));
                $soldtime_end_date = date('Y-m-d');
            }
            if($soldtime_start_date != '' && $soldtime_end_date != '')
            {
                $where[] = ['purchases.created','>=',$soldtime_start_date];
                $where[] = ['purchases.created','<=',$soldtime_end_date.' 11:59:59'];
            } 
            //get all records  
            $purchases = DB::table('purchases')
                                ->join('customers', 'customers.id', '=' ,'purchases.customer_id')
                                ->join('discounts', 'discounts.id', '=' ,'purchases.discount_id')
                                ->join('show_times', 'show_times.id', '=', 'purchases.show_time_id')
                                ->join('shows', 'shows.id', '=', 'show_times.show_id')
                                ->join('venues', 'venues.id', '=', 'shows.venue_id')
                                ->join('tickets', 'tickets.id', '=', 'purchases.ticket_id')
                                ->join('packages', 'packages.id', '=', 'tickets.package_id')
                                ->leftJoin('transactions', 'transactions.id', '=', 'purchases.transaction_id')
                                ->select('purchases.*', 'transactions.card_holder', 'transactions.authcode', 'transactions.refnum', 'transactions.last_4', 'discounts.code', 'tickets.ticket_type AS ticket_type_type', 
                                        'venues.name AS venue_name', 'customers.first_name', 'customers.last_name', 'customers.email', 'show_times.show_time', 'shows.name AS show_name', 'packages.title')
                                ->where($where)
                                ->orderBy('purchases.created','purchases.transaction_id','purchases.user_id','purchases.price_paid')
                                ->get();
            $status = Util::getEnumValues('purchases','status');
            $venues = Venue::all('id','name');
            $shows = Show::all('id','name','venue_id');
            return view('admin.purchases.index',compact('purchases','status','venues','shows','venue','show','showtime_start_date','showtime_end_date','soldtime_start_date','soldtime_end_date'));
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
                    $note = '&nbsp;<b>'.Auth::user()->first_name.' '.Auth::user()->last_name.' ('.date('m/d/Y g:i a',strtotime($current)).'): </b>'.$input['note'].'&nbsp;';
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