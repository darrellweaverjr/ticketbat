<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Model;
use App\Http\Libraries\usaepay\umTransaction;

/**
 * TransactionRefund class
 *
 * @author ivan
 */
class TransactionRefund extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_refunds';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Get the purchase record associated with the transaction.
     */
    public function purchase()
    {
        return $this->belongsTo('App\Http\Models\Purchase','purchase_id');
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
    public static function usaepay($purchase,$user,$amount,$description=null,$created)
    {
        try {
            //init params
            $tran=new umTransaction();
            $tran->testmode=env('USAEPAY_TEST',1);
            $tran->key=env('USAEPAY_KEY_REFUND','1AGjBQ3Z5Iq10NB154PAZ04I1xnWPdZv');
            $tran->pin=env('USAEPAY_PIN_REFUND','4826');;
            $tran->ip=Request::getClientIp();
            //command
            $tran->command = 'creditvoid';
            //refund info
            $tran->refnum=$purchase->transaction->refnum;	
            //$tran->invoice=$purchase->transaction->invoice_num;
            //$tran->usesandbox=true;
            $tran->amount=$amount;
            if(!empty($description))
                $tran->description=$description;
            //process
            $success = ($tran->Process() && $tran->result=='Approved');
            //store into DB
            $transaction = new TransactionRefund;
            $transaction->purchase_id = $purchase->id;
            $transaction->user_id = $user->id;
            $transaction->amount = $tran->amount;
            if(!empty($description))
                $transaction->description = $description;
            if(!empty($tran->type))
                $transaction->type = $tran->type;
            if(!empty($tran->key))
                $transaction->key = $tran->key;
            if(!empty($tran->ref_num))
                $transaction->ref_num = $tran->ref_num;
            if(!empty($tran->authcode))
                $transaction->authcode = $tran->authcode;
            if(!empty($tran->is_duplicate))
                $transaction->is_duplicate = $tran->is_duplicate;
            if(!empty($tran->result_code))
                $transaction->result_code = $tran->result_code;
            if(!empty($tran->result))
                $transaction->result = $tran->result;
            if(!empty($tran->error))
                $transaction->error = $tran->error;
            if(!empty($tran->error_code))
                $transaction->error_code = $tran->error_code;
            $transaction->created = $created;
            $transaction->save();
            //return
            if($success)
                return ['success'=>true, 'msg'=>'<b>'.$transaction->result.'</b>'];
            return ['success'=>false, 'msg'=>'<b>'.$transaction->result.'. '.$transaction->error.'</b>'];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
}
