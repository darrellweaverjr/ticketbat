<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Util;
use App\Http\Models\Shoppingcart;

class UserConsignmentController extends Controller
{
    /**
     * Consignments options.
     *
     * @return Method
     */
    public function index()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && !empty($input['id']))
            {
                //get record
                $consignment = $this->check($input['id']);
                if(!$consignment)
                    return ['success' => false, 'msg' => 'This consignment is not longer available. You cannot sell more tickets for this event.'];
                //get all seats
                $seats = DB::table('seats')
                            ->join('tickets', 'tickets.id', '=', 'seats.ticket_id')
                            ->select(DB::raw('seats.id, tickets.ticket_type, seats.seat, seats.status,
                                             COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0)) AS retail_price,
                                             COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0)) AS processing_fee,
                                             ROUND(COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0))+COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0)),2) AS total'))
                            ->where('seats.consignment_id','=',$consignment->id)
                            ->orderBy('tickets.ticket_type','ASC')->orderByRaw('CAST(seats.seat AS UNSIGNED) ASC')->get();
                if(!count($seats))
                    return ['success' => false, 'msg' => 'This consignment has no tickets availables for sale'];
                //summary of seats
                $summary = DB::table('seats')
                            ->join('tickets', 'tickets.id', '=', 'seats.ticket_id')
                            ->select(DB::raw('COUNT(seats.id) AS qty, seats.status,
                                             SUM(ROUND(COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0))+COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0)),2)) AS total'))
                            ->where('seats.consignment_id','=',$consignment->id)
                            ->orderBy('seats.status')->groupBy('seats.status')->get()->toArray();
                //if it has purchase to make check if the element is in the shoppingcart
                $shoppingcart = ['qty'=>0,'status'=>'Shoppingcart','total'=>0,'seats'=>[]];
                if(!($consignment->purchase || $consignment->qty==0))
                {
                    $s_token = Util::s_token(false,true);
                    $items = Shoppingcart::whereNotNull('options')->where('session_id', $s_token)->get(['options','number_of_items','total_cost']);
                    foreach ($items as $i)
                    {
                        if(!empty($i->options) && Util::isJSON($i->options))
                        {
                            $option = json_decode($i->options,true);
                            if($option['consignments'] == $consignment->id)
                            {
                                $shoppingcart['qty'] += $i->number_of_items;
                                $shoppingcart['total'] += $i->total_cost;
                                $shoppingcart['seats'][] = $option['seats'];
                            }
                        }
                    }
                }
                $summary[] = $shoppingcart;
                //code here
                return ['success'=>true,'seats'=>$seats,'shoppingcart'=>$shoppingcart,'summary'=>$summary];
            }
            else
            {
                $current = date('Y-m-d H:i:s');
                //conditions to search
                $where = [['users.id','=',Auth::user()->id]];
                //search event status
                if(!empty($input['e_status']))
                {
                    $search['e_status'] = 1;
                    $where[] = ['show_times.show_time','>',$current];
                }
                else
                    $search['e_status'] = 0;
                //search accounting status
                if(!empty($input['a_status']))
                {
                    $search['a_status'] = 1;
                    $where[] = ['consignments.status','!=','Voided'];
                }
                else
                    $search['a_status'] = 0;
                //get all records
                $consignments = DB::table('consignments')
                            ->join('users', 'users.id', '=', 'consignments.seller_id')
                            ->join('show_times', 'show_times.id', '=', 'consignments.show_time_id')
                            ->join('shows', 'shows.id', '=', 'show_times.show_id')
                            ->join('venues', 'venues.id', '=', 'shows.venue_id')
                            ->leftJoin('seats', 'seats.consignment_id', '=', 'consignments.id')
                            ->leftJoin('tickets', 'tickets.id', '=', 'seats.ticket_id')
                            ->leftJoin('purchases', 'purchases.id', '=', 'seats.purchase_id')
                            ->select(DB::raw('consignments.id, shows.name AS show_name, venues.name AS venue_name, show_times.show_time,
                                             IF(show_times.show_time>NOW(),1,0) AS e_status, 
                                             IF(consignments.created = purchases.created,1,0) AS purchase, consignments.due_date,
                                             consignments.status AS a_status, COUNT(seats.id) AS qty, shows.cutoff_hours,
                                             ROUND(SUM(COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0))+COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0))),2) AS total'))
                            ->where($where)
                            ->where(function($query)
                            {
                                $query->where('seats.status','!=','Voided')
                                      ->orWhereNull('seats.status');
                            })
                            ->groupBy('consignments.id')->orderBy('show_times.show_time','DESC')->orderBy('shows.name')->get();
                foreach ($consignments as $c)
                {
                    if($c->a_status!='Voided' && (date('Y-m-d',strtotime($c->show_time))>date('Y-m-d') || 
                            (date('Y-m-d',strtotime($c->show_time))==date('Y-m-d') &&  date('H',strtotime($c->show_time))-$c->cutoff_hours > date('H') )))
                        $c->active = true;
                    else
                        $c->active = false;
                }
                //return view
                return view('production.user.consignments',compact('consignments','search'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Production User Consignments: '.$ex->getMessage());
        }
    }
    /**
     * Check valid consignment.
     *
     * @return Method
     */
    public function check($id)
    {
        try {
            //get record
            return DB::table('consignments')
                        ->join('users', 'users.id', '=', 'consignments.seller_id')
                        ->join('show_times', 'show_times.id', '=', 'consignments.show_time_id')
                        ->join('shows', 'shows.id', '=', 'show_times.show_id')
                        ->join('venues', 'venues.id', '=', 'shows.venue_id')
                        ->leftJoin('seats', 'seats.consignment_id', '=', 'consignments.id')
                        ->leftJoin('tickets', 'tickets.id', '=', 'seats.ticket_id')
                        ->leftJoin('purchases', 'purchases.id', '=', 'seats.purchase_id')
                        ->select(DB::raw('consignments.id, shows.name AS show_name, venues.name AS venue_name, show_times.show_time,
                                         IF(show_times.show_time>NOW(),1,0) AS e_status, show_times.id AS show_time_id,
                                         IF(consignments.created = purchases.created,1,0) AS purchase, consignments.due_date,
                                         consignments.status AS a_status, COUNT(seats.id) AS qty, shows.cutoff_hours,
                                         ROUND(SUM(COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0))+COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0))),2) AS total'))
                        ->where('users.id','=',Auth::user()->id)->where('consignments.id','=',$id)
                        ->where(function($query)
                        {
                            $query->where('seats.status','!=','Voided')
                                  ->orWhereNull('seats.status');
                        })
                        ->where(function($query)
                        {
                            $query->whereDate('show_times.show_time','>',date('Y-m-d'))
                                  ->orWhereRaw('DATE(show_times.show_time)="'.date('Y-m-d').'" AND HOUR(show_times.show_time)-shows.cutoff_hours','>',date('H'));
                        })
                        ->groupBy('consignments.id')->orderBy('show_times.show_time','DESC')->orderBy('shows.name')->first();
        } catch (Exception $ex) {
            throw new Exception('Error Production User Consignments save: '.$ex->getMessage());
        }
    }
    
    /**
     * Save updated consignment.
     *
     * @return Method
     */
    public function save()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && !empty($input['consignment_id']) && !empty($input['seat']) && !empty($input['total_money']) && !empty($input['total_qty']))
            {
                //get record
                $consignment = $this->check($input['consignment_id']);
                if(!$consignment)
                    return ['success' => false, 'msg' => 'This consignment is not longer available. You cannot sell more tickets for this event.'];
                if($consignment->purchase)
                {
                    DB::table('seats')->whereIn('id', $input['seat'])->update(['status' => 'Sold']);
                    return ['success'=>true,'msg'=>'Consigment tickets updated successfully!'];
                }
                else
                {
                    //add items to session
                    $s_token = Util::s_token(false,true);
                    foreach ($input['seat'] as $seat_id)
                    {
                        $success = Shoppingcart::add_item($consignment->show_time_id, null, 1, $s_token, $seat_id);
                        if(!$success['success'])
                            return $success;
                    } 
                    return ['success'=>true,'msg'=>'Consigment tickets updated successfully!'];
                }
            }
            return ['success' => false, 'msg' => 'This consignment cannot be modify.<br>Invalid parameters.'];
        } catch (Exception $ex) {
            throw new Exception('Error Production User Consignments save: '.$ex->getMessage());
        }
    }
    
       
}
