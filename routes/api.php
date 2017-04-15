<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//ADMIN ROUTES FOR APP
Route::group(['prefix' => 'app','middleware' => 'cors','namespace' => 'App'], function () {
    //apps config general
    Route::get('init', 'GeneralController@init');
    Route::get('shows/{id?}/{venue_id?}', 'GeneralController@shows');
    Route::get('showtime/{id}', 'GeneralController@showtime');
    Route::post('contact', 'GeneralController@contact');
    //apps config session
    Route::post('login', 'SessionController@login');
    Route::post('purchases', 'SessionController@purchases');
    Route::post('venues_check', 'SessionController@venues_to_check');
    Route::post('events_check', 'SessionController@events_to_check');
    Route::post('purchases_check', 'SessionController@purchases_to_check');
    Route::post('check_tickets', 'SessionController@check_tickets');
    Route::post('scan_tickets', 'SessionController@scan_tickets');
});
//ADMIN ROUTES FOR JSON FEED
Route::group(['prefix' => 'feed','middleware' => 'cors','namespace' => 'Feed'], function () {
    //feeds config
    Route::get('events/{venue_id}', 'FeedController@events');
});
