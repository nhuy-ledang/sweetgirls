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

Route::get('pd_manufacturers_all', ['uses' => 'ManufacturerController@all']);
Route::group(['prefix' => 'pd_manufacturers'], function() {
    Route::get('', ['uses' => 'ManufacturerController@index']);
    Route::get('{id}', ['uses' => 'ManufacturerController@show']);
    Route::post('', ['uses' => 'ManufacturerController@store']);
    Route::put('{id}', ['uses' => 'ManufacturerController@update']);
    Route::post('{id}', ['uses' => 'ManufacturerController@update']);
    Route::delete('{id}', ['uses' => 'ManufacturerController@destroy']);
});
Route::get('pd_categories_all', ['uses' => 'CategoryController@all']);
Route::group(['prefix' => 'pd_categories'], function() {
    Route::get('', ['uses' => 'CategoryController@index']);
    Route::get('{id}', ['uses' => 'CategoryController@show']);
    Route::post('', ['uses' => 'CategoryController@store']);
    Route::post('{id}', ['uses' => 'CategoryController@update']);
    Route::put('{id}', ['uses' => 'CategoryController@update']);
    Route::patch('{id}', ['uses' => 'CategoryController@patch']);
    Route::delete('{id}', ['uses' => 'CategoryController@destroy']);
});
Route::get('pd_options_all', ['uses' => 'OptionController@all']);
Route::group(['prefix' => 'pd_options'], function() {
    Route::get('', ['uses' => 'OptionController@index']);
    Route::get('{id}', ['uses' => 'OptionController@show']);
    Route::post('', ['uses' => 'OptionController@store']);
    Route::put('{id}', ['uses' => 'OptionController@update']);
    Route::delete('{id}', ['uses' => 'OptionController@destroy']);
    Route::get('{id}/values', ['uses' => 'OptionController@values']);
});
Route::group(['prefix' => 'pd_option_values'], function() {
    Route::get('', ['uses' => 'OptionValueController@index']);
    Route::get('{id}', ['uses' => 'OptionValueController@show']);
    Route::post('', ['uses' => 'OptionValueController@store']);
    Route::put('{id}', ['uses' => 'OptionValueController@update']);
    Route::delete('{id}', ['uses' => 'OptionValueController@destroy']);
});
Route::get('pd_products_all', ['uses' => 'ProductController@all']);
Route::get('pd_products_search', ['uses' => 'ProductController@search']);
Route::group(['prefix' => 'pd_products'], function() {
    Route::get('', ['uses' => 'ProductController@index']);
    Route::get('{id}', ['uses' => 'ProductController@show']);
    Route::post('', ['uses' => 'ProductController@store']);
    Route::put('{id}', ['uses' => 'ProductController@update']);
    Route::post('{id}', ['uses' => 'ProductController@update']);
    Route::patch('{id}', ['uses' => 'ProductController@patch']);
    Route::delete('{id}', ['uses' => 'ProductController@destroy']);
    Route::get('{id}/options', ['uses' => 'ProductController@options']);
    Route::get('{id}/variants', ['uses' => 'ProductController@getVariants']);
    Route::post('{id}/variants', ['uses' => 'ProductController@createVariant']);
    Route::put('{id}/variants', ['uses' => 'ProductController@updateVariant']);
    Route::delete('{id}/variants', ['uses' => 'ProductController@destroyVariant']);
    Route::group(['prefix' => '{id}/images'], function() {
        Route::get('', ['uses' => 'ProductImageController@indexImage']);
        Route::post('', ['uses' => 'ProductImageController@storeImage']);
        Route::post('multiple', ['uses' => 'ProductImageController@storeImages']);
        Route::put('{image_id}', ['uses' => 'ProductImageController@updateImage']);
        Route::post('{image_id}', ['uses' => 'ProductImageController@updateImage']);
        Route::delete('{image_id}', ['uses' => 'ProductImageController@destroyImage']);
        Route::patch('', ['uses' => 'ProductImageController@patchImage']);
    });
});
Route::group(['prefix' => 'pd_product_options'], function() {
    Route::get('', ['uses' => 'ProductOptionController@index']);
    Route::post('', ['uses' => 'ProductOptionController@store']);
    Route::put('{id}', ['uses' => 'ProductOptionController@update']);
    Route::delete('{id}', ['uses' => 'ProductOptionController@destroy']);
});
Route::group(['prefix' => 'pd_product_variants'], function() {
    Route::get('', ['uses' => 'ProductVariantController@index']);
    Route::post('', ['uses' => 'ProductVariantController@store']);
    Route::put('{id}', ['uses' => 'ProductVariantController@update']);
    Route::delete('{id}', ['uses' => 'ProductVariantController@destroy']);
});
