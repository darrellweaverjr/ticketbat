<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Permission class
 *
 * @author ivan
 */
class Permission extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
