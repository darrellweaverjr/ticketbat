<?php

//ADMIN ROUTES FOR APP GENERAL
Route::group(['prefix' => 'app','middleware' => 'app.security:0','namespace' => 'App'], function () {
    //apps config general
    //Route::post('general_init', 'GeneralController@init');
    //Route::options('general_init', function() { return; });
    //Route::match(['options','post'], 'general_init', function() { return ['success'=>false, 'msg'=>'There is an error with the server!']; });
    Route::match(['options','post'], 'general_init', 'GeneralController@init');
    Route::match(['options','post'], 'general_show', 'GeneralController@show');
    Route::match(['options','post'], 'general_event', 'GeneralController@event');
    Route::match(['options','post'], 'general_contact', 'GeneralController@contact');
    //apps config session auth
    Route::match(['options','post'], 'auth_login', 'AuthController@login');
    Route::match(['options','post'], 'auth_register', 'AuthController@register');
    Route::match(['options','post'], 'auth_recover', 'AuthController@recover');
    Route::match(['options','post'], 'auth_change', 'AuthController@change');
    //apps config manage shopping cart
    Route::match(['options','post'], 'cart_get', 'ShoppingCartController@get');
    Route::match(['options','post'], 'cart_add', 'ShoppingCartController@add');
    Route::match(['options','post'], 'cart_update', 'ShoppingCartController@update');
    Route::match(['options','post'], 'cart_remove', 'ShoppingCartController@remove');
    Route::match(['options','post'], 'cart_coupon', 'ShoppingCartController@coupon');
    //apps config purchase options
    Route::match(['options','post'], 'purchase_make', 'PurchaseController@buy');    
});
//ADMIN ROUTES FOR APP WITH LOGIN
Route::group(['prefix' => 'app','middleware' => 'app.security:1','namespace' => 'App'], function () {
    //apps config user options    
    Route::match(['options','post'], 'my_purchases', 'UserController@purchases');
    Route::match(['options','post'], 'my_venues_check', 'UserController@venues_to_check');
    Route::match(['options','post'], 'my_events_check', 'UserController@events_to_check');
    Route::match(['options','post'], 'my_purchases_check', 'UserController@purchases_to_check');
    Route::match(['options','post'], 'my_tickets_check', 'UserController@check_tickets');
    Route::match(['options','post'], 'my_tickets_scan', 'UserController@scan_tickets');
});
//ADMIN ROUTES FOR JSON FEED
Route::group(['prefix' => 'feed','middleware' => 'cors','namespace' => 'Feed'], function () {
    //venue
    Route::match(['options','get'], 'venue/events/{venue_id}', 'VenueController@events');
    //restaurant
    Route::match(['options','get'], 'restaurant/general/{restaurant_id}', 'RestaurantController@general');
    Route::match(['options','get'], 'restaurant/menu/{restaurant_id}', 'RestaurantController@menu');
    Route::match(['options','post'], 'restaurant/reservations', 'RestaurantController@reservations');
    Route::match(['options','get'], 'restaurant/specials/{restaurant_id}', 'RestaurantController@specials');
    Route::match(['options','get'], 'restaurant/awards/{restaurant_id}', 'RestaurantController@awards');
    Route::match(['options','get'], 'restaurant/reviews/{restaurant_id}', 'RestaurantController@reviews');
    Route::match(['options','get'], 'restaurant/comments/{restaurant_id}', 'RestaurantController@comments');
    Route::match(['options','get'], 'restaurant/albums/{restaurant_id}', 'RestaurantController@albums');
});