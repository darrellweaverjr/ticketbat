<?php

namespace App\Mail;

use App\Exceptions\Handler;

/**
 * Class EmailSG sends emails thru SendGrid
 *
 * @author ivan
 */
class MailChimp {

    public function __construct()
    {
        
    }
    
    public static function subscribe($email)
    {
        try {
            if(filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $mailchimp = new \Mailchimp;
                $mailchimp->lists->subscribe(
                    'df5575b6c2',
                    ['email' => $email]
                );
                return true;
            }
            return false;
            //return redirect()->back()->with('success','Email Subscribed successfully');
        } catch (\Mailchimp_List_AlreadySubscribed $e) {
            return false;
            //return redirect()->back()->with('error','Email is Already Subscribed');
        } catch (\Mailchimp_Error $e) {
            return false;
            //return redirect()->back()->with('error','Error from MailChimp');
        }

    }

}
