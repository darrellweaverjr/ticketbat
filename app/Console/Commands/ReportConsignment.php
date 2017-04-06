<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Mail\EmailSG;
use App\Http\Models\Consignment;

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
            $current = date('Y-m-d');
            //get all consignments with cuttoff hours for today, that the report is not sent
            $consignments = DB::table('consignments')
                                ->join('users', 'users.id', '=' ,'consignments.seller_id')
                                ->join('show_times', 'show_times.id', '=' ,'consignments.show_time_id')
                                ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                ->leftJoin('seats', 'seats.consignment_id', '=' ,'consignments.id')
                                ->leftJoin('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->leftJoin('purchases', 'purchases.id', '=' ,'seats.purchase_id')
                                ->select(DB::raw('consignments.id,consignments.status,shows.emails, show_times.show_time,
                                        shows.name AS show_name, CONCAT(users.first_name," ",users.last_name) AS seller,
                                        (CASE WHEN (consignments.created = purchases.created) THEN 1 ELSE 0 END) as purchase'))
                                ->where(function ($query) {
                                    return $query->whereNull('seats.status')
                                                 ->orWhere('seats.status','<>','Voided');
                                })
                                ->where('consignments.report','!=',1)->where('consignments.status','<>','Voided')
                                ->whereDate('show_times.show_time', '=', $current)
                                ->where(DB::raw('HOUR(show_times.show_time) - shows.cutoff_hours'),'<=',date('H'))
                                ->groupBy('consignments.id')    
                                ->orderBy('show_times.show_time')
                                ->get();        
            //create report for each consignment and send it
            foreach ($consignments as $c)
            {
                //get all seats for the consignment
                $seats = DB::table('seats')
                                ->join('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->select(DB::raw('seats.id, tickets.ticket_type, seats.seat, 
                                                  (CASE WHEN (seats.status = "Created") THEN "Cancelled" ELSE seats.status END) as status'))
                                ->where('seats.consignment_id','=',$c->id)
                                ->orderBy('tickets.ticket_type')->orderByRaw('CAST(seats.seat AS UNSIGNED)')
                                ->distinct()->get()->toArray();
                $c->seats = $seats;
                //create csv
                $format = 'csv';
                $manifest_csv = View::make('command.report_consignments', compact('c','format'));
                $csv_path = '/tmp/ReportConsignment_'.$current.'_'.$c->id.'_'.date('U').'.csv';
                $fp_csv= fopen($csv_path, "w"); fwrite($fp_csv, $manifest_csv->render()); fclose($fp_csv);
                //sending email
                $email = new EmailSG(env('MAIL_REPORT_FROM'),$c->manifest_emails,'Consignment Report #'.$c->id.' - '.$c->show_name.' @ '.date('m/d/Y g:ia',strtotime($c->show_time)));
                $email->cc(env('MAIL_REPORT_CC'));
                $email->category('Consignments');
                $email->attachment([$csv_path]);
                if($email->send())
                    Consignment::where('id','=',$c->id)->update(['report'=>1]);
                unlink($csv_path);                 
            }
            return true;
        } catch (Exception $ex) {
            throw new Exception('Error creating, saving and sending emails with ReportConsignment Command: '.$ex->getMessage());
        }        
    }
}
