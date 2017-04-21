<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Models\Shoppingcart;
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
            return $this->transaction_make();
        } catch (Exception $ex) {
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    }   
    
    /*
     * make transaction
     */
    public function transaction_make()
    {
        try {
            $tran=new umTransaction();
 
            $tran->key="_U88GQ3F4A64h5QH82x26DhuBfB1aH5C"; 		// Your Source Key
            $tran->pin="1234";		// Source Key Pin
            $tran->usesandbox=true;		// Sandbox true/false
            //$tran->ip=$REMOTE_ADDR;   // This allows fraud blocking on the customers ip address 
            $tran->testmode=0;    // Change this to 0 for the transaction to process

            //$tran->command="cc:sale";    // Command to run; Possible values are: cc:sale, cc:authonly, cc:capture, cc:credit, cc:postauth, check:sale, check:credit, void, void:release, refund, creditvoid and cc:save. Default is cc:sale. 

            $tran->card="4000100011112224";		// card number, no dashes, no spaces
            $tran->exp="0919";			// expiration date 4 digits no /
            $tran->amount="1.00";			// charge amount in dollars
            $tran->invoice="1234";   		// invoice number.  must be unique.
            $tran->cardholder="Test T Jones"; 	// name of card holder
            $tran->street="1234 Main Street";	// street address
            $tran->zip="05673";			// zip code
            $tran->description="Online Order";	// description of charge
            $tran->cvv2="123";			// cvv2 code	


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
    
}
