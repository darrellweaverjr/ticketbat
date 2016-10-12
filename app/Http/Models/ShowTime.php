<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ShowTime class
 *
 * @author ivan
 */
class ShowTime extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'show_times';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;   
}
