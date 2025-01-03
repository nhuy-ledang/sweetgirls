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

Route::get('st_departments_all', ['uses' => 'DepartmentController@all']);
Route::group(['prefix' => 'st_departments'], function() {
    Route::get('', ['uses' => 'DepartmentController@index']);
    Route::get('{id}', ['uses' => 'DepartmentController@show']);
    Route::post('', ['uses' => 'DepartmentController@store']);
    Route::put('{id}', ['uses' => 'DepartmentController@update']);
    Route::delete('{id}', ['uses' => 'DepartmentController@destroy']);
});

Route::get('st_users_all', ['uses' => 'UserController@all']);
Route::group(['prefix' => 'st_users'], function() {
    Route::get('', ['uses' => 'UserController@index']);
    Route::get('{id}', ['uses' => 'UserController@show']);
    Route::post('', ['uses' => 'UserController@store']);
    Route::put('{id}', ['uses' => 'UserController@update']);
    Route::post('{id}', ['uses' => 'UserController@update']);
    Route::patch('{id}', ['uses' => 'UserController@patch']);
    Route::delete('{id}', ['uses' => 'UserController@destroy']);
});

Route::get('st_salaries_all', ['uses' => 'SalaryController@all']);
Route::group(['prefix' => 'st_salaries'], function() {
    Route::get('', ['uses' => 'SalaryController@index']);
    Route::get('{id}', ['uses' => 'SalaryController@show']);
    Route::post('', ['uses' => 'SalaryController@store']);
    Route::put('{id}', ['uses' => 'SalaryController@update']);
    Route::patch('{id}', ['uses' => 'SalaryController@patch']);
    Route::delete('{id}', ['uses' => 'SalaryController@destroy']);
});
Route::get('st_salaries_stats', ['uses' => 'SalaryController@stats']);
