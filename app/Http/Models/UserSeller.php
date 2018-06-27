<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * UserType class
 *
 * @author ivan
 */
class UserSeller extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_seller';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS MANY-MANY
}
