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
    //RELATIONSHIPS ONE-MANY
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
    /**
     * Get the purchases for the user.
     */
    public function purchases()
    {
        return $this->hasMany('App\Http\Models\Purchase','user_id');
    }
    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany('App\Http\Models\Transaction','user_id');
    }
    //RELATIONSHIPS MANY-MANY
    /**
     * The user_discounts that belong to the user.
     */
    public function user_discounts()
    {
        return $this->belongsToMany('App\Http\Models\Discount','user_discounts','user_id','discount_id');
    }
    /**
     * The user_images that belong to the user.
     */
    public function user_images()
    {
        return $this->belongsToMany('App\Http\Models\Image','user_images','user_id','image_id');
    }
}
