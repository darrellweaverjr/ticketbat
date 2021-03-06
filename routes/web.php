<?php

//GENERAL ROUTES
Auth::routes();
Route::get('/', function () { return redirect()->route('index'); });
Route::get('/admin', function () { return redirect()->route('home'); });
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
    Route::match(['get','post'], 'dashboard/coupons', 'DashboardController@coupons')->middleware('permissions:REPORTS');
    Route::match(['get','post'], 'dashboard/future_sales', 'DashboardController@future_sales')->middleware('permissions:REPORTS');
    Route::match(['get','post'], 'dashboard/channels', 'DashboardController@channels')->middleware('permissions:REPORTS');
    Route::match(['get','post'], 'dashboard/sellers', 'DashboardController@sellers')->middleware('permissions:REPORTS');
    //users
    Route::post('users/profile', 'UserController@profile')->middleware('permissions:USERS');
    Route::post('users/impersonate', 'UserController@impersonate')->middleware('permissions:USERS');
    Route::post('users/save', 'UserController@save')->middleware('permissions:USERS');
    Route::post('users/remove', 'UserController@remove')->middleware('permissions:USERS');
    Route::match(['get','post'], 'users', 'UserController@index')->middleware('permissions:USERS');
    //bands
    Route::post('bands/save', 'BandController@save')->middleware('permissions:BANDS');
    Route::post('bands/remove', 'BandController@remove')->middleware('permissions:BANDS');
    Route::match(['get','post'], 'bands/{autopen?}', 'BandController@index')->middleware('permissions:BANDS');
    //venues
    Route::post('venues/pos', 'VenueController@pos')->middleware('permissions:VENUES');
    Route::post('venues/images', 'VenueController@images')->middleware('permissions:VENUES');
    Route::post('venues/banners', 'VenueController@banners')->middleware('permissions:VENUES');
    Route::post('venues/videos', 'VenueController@videos')->middleware('permissions:VENUES');
    Route::post('venues/stages', 'VenueController@stages')->middleware('permissions:VENUES');
    Route::post('venues/stage_images', 'VenueController@stage_images')->middleware('permissions:VENUES');
    Route::post('venues/ads', 'VenueController@ads')->middleware('permissions:VENUES');
    Route::post('venues/reports', 'VenueController@reports')->middleware('permissions:VENUES');
    Route::post('venues/slug', 'VenueController@slug')->middleware('permissions:VENUES');
    Route::post('venues/save', 'VenueController@save')->middleware('permissions:VENUES');
    Route::post('venues/remove', 'VenueController@remove')->middleware('permissions:VENUES');
    Route::match(['get','post'], 'venues', 'VenueController@index')->middleware('permissions:VENUES');
    //shows
    Route::post('shows/sweepstakes', 'ShowController@sweepstakes')->middleware('permissions:SHOWS');
    Route::post('shows/passwords', 'ShowController@passwords')->middleware('permissions:SHOWS');
    Route::post('shows/tickets', 'ShowController@tickets')->middleware('permissions:SHOWS');
    Route::post('shows/bands', 'ShowController@bands')->middleware('permissions:SHOWS');
    Route::post('shows/showtimes', 'ShowController@showtimes')->middleware('permissions:SHOWS');
    Route::match(['get','post'],'shows/contracts/{format?}/{id?}', 'ShowController@contracts')->middleware('permissions:SHOWS');
    Route::post('shows/images', 'ShowController@images')->middleware('permissions:SHOWS');
    Route::post('shows/banners', 'ShowController@banners')->middleware('permissions:SHOWS');
    Route::post('shows/videos', 'ShowController@videos')->middleware('permissions:SHOWS');
    Route::post('shows/reviews', 'ShowController@reviews')->middleware('permissions:SHOWS');
    Route::post('shows/slug', 'ShowController@slug')->middleware('permissions:SHOWS');
    Route::post('shows/save', 'ShowController@save')->middleware('permissions:SHOWS');
    Route::post('shows/remove', 'ShowController@remove')->middleware('permissions:SHOWS');
    Route::match(['get','post'], 'shows', 'ShowController@index')->middleware('permissions:SHOWS');
    //ticket_types
    Route::post('ticket_types/classes', 'TicketTypeController@classes')->middleware('permissions:TYPES');
    Route::post('ticket_types/styles', 'TicketTypeController@styles')->middleware('permissions:TYPES');
    Route::post('ticket_types/save', 'TicketTypeController@save')->middleware('permissions:TYPES');
    Route::match(['get','post'], 'ticket_types', 'TicketTypeController@index')->middleware('permissions:TYPES');
    //categories
    Route::post('categories/save', 'CategoryController@save')->middleware('permissions:CATEGORIES');
    Route::post('categories/remove', 'CategoryController@remove')->middleware('permissions:CATEGORIES');
    Route::match(['get','post'], 'categories', 'CategoryController@index')->middleware('permissions:CATEGORIES');
    //coupons
    Route::post('coupons/tickets', 'DiscountController@tickets')->middleware('permissions:COUPONS');
    Route::post('coupons/save', 'DiscountController@save')->middleware('permissions:COUPONS');
    Route::post('coupons/remove', 'DiscountController@remove')->middleware('permissions:COUPONS');
    Route::match(['get','post'], 'coupons', 'DiscountController@index')->middleware('permissions:COUPONS');
    //packages
    Route::post('packages/save', 'PackageController@save')->middleware('permissions:PACKAGES');
    Route::post('packages/remove', 'PackageController@remove')->middleware('permissions:PACKAGES');
    Route::match(['get','post'], 'packages', 'PackageController@index')->middleware('permissions:PACKAGES');    
    //manifests emails
    Route::post('manifests/generate', 'ManifestController@generate')->middleware('permissions:MANIFESTS');
    Route::post('manifests/send', 'ManifestController@send')->middleware('permissions:MANIFESTS');
    Route::get('manifests/view/{format}/{id}', 'ManifestController@view')->middleware('permissions:MANIFESTS');
    Route::match(['get','post'], 'manifests', 'ManifestController@index')->middleware('permissions:MANIFESTS');
    //contact logs
    Route::post('contacts/save', 'ContactController@save')->middleware('permissions:CONTACTS');
    Route::match(['get','post'], 'contacts', 'ContactController@index')->middleware('permissions:CONTACTS');
    //purchases
    Route::post('purchases/check', 'RefundController@check')->middleware('permissions:REFUNDS');
    Route::post('purchases/refund', 'RefundController@refund')->middleware('permissions:REFUNDS');
    Route::post('purchases/filter', 'PurchaseController@filter')->middleware('permissions:PURCHASES');
    Route::post('purchases/resend', 'PurchaseController@resend')->middleware('permissions:REFUNDS');
    Route::post('purchases/email', 'PurchaseController@email')->middleware('permissions:PURCHASES');
    Route::get('purchases/tickets/{type}/{ids}', 'PurchaseController@tickets')->middleware('permissions:PURCHASES');
    Route::post('purchases/save', 'PurchaseController@save')->middleware('permissions:PURCHASES');
    Route::match(['get','post'], 'purchases', 'PurchaseController@index')->middleware('permissions:PURCHASES');
    //refunds    
    Route::post('refunds/save', 'RefundController@save')->middleware('permissions:REFUNDS');
    Route::get('refunds/pendings', 'RefundController@pendings')->middleware('permissions:REFUNDS');
    Route::match(['get','post'], 'refunds', 'RefundController@index')->middleware('permissions:REFUNDS');
    //home sliders
    Route::post('sliders/save', 'SliderController@save')->middleware('permissions:SLIDERS');
    Route::post('sliders/remove', 'SliderController@remove')->middleware('permissions:SLIDERS');
    Route::match(['get','post'], 'sliders', 'SliderController@index')->middleware('permissions:SLIDERS');
    //consignment tickets
    Route::get('consignments/contract/{id}', 'ConsignmentController@contract')->middleware('permissions:CONSIGNMENTS');
    Route::get('consignments/tickets/{type}/{ids}/{start?}/{end?}', 'ConsignmentController@tickets')->middleware('permissions:CONSIGNMENTS');
    Route::get('consignments/view/{type}/{id}', 'ConsignmentController@view')->middleware('permissions:CONSIGNMENTS');
    Route::post('consignments/save', 'ConsignmentController@save')->middleware('permissions:CONSIGNMENTS');
    Route::match(['get','post'], 'consignments', 'ConsignmentController@index')->middleware('permissions:CONSIGNMENTS');
    //acls
    Route::post('acls/commands', 'AclController@commands')->middleware('permissions:ACLS');
    Route::post('acls/save', 'AclController@save')->middleware('permissions:ACLS');
    Route::post('acls/remove', 'AclController@remove')->middleware('permissions:ACLS');
    Route::match(['get','post'], 'acls', 'AclController@index')->middleware('permissions:ACLS');
    Route::match(['get','post'], 'user_types', 'AclController@user_types')->middleware('permissions:ACLS');
    //ngbc
    Route::match(['get','post'], 'ngcb/sales', 'NGCBController@sales')->middleware('permissions:NGCB');
    Route::match(['get','post'], 'ngcb', 'NGCBController@index')->middleware('permissions:NGCB');
});


