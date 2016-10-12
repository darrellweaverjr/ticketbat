<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Transaction class
 *
 * @author ivan
 */
class Transaction extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
