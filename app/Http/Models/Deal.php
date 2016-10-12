<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Deal class
 *
 * @author ivan
 */
class Deal extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'deals';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
