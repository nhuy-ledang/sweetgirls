<?php

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
use Illuminate\Support\Facades\Route;


Route::get('user_group_all', ['uses' => 'GroupController@all']);
Route::group(['prefix' => 'user_groups'], function() {
    Route::get('', ['uses' => 'GroupController@index']);
    Route::post('', ['uses' => 'GroupController@store']);
    Route::post('{id}', ['uses' => 'GroupController@update']);
    Route::put('{id}', ['uses' => 'GroupController@update']);
    Route::delete('{id}', ['uses' => 'GroupController@destroy']);
});

Route::group(['prefix' => 'users'], function() {
    Route::get('', ['uses' => 'UserController@index']);
    Route::post('', ['uses' => 'UserController@store']);
    Route::put('{id}', ['uses' => 'UserController@update']);
    Route::post('{id}', ['uses' => 'UserController@update']);
    Route::get('{id}', ['uses' => 'UserController@show']);
    Route::delete('{id}', ['uses' => 'UserController@destroy']);
    Route::post('{id}/banned', ['uses' => 'UserController@banned']);
    Route::group(['prefix' => '{id}'], function() {
        Route::get('address_all', ['uses' => 'AddressController@allAddresses']);
        Route::post('addresses', ['uses' => 'AddressController@storeAddresses']);
        Route::put('addresses/{address_id}', ['uses' => 'AddressController@updateAddresses']);
        Route::delete('addresses/{address_id}', ['uses' => 'AddressController@destroyAddresses']);
        Route::get('ticket_all', ['uses' => 'TicketController@allTickets']);
    });
});
Route::get('user_search', ['uses' => 'UserController@search']);
Route::get('users_exports', ['uses' => 'UserController@exportExcel']);
//Route::post('users_import_check', ['uses' => 'UserController@importCheck']);
//Route::post('users_import', ['uses' => 'UserController@import']);

Route::get('lead_source_all', ['uses' => 'LeadSourceController@all']);
Route::group(['prefix' => 'lead_sources'], function() {
    Route::get('', ['uses' => 'LeadSourceController@index']);
    Route::post('', ['uses' => 'LeadSourceController@store']);
    Route::post('{id}', ['uses' => 'LeadSourceController@update']);
    Route::put('{id}', ['uses' => 'LeadSourceController@update']);
    Route::delete('{id}', ['uses' => 'LeadSourceController@destroy']);
});
Route::group(['prefix' => 'leads'], function() {
    Route::get('', ['uses' => 'LeadController@index']);
    Route::post('', ['uses' => 'LeadController@store']);
    Route::put('{id}', ['uses' => 'LeadController@update']);
    Route::post('{id}', ['uses' => 'LeadController@update']);
    Route::get('{id}', ['uses' => 'LeadController@show']);
    Route::delete('{id}', ['uses' => 'LeadController@destroy']);
});

Route::get('user_ranks_all', ['uses' => 'UserRankController@all']);
Route::group(['prefix' => 'user_ranks'], function() {
    Route::get('', ['uses' => 'UserRankController@index']);
    Route::post('', ['uses' => 'UserRankController@store']);
    Route::get('{id}', ['uses' => 'UserRankController@show']);
    Route::put('{id}', ['uses' => 'UserRankController@update']);
    Route::patch('{id}', ['uses' => 'UserRankController@patch']);
    Route::delete('{id}', ['uses' => 'UserRankController@destroy']);
});

Route::group(['prefix' => 'user_stats'], function() {
    Route::get('buyers_rank', ['uses' => 'StatisticController@indexBuyerRank']);
    Route::get('birthday', ['uses' => 'StatisticController@indexBirthday']);
});

Route::get('user_settings_all', ['uses' => 'SettingController@all']);
Route::group(['prefix' => 'user_settings'], function () {
    Route::get('{key}', ['uses' => 'SettingController@show']);
    Route::post('', ['uses' => 'SettingController@store']);
});
