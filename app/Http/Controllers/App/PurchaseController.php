<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Models\Shoppingcart;
use App\Http\Models\Transaction;
use App\Http\Models\Util;
use App\Http\Libraries\usaepay\umTransaction;

/**
 * Buy tickets for the app
 *
 * @author ivan
 */
class PurchaseController extends Controller{
        
    /*
     * buy all items in the cart
     */
    public function buy()
    {
        try {
            $current = date('Y-m-d h:i:s');
            if(!empty($info['first_name']) && !empty($info['last_name']) && !empty($info['address']) && !empty($info['city']) 
            && !empty($info['country']) && !empty($info['region']) && !empty($info['zip']) && !empty($info['phone']) && !empty($info['s_token'])
            && !empty($info['email']) && !empty($info['card']) && !empty($info['month']) && !empty($info['year']) && !empty($info['cvv']))
            {
                $client = $this->customer_set($info, $current);
                if(!$client['success'])
                    return Util::json($client);
                //get all items in shoppingcart
                $shoppingcart = Shoppingcart::calculate_session($info['s_token'],true);
                //check payment or free
                if($shoppingcart['total']>0)
                {
                    //make transaction
                    $transaction = Transaction::usaepay($client->user_id, $client->customer_id, $info, $shoppingcart, $current);
                }
                else
                {
                    
                }
                //save purchase
                
                //send receipts
                
            }
            return Util::json(['success'=>false, 'msg'=>'Fill the form out correctly!']);
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    } 
    
    /*
     * setting up the customer
     */
    public function customer_set($info,$current)
    {
        try {
            //init set 
            $send_welcome_email = false;
            //checking the email
            $info['email'] = trim(strtolower($info['email']));
            if(!filter_var($info['email'], FILTER_VALIDATE_EMAIL))
                return ['success'=>false, 'msg'=>'Enter a valid email address.'];
            //set up user and customer
            $user = User::where('email','=',$info['email'])->first();
            if(!$user)
            {
                //send welcome email
                $send_welcome_email = true;
                //create user
                $user = new User;
                $user->created = $current;
                $user->user_type_id = 2;
                $user->is_active = 1;
                $user->force_password_reset = 0;
                $location = new Location;
                $location->created = $current;
            }
            else
                $location = $user->location();
            //save location
            $location->address = $input['address'];
            $location->city = $input['city'];
            $location->state = strtoupper($input['region']);
            $location->zip = $input['zip'];
            $location->country = $input['country'];
            $location->set_lng_lat();
            $location->save();
            //save user
            $user->location()->associate($location);
            $user->first_name = $input['first_name'];
            $user->last_name = $input['last_name'];
            $user->phone = $input['phone'];
            $user->save();
            //send email welcome
            if($send_welcome_email)
                $user->welcome_email(true);
            //get customer
            $customer_id = $user->update_customer();
            if(!$customer)
                return ['success'=>false, 'msg'=>'There is an error setting up the customer information.'];
            return ['success'=>true, 'user_id'=>$user->id, 'customer_id'=>$customer_id];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error setting up the customer information!'];
        }
    }  
    
    /*
     * saving the purchase into the database
     */
    public function purchase_save()
    {
        try {
            
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }  
    
}
