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
    public static function usaepay($purchase,$user,$amount,$description=null,$created,$input=[])
    {
        try {
            //init
            $ref_num = $purchase->transaction->refnum;
            $tran = null;
            //total refund
            if($amount==$purchase->price_paid)
            {
                $operation = TransactionRefund::connect_usaepay('void',$ref_num,$amount,$description);
                if($operation['success'])
                {
                    TransactionRefund::store_refund($operation['tran'],$purchase,$user,$description,$created,$input);
                    Purchase::where('id',$purchase->id)->update(['status'=>'Void']);
                    return ['success'=>true, 'msg'=>'<b>Purchase #'.$purchase->id.' was voided by USAePay.</b>'];
                }
                else
                {
                    $operation = TransactionRefund::connect_usaepay('refund',$ref_num,$amount,$description);
                    TransactionRefund::store_refund($operation['tran'],$purchase,$user,$description,$created,$input);
                    if($operation['success'])
                    {
                        Purchase::where('id',$purchase->id)->update(['status'=>'Refunded']);
                        return ['success'=>true, 'msg'=>'<b>Purchase #'.$purchase->id.' was refunded by USAePay.</b>'];
                    }
                    else
                        return ['success'=>false, 'msg'=>'<b>The system could not refund purchase #'.$purchase->id.' througth USAePay.</b>'];
                }
            }
            //partial refund
            else
            {
                $refunded_on = date('Y-m-d', strtotime($purchase->created.' +5 days'));
                //if more than 5 days make the refund
                if(date('Y-m-d') >= $refunded_on)
                {
                    $operation = TransactionRefund::connect_usaepay('refund',$ref_num,$amount,$description);
                    TransactionRefund::store_refund($operation['tran'],$purchase,$user,$description,$created,$input);
                    if($operation['success'])
                    {
                        Purchase::where('id',$purchase->id)->update(['status'=>'Refunded']);
                        return ['success'=>true, 'msg'=>'<b>Purchase #'.$purchase->id.' was refunded by USAePay.</b>'];
                    }
                    else
                        return ['success'=>false, 'msg'=>'<b>The system could not refund purchase #'.$purchase->id.' througth USAePay.</b>'];
                }
                else
                    return ['success'=>false, 'msg'=>'<b>The system could not refund purchase #'.$purchase->id.'.<br>You have to wait for 5 days to the transaction be settled on USAePay to refund it.</b>'];
            }
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }

    /*
     * connect with usaepay
     */
    public static function connect_usaepay($command,$ref_num,$amount,$description)
    {
        try {
                //init params
                $tran=new umTransaction();
                $tran->testmode=env('USAEPAY_TEST',1);
                $tran->key=env('USAEPAY_KEY_REFUND','1AGjBQ3Z5Iq10NB154PAZ04I1xnWPdZv');
                $tran->pin=env('USAEPAY_PIN_REFUND','4826');;
                $tran->ip=Request::getClientIp();
                //command
                $tran->command = $command;
                //refund info
                $tran->refnum=$ref_num;
                $tran->amount=$amount;
                if(!empty($description))
                    $tran->description=$description;
                //process
                $success = ($tran->Process() && $tran->result=='Approved');
                return ['success'=>$success,'tran'=>$tran];

        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }

    /*
     * connect with usaepay
     */
    public static function store_refund($tran,$purchase,$user,$description,$created,$input)
    {
        try {
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
                $transaction->payment_type = 'Credit';
                if($transaction->result == 'Approved')
                {
                    $transaction->refunded_reason = $purchase->refunded_reason;
                    $transaction->quantity = (isset($input['quantity']))? $input['quantity'] : $purchase->quantity;
                    $transaction->retail_price = (isset($input['retail_price']))? $input['retail_price'] : $purchase->retail_price;
                    $transaction->savings = (isset($input['savings']))? $input['savings'] : $purchase->savings;
                    $transaction->processing_fee = (isset($input['processing_fee']))? $input['processing_fee'] : $purchase->processing_fee;
                    $transaction->printed_fee = (isset($input['printed_fee']))? $input['printed_fee'] : $purchase->printed_fee;
                    $transaction->sales_taxes = (isset($input['sales_taxes']))? $input['sales_taxes'] : $purchase->sales_taxes;
                    $transaction->commission_percent = (isset($input['commission_percent']))? $input['commission_percent'] : $purchase->commission_percent;
                }
                $transaction->save();

        } catch (Exception $ex) {

        }
    }


}
