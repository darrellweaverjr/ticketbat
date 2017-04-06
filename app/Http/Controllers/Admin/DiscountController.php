<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Discount;
use App\Http\Models\Show;
use App\Http\Models\Util;

/**
 * Manage Discounts
 *
 * @author ivan
 */
class DiscountController extends Controller{    
    /**
     * List all coupons and return default view.
     *
     * @return view
     */
    public function index()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && isset($input['id']))
            {
                //get selected record
                $discount = Discount::find($input['id']);  
                if(!$discount)
                    return ['success'=>false,'msg'=>'There was an error getting the coupon.<br>Maybe it is not longer in the system.'];
                $tickets = DB::table('discount_tickets')
                                ->select('ticket_id AS id','fixed_commission AS fc')
                                ->where('discount_id','=',$discount->id)->get();
                return ['success'=>true,'discount'=>$discount,'tickets'=>$tickets];
            }
            else
            {
                //SEARCH
                $discount_types = [];
                $discount_scopes = [];
                $coupon_types = [];
                $tickets = [];
                $discounts = [];
                //if user has permission to view
                if(in_array('View',Auth::user()->user_type->getACLs()['COUPONS']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['COUPONS']['permission_scope'] != 'All')
                    {
                        $discounts = DB::table('discounts')
                                ->leftJoin('purchases', 'purchases.discount_id', '=', 'discounts.id')
                                ->select(DB::raw('discounts.id,discounts.code,discounts.description,discounts.discount_type,discounts.discount_scope,discounts.coupon_type, 
                                                  COUNT(purchases.id) AS purchases'))
                                ->where('discounts.audit_user_id','=',Auth::user()->id)
                                ->groupBy('discounts.id')
                                ->orderBy('discounts.code')
                                ->get();
                        $tickets = DB::table('tickets')
                                ->join('packages', 'packages.id', '=', 'tickets.package_id')
                                ->join('shows', 'shows.id', '=', 'tickets.show_id')
                                ->join('venues', 'venues.id', '=', 'shows.venue_id')
                                ->select('tickets.id','venues.name AS venue_name','shows.name AS show_name','tickets.ticket_type','packages.title')
                                ->where('tickets.is_active','=',1)
                                ->where(function($query)
                                {
                                    $query->whereIn('shows.venue_id',[Auth::user()->venues_edit])
                                          ->orWhere('discounts.audit_user_id','=',Auth::user()->id);
                                })
                                ->groupBy('tickets.id')
                                ->orderBy('venues.name','ASC')
                                ->orderBy('shows.name','ASC')
                                ->orderBy('tickets.ticket_type','ASC')
                                ->get();
                    }//all
                    else
                    {
                        $discounts = DB::table('discounts')
                                ->leftJoin('purchases', 'purchases.discount_id', '=', 'discounts.id')
                                ->select(DB::raw('discounts.id,discounts.code,discounts.description,discounts.discount_type,discounts.discount_scope,discounts.coupon_type, 
                                                  COUNT(purchases.id) AS purchases'))
                                ->groupBy('discounts.id')
                                ->orderBy('discounts.code')
                                ->get();
                        $tickets = DB::table('tickets')
                                ->join('packages', 'packages.id', '=', 'tickets.package_id')
                                ->join('shows', 'shows.id', '=', 'tickets.show_id')
                                ->join('venues', 'venues.id', '=', 'shows.venue_id')
                                ->select('tickets.id','venues.name AS venue_name','shows.name AS show_name','tickets.ticket_type','packages.title')
                                ->where('tickets.is_active','=',1)
                                ->groupBy('tickets.id')
                                ->orderBy('venues.name','ASC')
                                ->orderBy('shows.name','ASC')
                                ->orderBy('tickets.ticket_type','ASC')
                                ->get();
                    }
                    //enum
                    $discount_types = Util::getEnumValues('discounts','discount_type');
                    $discount_scopes = Util::getEnumValues('discounts','discount_scope');
                    $coupon_types = Util::getEnumValues('discounts','coupon_type');
                }
                //return view
                return view('admin.coupons.index',compact('discounts','discount_types','discount_scopes','coupon_types','tickets'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Discount Index: '.$ex->getMessage());
        }
    }
    /**
     * Save new or updated discount.
     *
     * @void
     */
    public function save()
    {
        try {
            //init
            $input = Input::all();  
            //save all record      
            if($input)
            {
                $current = date('Y-m-d H:i:s');
                if(isset($input['id']) && $input['id'])
                {
                    if(Discount::where('code','=',$input['code'])->where('id','!=',$input['id'])->count())
                        return ['success'=>false,'msg'=>'There was an error saving the discount.<br>That code is already in the system.','errors'=>'code'];
                    $discount = Discount::find($input['id']);
                    $discount->updated = $current;
                }                    
                else
                {                    
                    if(Discount::where('code','=',$input['code'])->count())
                        return ['success'=>false,'msg'=>'There was an error saving the discount.<br>That code is already in the system.','errors'=>'code'];
                    $discount = new Discount;
                    $discount->created = $current;
                    $discount->audit_user_id = Auth::user()->id;
                }
                //save discount
                $discount->code = $input['code'];
                $discount->description = $input['description'];
                $discount->discount_type = $input['discount_type'];
                $discount->discount_scope = $input['discount_scope'];
                $discount->start_date = $input['start_date'];
                $discount->end_date = $input['end_date'];
                $discount->start_num = $input['start_num'];
                $discount->end_num = (isset($input['end_num']) && $input['end_num'])? $input['end_num'] : null;
                $discount->quantity = $input['quantity'];
                if(isset($input['effective_start_date']) && strtotime($input['effective_start_date']) && isset($input['effective_end_date']) && strtotime($input['effective_end_date']))
                {
                    $discount->effective_start_date = $input['effective_start_date'];
                    $discount->effective_end_date = $input['effective_end_date'];
                    $discount->effective_dates = 1;
                }
                else
                {
                    $discount->effective_start_date = null;
                    $discount->effective_end_date = null;
                    $discount->effective_dates = 0;
                }
                $discount->coupon_type = $input['coupon_type'];
                $discount->save();
                //update intermediate table with tickets
                if(isset($input['tickets']) && $input['tickets'] && count($input['tickets']))
                {
                    $ticket_ids = [];
                    $discount_tickets = [];
                    foreach ($input['tickets'] as $ticket_id => $fixed_commission)
                    {
                        $ticket_ids[] = $ticket_id;
                        $discount->discount_tickets()->updateExistingPivot($ticket_id,['fixed_commission'=>(!empty($fixed_commission))? $fixed_commission : null]);
                    }
                    $discount->discount_tickets()->sync($ticket_ids);
                }    
                else
                    $discount->discount_tickets()->detach();
                //return
                return ['success'=>true,'msg'=>'Discount saved successfully!'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the discount.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Discounts Save: '.$ex->getMessage());
        }
    }
    /**
     * Remove discounts.
     *
     * @void
     */
    public function remove()
    {
        try {
            //init
            $input = Input::all();
            //delete all records   
            foreach ($input['id'] as $id)
            {
                $discount = Discount::find($id);
                if($discount)
                {
                    $discount->discount_tickets()->detach();
                    $discount->discount_shows()->detach();
                    $discount->user_discounts()->detach();
                }
                if(!$discount->delete())
                    return ['success'=>false,'msg'=>'There was an error deleting the discount(s)!<br>They might have some dependences.'];
            }
            return ['success'=>true,'msg'=>'All records deleted successfully!'];
        } catch (Exception $ex) {
            throw new Exception('Error Discounts Remove: '.$ex->getMessage());
        }
    }
}
