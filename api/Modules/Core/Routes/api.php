<?php

use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'core_banks'], function () {
    Route::get('', ['uses' => 'BankController@all']);
    Route::post('', ['uses' => 'BankController@store']);
    Route::delete('{id}', ['uses' => 'BankController@destroy']);
});
