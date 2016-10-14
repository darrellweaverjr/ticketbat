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
    //RELATIONSHIPS MANY-MANY
    /**
     * The user_type_permissions that belong to the permission.
     */
    public function user_type_permissions()
    {
        return $this->belongsToMany('App\Http\Models\UserType','user_type_permissions','permission_id','user_type_id')->withPivot('permission_type', 'permission_scope','audit_user_id','updated');
    }
}
