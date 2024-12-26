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

Route::get('bus_categories_all', ['uses' => 'CategoryController@all']);
Route::post('bus_categories_sort_order', ['uses' => 'CategoryController@sortOrder']);
Route::get('bus_categories_stats', ['uses' => 'CategoryController@stats']);
Route::group(['prefix' => 'bus_categories'], function() {
    Route::get('', ['uses' => 'CategoryController@index']);
    Route::get('{id}', ['uses' => 'CategoryController@show']);
    Route::post('', ['uses' => 'CategoryController@store']);
    Route::put('{id}', ['uses' => 'CategoryController@update']);
    Route::delete('{id}', ['uses' => 'CategoryController@destroy']);
});
Route::group(['prefix' => 'bus_products'], function() {
    Route::get('', ['uses' => 'ProductController@index']);
    Route::get('{id}', ['uses' => 'ProductController@show']);
    Route::post('', ['uses' => 'ProductController@store']);
    Route::put('{id}', ['uses' => 'ProductController@update']);
    Route::post('{id}', ['uses' => 'ProductController@update']);
    Route::patch('{id}', ['uses' => 'ProductController@patch']);
    Route::delete('{id}', ['uses' => 'ProductController@destroy']);
});
Route::post('bus_my_products_products', ['uses' => 'MyProductController@create']);
Route::group(['prefix' => 'bus_my_products'], function() {
    Route::get('', ['uses' => 'MyProductController@index']);
    Route::get('{id}', ['uses' => 'MyProductController@show']);
    Route::post('', ['uses' => 'MyProductController@store']);
    Route::put('{id}', ['uses' => 'MyProductController@update']);
    Route::patch('{id}', ['uses' => 'MyProductController@patch']);
    Route::delete('{id}', ['uses' => 'MyProductController@destroy']);
});
Route::post('bus_my_services_products', ['uses' => 'MyServiceController@create']);
Route::group(['prefix' => 'bus_my_services'], function() {
    Route::get('', ['uses' => 'MyServiceController@index']);
    Route::get('{id}', ['uses' => 'MyServiceController@show']);
    Route::post('', ['uses' => 'MyServiceController@store']);
    Route::put('{id}', ['uses' => 'MyServiceController@update']);
    Route::patch('{id}', ['uses' => 'MyServiceController@patch']);
    Route::delete('{id}', ['uses' => 'MyServiceController@destroy']);
});
Route::post('bus_out_products_products', ['uses' => 'OutProductController@create']);
Route::group(['prefix' => 'bus_out_products'], function() {
    Route::get('', ['uses' => 'OutProductController@index']);
    Route::get('{id}', ['uses' => 'OutProductController@show']);
    Route::post('', ['uses' => 'OutProductController@store']);
    Route::put('{id}', ['uses' => 'OutProductController@update']);
    Route::patch('{id}', ['uses' => 'OutProductController@patch']);
    Route::delete('{id}', ['uses' => 'OutProductController@destroy']);
});
Route::post('bus_out_services_products', ['uses' => 'OutServiceController@create']);
Route::group(['prefix' => 'bus_out_services'], function() {
    Route::get('', ['uses' => 'OutServiceController@index']);
    Route::get('{id}', ['uses' => 'OutServiceController@show']);
    Route::post('', ['uses' => 'OutServiceController@store']);
    Route::put('{id}', ['uses' => 'OutServiceController@update']);
    Route::patch('{id}', ['uses' => 'OutServiceController@patch']);
    Route::delete('{id}', ['uses' => 'OutServiceController@destroy']);
});

Route::get('bus_policy_groups_all', ['uses' => 'PolicyGroupController@all']);
Route::group(['prefix' => 'bus_policy_groups'], function() {
    Route::get('', ['uses' => 'PolicyGroupController@index']);
    Route::get('{id}', ['uses' => 'PolicyGroupController@show']);
    Route::post('', ['uses' => 'PolicyGroupController@store']);
    Route::put('{id}', ['uses' => 'PolicyGroupController@update']);
    Route::delete('{id}', ['uses' => 'PolicyGroupController@destroy']);
});
Route::get('bus_policies_all', ['uses' => 'PolicyController@all']);
Route::group(['prefix' => 'bus_policies'], function() {
    Route::get('', ['uses' => 'PolicyController@index']);
    Route::get('{id}', ['uses' => 'PolicyController@show']);
    Route::post('', ['uses' => 'PolicyController@store']);
    Route::put('{id}', ['uses' => 'PolicyController@update']);
    Route::patch('{id}', ['uses' => 'PolicyController@patch']);
    Route::delete('{id}', ['uses' => 'PolicyController@destroy']);
});
Route::get('bus_promos_all', ['uses' => 'PromoController@all']);
Route::group(['prefix' => 'bus_promos'], function() {
    Route::get('', ['uses' => 'PromoController@index']);
    Route::get('{id}', ['uses' => 'PromoController@show']);
    Route::post('', ['uses' => 'PromoController@store']);
    Route::put('{id}', ['uses' => 'PromoController@update']);
    Route::patch('{id}', ['uses' => 'PromoController@patch']);
    Route::delete('{id}', ['uses' => 'PromoController@destroy']);
});

