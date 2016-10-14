<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Slider class
 *
 * @author ivan
 */
class Slider extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sliders';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
