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
            $created = date('Y-m-d h:i:s');
            if(!empty($info['first_name']) && !empty($info['last_name']) && !empty($info['address']) && !empty($info['city']) 
            && !empty($info['country']) && !empty($info['region']) && !empty($info['zip']) && !empty($info['phone']) && !empty($info['s_token'])
            && !empty($info['email']) && !empty($info['card']) && !empty($info['month']) && !empty($info['year']) && !empty($info['cvv']))
            {
                //get all items in shoppingcart
                $shoppingcart = Shoppingcart::calculate_session($info['s_token'],true);
                //make transaction
                $transaction = Transaction::usaepay($user_id, $customer_id, $info, $shoppingcart, $created);
            }
            
            
            
            
            
            return $this->transaction_make();
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    } 
    
    /*
     * setting up the customer
     */
    public function customer_set()
    {
        try {
            
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }  
    
    /*
     * make transaction
     */
    public function transaction_make()
    {
        try {
            $tran=new umTransaction();
 
            $tran->key="_U88GQ3F4A64h5QH82x26DhuBfB1aH5C"; 		
            $tran->pin="1234";		
            $tran->usesandbox=true;
            $tran->testmode=1; 
            $tran->card="4000100011112224";	
            $tran->exp="0919";			
            $tran->amount="1.00";			
            $tran->invoice="1234";   		
            $tran->cardholder="Test T Jones"; 	
            $tran->street="1234 Main Street";	
            $tran->zip="05673";			
            $tran->description="Online Order";	
            $tran->cvv2="123";			


            echo "<h1>Please wait one moment while we process your card...<br>\n";
            flush();

            if($tran->Process())
            {
                    echo "<b>Card Approved</b><br>";
                    echo "<b>Authcode:</b> " . $tran->authcode . " <br>".env('SERVER_NAME').'<br>';
                    echo "<b>RefNum:</b> " . $tran->refnum . "<br>";
                    echo "<b>AVS Result:</b> " . $tran->avs_result . "<br>";
                    echo "<b>Cvv2 Result:</b> " . $tran->cvv2_result . "<br>";
            } else {
                    echo "<b>Card Declined</b> (" . $tran->result . ")<br>";
                    echo "<b>Reason:</b> " . $tran->error . "<br>";	
                    if(@$tran->curlerror) echo "<b>Curl Error:</b> " . $tran->curlerror . "<br>";	
            }	
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }  
    
    /*
     * saving the transaction into the database
     */
    public function transaction_save()
    {
        try {
            
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
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
