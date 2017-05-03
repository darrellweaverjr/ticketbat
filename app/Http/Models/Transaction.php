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
    public static function usaepay($client,$payment,$shoppingcart,$created)
    {
        try {
            //init params
            $tran=new umTransaction();
            $tran->testmode=env('USAEPAY_TEST',1); 
            if($tran->testmode)
            {
                $tran->key="_5n4fazc17ya1luc3euqVSj648zOs0D8"; 
                $tran->usesandbox=true;
            }
            else
                $tran->key="0549A863bCqbKNzS1uw6o75EMgPL3xpQ"; 
            //card info            
            $tran->card = $payment['card'];	
            $tran->exp = $payment['month'].substr($payment['year'],-2);
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
            $coupon = (!empty($shoppingcart['coupon']))? ' coupon: '.$shoppingcart['coupon'] : ' no coupon';
            foreach($shoppingcart['items'] as $item)
            {
                $tran_description.= '* '.$item->number_of_items.' '.$item->product_type.' '.$item->package.' for '.$item->name.' on '.$item->show_time.' with '.$coupon.' *'; 
                $tran->custid = $item->item_id;
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
            $payment['card'] = substr($payment['card'], -4); 
            //store into DB
            $transaction = new Transaction;
            $transaction->show_time_id = $tran->custid;
            $transaction->customer_id = $client['customer_id'];
            $transaction->user_id = $client['user_id'];
            $transaction->trans_result = $tran->result;
            $transaction->invoice_num = $tran->invoice;
            $transaction->amount = $tran->amount;
            $transaction->card_holder = $tran->cardholder;
            $transaction->avs_result = $tran->avs_result;
            $transaction->cvv2_result = $tran->cvv2_result;
            $transaction->error_code = $tran->errorcode;
            $transaction->error = $tran->error;
            $transaction->authcode = $tran->authcode;
            $transaction->refnum = $tran->refnum;
            $transaction->last_4 = $payment['card'];
            $transaction->result = json_encode($tran,true);
            $transaction->tracking_id = 0;
            $transaction->shopping_cart_session_id = $payment['s_token'];
            $transaction->transaction_status = 0;
            $transaction->created = $created;
            $transaction->save();
            //return
            if($success)
                return ['success'=>true, 'transaction_id'=>$transaction->id];
            else
            {
                $html = '<b>Transaction:<b><br>'.json_encode(array_filter((array)$tran),true).'<br><br>';
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
