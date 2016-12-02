<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ticket class
 *
 * @author ivan
 */
class Ticket extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tickets';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the show record associated with the ticket.
     */
    public function show()
    {
        return $this->belongsTo('App\Http\Models\Show','show_id');
    }
    /**
     * Get the package record associated with the ticket.
     */
    public function package()
    {
        return $this->belongsTo('App\Http\Models\Package','package_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * The discount_tickets that belong to the ticket.
     */
    public function discount_tickets()
    {
        return $this->belongsToMany('App\Http\Models\Discount','discount_tickets','ticket_id','discount_id');
    }
    /**
     * The soldout tickets that belong to the showtime.
     */
    public function soldout_tickets()
    {
        return $this->belongsToMany('App\Http\Models\ShowTime','soldout_tickets','ticket_id','show_time_id')->withPivot('created');
    }  
    //PERSONALIZED FUNCTIONS
    /**
     * Generate QR code.
     *
     * @return csv
     */
    public static function getQRcode($purchase_id,$user_id,$ticket_number)
    {
        try {
            $code = 'TB'.str_pad((string)$purchase_id,6,'0',STR_PAD_LEFT).str_pad((string)$user_id,5,'0',STR_PAD_LEFT).$ticket_number;
            return 'https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl='.htmlentities($code).'&choe=UTF-8';
        } catch (Exception $ex) {
            throw new Exception('Error Util getQRcode: '.$ex->getMessage());
        }
    }
}
