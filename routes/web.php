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
    //home
    Route::get('home', 'DashboardController@index')->name('home');
    //dashboard
    Route::get('dashboard/ticket_sales', 'DashboardController@ticket_sales');
    Route::get('dashboard/chargebacks', 'DashboardController@chargebacks');
    Route::get('dashboard/future_liabilities', 'DashboardController@future_liabilities');
    Route::get('dashboard/trend_pace', 'DashboardController@trend_pace');
    Route::get('dashboard/referrals', 'DashboardController@referrals');
    //users
    Route::match(['get','post'], 'users', 'UserController@index');
    Route::post('users/save', 'UserController@save');
    Route::post('users/remove', 'UserController@remove');  
    //bands
    Route::match(['get','post'], 'bands', 'BandController@index');
    Route::post('bands/save', 'BandController@save');
    Route::post('bands/remove', 'BandController@remove');
    
    //utils
    Route::post('media/load','BandController@load_social_media');
    
    
});

//COMMAND ROUTES
Route::group(['prefix' => 'command'], function () {
    //reports
    Route::get('ReportManifest', function () {
        Artisan::call('Report:manifest');
    });
    Route::get('ReportSales', function () {
        Artisan::call('Report:sales',['days'=>100]);
    });
    Route::get('ReportSalesReceipt', function () {
        Artisan::call('Report:sales_receipt',['days'=>1]);
    });
    Route::get('ReportFinancial', function () {
        Artisan::call('Report:financial',['weeks'=>0]);
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