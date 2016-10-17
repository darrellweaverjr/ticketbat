<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReportManifest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Report:manifest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for sending information to the BO when ticket sales have been shut down.';

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
        //
    }
}
