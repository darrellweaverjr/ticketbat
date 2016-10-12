<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Contact class
 *
 * @author ivan
 */
class Contact extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contacts';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
