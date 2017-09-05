<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Discount;
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
                                ->select('discount_tickets.ticket_id AS id')
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
                                                  IF(DATE(discounts.start_date)<=CURDATE() && DATE(discounts.end_date)>=CURDATE(),1,0) AS active,
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
                                                  IF(DATE(discounts.start_date)<=CURDATE() && DATE(discounts.end_date)>=CURDATE(),1,0) AS active,
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
                //check type of coupon
                if(!empty($input['multiple']))
                {
                    $input['start_num']=0;
                    $input['end_num']=null;
                }
                else
                {
                    if(empty($input['start_num']))
                        return ['success'=>false,'msg'=>'You have to set the discount off for this coupon.','errors'=>'start_num'];
                    if($input['discount_type']=='N for N')
                    {
                        if(empty($input['end_num']))
                            return ['success'=>false,'msg'=>'You have to set the end qty range for this coupon.','errors'=>'end_num'];
                    }
                    else
                        $input['end_num']=null;
                }
                //get element
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
                $discount->description = strip_tags($input['description']);
                $discount->discount_type = $input['discount_type'];
                $discount->discount_scope = $input['discount_scope'];                
                $discount->start_date = $input['start_date'];
                $discount->end_date = $input['end_date'];
                $discount->start_num = $input['start_num'];
                $discount->end_num = $input['end_num'];
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
                $discount->distributed_at = (!empty($input['distributed_at']))? $input['distributed_at'] : null;
                $discount->save();
                //update intermediate table with tickets
                if(isset($input['tickets']) && $input['tickets'] && count($input['tickets']))
                {                    
                    $discount->discount_tickets()->sync($input['tickets']);
                    if($input['multiple']>0)
                    {
                        if($input['discount_scope']!='N for N')
                            DB::table('discount_tickets')->where('discount_id','=',$discount->id)
                                ->update(['end_num'=>null]);
                    }
                    else
                    {
                        DB::table('discount_tickets')->where('discount_id','=',$discount->id)
                                ->update(['start_num'=>null,'end_num'=>null]);
                    }
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
    
    /**
     * Update tickets .
     *
     * @void
     */
    public function tickets()
    {
        try {
            //init
            $input = Input::all();
            //action 0, get
            if(isset($input['action']) && $input['action']==0 && !empty($input['ticket_id']) && !empty($input['discount_id']))
            {
                $discount_tickets = DB::table('discount_tickets')->select('discount_tickets.*')
                                ->where('discount_id','=',$input['discount_id'])->where('ticket_id','=',$input['ticket_id'])->first();
                return ['success'=>true,'discount_tickets'=>$discount_tickets];
            }
            //action 1, update
            else if(isset($input['action']) && $input['action']==1)
            {
                if(empty($input['fixed_commission']))
                    $input['fixed_commission']=null;
                if(empty($input['start_num']))
                    $input['start_num']=null;
                if(empty($input['end_num']))
                    $input['end_num']=null;
                if($input['multiple'] && empty($input['start_num']))
                    return ['success'=>false,'msg'=>'You must set a qty off for this ticket!'];
                $discount_tickets = DB::table('discount_tickets')->select('discount_tickets.*')
                                ->where('discount_id','=',$input['discount_id'])->where('ticket_id','=',$input['ticket_id'])->first();
                if($discount_tickets)
                {
                    DB::table('discount_tickets')->where('discount_id','=',$input['discount_id'])->where('ticket_id','=',$input['ticket_id'])
                            ->update(['fixed_commission'=>$input['fixed_commission'],'start_num'=>$input['start_num'],'end_num'=>$input['end_num']]);
                }
                else
                {
                    DB::table('discount_tickets')->insert(
                            ['discount_id'=>$input['discount_id'],'ticket_id'=>$input['ticket_id'],
                             'fixed_commission'=>$input['fixed_commission'],'start_num'=>$input['start_num'],'end_num'=>$input['end_num']]
                    );
                }
                return ['success'=>true];
            }
            return ['success'=>false,'msg'=>'You must fill out correctly the form!'];
        } catch (Exception $ex) {
            throw new Exception('Error Discounts Tickets: '.$ex->getMessage());
        }
    }
}
