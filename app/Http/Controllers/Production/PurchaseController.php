<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Models\Shoppingcart;
use App\Http\Models\Transaction;
use App\Http\Models\Purchase;
use App\Http\Models\Util;
use App\Http\Models\Location;
use App\Http\Models\User;
use App\Mail\EmailSG;
use App\Mail\MailChimp;

class PurchaseController extends Controller
{
    /*
     * buy all items in the cart
     */
    public function buy()
    {
        try {
            //init
            $info = Input::all();  
            $current = date('Y-m-d H:i:s');
            $info['s_token'] = Util::s_token(false,true);
            //check required params
            if(!empty($info['customer']) && !empty($info['email']))
            {
                //checking the email
                $info['email'] = trim(strtolower($info['email']));
                if(!filter_var($info['email'], FILTER_VALIDATE_EMAIL))
                    return redirect()->back()->withErrors(['Enter a valid email address.'])->withInput();
                //added to mailchimp
                if(!empty($info['newsletter']))
                    MailChimp::subscribe($info['email']);
                //check the correct name
                if(strpos(trim($info['customer']), ' ') === false)
                    return redirect()->back()->withErrors(['You must enter your full name.'])->withInput();
                $info['customer'] = explode(' ',trim($info['customer']),2);
                $info['first_name'] = $info['customer'][0];
                $info['last_name'] = $info['customer'][1];    
            }
            else
                return redirect()->back()->withErrors(['Fill the form out correctly!'])->withInput();
            //get all items in shoppingcart
            $shoppingcart = Shoppingcart::calculate_session($info['s_token'],true);
            if(!$shoppingcart['success'])
                return redirect()->back()->withErrors([$shoppingcart['msg']])->withInput();
            if(!count($shoppingcart['items']) || !$shoppingcart['quantity'])
                return redirect()->back()->withErrors(['There are no items to buy in the Shopping Cart.'])->withInput();
            //remove unavailable items from shopingcart
            foreach($shoppingcart['items'] as $key=>$item)
                if($item->unavailable)
                    unset($shoppingcart['items'][$key]);
            //set up customer
            $client = $this->customer_set($info, $current);
            if(!$client['success'])
                return redirect()->back()->withErrors([$client['msg']])->withInput();
            //check payment method
            if(!empty($info['method']))
            {
                switch ($info['method'])
                {
                    case 'card':
                        if($shoppingcart['total']>0) 
                        {
                            if(empty($info['card']) || empty($info['month']) || empty($info['year']) || empty($info['cvv']))
                                return redirect()->back()->withErrors(['There is no payment method for your item(s).'])->withInput();
                            if(strtotime(date('m/Y')) > strtotime($info['month'].'/'.$info['year']))
                                return redirect()->back()->withErrors(['The card is expired.'])->withInput();
                            if(empty($info['address']) || empty($info['city']) || empty($info['zip']) || empty($info['country']) || empty($info['state']))
                                return redirect()->back()->withErrors(['You must enter your address, city and zip code.'])->withInput();
                        }
                        else
                            return redirect()->back()->withErrors(['Incorrect payment method! Please, contact us.'])->withInput();
                        //make transaction continue and do not break
                    case 'swipe':
                        if($info['method']=='swipe') //check to skip en case of card
                        {
                            if(!(Auth::check() && in_array(Auth::user()->user_type_id, [1,7])))
                                return redirect()->back()->withErrors(['You are now allow to perfom this operation.'])->withInput();
                            if($shoppingcart['total']>0) 
                            {
                                if(empty($info['UMmagstripe']) || empty($info['customer']) || empty($info['card']) || empty($info['month']) || empty($info['year']))
                                    return redirect()->back()->withErrors(['You must swipe a valid card.'])->withInput();
                                if(strtotime(date('m/Y')) > strtotime($info['month'].'/'.$info['year']))
                                    return redirect()->back()->withErrors(['The card is expired.'])->withInput();
                            }
                            else
                                return redirect()->back()->withErrors(['Incorrect payment method!<br>Please, contact us.'])->withInput();
                        }
                        //make transaction for card and swipe
                        $transaction = Transaction::usaepay($client,$info,$shoppingcart,$current);
                        if(!$transaction['success'])
                            return redirect()->back()->withErrors([$transaction['msg']])->withInput();
                        //remove hide credit card number
                        $info['card'] = '...'.substr($info['card'], -4); 
                        $shoppingcart['transaction_id'] = $transaction['transaction_id'];
                        $shoppingcart['payment_type'] = 'Credit';
                        break;
                    case 'cash':
                        if(!(Auth::check() && in_array(Auth::user()->user_type_id, [1,7])))
                            return redirect()->back()->withErrors(['You are now allow to perfom this operation.'])->withInput();
                        Session::forget('change');
                        if($shoppingcart['total']>0) 
                        {
                            $paid = 0;
                            if(!empty($info['x100'])) $paid += $info['x100']*100;
                            if(!empty($info['x50'])) $paid += $info['x50']*50;
                            if(!empty($info['x20'])) $paid += $info['x20']*20;
                            if(!empty($info['x10'])) $paid += $info['x10']*10;
                            if(!empty($info['x5'])) $paid += $info['x5']*5;
                            if(!empty($info['x1'])) $paid += $info['x1'];
                            if(!empty($info['change'])) $paid += $info['change']/100;
                            if($paid < $shoppingcart['total'])
                                return redirect()->back()->withErrors(['There is still money to collect.'])->withInput();
                            Session::put('change',$paid-$shoppingcart['total']);
                        }
                        else
                            return redirect()->back()->withErrors(['Incorrect payment method! Please, contact us.'])->withInput();
                        $shoppingcart['payment_type'] = 'Cash';
                        break;
                    case 'skip':
                        if($shoppingcart['total']>0) 
                            return redirect()->back()->withErrors(['Incorrect payment method! Please, contact us.'])->withInput();
                        $shoppingcart['payment_type'] = 'None';
                        break;
                    default:
                        return redirect()->back()->withErrors(['Incorrect payment method! Please, contact us.'])->withInput();
                }
            }
            else
                return redirect()->back()->withErrors(['Incorrect payment method! Please, contact us.'])->withInput();
            //save purchase
            $purchase = $this->purchase_save($info['s_token'],$client,$shoppingcart,$current);
            if(!$purchase['success'])
                return redirect()->back()->withErrors([$purchase['msg']])->withInput();
            if(count($purchase['errors']))
            {
                $html = '<b>Customer:<b><br>'.json_encode($info,true).'<br><br>';
                $html.= '<b>Items:<b><br>'.json_encode($shoppingcart,true).'<br><br>';
                $html.= '<b>Purchases ID success:<b><br>'.implode(',',$purchase['ids']).'<br><br>';
                $html.= '<b>ShoppingCart ID error:<b><br>'.implode(',',$purchase['errors']).'<br><br>';
                $email = new EmailSG(null,env('MAIL_ADMIN','debug@ticketbat.com'),'TicketBat Web - Purchase Error');
                $email->html($html);
                $email->send();
            }
            if(!count($purchase['ids']))
                return redirect()->back()->withErrors(['The system could not save your purchases correctly! Please, contact us.'])->withInput();
            //show complete page
            return $this->complete($purchase,$client['send_welcome_email']);
        } catch (Exception $ex) {
            $html  = '<b>Exception:<b><br>'. strval($ex).'<br>';
            $email = new EmailSG(null,env('MAIL_ADMIN','debug@ticketbat.com'),'TicketBat Web - Sell Error');
            $email->html($html);
            $email->send();
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
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
                $user->user_type_id = 2;
                $user->is_active = 1;
                $user->force_password_reset = 0;
                $location = new Location;
                $location->created = $current;
            }
            else
                $location = $user->location;
            //save location
            if(!empty($info['address']) && !empty($info['city']) && !empty($info['region']) && !empty($info['zip']) && !empty($info['country']))
            {
                $location->address = $info['address'];
                $location->city = $info['city'];
                $location->state = strtoupper($info['region']);
                $location->zip = $info['zip'];
                $location->country = $info['country'];
                $location->set_lng_lat();
            }
            else
            {
                $location->address =  $location->city = 'Unknown';
                $location->state = 'NA';
                $location->country = 'US';
            }
            $location->save();
            //save user
            $user->location()->associate($location);
            $user->first_name = $info['first_name'];
            $user->last_name = $info['last_name'];
            $user->phone = (!empty($info['phone']))? $info['phone'] : null;
            $user->save();
            //send email welcome
            if($send_welcome_email)
                $send_welcome_email = $user->welcome_email(true);
            else
                $send_welcome_email = null;
            //erase temp pass
            $user->set_slug();
            //get customer
            $customer_id = $user->update_customer();
            if(!$customer_id)
                return ['success'=>false, 'send_welcome_email'=>$send_welcome_email, 'msg'=>'There is an error setting up the customer information.'];
            return ['success'=>true, 'send_welcome_email'=>$send_welcome_email, 'user_id'=>$user->id, 'customer_id'=>$customer_id];
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
            foreach ($shoppingcart['items'] as $i)
            {
                //create purchase
                $purchase = new Purchase;
                $purchase->user_id = $client['user_id'];
                $purchase->customer_id = $client['customer_id'];
                $purchase->transaction_id = (!empty($shoppingcart['transaction_id']))? $shoppingcart['transaction_id'] : null;
                $purchase->payment_type = (!empty($shoppingcart['payment_type']))? $shoppingcart['payment_type'] : 'None';
                $purchase->discount_id = $i->discount_id;
                $purchase->ticket_id = $i->ticket_id;
                $purchase->show_time_id = $i->item_id;
                $purchase->session_id = $x_token;
                $purchase->referrer_url = substr(strval( url()->current() ),0,499);
                $purchase->quantity = $i->number_of_items;
                $purchase->savings = $i->savings;
                $purchase->status = 'Active';
                $purchase->ticket_type = $i->name.' '.$i->product_type;
                $purchase->retail_price = $i->retail_price;
                $purchase->commission_percent = $i->commission;
                $purchase->processing_fee = $i->processing_fee;
                $purchase->price_paid = Util::round($purchase->retail_price+$purchase->processing_fee-$purchase->savings);
                $purchase->updated = $current;
                $purchase->created = $current;
                $purchase->merchandise = ($i->product_type=='merchandise')? 1 : 0;  
                if($purchase->save())
                {
                    //get id for receipts
                    $purchase_ids[] = $purchase->id;
                    //get shoppingcart 
                    $sc = Shoppingcart::find($i->id);
                    if($sc)
                    {
                        if(!empty($cs->gifts) && Util::isJSON($cs->gifts))
                        {
                            $shared = [];
                            $indexes = json_decode($cs->gifts,true);
                            foreach ($indexes as $i)
                                $shared[] = ['first_name'=>$i['first_name'],'last_name'=>$i['last_name'],'email'=>$i['email'],
                                             'comment'=>(!empty($i['comment']))? $i['comment'] : null,'qty'=>$i['qty']];
                            $purchase->share_tickets($shared);
                        }
                        else
                        {
                            //create tickets, no gifts
                            $tickets = implode(range(1,$purchase->quantity));
                            DB::table('ticket_number')->insert( ['purchases_id'=>$purchase->id,'customers_id'=>$purchase->customer_id,'tickets'=>$tickets] );
                        }
                        //remove item from shoppingcart
                        $sc->delete();
                    }
                }
                else
                    $errors_ids[] = $i->id;
            }
            return ['success'=>true, 'ids'=>$purchase_ids, 'errors'=>$errors_ids];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    } 
    
