<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

//Route::get('/', array('middleware' => 'auth', 'uses' => 'ProfileController@anyOrders'))->name('datatables.dataOrders');
Route::get('/', 'Admin\UserController@index');
/*
Route::get('/', function () {
    return view('welcome');
});
*/