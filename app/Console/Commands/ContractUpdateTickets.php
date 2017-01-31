<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ShowContract;
use App\Http\Models\Ticket;
use App\Http\Models\Util;

class ContractUpdateTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Contract:update_tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for update all tickets for the contracts for the shows';

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
            $current = date('Y-m-d');
            $contracts = ShowContract::where('effective_date','=',$current)->whereNotNull('data')->get();     
            //create progress bar
            $progressbar = $this->output->createProgressBar(count($contracts));
            foreach ($contracts as $c)
            {
                if(Util::isJSON($c->data))
                {
                    $tickets = json_decode($c->data,true);
                    foreach ($tickets as $t)
                    {
                        $ticket = Ticket::find($t['ticket_id']);
                        if($ticket)
                        {
                            $ticket->ticket_type = $t['ticket_type'];
                            $ticket->package_id = $t['package_id'];
                            $ticket->retail_price = $t['retail_price'];
                            $ticket->max_tickets = $t['max_tickets'];
                            $ticket->processing_fee = $t['processing_fee'];
                            $ticket->percent_pf = $t['percent_pf'];
                            $ticket->percent_commission = $t['percent_commission'];
                            $ticket->is_default = $t['is_default'];
                            $ticket->is_active = $t['is_active'];
                            $ticket->save();
                        }
                    }
                }
                ShowContract::where('id','=',$c->id)->update(['data'=>null]); 
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
