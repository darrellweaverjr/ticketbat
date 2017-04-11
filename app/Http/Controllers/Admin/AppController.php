<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

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
        //return $this->deals();
    }
    
}
