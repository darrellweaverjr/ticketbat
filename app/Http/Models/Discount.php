<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

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
     * The discount_shows that belong to the discount.
     */
    public function discount_shows()
    {
        return $this->belongsToMany('App\Http\Models\Show','discount_shows','discount_id','show_id');
    }
    /**
     * The discount_tickets that belong to the discount.
     */
    public function discount_tickets()
    {
        return $this->belongsToMany('App\Http\Models\Ticket','discount_tickets','discount_id','ticket_id')->withPivot('fixed_commission','start_num','end_num');
    }
    /**
     * The user_discounts that belong to the discount.
     */
    public function user_discounts()
    {
        return $this->belongsToMany('App\Http\Models\User','user_discounts','discount_id','user_id');
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
                    $maxFreeSets = floor($qty / $start_num);
                    $free = $total = 0;
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
    public function full_description()
    {
        $details = null;
        if($this->discount_scope=='Total')
            $sufix = ' the total amount of purchase.';
        else
            $sufix = ' every purchased item associated with this coupon.';
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
                $details = 'discount of: Buy '. $this->start_num.' Get '.$this->end_num. ' for free (applies only for items associated with this coupon).';
                break;
        }
        //return
        if(empty($details))
            return $this->description;
        return $details;
    }
}
