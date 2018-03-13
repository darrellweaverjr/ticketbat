<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Package;

/**
 * Manage Packages
 *
 * @author ivan
 */
class PackageController extends Controller{
    /**
     * List all Packages and return default view.
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
                $package = Package::find($input['id']);  
                if(!$package)
                    return ['success'=>false,'msg'=>'There was an error getting the coupon.<br>Maybe it is not longer in the system.'];
                return ['success'=>true,'package'=>array_merge($package->getAttributes())];
            }
            else
            {
                $packages = [];
                //if user has permission to view
                if(in_array('View',Auth::user()->user_type->getACLs()['PACKAGES']['permission_types']))
                {
                    if(Auth::user()->user_type->getACLs()['PACKAGES']['permission_scope'] != 'All')
                    {
                        $packages = Package::where('audit_user_id','=',Auth::user()->id)->orderBy('title')->get(['id','title','description']);
                    }
                    else
                    {
                        $packages = Package::orderBy('title')->get(['id','title','description']);
                    }
                }
                //return view
                return view('admin.packages.index',compact('packages'));
            }
        } catch (Exception $ex) {
            throw new Exception('Error Package Index: '.$ex->getMessage());
        }
    }
    /**
     * Save new or updated Package.
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
                    if(Package::where('title','=',$input['title'])->where('id','!=',$input['id'])->count())
                        return ['success'=>false,'msg'=>'There was an error saving the package.<br>That title is already in the system.','errors'=>'title'];
                    $package = Package::find($input['id']);
                    $package->updated = $current;
                }                    
                else
                {                    
                    if(Package::where('title','=',$input['title'])->count())
                        return ['success'=>false,'msg'=>'There was an error saving the package.<br>That title is already in the system.','errors'=>'title'];
                    $package = new Package;
                    $package->created = $current;
                    $package->audit_user_id = Auth::user()->id;
                }
                //save package
                $package->title = trim(strip_tags($input['title']));
                $package->description = trim(strip_tags($input['description']));
                $package->save();
                //return
                return ['success'=>true,'msg'=>'Package saved successfully!'];
            }
            return ['success'=>false,'msg'=>'There was an error saving the package.<br>The server could not retrieve the data.'];
        } catch (Exception $ex) {
            throw new Exception('Error Package Save: '.$ex->getMessage());
        }
    }
    /**
     * Remove Packages.
     *
     * @void
     */
    public function remove()
    {
        try {
            //init
            $input = Input::all();
            //delete all records   
            if(Package::destroy($input['id']))
                return ['success'=>true,'msg'=>'All records deleted successfully!'];
            return ['success'=>false,'msg'=>'There was an error deleting the packages(s)!<br>They might have some dependences.'];
        } catch (Exception $ex) {
            throw new Exception('Error Package Remove: '.$ex->getMessage());
        }
    }    
}
