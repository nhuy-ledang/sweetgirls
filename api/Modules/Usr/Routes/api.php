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
Route::group(['prefix' => 'usr_auth'], function () {
    Route::post('login', ['uses' => 'AuthController@login']);
    Route::post('logout', ['uses' => 'AuthController@logout']);
    Route::get('', ['uses' => 'AuthController@index']);
    Route::post('pw-change', ['uses' => 'AuthController@passwordChange']);
    Route::post('profile-change', ['uses' => 'AuthController@profileChange']);
//    Route::post('forgot', ['uses' => 'AuthController@forgot']);
//    Route::post('forgot-checkotp', ['uses' => 'AuthController@forgotCheckOTP']);
//    Route::post('forgot-newpw', ['uses' => 'AuthController@forgotNewPassword']);
//    Route::post('phone-change', ['uses' => 'AuthController@phoneChange']);
//    Route::post('phone-verify', ['uses' => 'AuthController@phoneVerify']);
//    Route::post('email-change', ['uses' => 'AuthController@emailChange']);
//    Route::post('email-verify', ['uses' => 'AuthController@emailVerify']);
//    Route::post('email-check', ['uses' => 'AuthController@emailCheck']);
});
Route::get('usr_group_all', ['uses' => 'GroupController@all']);
Route::group(['prefix' => 'usr_groups'], function() {
    Route::get('', ['uses' => 'GroupController@index']);
    Route::post('', ['uses' => 'GroupController@store']);
    Route::post('{id}', ['uses' => 'GroupController@update']);
    Route::put('{id}', ['uses' => 'GroupController@update']);
    Route::delete('{id}', ['uses' => 'GroupController@destroy']);
});
Route::get('usr_roles_all', ['uses' => 'RoleController@all']);
Route::group(['prefix' => 'usr_roles'], function() {
    Route::get('', ['uses' => 'RoleController@index']);
    Route::post('', ['uses' => 'RoleController@store']);
    Route::put('{id}', ['uses' => 'RoleController@update']);
    Route::delete('{id}', ['uses' => 'RoleController@destroy']);
});
Route::get('usrs_all', ['uses' => 'UsrController@all']);
Route::group(['prefix' => 'usrs'], function() {
    Route::get('', ['uses' => 'UsrController@index']);
    Route::post('', ['uses' => 'UsrController@store']);
    Route::put('{id}', ['uses' => 'UsrController@update']);
    Route::post('{id}', ['uses' => 'UsrController@update']);
    Route::get('{id}', ['uses' => 'UsrController@show']);
    Route::delete('{id}', ['uses' => 'UsrController@destroy']);
    Route::post('{id}/banned', ['uses' => 'UsrController@banned']);
    Route::post('{id}/roles', ['uses' => 'UsrController@roles']);
});

Route::post('usr_notifies_mark_read', ['uses' => 'NotifyController@markRead']);
Route::post('usr_notifies_destroys', ['uses' => 'NotifyController@destroys']);
Route::group(['prefix' => 'usr_notifies'], function() {
    Route::get('', ['uses' => 'NotifyController@index']);
    Route::get('{id}', ['uses' => 'NotifyController@show']);
    Route::delete('{id}', ['uses' => 'NotifyController@destroy']);
});
Route::group(['prefix' => 'usr_notifies_alert'], function() {
    Route::get('', ['uses' => 'NotifyController@getAlerts']);
    Route::post('', ['uses' => 'NotifyController@markAlerts']);
});
Route::get('usr_notifies_unread_total', ['uses' => 'NotifyController@getUnreadTotal']);
Route::group(['prefix' => 'usr_activities'], function() {
    Route::get('', ['uses' => 'ActivityController@index']);
});