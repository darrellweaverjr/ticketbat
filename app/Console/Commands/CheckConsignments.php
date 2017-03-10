<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Mail\EmailSG;

class CheckConsignments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Consignments:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for check integrity of consignment tickets';

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
            $consignments = DB::table('consignments')
                                ->join('users', 'users.id', '=' ,'consignments.seller_id')
                                ->join('show_times', 'show_times.id', '=' ,'consignments.show_time_id')
                                ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                ->leftJoin('seats', 'seats.consignment_id', '=' ,'consignments.id')
                                ->leftJoin('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->select(DB::raw('shows.name AS show_name,users.first_name,users.last_name,users.email,show_times.show_time, 
                                        COUNT(seats.id) AS qty, CONCAT(tickets.ticket_type) AS ticket_types, shows.accounting_email, consignments.agreement'))
                                ->where('consignments.status','=','Un-paid')
                                ->groupBy('consignments.id')->get();
            //create progress bar
            $progressbar = $this->output->createProgressBar(count($consignments));
            foreach ($consignments as $c)
            {
                if(empty($c->agreement))
                {
                    if(!empty($c->accounting_email))
                        $to = $c->accounting_email.','.env('MAIL_REPORT_CC');
                    else
                        $to = env('MAIL_REPORT_CC');
                    $html = '<b>Seller: </b>'.$c->email.' [ '.$c->first_name.' '.$c->last_name.' ]<br>';
                    $html.= '<b>Show: </b>'.$c->show_name.'<br>';
                    $html.= '<b>Date: </b>'.date('m/d/Y @ g:ia',strtotime($c->show_time)).'<br>';
                    $html.= '<b>Quantity: </b>'.$c->qty.'<br>';
                    $html.= '<b>Ticket Types: </b>'.$c->ticket_types;
                    //SENDING EMAIL
                    $email = new EmailSG(null, $to ,'No Contract Uploaded for Consignment Tickets');
                    $email->cc(env('MAIL_REPORT_CC'));
                    $email->category('Important');
                    $email->html($html);
                    $email->send();
                }
                //advance progress bar
                $progressbar->advance(); 
            }
            //finish progress bar
            $progressbar->finish();
        } catch (Exception $ex) {
            throw new Exception('Error checking the integrity of consignments with CheckConsignments Command: '.$ex->getMessage());
        }
    }
}
