<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Banner class
 *
 * @author ivan
 */
class Banner extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'banners';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
