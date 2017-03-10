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
    Route::post('media/load','BandController@load_social_media')->middleware('permissions:MEDIA');
    Route::post('media/upload_image','ImageController@upload_image')->middleware('permissions:MEDIA'); 
    Route::get('media/preview/{filename}', function ($filename) {return Image::make(storage_path().'/app/tmp/'.$filename)->response();})->middleware('permissions:MEDIA');
    //dashboard
    Route::match(['get','post'], 'dashboard/ticket_sales', 'DashboardController@ticket_sales')->middleware('permissions:REPORTS');
    Route::match(['get','post'], 'dashboard/chargebacks', 'DashboardController@chargebacks')->middleware('permissions:REPORTS');
    Route::match(['get','post'], 'dashboard/future_liabilities', 'DashboardController@future_liabilities')->middleware('permissions:REPORTS');
    Route::match(['get','post'], 'dashboard/trend_pace', 'DashboardController@trend_pace')->middleware('permissions:REPORTS');
    Route::match(['get','post'], 'dashboard/referrals', 'DashboardController@referrals')->middleware('permissions:REPORTS');
    //users
    Route::post('users/profile', 'UserController@profile')->middleware('permissions:USERS');
    Route::match(['get','post'],'users/impersonate/{user?}/{code?}', 'UserController@impersonate')->middleware('permissions:USERS');
    Route::post('users/save', 'UserController@save')->middleware('permissions:USERS');
    Route::post('users/remove', 'UserController@remove')->middleware('permissions:USERS'); 
    Route::match(['get','post'], 'users', 'UserController@index')->middleware('permissions:USERS');
    //bands
    Route::post('bands/save', 'BandController@save')->middleware('permissions:BANDS');
    Route::post('bands/remove', 'BandController@remove')->middleware('permissions:BANDS');
    Route::match(['get','post'], 'bands/{autopen?}', 'BandController@index')->middleware('permissions:BANDS');
    //venues
    Route::match(['get','post'], 'venues/images', 'VenueController@images')->middleware('permissions:VENUES');
    Route::match(['get','post'], 'venues/banners', 'VenueController@banners')->middleware('permissions:VENUES');
    Route::match(['get','post'], 'venues/videos', 'VenueController@videos')->middleware('permissions:VENUES');
    Route::match(['get','post'], 'venues/stages', 'VenueController@stages')->middleware('permissions:VENUES');
    Route::post('venues/slug', 'VenueController@slug')->middleware('permissions:VENUES');
    Route::post('venues/save', 'VenueController@save')->middleware('permissions:VENUES');
    Route::post('venues/remove', 'VenueController@remove')->middleware('permissions:VENUES');
    Route::match(['get','post'], 'venues', 'VenueController@index')->middleware('permissions:VENUES');
    //shows
    Route::match(['get','post'], 'shows/passwords', 'ShowController@passwords')->middleware('permissions:SHOWS');
    Route::match(['get','post'], 'shows/tickets', 'ShowController@tickets')->middleware('permissions:SHOWS');
    Route::match(['get','post'], 'shows/bands', 'ShowController@bands')->middleware('permissions:SHOWS');
    Route::match(['get','post'], 'shows/showtimes', 'ShowController@showtimes')->middleware('permissions:SHOWS');
    Route::match(['get','post'], 'shows/contracts/{format?}/{id?}', 'ShowController@contracts')->middleware('permissions:SHOWS');
    Route::match(['get','post'], 'shows/images', 'ShowController@images')->middleware('permissions:SHOWS');
    Route::match(['get','post'], 'shows/banners', 'ShowController@banners')->middleware('permissions:SHOWS');
    Route::match(['get','post'], 'shows/videos', 'ShowController@videos')->middleware('permissions:SHOWS');
    Route::post('shows/slug', 'ShowController@slug')->middleware('permissions:SHOWS');
    Route::post('shows/save', 'ShowController@save')->middleware('permissions:SHOWS');
    Route::post('shows/remove', 'ShowController@remove')->middleware('permissions:SHOWS');
    Route::match(['get','post'], 'shows', 'ShowController@index')->middleware('permissions:SHOWS');
    //ticket_types
    Route::post('ticket_types/save', 'TicketTypeController@save')->middleware('permissions:TYPES');
    Route::match(['get','post'], 'ticket_types', 'TicketTypeController@index')->middleware('permissions:TYPES');
    //coupons
    Route::post('coupons/save', 'DiscountController@save')->middleware('permissions:COUPONS');
    Route::post('coupons/remove', 'DiscountController@remove')->middleware('permissions:COUPONS');
    Route::match(['get','post'], 'coupons', 'DiscountController@index')->middleware('permissions:COUPONS');
    //packages
    Route::post('packages/save', 'PackageController@save')->middleware('permissions:PACKAGES');
    Route::post('packages/remove', 'PackageController@remove')->middleware('permissions:PACKAGES');
    Route::match(['get','post'], 'packages', 'PackageController@index')->middleware('permissions:PACKAGES');
    //acls
    Route::post('acls/save', 'AclController@save')->middleware('permissions:ACLS');
    Route::post('acls/remove', 'AclController@remove')->middleware('permissions:ACLS');
    Route::match(['get','post'], 'acls', 'AclController@index')->middleware('permissions:ACLS');
    Route::match(['get','post'], 'user_types', 'AclController@user_types')->middleware('permissions:ACLS');
    //manifests emails
    Route::get('manifests/view/{format}/{id}', 'ManifestController@view')->middleware('permissions:MANIFESTS');
    Route::match(['get','post'], 'manifests', 'ManifestController@index')->middleware('permissions:MANIFESTS');
    //contact logs
    Route::match(['get','post'], 'contacts', 'ContactController@index')->middleware('permissions:CONTACTS');
    //purchases
    Route::post('purchases/email', 'PurchaseController@email')->middleware('permissions:PURCHASES');
    Route::get('purchases/tickets/{type}/{ids}', 'PurchaseController@tickets')->middleware('permissions:PURCHASES');
    Route::post('purchases/save', 'PurchaseController@save')->middleware('permissions:PURCHASES');
    Route::match(['get','post'], 'purchases', 'PurchaseController@index')->middleware('permissions:PURCHASES');
    //home sliders
    Route::post('sliders/save', 'SliderController@save')->middleware('permissions:SLIDERS');
    Route::post('sliders/remove', 'SliderController@remove')->middleware('permissions:SLIDERS');
    Route::match(['get','post'], 'sliders', 'SliderController@index')->middleware('permissions:SLIDERS');
    //consignment tickets
    Route::get('consignments/tickets/{type}/{ids}', 'ConsignmentController@tickets')->middleware('permissions:CONSIGNMENTS');
    Route::get('consignments/view/{type}/{id}', 'ConsignmentController@view')->middleware('permissions:CONSIGNMENTS');
    Route::post('consignments/save', 'ConsignmentController@save')->middleware('permissions:CONSIGNMENTS');
    Route::match(['get','post'], 'consignments', 'ConsignmentController@index')->middleware('permissions:CONSIGNMENTS');
    //apps
    Route::match(['get','post'], 'apps/deals', 'AppController@deals')->middleware('permissions:APPS');
    Route::match(['get','post'], 'apps', 'AppController@index')->middleware('permissions:APPS');
});
//ADMIN ROUTES FOR APP
Route::group(['prefix' => 'app','middleware' => 'cors','namespace' => 'App'], function () {
    //apps config
    Route::get('cities', 'AppController@cities');
    Route::get('shows/{id?}/{venue_id?}', 'AppController@shows');
    Route::get('venues', 'AppController@venues');
});
//COMMAND ROUTES
Route::group(['prefix' => 'command','middleware' => 'auth'], function () {
    //reports
    Route::get('ReportManifest', function () {
        Artisan::call('Report:manifest');
    })->middleware('permissions:COMMANDS');
    Route::get('ReportSales', function () {
        Artisan::call('Report:sales',['days'=>100,'onlyadmin'=>1]);
    })->middleware('permissions:COMMANDS');
    Route::get('ReportSalesReceipt', function () {
        Artisan::call('Report:sales_receipt',['days'=>1]);
    })->middleware('permissions:COMMANDS');
    Route::get('ReportFinancial', function () {
        Artisan::call('Report:financial',['weeks'=>0]);
    })->middleware('permissions:COMMANDS');
    //promotions
    Route::get('PromoAnnounced', function () {
        Artisan::call('Promo:announced',['days'=>7]);
    })->middleware('permissions:COMMANDS');
    //utilities
    Route::get('ShoppingcartClean', function () {
        Artisan::call('Shoppingcart:clean',['days'=>10]);
    })->middleware('permissions:COMMANDS');
    Route::get('ShoppingcartRecover', function () {
        Artisan::call('Shoppingcart:recover',['hours'=>4]);
    })->middleware('permissions:COMMANDS');
    Route::get('ContractUpdateTickets', function () {
        Artisan::call('Contract:update_tickets');
    })->middleware('permissions:COMMANDS');
    Route::get('CheckConsignments', function () {
        Artisan::call('Consignments:check');
    })->middleware('permissions:COMMANDS');
});