<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Video class
 *
 * @author ivan
 */
class Video extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'videos';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
