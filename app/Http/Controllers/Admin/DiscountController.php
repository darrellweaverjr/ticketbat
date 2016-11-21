<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
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
                $shows = [];
                foreach($discount->discount_shows as $s)
                    $shows[] = $s->pivot->show_id;
                return ['success'=>true,'discount'=>array_merge($discount->getAttributes(),['shows[]'=>$shows])];
            }
            else
            {
                //get all records        
                $discounts = Discount::orderBy('code')->get();
                $discount_types = Util::getEnumValues('discounts','discount_type');
                $discount_scopes = Util::getEnumValues('discounts','discount_scope');
                $coupon_types = Util::getEnumValues('discounts','coupon_type');
                $shows = Show::orderBy('name')->get();
                //return view
                return view('admin.coupons.index',compact('discounts','discount_types','discount_scopes','coupon_types','shows'));
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
                $discount->quantity = $input['quantity'];
                $discount->effective_dates = $input['effective_dates'];
                $discount->effective_start_date = $input['effective_start_date'];
                $discount->effective_end_date = $input['effective_end_date'];
                $discount->coupon_type = $input['coupon_type'];
                $discount->save();
                //update intermediate table with shows
                if(isset($input['shows']) && $input['shows'] && count($input['shows']))
                    $discount->discount_shows()->sync($input['shows']);
                else
                    $discount->discount_shows()->detach();
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
            if(Discount::destroy($input['id']))
                return ['success'=>true,'msg'=>'All records deleted successfully!'];
            return ['success'=>false,'msg'=>'There was an error deleting the discount(s)!<br>They might have some dependences.'];
        } catch (Exception $ex) {
            throw new Exception('Error Discounts Remove: '.$ex->getMessage());
        }
    }
}
