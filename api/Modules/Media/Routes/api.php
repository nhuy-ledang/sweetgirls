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

Route::get('media_folders_all', ['uses' => 'FolderController@all']);
Route::group(['prefix' => 'media_folders'], function () {
    Route::get('', ['uses' => 'FolderController@index']);
    Route::post('', ['uses' => 'FolderController@store']);
    Route::put('{id}', ['uses' => 'FolderController@update']);
    Route::get('{id}', ['uses' => 'FolderController@show']);
    Route::delete('{id}', ['uses' => 'FolderController@destroy']);
});
Route::group(['prefix' => 'media'], function () {
    Route::post('upload', ['uses' => 'MediaController@storeFile']);
    Route::post('uploads', ['uses' => 'MediaController@storeFiles']);
    Route::post('unlink', ['uses' => 'MediaController@unlinkFile']);
    //Route::post('upload/document', ['uses' => 'MediaController@uploadDocument']);
});
Route::post('media_deletes', ['uses' => 'MediaController@deleteAll']);
Route::post('media_moves', ['uses' => 'MediaController@moveAll']);
Route::group(['prefix' => 'media_files'], function () {
    Route::put('{id}', ['uses' => 'MediaController@update']);
});
Route::group(['prefix' => 'filemanager'], function () {
    Route::get('', ['uses' => 'FilemanagerController@index']);
});
