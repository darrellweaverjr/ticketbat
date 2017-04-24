<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Models\Shoppingcart;
use App\Http\Models\Transaction;
use App\Http\Models\Purchase;
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
            $current = date('Y-m-d h:i:s');
            if(!empty($info['cardholder']) && !empty($info['address']) && !empty($info['city']) && !empty($info['s_token'])
            && !empty($info['country']) && !empty($info['region']) && !empty($info['zip']) && !empty($info['x_token'])
            && !empty($info['email']) && !empty($info['card']) && !empty($info['month']) && !empty($info['year']) && !empty($info['cvv']))
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
                if(!count($shoppingcart['items']))
                    return Util::json(['success'=>false, 'msg'=>'There are no items to buy in the Shopping Cart.']);
                //set up customer
                $client = $this->customer_set($info, $current);
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
                $purchase = $this->purchase_save($info['x_token'],$client,$shoppingcart,$current);
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
                    return Util::json(['success'=>true, 'msg'=>'Purchase successfully!<br>We sent you a receipt by email.<br>You can also see the purchases and the tickets in your options.']);
                return Util::json(['success'=>true, 'msg'=>'Purchase successfully!<br>We could not send you a receipt by email.<br>You can see the purchases and the tickets in your options.']);
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
    
    /*
     * setting up the customer
     */
    public function customer_set($info,$current)
    {
        try {
            //init set 
            $send_welcome_email = false;            
            //set up user and customer
            $user = User::where('email','=',$info['email'])->first();
            if(!$user)
            {
                //send welcome email
                $send_welcome_email = true;
                //create user
                $user = new User;
                $user->created = $current;
                $user->user_type_id = 2;
                $user->is_active = 1;
                $user->force_password_reset = 0;
                $location = new Location;
                $location->created = $current;
            }
            else
                $location = $user->location;
            //save location
            $location->address = $info['address'];
            $location->city = $info['city'];
            $location->state = strtoupper($info['region']);
            $location->zip = $info['zip'];
            $location->country = $info['country'];
            $location->set_lng_lat();
            $location->save();
            //save user
            $user->location()->associate($location);
            $user->first_name = $info['first_name'];
            $user->last_name = $info['last_name'];
            $user->phone = (!empty($info['phone']))? $info['phone'] : null;
            $user->save();
            //send email welcome
            if($send_welcome_email)
                $user->welcome_email(true);
            //get customer
            $customer_id = $user->update_customer();
            if(!$customer_id)
                return ['success'=>false, 'msg'=>'There is an error setting up the customer information.'];
            return ['success'=>true, 'user_id'=>$user->id, 'customer_id'=>$customer_id];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error setting up the customer information!'];
        }
    }  
    
    /*
     * saving the purchase into the database
     */                          
    public function purchase_save($x_token,$client,$shoppingcart,$current)
    {
        try {
            $purchase_ids=[];
            $errors_ids=[];
            foreach ($shoppingcart['items'] as $id=>$i)
            {
                //create purchase
                $purchase = new Purchase;
                $purchase->user_id = $client['user_id'];
                $purchase->customer_id = $client['customer_id'];
                $purchase->transaction_id = (!empty($shoppingcart['transaction_id']))? $shoppingcart['transaction_id'] : null;
                $purchase->payment_type = (!empty($shoppingcart['payment_type']))? $shoppingcart['payment_type'] : 'None';
                $purchase->discount_id = $i['discount_id'];
                $purchase->ticket_id = $i['ticket_id'];
                $purchase->show_time_id = $i['show_time_id'];
                $purchase->session_id = $x_token;
                $purchase->referrer_url = 'http://app.ticketbat.com';
                $purchase->quantity = $i['quantity'];
                $purchase->savings = $i['savings'];
                $purchase->status = 'Active';
                $purchase->ticket_type = $i['name'].' '.$i['product_type'];
                $purchase->retail_price = $i['retail_price'];
                $purchase->commission_percent = $i['commission_percent'];
                $purchase->processing_fee = $i['processing_fee'];
                $purchase->price_paid = Util::round($purchase->retail_price+$purchase->processing_fee-$purchase->savings);
                $purchase->updated = $current;
                $purchase->created = $current;
                $purchase->merchandise = ($i['product_type']=='merchandise')? 1 : 0;  
                if($purchase->save())
                {
                    //remove item from shoppingcart
                    Shoppingcart::find($id)->delete();
                    //create tickets, no gifts
                    $tickets = implode(range(1,$purchase->quantity));
                    DB::table('ticket_number')->insert( ['purchases_id'=>$purchase->id,'customers_id'=>$purchase->customer_id,'tickets'=>$tickets] );
                    //get id for receipts
                    $purchase_ids[] = $purchase->id;
                }
                else
                    $errors_ids[] = $i['id'];
            }
            return ['success'=>true, 'ids'=>$purchase_ids, 'errors'=>$errors_ids];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }  
    
}
