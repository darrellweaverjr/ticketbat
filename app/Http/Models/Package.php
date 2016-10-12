<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Package class
 *
 * @author ivan
 */
class Package extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'packages';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
