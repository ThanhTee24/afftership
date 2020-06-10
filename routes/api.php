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


Route::post('call-tracking', 'Api\TrackingController@CallTracking')->name('call_tracking');

Route::post('update-tracking', 'Api\UpdateTrackingController@UpdateTracking')->name('update_tracking');

Route::post('paypal-tracking', 'Api\PaypalTrackingController@PaypalTracking')->name('paypal_tracking');

Route::post('add-tracking', 'Api\AddPaypalController@add_paypal')->name('add_paypal');