    /*
     * complete the purchase showing receipts pag
     */                          
    public function complete($purchases, $send_welcome_email)
    {
        $receipts=[];
        $purchased=[];
        $sent_to = null;
        $seller = (Auth::check() && in_array(Auth::user()->user_type_id,[1,7]))? 1 : 0;
        try {
            //send receipts
            $data = $this->receipts($purchases);
            //get data
            if(is_array($purchases['ids']))
                $purchases['ids'] = implode (',', $purchases['ids']);
            $receipts = $data['receipts'];
            $purchased = $data['purchased'];
            $sent_to = $data['sent_to'];
            $sent_receipts = $data['sent_receipts'];
            Session::forget('change');
            return view('production.shoppingcart.complete',compact('sent_to','sent_receipts','purchases','purchased','send_welcome_email','seller'));
        } catch (Exception $ex) {
            return view('production.shoppingcart.complete',compact('sent_to','sent_receipts','purchases','purchased','send_welcome_email','seller'));
        }
    }
    
    /*
     * resend receipts
     */                          
    public function receipts($purchases=null)
    {
        $receipts=[];
        $purchased=[];
        $sent_to = null;
        $sent_receipts = false;
        $input = Input::all(); 
        //load input 
        if(empty($purchases) && !empty($input['purchases']))
            $purchases['ids'] = explode(',', $input['purchases']);
        try {
            //send receipts
            foreach ($purchases['ids'] as $id)
            {
                $p = Purchase::find($id);
                if($p)
                {
                    $purchases[] = $id;
                    if(empty($sent_to))
                        $sent_to = ['id'=>$p->user_id, 'email'=>$p->customer->email];
                    $receipts[] = $p->get_receipt();
                    $purchased[] = ['qty'=>$p->quantity,'event'=>$p->ticket->show->name,'schedule'=>date('l, F j, Y @ g:i A', strtotime($p->show_time->show_time)),
                                    'slug'=>$p->ticket->show->slug,'show_time_id'=>$p->show_time->id];
                }
            }
            //sent email
            $sent_receipts = Purchase::email_receipts('TicketBat Purchase',$receipts,'receipt',null,true);
            return ['success'=>true, 'receipts'=>$receipts, 'purchased'=>$purchased, 'purchases'=>$purchases, 'sent_to'=>$sent_to, 'sent_receipts'=>$sent_receipts];
        } catch (Exception $ex) {
            return ['success'=>false, 'receipts'=>$receipts, 'purchased'=>$purchased, 'purchases'=>$purchases, 'sent_to'=>$sent_to, 'sent_receipts'=>$sent_receipts];
        }
    }
    
    /*
     * resend welcome email
     */                          
    public function welcome()
    {
        try {
            //init
            $input = Input::all(); 
            if(!empty($input['user_id']))
            {
                $p = Purchase::find($user_id);
                if($p)
                {
                    if($p->welcome_email(true))
                        return ['success'=>true, 'msg'=>'Email sent successfully!'];
                    return ['success'=>false, 'msg'=>'The system could not sent the email!'];
                }
                return ['success'=>false, 'msg'=>'The system could not sent the email to that client!'];
            }
            return ['success'=>false, 'msg'=>'The system could not sent the email to the client!'];
        } catch (Exception $ex) {
            return ['success'=>false, 'msg'=>'There is an error with the server!'];
        }
    }
       
}
