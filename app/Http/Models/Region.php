<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Region class
 *
 * @author ivan
 */
class Region extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'regions';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
