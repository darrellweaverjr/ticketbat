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


Auth::routes();
Route::get('/', function () { return redirect()->route('home'); });
Route::get('/logout', 'Auth\LoginController@logout');

Route::group(['prefix' => 'admin','middleware' => 'auth','namespace' => 'Admin'], function () {
    
    Route::get('home', 'HomeController@index')->name('home');
    
});



//Route::get('/', array('middleware' => 'auth', 'uses' => 'ProfileController@anyOrders'))->name('datatables.dataOrders');
/*
Route::get('/', function () {
    return view('welcome');
});
*/
//Auth::routes();
//Route::get('/home', 'HomeController@index');
//Route::get('/home', 'Admin\UserController@index')->middleware('auth');
