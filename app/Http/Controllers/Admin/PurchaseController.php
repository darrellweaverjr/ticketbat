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
use App\Http\Models\Ticket;
use App\Http\Models\ShowTime;
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
            if(isset($input) && isset($input['action']) && $input['action']==0)
            {
                $show = DB::table('show_times')
                                ->join('purchases', 'purchases.show_time_id', '=', 'show_times.id')
                                ->join('shows', 'shows.id', '=', 'show_times.show_id')
                                ->select('shows.id')
                                ->where('purchases.id','=',$input['purchase_id'])->first();
                $showtimes = DB::table('show_times')->select('id','show_time')
                                ->where('show_id','=',$show->id)->where('is_active','=',1)->where('show_times.show_time','>',date('Y-m-d H:i:s'))
                                ->orderBy('show_times.show_time')->get();
                $ticket = DB::table('tickets')
                                ->join('purchases','purchases.ticket_id','=','tickets.id')
                                ->select('tickets.*')
                                ->where('purchases.id','=',$input['purchase_id'])->first();
                return ['success'=>true,'ticket'=>$ticket,'showtimes'=>$showtimes];
            }
            else if(isset($input) && isset($input['action']) && $input['action']==1)
            {
                $showtime = ShowTime::find($input['show_time_id']);
                if($showtime)
                {
                    $ticket = null;
                    $contracts = DB::table('show_contracts')->select('data')
                                ->where('show_id','=',$showtime->show_id)
                                ->where('effective_date','<=',date('Y-m-d',strtotime($showtime->show_time)))->where('effective_date','>=',date('Y-m-d'))
                                ->orderBy('effective_date','desc')->get();
                    foreach ($contracts as $c)
                    {
                        if(!empty($c->data) && Util::isJSON($c->data))
                        {
                            $data = json_decode($c->data);
                            foreach ($data as $d)
                            {
                                if($d->ticket_id == $input['ticket_id'])
                                    return ['success'=>true,'ticket'=>$d];
                            }
                        }
                    }
                    if(!$ticket)
                        $ticket = Ticket::find($input['ticket_id']);
                    return ['success'=>true,'ticket'=>$ticket];
                }
                else return ['success'=>false,'msg'=>'There was an error.<br>That event date is not longer in the system.'];
                        
                
                return ['success'=>true,'ticket'=>$ticket,'showtimes'=>$showtimes];
            }
            else
            {
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
                    $where[] = [DB::raw('DATE(show_times.show_time)'),'>=',$showtime_start_date];
                    $where[] = [DB::raw('DATE(show_times.show_time)'),'<=',$showtime_end_date];
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
                    $where[] = [DB::raw('DATE(purchases.created)'),'>=',$soldtime_start_date];
                    $where[] = [DB::raw('DATE(purchases.created)'),'<=',$soldtime_end_date];
                } 
                //if user has permission to view
                $status = [];
                $venues = [];
                $shows = [];
                $purchases = [];
                if(in_array('View',Auth::user()->user_type->getACLs()['PURCHASES']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['PURCHASES']['permission_scope'] != 'All')
                    {
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
                                    ->where(DB::raw('shows.venue_id IN ('.Auth::user()->venues_edit.') OR shows.audit_user_id'),'=',Auth::user()->id)
                                    ->orderBy('purchases.created','purchases.transaction_id','purchases.user_id','purchases.price_paid')
                                    ->get();
                        $venues = Venue::whereIn('id',explode(',',Auth::user()->venues_edit))->orderBy('name')->get(['id','name']);
                        $shows = Show::whereIn('venue_id',explode(',',Auth::user()->venues_edit))->orWhere('audit_user_id',Auth::user()->id)->orderBy('name')->get(['id','name','venue_id']);

                    }//all
                    else
                    {
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
                        $venues = Venue::orderBy('name')->get(['id','name']);
                        $shows = Show::orderBy('name')->get(['id','name','venue_id']);
                    }   
                    $status = Util::getEnumValues('purchases','status');
                }
                return view('admin.purchases.index',compact('purchases','status','venues','shows','venue','show','showtime_start_date','showtime_end_date','soldtime_start_date','soldtime_end_date'));
            }
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
            $current = date('Y-m-d H:i:s');
            //save all record      
            if($input && isset($input['id']))
            {
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
            else if($input && isset($input['purchase_id']))
            {
                $purchase = Purchase::find($input['purchase_id']);
                if($purchase)
                {
                    $showtime_from = ShowTime::find($purchase->show_time_id);
                    $showtime_to = ShowTime::find($input['show_time_id']);
                    $note = '&nbsp;<b>'.Auth::user()->first_name.' '.Auth::user()->last_name.' ('.date('m/d/Y g:i a',strtotime($current))
                            .'): </b>Change show time date from '.date('m/d/Y g:i a',strtotime($showtime_from->show_time))
                            .' to '.date('m/d/Y g:i a',strtotime($showtime_to->show_time)).' &nbsp;';
                    $purchase->note = ($purchase->note)? $purchase->note.$note : $note; 
                    $purchase->show_time_id = $input['show_time_id'];
                    $purchase->save();
                    return ['success'=>true,'msg'=>'Purchase saved successfully!'];
                }
                else return ['success'=>false,'msg'=>'There was an error saving the purchase.<br>That purchase is not longer in the system.'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the purchase.<br>Invalid Option.'];
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