<?php

namespace App\Http\Models;

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
            if($tran->testmode>0)
            {
                $tran->key="_5n4fazc17ya1luc3euqVSj648zOs0D8";
                $tran->usesandbox=true;
            }
            else
                $tran->key="0549A863bCqbKNzS1uw6o75EMgPL3xpQ";
            //command
            $tran->command = 'creditvoid';
            $tran->pin = '4826';
            //refund info
            $tran->refnum=$purchase->transaction->refnum;	
            $tran->amount=$amount;
            if(!empty($description))
                $tran->description=$description;
            //process
            $success = $tran->Process();
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
                return ['success'=>true, 'msg'=>$transaction->result];
            return ['success'=>false, 'msg'=>$transaction->error];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
}
