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
Route::get('sys_settings_all', ['uses' => 'SettingController@all']);
Route::group(['prefix' => 'sys_settings'], function () {
    Route::get('{key}', ['uses' => 'SettingController@show']);
    Route::post('', ['uses' => 'SettingController@store']);
});

Route::get('sys_banners_all', ['uses' => 'BannerController@all']);
Route::group(['prefix' => 'sys_banners'], function() {
    Route::get('', ['uses' => 'BannerController@index']);
    Route::get('{id}', ['uses' => 'BannerController@show']);
    Route::post('', ['uses' => 'BannerController@store']);
    Route::post('{id}', ['uses' => 'BannerController@update']);
    Route::patch('{id}', ['uses' => 'BannerController@patch']);
    Route::delete('{id}', ['uses' => 'BannerController@destroy']);
    Route::group(['prefix' => '{id}/images'], function() {
        Route::get('', ['uses' => 'BannerImageController@indexImage']);
        Route::post('', ['uses' => 'BannerImageController@storeImage']);
        Route::put('{image_id}', ['uses' => 'BannerImageController@updateImage']);
        Route::post('{image_id}', ['uses' => 'BannerImageController@updateImage']);
        Route::delete('{image_id}', ['uses' => 'BannerImageController@destroyImage']);
    });
});

Route::get('sys_intro_all', ['uses' => 'SettingIntroController@all']);
Route::group(['prefix' => 'sys_intro'], function () {
    Route::get('{key}', ['uses' => 'SettingIntroController@show']);
    Route::post('', ['uses' => 'SettingIntroController@store']);
});

Route::get('sys_banner_all', ['uses' => 'SettingBannerController@all']);
Route::group(['prefix' => 'sys_banner'], function () {
    Route::get('{key}', ['uses' => 'SettingBannerController@show']);
    Route::post('', ['uses' => 'SettingBannerController@store']);
    Route::post('type', ['uses' => 'SettingBannerController@updateType']);
});

Route::group(['prefix' => 'sys_translates'], function() {
    Route::get('', ['uses' => 'TranslateController@index']);
    Route::post('', ['uses' => 'TranslateController@store']);
    Route::delete('{id}', ['uses' => 'TranslateController@destroy']);
});

Route::get('sys_languages_all', ['uses' => 'LanguageController@all']);
Route::group(['prefix' => 'sys_languages'], function() {
    Route::get('', ['uses' => 'LanguageController@index']);
    Route::post('', ['uses' => 'LanguageController@store']);
    Route::put('{id}', ['uses' => 'LanguageController@update']);
    Route::delete('{id}', ['uses' => 'LanguageController@destroy']);
});

Route::post('system_contacts', ['uses' => 'ContactController@store']);
Route::group(['prefix' => 'contacts'], function() {
    Route::get('', ['uses' => 'ContactController@index']);
});