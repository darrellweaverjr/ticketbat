<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * SellerTally class
 *
 * @author ivan
 */
class SellerTally extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seller_tally';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS MANY-MANY
}
