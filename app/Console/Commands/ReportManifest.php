<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Command\ReportManifestController;

class ReportManifest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Report:manifest {date=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for sending manifest report about sales for the day or X days (default today)';

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
            $date = $this->argument('date');

            //create progress bar
            $progressbar = $this->output->createProgressBar(1);
            //call controller
            $control = new ReportManifestController($date);
            $response = $control->init();
            //advance progress bar
            $progressbar->advance();
            //finish progress bar
            $progressbar->finish();
            return $response;
        } catch (Exception $ex) {
            throw new Exception('Error creating and sending emails with ReportManifest Command: '.$ex->getMessage());
        }
    }
}
