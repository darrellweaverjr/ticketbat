<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Discount class
 *
 * @author ivan
 */
class Discount extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'discounts';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS ONE-MANY
    /**
     * Get the purchases for the user.
     */
    public function purchases()
    {
        return $this->hasMany('App\Http\Models\Purchase','discount_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * The discount_tickets that belong to the discount.
     */
    public function discount_tickets()
    {
        return $this->belongsToMany('App\Http\Models\Ticket','discount_tickets','discount_id','ticket_id')->withPivot('fixed_commission','start_num','end_num');
    }
    /**
     * The discount_showtimes that belong to the discount.
     */
    public function discount_showtimes()
    {
        return $this->belongsToMany('App\Http\Models\ShowTime','discount_show_times','discount_id','show_time_id');
    }
    /**
     * The user_discounts that belong to the discount.
     */
    public function user_discounts()
    {
        return $this->belongsToMany('App\Http\Models\User','user_discounts','discount_id','user_id');
    }
    /**
     * The discount by code.
     */
    public static function get_coupon($code)
    {
        return DB::table('discounts')
                            ->join('discount_tickets', 'discount_tickets.discount_id', '=' ,'discounts.id')
                            ->join('tickets', 'discount_tickets.ticket_id', '=' ,'tickets.id')
                            ->leftJoin('discount_show_times', 'discount_show_times.discount_id', '=' ,'discounts.id')
                            ->select(DB::raw('discounts.id, discounts.code, discounts.description, discounts.start_num, discounts.coupon_type,
                                              discounts.discount_type, discounts.discount_scope, discounts.end_num, GROUP_CONCAT(discount_show_times.show_time_id) AS showtimes'))
                            ->where('discounts.code',$code)->groupBy('discounts.id')->first();
    }
    /**
     * The user_discounts that belong to the discount.
     */
    public function free_tickets($qty,$start_num=null,$end_num=null)
    {
        $start_num = ($start_num)? $start_num : $this->start_num;
        $end_num = ($end_num)? $end_num : $this->end_num;
        $free = $total = 0;
        if(!empty($start_num) && !empty($end_num))
        {
            $maxFreeSets = floor($qty / $start_num);
            while ($maxFreeSets > 0) 
            {
                $a = 0;
                while ($a < $start_num && $total < $qty) {
                    $total++; $a++;
                }
                $b = 0;
                while ($b < $end_num && $total < $qty) {
                    $free++; $total++; $b++;
                }
                $maxFreeSets--;
            }
        }
        return $free;
    }
    /**
     * The user_discounts that belong to the discount.
     */
    public function calculate_savings($qty,$cost,$start_num=null,$end_num=null)
    {
        $savings = 0;
        $start_num = ($start_num)? $start_num : $this->start_num;
        $end_num = ($end_num)? $end_num : $this->end_num;
        switch($this->discount_type)
        {
            case 'Percent':
                    $savings = Util::round($cost * $start_num / 100);
                    break;
            case 'Dollar':
                    $savings = ($this->discount_scope=='Total')? $start_num : $start_num * $qty;
                    break;
            case 'N for N':
                    $free = $this->free_tickets($qty, $start_num, $end_num);
                    $savings = Util::round($cost / $qty * $free);
                    break;
            default:  
                    break;
        }
        return $savings;
    }
    /**
     * The full description that belong to the discount.
     */
    public function full_description($items=null)
    {
        $details = null;
        if($this->discount_scope=='Total')
            $sufix = ' the total amount of purchase.';
        else
        {
            $sufix = ' every purchased item associated with this coupon.';
            if(!empty($items)) 
            {
                foreach ($items as $k=>$i)
                {
                    if(count($items)==1)
                        $sufix.= ' "'.$i->name.' '.$i->product_type.'" ';
                    else if($k+1 == count($items))
                        $sufix.= ' and "'.$i->name.' '.$i->product_type.'" ';
                    else
                        $sufix.= ', "'.$i->name.' '.$i->product_type.'" ';
                }
                $sufix.= ' tickets.';
            }
        }
        //calc description
        switch ($this->discount_type)
        {
            case 'Dollar':
                $details = 'discount of $'. $this->start_num. ' on'.$sufix;
                break;
            case 'Percent':
                $details = 'discount of '. $this->start_num. '% on'.$sufix;
                break;
            case 'N for N':
                $details = 'discount of: Buy '. $this->start_num.' Get '.$this->end_num. ' for free on'.$sufix;
                break;
        }
        //return
        if(empty($details))
            return $this->description;
        return $details;
    }
}
