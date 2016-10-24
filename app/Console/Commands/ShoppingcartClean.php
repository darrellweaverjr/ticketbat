<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Models\Shoppingcart;

class ShoppingcartClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Shoppingcart:clean {days=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for cleaning all sessions saved in shoppingcart X days ago (default 10 days)';

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
            $days = $this->argument('days');
            //create progress bar
            $progressbar = $this->output->createProgressBar(1);
            Shoppingcart::where('timestamp','<',date('Y-m-d H:i:s',strtotime('-'.$days.' day',strtotime(date('Y-m-d')))))->delete();
            //advance progress bar
            $progressbar->advance(); 
            //finish progress bar
            $progressbar->finish();
        } catch (Exception $ex) {
            throw new Exception('Error cleaning shoppingcart table with ShoppingcartClean Command: '.$ex->getMessage());
        }
    }
}
