<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Mail\EmailSG;
use App\Http\Models\Shoppingcart;

class ShoppingcartRecover extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Shoppingcart:recover {hours=4}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for sending emails for recovered all sessions saved in shoppingcart';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $hours = $this->argument('hours');
            $sessions = array();    
            $abandoned_carts = DB::select(' SELECT DISTINCT sh.*, u.email, CONCAT(u.first_name," ",u.last_name) AS name, i.url AS image, s.id AS show_id 
                                            FROM shoppingcart sh INNER JOIN users u ON u.id = sh.user_id OR u.email = sh.user_id
                                            INNER JOIN show_times st ON st.id = sh.item_id INNER JOIN shows s ON st.show_id = s.id 
                                            INNER JOIN show_images si ON s.id = si.show_id INNER JOIN images i ON si.image_id
                                            WHERE sh.status = 0 AND (sh.timestamp + INTERVAL '+$hours+' HOUR) <= NOW() AND i.image_type = "Header" GROUP BY id');        
            //create progress bar
            $progressbar = $this->output->createProgressBar(count($abandoned_carts));
            foreach ($abandoned_carts as $cart)
            {
                if(substr($cart->image,0,1)=='/') $cart->image = url()->current().$cart->image;    
                $image = '<img src="'.$cart->image.'" />';    
                if(isset($sessions[$cart->session_id]) && strpos($sessions[$cart->session_id]['images'], $image)===false) $sessions[$cart->session_id]['images'].=$image; 
                else $sessions[$cart->session_id] = array('name'=>$cart->name,'email'=>$cart->email,'link'=>url()."/shoppingcart/viewcart/".$cart->session_id,'images'=>$image); 
            }        
            foreach ($sessions as $s_id => $s) 
            {   
                $dataSendEmail = array('name'=>$s['name'],'email'=>$s['email'],'link'=>url()->current()."/shoppingcart/viewcart/".$s_id,'images'=>$s['images']);
                $email = new EmailSG(env('MAIL_REMINDER_FROM'),$s['email'],env('MAIL_REMINDER_SUBJECT'));
                $email->cc(env('MAIL_REMINDER_CC'));
                $email->body('recover_cart',$dataSendEmail);
                $email->category('Reminder');
                $email->template('b4f88104-ba9b-49f5-9c37-d8d782885e6f');
                $response = $email->send(); 
                if($response) 
                    Shoppingcart::where('session_id',$s_id)->update(['status'=>1]);
                //advance progress bar
                $progressbar->advance(); 
            }
            //finish progress bar
            $progressbar->finish();
        } catch (Exception $ex) {
            throw new Exception('Error recovering shoppingcart sessions with ShoppingcartRecover Command: '.$ex->getMessage());
        }
    }
}
