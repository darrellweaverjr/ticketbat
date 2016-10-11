<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EmailSG;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $email = new EmailSG('nick@ticketbat.com','ivan@ticketbat.com,ivankbc333@gmail.com','Welcome to TicketBat!');
        $email->body('welcome',array('username'=>'ivankbc333@gmail.com','password'=>'mycontrasena'));
        $email->template('a7b5c451-4d26-4292-97cd-239880e7dd20');
        $response = $email->send();
        
        
        
        
        dd('Mail sent successfully');
    }
}
