<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Command\ReportConsignmentController;

class ReportConsignment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Report:consignment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for sending information to the BO of the sales report on cuttoff hours for consignments.';

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
            //create progress bar
            $progressbar = $this->output->createProgressBar(1);
            //call controller
            $control = new ReportConsignmentController();
            $response = $control->init();
            //advance progress bar
            $progressbar->advance(); 
            //finish progress bar
            $progressbar->finish(); 
            return $response;
        } catch (Exception $ex) {
            throw new Exception('Error creating, saving and sending emails with ReportConsignment Command: '.$ex->getMessage());
        }        
    }
}
