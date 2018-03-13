<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Command\ReportSalesController;

class ReportSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Report:sales {days=1} {onlyadmin=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for sending information about sales for the day or X days (default yesterday)';

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
            $onlyadmin = $this->argument('onlyadmin');
                        
            //create progress bar
            $progressbar = $this->output->createProgressBar(1);
            //call controller
            $control = new ReportSalesController($days,$onlyadmin);
            $response = $control->init();
            //advance progress bar
            $progressbar->advance(); 
            //finish progress bar
            $progressbar->finish(); 
            return $response;
        } catch (Exception $ex) {
            throw new Exception('Error creating and sending emails with ReportSales Command: '.$ex->getMessage());
        }
    }
}
