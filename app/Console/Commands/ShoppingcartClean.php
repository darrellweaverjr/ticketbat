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
    protected $signature = 'Shoppingcart:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for cleaning all sessions saved in shoppingcart';

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
            Shoppingcart::where('timestamp','<',date('Y-m-d H:i:s',strtotime('-10 day',strtotime(date('Y-m-d')))))->delete();
        } catch (Exception $ex) {
            throw new Exception('Error cleaning shoppingcart table with ShoppingcartClean: '.$ex->getMessage());
        }
    }
}
