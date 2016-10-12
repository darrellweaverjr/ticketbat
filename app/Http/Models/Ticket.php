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
}
