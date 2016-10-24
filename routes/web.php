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

//GENERAL ROUTES
Auth::routes();
Route::get('/', function () { return redirect()->route('home'); });
Route::get('/home', function () { return redirect()->route('home'); });
Route::get('/logout', 'Auth\LoginController@logout');

//ADMIN ROUTES
Route::group(['prefix' => 'admin','middleware' => 'auth','namespace' => 'Admin'], function () {
    
    Route::get('home', 'HomeController@index')->name('home');
    
});

//COMMAND ROUTES
Route::group(['prefix' => 'command'], function () {
    //reports
    Route::get('ReportManifest', function () {
        Artisan::call('Report:manifest');
    });
    Route::get('ReportSales', function () {
        Artisan::call('Report:sales',['days'=>1]);
    });
    Route::get('ReportSalesReceipt', function () {
        Artisan::call('Report:sales_receipt',['days'=>1]);
    });
    Route::get('ReportFinancial', function () {
        Artisan::call('Report:financial');
    });
    //promotions
    Route::get('PromoAnnounced', function () {
        Artisan::call('Promo:announced',['days'=>7]);
    });
    //utilities
    Route::get('ShoppingcartClean', function () {
        Artisan::call('Shoppingcart:clean',['days'=>10]);
    });
    Route::get('ShoppingcartRecover', function () {
        Artisan::call('Shoppingcart:recover');
    });
});