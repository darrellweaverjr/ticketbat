<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Models\Shoppingcart;
use App\Http\Models\Transaction;
use App\Http\Models\Purchase;
use App\Http\Models\Location;
use App\Http\Models\Util;
use App\Http\Models\User;

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
            $info = Input::all();  
            $current = date('Y-m-d H:i:s');
            if(!empty($info['cardholder']) && !empty($info['address']) && !empty($info['city']) && !empty($info['email']) 
            && !empty($info['country']) && !empty($info['region']) && !empty($info['zip']) && !empty($info['x_token']) && !empty($info['s_token']))
            {
                //checking the email
                $info['email'] = trim(strtolower($info['email']));
                if(!filter_var($info['email'], FILTER_VALIDATE_EMAIL))
                    return ['success'=>false, 'msg'=>'Enter a valid email address.'];
                //check the correct name
                $info['cardholder'] = explode(' ',trim($info['cardholder']),2);
                $info['first_name'] = $info['cardholder'][0];
                $info['last_name'] = $info['cardholder'][1];    
                //get all items in shoppingcart
                $shoppingcart = Shoppingcart::calculate_session($info['s_token'],true);
                if(!$shoppingcart['success'])
                    return Util::json($shoppingcart);
                if(!count($shoppingcart['items']) || !$shoppingcart['quantity'])
                    return Util::json(['success'=>false, 'msg'=>'There are no items to buy in the Shopping Cart.']);
                //remove unavailable items from shopingcart
                foreach($shoppingcart['items'] as $key=>$item)
                    if($item->unavailable)
                        unset($shoppingcart['items'][$key]);
                //check if it has to pay for the items or there are free
                if($shoppingcart['total']>0)    
                    if(empty($info['card']) || empty($info['month']) || empty($info['year']) || empty($info['cvv']))
                        return Util::json(['success'=>false, 'msg'=>'There is no payment method for your items.']);
                //set up customer
                $client = User::customer_set($info, $current);
                if(!$client['success'])
                    return Util::json($client);                
                //check payment type, if not free tickets
                if($shoppingcart['total']>0)
                {
                    //make transaction
                    $transaction = Transaction::usaepay($client,$info,$shoppingcart,$current);
                    //remove hide credit card number
                    $info['card'] = '...'.substr($info['card'], -4); 
                    if(!$transaction['success'])
                        return Util::json($transaction);
                    $shoppingcart['transaction_id'] = $transaction['transaction_id'];
                    $shoppingcart['payment_type'] = 'Credit';
                }
                //save purchase
                $purchase = Purchase::purchase_save($info['s_token'], $client, $shoppingcart, $current, true);
                if(!$purchase['success'])
                        return Util::json($purchase);
                if(count($purchase['errors']))
                {
                    $html = '<b>Customer:<b><br>'.json_encode($info,true).'<br><br>';
                    $html.= '<b>Items:<b><br>'.json_encode($shoppingcart,true).'<br><br>';
                    $html.= '<b>Purchases ID success:<b><br>'.implode(',',$purchase['ids']).'<br><br>';
                    $html.= '<b>ShoppingCart ID error:<b><br>'.implode(',',$purchase['errors']).'<br><br>';
                    $email = new EmailSG(null,env('MAIL_APP_ADMIN','debug@ticketbat.com'),'TicketBat App - Purchase Error');
                    $email->html($html);
                    $email->send();
                }
                if(!count($purchase['ids']))
                    return Util::json(['success'=>false, 'msg'=>'The system could not save your purchases correctly!<br>Please contact us.']);
                //send receipts
                $receipts=[];
                foreach ($purchase['ids'] as $id)
                {
                    $p = Purchase::find($id);
                    if($p)  $receipts[] = $p->get_receipt();
                }
                $sent = Purchase::email_receipts('TicketBat Purchase',$receipts,'receipt',null,true);
                if($sent)
                    return Util::json(['success'=>true, 'msg'=>'We sent you a receipt by email.<br>You can also see the purchases and the tickets in Profile -> My Purchases.']);
                return Util::json(['success'=>true, 'msg'=>'We could not send you a receipt by email.<br>You can see the purchases and the tickets int Profile -> My Purchases.']);
            }
            return Util::json(['success'=>false, 'msg'=>'Fill the form out correctly!']);
        } catch (Exception $ex) {
            $html  = '<b>Exception:<b><br>'. strval($ex).'<br>';
            $email = new EmailSG(null,env('MAIL_APP_ADMIN','debug@ticketbat.com'),'TicketBat App - Sell Error');
            $email->html($html);
            $email->send();
            return Util::json(['success'=>false, 'msg'=>'There is an error with the server!']);
        }
    } 
    
}
