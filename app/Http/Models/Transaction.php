<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Libraries\usaepay\umTransaction;

/**
 * Transaction class
 *
 * @author ivan
 */
class Transaction extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Get the show_time record associated with the transaction.
     */
    public function show_time()
    {
        return $this->belongsTo('App\Http\Models\ShowTime','show_time_id');
    }
    /**
     * Get the customer record associated with the transaction.
     */
    public function customer()
    {
        return $this->belongsTo('App\Http\Models\Customer','customer_id');
    }
    /**
     * Get the user record associated with the transaction.
     */
    public function user()
    {
        return $this->belongsTo('App\Http\Models\User','user_id');
    }
    
    //PERSONALIZED FUNCTIONS
    /*
     * make transaction
     */
    public function usaepay($card_info,$card_holder,$items)
    {
        try {
            //init params
            $tran=new umTransaction();
            $tran->testmode=env('USAEPAY_TEST',1); 
            if($tran->testmode)
            {
                $tran->key="_U88GQ3F4A64h5QH82x26DhuBfB1aH5C"; 
                $tran->usesandbox=true;
            }
            else
                $tran->key="0549A863bCqbKNzS1uw6o75EMgPL3xpQ"; 
            //card info            
            $tran->card = $card_info['card'];	
            $tran->exp = $card_info['month'].$card_info['year'];
            if(!empty($card_info['cvv']))
                $tran->cvv2 = $card_info['cvv'];
            $tran->amount = $card_info['amount'];			
            $tran->invoice = $card_info['s_token'];  
            $tran->orderid = $card_info['s_token']; 
            //cardholder info
            $tran->cardholder = strtoupper($card_holder['first_name'].' '.$card_holder['last_name']); 
            if(!empty($card_holder['street']))
                $tran->street = $tran->billstreet = $card_holder['street'];
            if(!empty($card_holder['city']))
                $tran->billcity = $card_holder['city'];
            if(!empty($card_holder['region']))
                $tran->billstate = $card_holder['region'];
            if(!empty($card_holder['country']))
                $tran->billcountry = $card_holder['country'];
            if(!empty($card_holder['zip']))
                $tran->zip = $tran->billzip = $card_holder['zip'];
            //description
            $tran->description = "Online Order";
            //swipe card
            if(!empty($card_info['UMcardpresent']))
            {
                $tran->cardpresent  = $card_info['UMcardpresent'];
                $tran->magstripe  = $card_info['UMmagstripe'];
                $tran->dukpt  = $card_info['UMdukpt'];
                $tran->termtype  = $card_info['UMtermtype'];
                $tran->magsupport  = $card_info['UMmagsupport'];
                $tran->contactless  = $card_info['UMcontactless'];
                $tran->signature  = $card_info['UMsignature'];
            }
            //process
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
            unset($tran->card); $info['card'] = '...'.substr($info['card'], -4); //dont show credit card number       
            // Add transaction even if it is declined
            $output = $this->addTransaction($tran, $info, $sc_session, $customer_id, $user_id, $coupon_id, $created);
            if(!$output['success'])
            {
                foreach ($tran as $key => $value) if(!$value) unset($tran->$key);
                $browser = 'IP('.$_SERVER ['REMOTE_ADDR'].') - '.$_SERVER['HTTP_USER_AGENT'];
                $params = array('stuff' => compact('tran', 'info', 'gift', 'items'),'errors'=>$output['errors'],'browser'=>$browser);
                $viewEmail = View::make('emails.empty', $params);
                $email = new EmailModel(Config::get('mail.from_transactions'),Config::get('mail.to_transactions'),'Transaction Error');
                $email->view($viewEmail);
                $response = $email->send();
            }      
            
            
            
            
            
            
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }  
}
