<?php

namespace App\Http\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Users class
 *
 * @author ivan
 */
class User extends Authenticatable
{    
    protected $fillable = [
        'email', 'password',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Encrypt the password before check md5/bcrypt.
     *
     * @return password
     */
    public function getAuthPassword()
    {
        return bcrypt($this->password);
    }
    /**
     * Overrides the method to ignore the remember token.
    */
    public function setAttribute($key, $value)
    {
        $isRememberTokenAttribute = $key == $this->getRememberTokenName();
        if (!$isRememberTokenAttribute)
        {
          parent::setAttribute($key, $value);
        }
    }
    /**
     * Get the user_type record associated with the user.
     */
    public function user_type()
    {
        return $this->belongsTo('App\Http\Models\UserType','user_type_id');
    }
    /**
     * Get the location record associated with the user.
     */
    public function location()
    {
        return $this->belongsTo('App\Http\Models\Location','location_id');
    }
}
