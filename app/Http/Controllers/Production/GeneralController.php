<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Http\Models\Country;
use App\Http\Models\Region;
use App\Http\Models\Contact;
use App\Http\Models\Util;

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
            
            $info = Input::all();
            if(!empty($info['name']) && !empty($info['email']) && !empty($info['message']))
            {
                //create entry on table
                $contact = new Contact;
                $contact->name = $info['name'];
                $contact->email = $info['email'];
                $contact->phone = (!empty($info['phone']))? $info['phone'] : null;
                $contact->show_name = (!empty($info['event']))? $info['event'] : null; 
                $contact->show_time = (!empty($info['date']))? $info['date'] : null; 
                $contact->system_info = Util::system_info();
                $contact->message = $info['message'];
                $contact->save();
                if($contact->email_us())
                {
                    $msg = "We've received your request (".$contact->id.").<br>We will get back to you as soon as we can, which will be no later than 48 business hours.";
                    return ['success'=>true, 'msg'=>$msg];
                }
                return ['success'=>false, 'msg'=>'There was an error sending the email. Please try later!'];
            }
            return ['success'=>false, 'msg'=>'You must fill out correctly the form!'];
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
    
    /**
     * Get merchandise page.
     *
     * @return Method
     */
    public function merchandises()
    {
        try {
            //return view
            return view('production.merchandises.index');
        } catch (Exception $ex) {
            throw new Exception('Error Production General merchandises: '.$ex->getMessage());
        }
    }
       
}
