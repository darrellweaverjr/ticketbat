<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Models\User;
use App\Http\Models\UserType;
use App\Http\Models\Discount;
use App\Http\Models\Venue;
use App\Http\Models\Country;

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
            $user_types = UserType::all();
            $discounts = Discount::all();
            $venues = Venue::all();
            $countries = Country::all();
            //return view
            return view('admin.users.index',compact('users','user_types','discounts','venues','countries'));
        } catch (Exception $ex) {
            throw new Exception('Error Users Index: '.$ex->getMessage());
        }
    }
    
}
