<?php

namespace App\Http\Controllers\Command;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * Manage DatabaseClean options for the commands
 *
 * @author ivan
 */
class DatabaseCleanController extends Controller{

    protected $days = 30;
    protected $registry = [];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($days,$registry=[])
    {
        $this->days = $days;
        $this->registry = $registry;
    }
    /*
     * ************************************************************************************************************************   init method
     * begin method for cleaning
     */
    public function init()
    {
        try {
            //init main variables
            
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }
    /*
     * calculate create_files
     */
    public function send_email()
    {
        try {
            
            
            $msg = '';
            //need to format array to get data
            
            $email = new EmailSG(env('MAIL_REPORT_FROM'), env('MAIL_ADMIN') ,'Cleaning TicketBat Database Report');
            $email->category('Reports');
            $email->body('custom',['body'=>$msg]);
            $email->template('46388c48-5397-440d-8f67-48f82db301f7');
            return $email->send();
        } catch (Exception $ex) {
            return false;
        }
    }


}
