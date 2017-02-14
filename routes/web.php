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
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
//ADMIN ROUTES
Route::group(['prefix' => 'admin','middleware' => 'auth','namespace' => 'Admin'], function () {
    //home
    Route::get('home', 'DashboardController@index')->name('home');
    //utils
    Route::post('media/load','BandController@load_social_media');
    Route::post('media/upload_image','ImageController@upload_image'); 
    Route::get('media/preview/{filename}', function ($filename) {return Image::make(storage_path().'/app/tmp/'.$filename)->response();});
    //dashboard
    Route::match(['get','post'], 'dashboard/ticket_sales', 'DashboardController@ticket_sales');
    Route::match(['get','post'], 'dashboard/chargebacks', 'DashboardController@chargebacks');
    Route::match(['get','post'], 'dashboard/future_liabilities', 'DashboardController@future_liabilities');
    Route::match(['get','post'], 'dashboard/trend_pace', 'DashboardController@trend_pace');
    Route::match(['get','post'], 'dashboard/referrals', 'DashboardController@referrals');
    //users
    Route::post('users/profile', 'UserController@profile');
    Route::match(['get','post'],'users/impersonate/{user?}/{code?}', 'UserController@impersonate');
    Route::post('users/save', 'UserController@save');
    Route::post('users/remove', 'UserController@remove'); 
    Route::match(['get','post'], 'users', 'UserController@index');
    //bands
    Route::post('bands/save', 'BandController@save');
    Route::post('bands/remove', 'BandController@remove');
    Route::match(['get','post'], 'bands/{autopen?}', 'BandController@index');
    //venues
    Route::match(['get','post'], 'venues/images', 'VenueController@images');
    Route::match(['get','post'], 'venues/banners', 'VenueController@banners');
    Route::match(['get','post'], 'venues/videos', 'VenueController@videos');
    Route::match(['get','post'], 'venues/stages', 'VenueController@stages');
    Route::post('venues/slug', 'VenueController@slug');
    Route::post('venues/save', 'VenueController@save');
    Route::post('venues/remove', 'VenueController@remove');
    Route::match(['get','post'], 'venues', 'VenueController@index');
    //shows
    Route::match(['get','post'], 'shows/passwords', 'ShowController@passwords');
    Route::match(['get','post'], 'shows/tickets', 'ShowController@tickets');
    Route::match(['get','post'], 'shows/bands', 'ShowController@bands');
    Route::match(['get','post'], 'shows/showtimes', 'ShowController@showtimes');
    Route::match(['get','post'], 'shows/contracts/{format?}/{id?}', 'ShowController@contracts');
    Route::match(['get','post'], 'shows/images', 'ShowController@images');
    Route::match(['get','post'], 'shows/banners', 'ShowController@banners');
    Route::match(['get','post'], 'shows/videos', 'ShowController@videos');
    Route::post('shows/slug', 'ShowController@slug');
    Route::post('shows/save', 'ShowController@save');
    Route::post('shows/remove', 'ShowController@remove');
    Route::match(['get','post'], 'shows', 'ShowController@index');
    //ticket_types
    Route::post('ticket_types/save', 'TicketTypeController@save');
    Route::match(['get','post'], 'ticket_types', 'TicketTypeController@index');
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
    Route::match(['get','post'], 'user_types', 'AclController@user_types');
    //manifests emails
    Route::get('manifests/view/{format}/{id}', 'ManifestController@view');
    Route::match(['get','post'], 'manifests', 'ManifestController@index');
    //contact logs
    Route::match(['get','post'], 'contacts', 'ContactController@index');
    //purchases
    Route::post('purchases/email', 'PurchaseController@email');
    Route::get('purchases/tickets/{type}/{ids}', 'PurchaseController@tickets');
    Route::post('purchases/save', 'PurchaseController@save');
    Route::match(['get','post'], 'purchases', 'PurchaseController@index');
    //home sliders
    Route::post('sliders/save', 'SliderController@save');
    Route::post('sliders/remove', 'SliderController@remove');
    Route::match(['get','post'], 'sliders', 'SliderController@index');
    //consignment tickets
    Route::get('consignments/tickets/{type}/{ids}', 'ConsignmentController@tickets');
    Route::get('consignments/view/{type}/{id}', 'ConsignmentController@view');
    Route::post('consignments/save', 'ConsignmentController@save');
    Route::match(['get','post'], 'consignments', 'ConsignmentController@index');
    //apps
    Route::match(['get','post'], 'apps/deals', 'AppController@deals');
    Route::match(['get','post'], 'apps', 'AppController@index');
});
//ADMIN ROUTES FOR APP
Route::group(['prefix' => 'admin','middleware' => 'auth','namespace' => 'App'], function () {
    //apps config
    //Route::post('contracts/save', 'xxxController@save');
    //Route::post('contracts/remove', 'xxxController@remove');
    //Route::match(['get','post'], 'contracts', 'xxxController@index');
});
//COMMAND ROUTES
Route::group(['prefix' => 'command','middleware' => 'auth'], function () {
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
    Route::get('ContractUpdateTickets', function () {
        Artisan::call('Contract:update_tickets');
    });
});