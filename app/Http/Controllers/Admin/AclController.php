<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Permission;
use App\Http\Models\UserType;
use App\Http\Models\Util;

/**
 * Manage ACLs
 *
 * @author ivan
 */
class AclController extends Controller{
    /**
     * List all acls and return default view.
     *
     * @return view
     */
    public function index()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && isset($input['id']))
            {
                //get selected record
                $permission = Permission::find($input['id']);  
                if(!$permission)
                    return ['success'=>false,'msg'=>'There was an error getting the acls.<br>Maybe it is not longer in the system.'];
                
                $permission->user_type_permissions();
                $user_type_permissions = [];
                foreach($permission->user_type_permissions as $p)
                {
                    $user_type_permissions[$p->pivot->user_type_id]['permission_scope'] = $p->pivot->permission_scope;
                    $user_type_permissions[$p->pivot->user_type_id]['permission_type'][] = $p->pivot->permission_type;
                }
                return ['success'=>true,'permission'=>array_merge($permission->getAttributes(),['user_type_permissions'=>$user_type_permissions])];
            }
            else
            {
                $permissions = [];
                $user_types = [];
                $permission_types = [];
                $permission_scopes = [];
                //if user has permission to view
                if(in_array('View',Auth::user()->user_type->getACLs()['ACLS']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['ACLS']['permission_scope'] != 'All')
                    {
                        $permissions = DB::table('permissions')
                                        ->join('user_type_permissions', 'user_type_permissions.permission_id', '=' ,'permissions.id')
                                        ->select('permissions.*')
                                        ->where('user_type_permissions.audit_user_id','=',Auth::user()->id)
                                        ->orderBy('permissions.code')
                                        ->distinct()->get();
                        $user_types = DB::table('user_types')
                                        ->join('user_type_permissions', 'user_type_permissions.user_type_id', '=' ,'user_types.id')
                                        ->select('user_types.*')
                                        ->where('user_type_permissions.audit_user_id','=',Auth::user()->id)
                                        ->orderBy('user_types.user_type')
                                        ->distinct()->get();
                    }//all
                    else
                    {
                        $permissions = Permission::orderBy('code')->get();
                        $user_types = UserType::orderBy('user_type')->get();
                    }
                    $permission_types = Util::getEnumValues('user_type_permissions','permission_type');
                    $permission_scopes = Util::getEnumValues('user_type_permissions','permission_scope');
                }
                //return view
                return view('admin.acls.index',compact('permissions','user_types','permission_types','permission_scopes'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error ACLs Index: '.$ex->getMessage());
        }
    }
    /**
     * Save new or updated acls.
     *
     * @void
     */
    public function save()
    {
        try {
            //init
            $input = Input::all();  
            //save all record      
            if($input)
            {
                $current = date('Y-m-d H:i:s');
                if(isset($input['id']) && $input['id'])
                {
                    if(Permission::where('code','=',$input['code'])->where('id','!=',$input['id'])->count())
                        return ['success'=>false,'msg'=>'There was an error saving the acls.<br>That code is already in the system.','errors'=>'title'];
                    $permission = Permission::find($input['id']);
                    $permission->updated = $current;
                }                    
                else
                {                    
                    if(Permission::where('code','=',$input['code'])->count())
                        return ['success'=>false,'msg'=>'There was an error saving the acls.<br>That code is already in the system.','errors'=>'title'];
                    $permission = new Permission;
                }
                //save permission
                $permission->permission = strip_tags($input['permission']);
                $permission->code = strip_tags($input['code']);
                $permission->description = strip_tags($input['description']);
                $permission->save();
                //update intermediate table with user_type_permissions
                $permission->user_type_permissions()->detach();
                if(isset($input['user_type_permissions']) && $input['user_type_permissions'] && count($input['user_type_permissions']))
                {
                    $user_type_permissions = [];
                    foreach ($input['user_type_permissions'] as $user_type_id => $p)
                        if(isset($p['permission_type']))
                            foreach ($p['permission_type'] as $type)
                                $user_type_permissions[] = ['permission_id'=>$permission->id,'user_type_id'=>$user_type_id,'permission_type'=>$type,'permission_scope'=>$p['permission_scope'],'audit_user_id'=>Auth::user()->id,'updated'=>$current];
                    $permission->user_type_permissions()->attach($user_type_permissions);
                }
                //return
                return ['success'=>true,'msg'=>'ACLs saved successfully!'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the package.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error ACLs Save: '.$ex->getMessage());
        }
    }
    /**
     * Remove acls.
     *
     * @void
     */
    public function remove()
    {
        try {
            //init
            $input = Input::all();
            //delete dependences   
            $acls = Permission::whereIn('id',$input['id']);
            foreach ($acls as $acl)
                $acl->user_type_permissions()->detach();
            //delete all records  
            if(Permission::destroy($input['id']))
                return ['success'=>true,'msg'=>'All records deleted successfully!'];
            return ['success'=>false,'msg'=>'There was an error deleting the acls(s)!<br>They might have some dependences.'];
        } catch (Exception $ex) {
            throw new Exception('Error ACLs Remove: '.$ex->getMessage());
        }
    } 
    /**
     * List all user_types and add new one.
     *
     * @return data
     */
    public function user_types()
    {
        try {
            //init
            $input = Input::all(); 
            if(isset($input) && isset($input['user_type']) && isset($input['description']))
            {
                $current = date('Y-m-d H:i:s');
                if(UserType::where('user_type','=',$input['user_type'])->count())
                    return ['success'=>false,'msg'=>'There was an error saving the user type.<br>That name is already in the system.','errors'=>'user_type'];
                $user_type = new UserType;
                $user_type->user_type = strip_tags($input['user_type']);
                $user_type->description = strip_tags($input['description']);
                $user_type->updated = $current;
                $user_type->save();
                //return
                return ['success'=>true,'msg'=>'User Type saved successfully!'];
            }
            else
            {
                //get all records        
                $user_types = UserType::orderBy('user_type')->get();
                $html = '<ol style="text-align:left">';
                foreach ($user_types as $t)
                    $html.= '<li><b>'.$t->user_type.'</b> * <i>'.$t->description.'</i></li>';
                $html .= '</ol>';
                //return array
                return ['success'=>true,'msg'=>$html];
            }
        } catch (Exception $ex) {
            throw new Exception('Error User Types Index: '.$ex->getMessage());
        }
    }   
}
