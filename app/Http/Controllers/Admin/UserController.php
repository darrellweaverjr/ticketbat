<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\User;

/**
 * Manage Users
 *
 * @author ivan
 */
class UserController extends Controller{
        
    /**
     * List all users and return default view.
     *
     * @return view
     */
    public function index()
    {
        try {
            
            
            
            //init
            //$input = Input::all();
            //get all records        
            $users = User::all();
            
            
            //dd($users[0]->user_type->user_type);
            
            //return view
            return view('admin.users.index',compact('users'));
        } catch (Exception $ex) {
            throw new Exception('Error Users Index: '.$ex->getMessage());
        }
    }
    
}
