<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Mail\EmailSG;

/**
 * Contact class
 *
 * @author ivan
 */
class Contact extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contacts';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //PERSONALIZED FUNCTIONS
    /*
     * send email for contact us
     */
    public function email_us()
    {
        try {
            //send email
            $html = '<b>Customer: </b>'.$this->name.'<br><b>Email: </b>'.$this->email.'</b><br><b>Phone: </b>'.$this->phone;
            $html .= '<br><b>Show/Venue: </b>'.$this->show_name;
            $html .= '<br><b>Date/Time: </b>'.$this->show_time;
            $html .= '<br><b>System Info: </b>'.$this->system_info.'<br><b>Message: </b>'.$this->message;
            $email = new EmailSG(null,env('MAIL_APP_ADMIN','debug@ticketbat.com'),'TicketBat App - Contact');
            $email->html($html);
            $email->reply($this->email);
            return $email->send();
        } catch (Exception $ex) {
            return false;
        }
    }    
}