// FRONTEND CUSTOMER SITE ROUTES
Route::group(['middleware' => ['ForceSSL','check'],'namespace' => 'Production'], function () {
    //home
    Route::get('/', 'HomeController@index')->name('index');
    Route::get('/home', function () { return redirect()->route('index'); });
    Route::get('/events', function () { return redirect()->route('index'); });
    Route::post('home/search', 'HomeController@search');
    //general
    Route::post('general/contact', 'GeneralController@contact');
    Route::post('general/country', 'GeneralController@country');
    Route::post('general/region', 'GeneralController@region');
    //user
    Route::post('user/login', 'UserController@login');
    Route::post('user/logout', 'UserController@logout');
    Route::post('user/register', 'UserController@register');
    Route::post('user/recover_password', 'UserController@recover_password');
    Route::post('user/reset_password', 'UserController@reset_password');
    Route::post('user/guest', 'UserController@guest');
    //user seller
    Route::post('user/seller/drawer_open', 'UserSellerController@drawer_open')->middleware('productioncheck');
    Route::post('user/seller/drawer_continue', 'UserSellerController@drawer_continue')->middleware('productioncheck');
    Route::post('user/seller/drawer_close', 'UserSellerController@drawer_close')->middleware('productioncheck');
    Route::post('user/seller/tally', 'UserSellerController@seller_tally')->middleware('productioncheck');
    Route::post('user/seller/report', 'UserSellerController@seller_report')->middleware('productioncheck');
    //user purchase
    Route::post('user/purchases/share', 'UserPurchaseController@share')->middleware('productioncheck');
    Route::get('user/purchases/receipts/{id}', 'UserPurchaseController@receipts')->middleware('productioncheck');
    Route::get('user/purchases/tickets/{type}/{id}', 'UserPurchaseController@tickets');
    Route::match(['get','post'], 'user/purchases', 'UserPurchaseController@index')->middleware('productioncheck');
    //user consignment
    Route::post('user/consignments/contract', 'UserConsignmentController@contract')->middleware('productioncheck');
    Route::post('user/consignments/save', 'UserConsignmentController@save')->middleware('productioncheck');
    Route::match(['get','post'], 'user/consignments', 'UserConsignmentController@index')->middleware('productioncheck');
    //shoppingcart
    Route::post('shoppingcart/add', 'ShoppingcartController@add');
    Route::post('shoppingcart/update', 'ShoppingcartController@update');
    Route::post('shoppingcart/remove', 'ShoppingcartController@remove');
    Route::post('shoppingcart/coupon', 'ShoppingcartController@coupon');
    Route::post('shoppingcart/share', 'ShoppingcartController@share');
    Route::post('shoppingcart/printed', 'ShoppingcartController@printed');
    Route::post('shoppingcart/items', 'ShoppingcartController@items');
    Route::post('shoppingcart/count', 'ShoppingcartController@count');
    Route::post('shoppingcart/countdown', 'ShoppingcartController@countdown');
    Route::post('shoppingcart/pos/update', 'ShoppingcartController@pos_update');
    Route::post('shoppingcart/pos/receipts', 'ShoppingcartController@pos_email_receipt');
    Route::match(['get','post'], 'shoppingcart/viewcart', 'ShoppingcartController@index');
    //purchase
    Route::post('purchase/process', 'PurchaseController@process');
    Route::post('purchase/complete', 'PurchaseController@complete');
    Route::post('purchase/welcome', 'PurchaseController@welcome');
    Route::post('purchase/receipts', 'PurchaseController@receipts');
    Route::get('purchase/printer/{purchases}', 'PurchaseController@printer');
    //event
    Route::post('event/reviews', 'EventController@reviews');
    Route::match(['get','post'], 'buy/{slug}/{product}', 'EventController@buy');
    Route::match(['get','post'], 'event/{slug}', 'EventController@index');
    //venues
    Route::match(['get','post'], 'venue/{slug}', 'VenueController@view');
    Route::match(['get','post'], 'venues', 'VenueController@index');
    //merchandise
    Route::match(['get','post'], 'merchandises', 'GeneralController@merchandises');
});
