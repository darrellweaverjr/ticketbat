<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Models\Purchase;

/**
 * Manage Users
 *
 * @author ivan
 */
class UserController extends Controller{
    
    public function index()
    {
        /*$users = Users::all();
        foreach ($users as $user) {
            echo $user->email.'<br>';
        }*/
        //return view('welcome');
        
        $purchases = Purchase::all();
        foreach ($purchases as $purchase) {
            print_r(json_encode($purchase)); echo '<br><br>';
        }
      
    }
    
}