Route::get('sup_categories_all', ['uses' => 'SupplierCategoryController@all']);
Route::group(['prefix' => 'sup_categories'], function() {
    Route::get('', ['uses' => 'SupplierCategoryController@index']);
    Route::get('{id}', ['uses' => 'SupplierCategoryController@show']);
    Route::post('', ['uses' => 'SupplierCategoryController@store']);
    Route::put('{id}', ['uses' => 'SupplierCategoryController@update']);
    Route::delete('{id}', ['uses' => 'SupplierCategoryController@destroy']);
});
Route::get('sup_groups_all', ['uses' => 'SupplierGroupController@all']);
Route::group(['prefix' => 'sup_groups'], function() {
    Route::get('', ['uses' => 'SupplierGroupController@index']);
    Route::get('{id}', ['uses' => 'SupplierGroupController@show']);
    Route::post('', ['uses' => 'SupplierGroupController@store']);
    Route::put('{id}', ['uses' => 'SupplierGroupController@update']);
    Route::delete('{id}', ['uses' => 'SupplierGroupController@destroy']);
});
Route::get('sup_suppliers_all', ['uses' => 'SupplierController@all']);
Route::get('sup_suppliers_stats', ['uses' => 'SupplierController@stats']);
Route::get('sup_suppliers_search', ['uses' => 'SupplierController@search']);
Route::group(['prefix' => 'sup_suppliers'], function() {
    Route::get('', ['uses' => 'SupplierController@index']);
    Route::get('{id}', ['uses' => 'SupplierController@show']);
    Route::post('', ['uses' => 'SupplierController@store']);
    //Route::put('{id}', ['uses' => 'SupplierController@update']);
    Route::post('{id}', ['uses' => 'SupplierController@update']);
    Route::patch('{id}', ['uses' => 'SupplierController@patch']);
    Route::delete('{id}', ['uses' => 'SupplierController@destroy']);
    Route::post('{id}/supplier_type', ['uses' => 'SupplierController@updateSupplierType']);
});
Route::group(['prefix' => 'sup_supplier_contacts'], function() {
    Route::get('', ['uses' => 'SupplierContactController@index']);
    Route::get('{id}', ['uses' => 'SupplierContactController@show']);
    Route::post('', ['uses' => 'SupplierContactController@store']);
    Route::put('{id}', ['uses' => 'SupplierContactController@update']);
    Route::post('{id}', ['uses' => 'SupplierContactController@update']);
    Route::delete('{id}', ['uses' => 'SupplierContactController@destroy']);
});
Route::group(['prefix' => 'sup_supplier_notes'], function() {
    Route::get('', ['uses' => 'SupplierNoteController@index']);
    Route::get('{id}', ['uses' => 'SupplierNoteController@show']);
    Route::post('', ['uses' => 'SupplierNoteController@store']);
    Route::put('{id}', ['uses' => 'SupplierNoteController@update']);
    Route::delete('{id}', ['uses' => 'SupplierNoteController@destroy']);
});
Route::get('sup_providers_stats', ['uses' => 'ProviderController@stats']);
Route::group(['prefix' => 'sup_providers'], function() {
    Route::get('', ['uses' => 'ProviderController@index']);
    Route::get('{id}', ['uses' => 'ProviderController@show']);
    Route::post('', ['uses' => 'ProviderController@store']);
    //Route::put('{id}', ['uses' => 'ProviderController@update']);
    Route::post('{id}', ['uses' => 'ProviderController@update']);
    Route::patch('{id}', ['uses' => 'ProviderController@patch']);
    Route::delete('{id}', ['uses' => 'ProviderController@destroy']);
    Route::post('{id}/supplier_type', ['uses' => 'ProviderController@updateSupplierType']);
});
