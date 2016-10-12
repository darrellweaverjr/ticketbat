<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Category class
 *
 * @author ivan
 */
class Category extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
