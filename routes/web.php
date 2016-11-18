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
    //utils
    Route::post('media/load','BandController@load_social_media');
    Route::post('media/upload_image','ImageController@upload_image');    
    //dashboard
    Route::get('dashboard/ticket_sales', 'DashboardController@ticket_sales');
    Route::get('dashboard/chargebacks', 'DashboardController@chargebacks');
    Route::get('dashboard/future_liabilities', 'DashboardController@future_liabilities');
    Route::get('dashboard/trend_pace', 'DashboardController@trend_pace');
    Route::get('dashboard/referrals', 'DashboardController@referrals');
    //users
    Route::post('users/save', 'UserController@save');
    Route::post('users/remove', 'UserController@remove'); 
    Route::match(['get','post'], 'users', 'UserController@index');
    //bands
    Route::post('bands/save', 'BandController@save');
    Route::post('bands/remove', 'BandController@remove');
    Route::match(['get','post'], 'bands', 'BandController@index');
    //venues
    Route::post('venues/save', 'VenueController@save');
    Route::post('venues/remove', 'VenueController@remove');
    Route::match(['get','post'], 'venues', 'VenueController@index');
    //shows
    Route::post('shows/save', 'ShowController@save');
    Route::post('shows/remove', 'ShowController@remove');
    Route::match(['get','post'], 'shows', 'ShowController@index');
    //ticket_types
    Route::post('ticket_types/save', 'TicketController@save');
    Route::post('ticket_types/remove', 'TicketController@remove');
    Route::match(['get','post'], 'ticket_types', 'TicketController@index');
    //coupons
    Route::post('coupons/save', 'DiscountController@save');
    Route::post('coupons/remove', 'DiscountController@remove');
    Route::match(['get','post'], 'coupons', 'DiscountController@index');
    //packages
    Route::post('packages/save', 'PackageController@save');
    Route::post('packages/remove', 'PackageController@remove');
    Route::match(['get','post'], 'packages', 'PackageController@index');
    //acls
    Route::post('acls/save', 'AclController@save');
    Route::post('acls/remove', 'AclController@remove');
    Route::match(['get','post'], 'acls', 'AclController@index');
    //manifests emails
    Route::post('manifests/save', 'ManifestController@save');
    Route::post('manifests/remove', 'ManifestController@remove');
    Route::match(['get','post'], 'manifests', 'ManifestController@index');
    //contact logs
    Route::post('contacts/save', 'ContactController@save');
    Route::post('contacts/remove', 'ContactController@remove');
    Route::match(['get','post'], 'contacts', 'ContactController@index');
    //purchases
    Route::post('purchases/save', 'PurchaseController@save');
    Route::post('purchases/remove', 'PurchaseController@remove');
    Route::match(['get','post'], 'purchases', 'PurchaseController@index');
    //home sliders
    Route::post('sliders/save', 'SliderController@save');
    Route::post('sliders/remove', 'SliderController@remove');
    Route::match(['get','post'], 'sliders', 'SliderController@index');
    //consignment tickets
    Route::post('tickets/save', 'TicketController@save');
    Route::post('tickets/remove', 'TicketController@remove');
    Route::match(['get','post'], 'tickets', 'TicketController@index');
    //contracts
    Route::post('contracts/save', 'xxxController@save');
    Route::post('contracts/remove', 'xxxController@remove');
    Route::match(['get','post'], 'contracts', 'xxxController@index');
});
//ADMIN ROUTES FOR APP
Route::group(['prefix' => 'admin','middleware' => 'auth','namespace' => 'App'], function () {
    //apps config
    //Route::post('contracts/save', 'xxxController@save');
    //Route::post('contracts/remove', 'xxxController@remove');
    //Route::match(['get','post'], 'contracts', 'xxxController@index');
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