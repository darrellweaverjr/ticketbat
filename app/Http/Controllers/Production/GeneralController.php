<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Models\Country;
use App\Http\Models\Region;
use App\Http\Models\Contact;

class GeneralController extends Controller
{
    /**
     * Send contact information.
     *
     * @return Method
     */
    public function contact()
    {
        try {
            
        } catch (Exception $ex) {
            throw new Exception('Error Production General Contact: '.$ex->getMessage());
        }
    }
    /**
     * Get countries.
     *
     * @return Method
     */
    public function country()
    {
        try {
            //get selected record
            $countries = Country::get(['code','name']);  
            $regions = Region::where('country','US')->get(['code','name']);  
            return ['success'=>true,'countries'=>$countries,'regions'=>$regions];
        } catch (Exception $ex) {
            throw new Exception('Error Production General country: '.$ex->getMessage());
        }
    }
    /**
     * Get regions of country.
     *
     * @return Method
     */
    public function region()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && isset($input['country']))
            {
                //get selected record
                $regions = Region::where('country',$input['country'])->get(['code','name']);  
                return ['success'=>true,'regions'=>$regions];
            }
            return ['success'=>false,'msg'=>'There was an error getting the regions.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Production General region: '.$ex->getMessage());
        }
    }
       
}
