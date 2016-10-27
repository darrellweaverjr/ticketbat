<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * UserType class
 *
 * @author ivan
 */
class UserType extends Model
{    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_types';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    //RELATIONSHIPS MANY-MANY
    /**
     * The user_type_permissions that belong to the user_type.
     */
    public function user_type_permissions()
    {
        return $this->belongsToMany('App\Http\Models\Permission','user_type_permissions','user_type_id','permission_id')->withPivot('permission_type', 'permission_scope','audit_user_id','updated');
    }
    /**
     * Get ACLs permissions formated that belong to the user_type.
     */
    public function getACLs()
    {
        $acls = array();
        $acl_codes = array();
        foreach ($this->user_type_permissions()->orderBy('permission_id')->get() as $p)
        {
            if(isset($acls[$p->pivot->permission_id]))
            {
                $acls[$p->pivot->permission_id]['permission_types'][] = $p->pivot->permission_type;
            }
            else
            {
                $acls[$p->pivot->permission_id] = array('permission_id'=>$p->pivot->permission_id,'user_type_id'=>$p->pivot->user_type_id,'code'=>$p->code,'user_type'=>$this->attributes['user_type'],'permission_types'=>array($p->pivot->permission_type),'permission_scope'=>$p->pivot->permission_scope);
                $acl_codes[$p->code] = $p->pivot->permission_scope;
            }
        }
        return compact("acls", "acl_codes");
    }
}
