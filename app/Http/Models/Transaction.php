<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Libraries\usaepay\umTransaction;
use App\Mail\EmailSG;

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
    public static function usaepay($user_id,$customer_id,$payment,$shoppingcart,$created)
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
            $tran->card = $payment['card'];	
            $tran->exp = $payment['month'].$payment['year'];
            if(!empty($payment['cvv']))
                $tran->cvv2 = $payment['cvv'];
            $tran->amount = $shoppingcart['total'];			
            $tran->invoice = $payment['s_token'];  
            $tran->orderid = $payment['s_token']; 
            //cardholder info
            $tran->cardholder = strtoupper($payment['first_name'].' '.$payment['last_name']); 
            if(!empty($payment['street']))
                $tran->street = $tran->billstreet = $payment['street'];
            if(!empty($payment['city']))
                $tran->billcity = $payment['city'];
            if(!empty($payment['region']))
                $tran->billstate = $payment['region'];
            if(!empty($payment['country']))
                $tran->billcountry = $payment['country'];
            if(!empty($payment['zip']))
                $tran->zip = $tran->billzip = $payment['zip'];
            //description
            $tran_description = '';
            $coupon = (!empty($shoppingcart['coupon']))? ' coupon: '.$shoppingcart['items'] : ' no coupon';
            foreach($shoppingcart['items'] as $item)
            {
                $tran_description.= '* '.$item->number_of_items.' '.$item->product_type.' for '.$item->name.' on '.$item->show_time.' with '.$coupon.' *'; 
                $tran->custid = $item->show_time_id;
            } 
            $tran->description = $tran_description;
            //swipe card
            if(!empty($payment['UMcardpresent']))
            {
                $tran->cardpresent  = $payment['UMcardpresent'];
                $tran->magstripe  = $payment['UMmagstripe'];
                $tran->dukpt  = $payment['UMdukpt'];
                $tran->termtype  = $payment['UMtermtype'];
                $tran->magsupport  = $payment['UMmagsupport'];
                $tran->contactless  = $payment['UMcontactless'];
                $tran->signature  = $payment['UMsignature'];
            }
            //process
            $success = $tran->Process();
            //hide credit card number  
            unset($tran->card); 
            $payment['card'] = '...'.substr($payment['card'], -4); 
            //store into DB
            $this->show_time_id = $tran->custid;
            $this->customer_id = $customer_id;
            $this->user_id = $user_id;
            $this->trans_result = $tran->result;
            $this->invoice_num = $tran->invoice;
            $this->amount = $tran->amount;
            $this->card_holder = $tran->cardholder;
            $this->avs_result = $tran->avs_result;
            $this->cvv2_result = $tran->cvv2_result;
            $this->error_code = $tran->errorcode;
            $this->error = $tran->error;
            $this->authcode = $tran->authcode;
            $this->refnum = $tran->refnum;
            $this->last_4 = $tran->last_4;
            $this->result = $tran->result;
            $this->tracking_id = 0;
            $this->shopping_cart_session_id = $payment['s_token'];
            $this->transaction_status = 0;
            $this->created = $created;
            $this->save();
            //return
            if($success)
                return ['success'=>true, 'transaction_id'=>$this->id];
            else
            {
                $html = '<b>Transaction:<b><br>'.json_encode((array)$tran,true).'<br>';
                $html.= '<b>Items:<b><br>'.json_encode($shoppingcart,true).'<br>';
                $email = new EmailSG(null,env('MAIL_APP_ADMIN','debug@ticketbat.com'),'TicketBat App - Transaction Error');
                $email->html($html);
                $email->send();
                return ['success'=>false, 'msg'=>'Card Declined.<br>'.$tran->error];
            }            
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }  
}
