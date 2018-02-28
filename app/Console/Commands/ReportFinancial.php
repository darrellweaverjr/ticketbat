<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail\EmailSG;
use Barryvdh\DomPDF\Facade as PDF;

class ReportFinancial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Report:financial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used for sending information about financial report previous/current week (if a param is added will be X previous week)';

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
            
            return true;
        } catch (Exception $ex) {
            throw new Exception('Error creating report with ReportFinancial Command: '.$ex->getMessage());
        }
    }
}
