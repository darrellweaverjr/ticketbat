<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class AppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the default method on the app.
     *
     * @return Method
     */
    public function index()
    {
        return $this->deals();
    }
    
    /**
     * Show the deals for the app.
     *
     * @return view
     */
    public function deals()
    {
        try {
            //init
            $input = Input::all();
            //return view
            return view('admin.apps.deals',compact('input'));
        } catch (Exception $ex) {
            throw new Exception('Error AppAdmin Deals: '.$ex->getMessage());
        }
    }
    
    
}
