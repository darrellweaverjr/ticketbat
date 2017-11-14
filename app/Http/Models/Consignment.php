<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade as PDF;

/**
 * Consignment  class
 *
 * @author ivan
 */
class Consignment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'consignments';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the showtime record associated with the Consignment.
     */
    public function show_time()
    {
        return $this->belongsTo('App\Http\Models\ShowTime','show_time_id');
    }
    /**
     * Get the seller record associated with the Consignment.
     */
    public function seller()
    {
        return $this->belongsTo('App\Http\Models\User','seller_id');
    }
    /**
     * Get the seats for the Consignment.
     */
    public function seats()
    {
        return $this->hasMany('App\Http\Models\Seat','consignment_id');
    }
    //PERSONALIZED FUNCTIONS
    /**
     * Set the agreement file for the Consignment.
     */
    public function set_agreement($file)
    {
        if($this->agreement && $this->agreement != '')
            Util::remove_file ($this->agreement);
        $this->agreement = Util::upload_file ($file,'consignment');
    }
    /**
     * create contract for the Consignment.
     */
    public static function generate_contract($id)
    {
        try {
            $consignment = DB::table('consignments')
                                ->join('users', 'users.id', '=' ,'consignments.seller_id')
                                ->join('show_times', 'show_times.id', '=' ,'consignments.show_time_id')
                                ->join('shows', 'shows.id', '=' ,'show_times.show_id')
                                ->leftJoin('seats', 'seats.consignment_id', '=' ,'consignments.id')
                                ->leftJoin('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->select(DB::raw('consignments.id,shows.name AS show_name,show_times.show_time, consignments.created,
                                        CONCAT(users.first_name," ",users.last_name) AS seller_name,
                                        COUNT(seats.id) AS qty,
                                        ROUND(SUM(COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0))+COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0))),2) AS total,
                                        ROUND(SUM(COALESCE(seats.collect_price,0)),2) AS due'))
                                ->where(function ($query) {
                                    return $query->whereNull('seats.status')
                                                 ->orWhere('seats.status','<>','Voided');
                                })
                                ->where('consignments.id','=',$id)
                                ->groupBy('consignments.id')
                                ->first();
            if($consignment && $consignment->qty > 0)
            {
                //set creator of the consignment
                $creator = DB::table('consignments')
                                ->join('users', 'users.id', '=' ,'consignments.create_user_id')
                                ->select(DB::raw('CONCAT(users.first_name," ",users.last_name) AS name'))
                                ->where('consignments.id','=',$id)
                                ->first();
                $consignment->creator = ($creator && isset($creator->name))? $creator->name : '___________________________________';
                //set up seats by type
                $types = DB::table('seats')
                                ->join('tickets', 'tickets.id', '=' ,'seats.ticket_id')
                                ->select(DB::raw('tickets.ticket_type, COUNT(seats.id) AS qty,
                                                  COALESCE(seats.retail_price,COALESCE(tickets.retail_price,0)) AS retail_price,
                                                  COALESCE(seats.collect_price,0) AS collect_price,
                                                  COALESCE(seats.processing_fee,COALESCE(tickets.processing_fee,0)) AS processing_fee'))
                                ->where('seats.consignment_id','=',$consignment->id)->where('seats.status','<>','Voided')
                                ->groupBy('tickets.ticket_type')->groupBy('retail_price')->groupBy('collect_price')->orderBy('tickets.ticket_type')
                                ->distinct()->get();
                $consignment->types = $types;
                //create pdf tickets
                $format = 'pdf';
                return View::make('command.consignment_contract', compact('consignment','format'));
            }
            else
                return false;
        } catch (Exception $ex) {                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               return false;
        }
    }

}
