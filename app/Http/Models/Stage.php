<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Users class
 *
 * @author ivan
 */
class Stage extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stages';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
