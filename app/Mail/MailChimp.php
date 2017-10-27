<?php

namespace App\Mail;

use App\Exceptions\Handler;

/**
 * Class EmailSG sends emails thru SendGrid
 *
 * @author ivan
 */
class MailChimp {

    public $mailchimp;
    public $listId = 'df5575b6c2';

    public function __construct(\Mailchimp $mailchimp)
    {
        $this->mailchimp = $mailchimp;
    }
    
    public static function subscribe($email)
    {
        try {
            if(filter_var($info['email'], FILTER_VALIDATE_EMAIL))
            {
                $this->mailchimp->lists->subscribe(
                    $this->listId,
                    ['email' => $email]
                );
            }
            //return redirect()->back()->with('success','Email Subscribed successfully');
        } catch (\Mailchimp_List_AlreadySubscribed $e) {
            //return redirect()->back()->with('error','Email is Already Subscribed');
        } catch (\Mailchimp_Error $e) {
            //return redirect()->back()->with('error','Error from MailChimp');
        }

    }

}
