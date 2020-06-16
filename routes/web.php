<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//
Route::get('/', [
    'as' => 'page',
    'uses' => 'PageController@getPage'
]);

Route::get('getdata', 'PageController@getdata')->name('getdata');

Route::get('getdatapaypal', 'PageController@getpaypal')->name('getpaypal');


Route::get('getpay_pal', 'PageController@getdata')->name('getpay_pal');

Route::get('paypal_table', 'PageController@getPaypal_table')->name('paypal_table');

Route::get('test', [
    'as' => 'test',
    'uses' => 'PageController@test'
]);

Route::get('export', 'TrackingExport@export')->name('exportfile');

Route::post('import', 'PageController@import')->name('import');

Route::POST('call', [
    'as' => 'callall',
    'uses' => 'PageController@CallTracking',

]);

Route::get('mapping', [
    'as' => 'mapping',
    'uses' => 'PageController@getMapping'
]);

Route::post('find_date', [
    'as' => 'find_date',
    'uses' => 'PageController@find_date'
]);

//Edit row

Route::post('Edit_tracking', 'PageController@postEdit');

Route::post('Edit_paypal', 'PageController@ ');

Route::post('Detail_tracking', 'PageController@Detail');


Route::resource('datatables', 'PageController');

Route::POST('exportTracking', 'PageController@exportTracking');






