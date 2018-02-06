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
            $transaction->amount = $amount;
            $transaction->description = $description;
            $transaction->type = $tran->type;
            $transaction->key = $tran->key;
            $transaction->ref_num = $tran->ref_num;
            $transaction->authcode = $tran->authcode;
            $transaction->is_duplicate = $tran->is_duplicate;
            $transaction->result_code = $tran->result_code;
            $transaction->result = $tran->result;
            $transaction->error = $tran->error;
            $transaction->error_code = $tran->error_code;
            $transaction->created = $created;
            $transaction->save();
            //return
            if($success)
            {
                $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($created)).'): </b> Refunded successfully $'.$amount.'/$'.$purchase->transaction->amount;
                $purchase->note = ($purchase->note)? $purchase->note.$note : $note;  
                $purchase->status = 'Chargeback';
                $purchase->updated = $current;
                $purchase->save();
                return ['success'=>true, 'msg'=>$transaction->result];
            }
            else
            {
                $note = '&nbsp;<br><b>'.$user->first_name.' '.$user->last_name.' ('.date('m/d/Y g:i a',strtotime($created)).'): </b> Intented to refund $'.$amount.'/$'.$purchase->transaction->amount;
                $purchase->note = ($purchase->note)? $purchase->note.$note : $note; 
                $purchase->updated = $current;
                $purchase->save();
                return ['success'=>false, 'msg'=>$transaction->error];
            }
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
}
