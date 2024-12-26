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

//Route::middleware('auth:api')->get('/page', function (Request $request) {
//    return $request->user();
//});

Route::get('pg_modules_all', ['uses' => 'ModuleController@all']);
Route::post('pg_modules_sort_order', ['uses' => 'ModuleController@sortOrder']);
Route::group(['prefix' => 'pg_modules'], function() {
    Route::get('', ['uses' => 'ModuleController@index']);
    Route::get('{id}', ['uses' => 'ModuleController@show']);
    Route::post('', ['uses' => 'ModuleController@store']);
    Route::put('{id}', ['uses' => 'ModuleController@update']);
    Route::post('{id}', ['uses' => 'ModuleController@update']);
    Route::patch('{id}', ['uses' => 'ModuleController@patch']);
    Route::delete('{id}', ['uses' => 'ModuleController@destroy']);
    Route::put('{id}/configs', ['uses' => 'ModuleController@configs']);
    Route::post('{id}/description', ['uses' => 'ModuleController@description']);
    Route::post('{id}/thumbnail', ['uses' => 'ModuleController@thumbnail']);
});
Route::get('pg_widgets_all', ['uses' => 'WidgetController@all']);
Route::group(['prefix' => 'pg_widgets'], function() {
    Route::get('', ['uses' => 'WidgetController@index']);
    Route::get('{id}', ['uses' => 'WidgetController@show']);
    Route::post('', ['uses' => 'WidgetController@store']);
    Route::put('{id}', ['uses' => 'WidgetController@update']);
    Route::delete('{id}', ['uses' => 'WidgetController@destroy']);
    Route::put('{id}/configs', ['uses' => 'WidgetController@configs']);
    Route::post('{id}/thumbnail', ['uses' => 'WidgetController@thumbnail']);
});
Route::get('pg_categories_all', ['uses' => 'CategoryController@all']);
Route::group(['prefix' => 'pg_categories'], function() {
    Route::get('', ['uses' => 'CategoryController@index']);
    Route::get('{id}', ['uses' => 'CategoryController@show']);
    Route::post('', ['uses' => 'CategoryController@store']);
    Route::put('{id}', ['uses' => 'CategoryController@update']);
    Route::post('{id}', ['uses' => 'CategoryController@update']);
    Route::patch('{id}', ['uses' => 'CategoryController@patch']);
    Route::delete('{id}', ['uses' => 'CategoryController@destroy']);
});
Route::get('pg_pages_all', ['uses' => 'PageController@all']);
Route::post('pg_pages_layouts', ['uses' => 'PageController@cloneLayouts']);
Route::group(['prefix' => 'pg_pages'], function() {
    Route::get('', ['uses' => 'PageController@index']);
    Route::get('{id}', ['uses' => 'PageController@show']);
    Route::post('', ['uses' => 'PageController@store']);
    Route::put('{id}', ['uses' => 'PageController@update']);
    Route::post('{id}', ['uses' => 'PageController@update']);
    Route::patch('{id}', ['uses' => 'PageController@patch']);
    Route::delete('{id}', ['uses' => 'PageController@destroy']);
    Route::post('{id}/copy', ['uses' => 'PageController@copy']);
    Route::post('{id}/description', ['uses' => 'PageController@description']);
    Route::post('{id}/layout', ['uses' => 'PageController@layout']);
});
Route::post('pg_page_contents_sort_order', ['uses' => 'PageContentController@sortOrder']);
Route::post('pg_page_contents_modules', ['uses' => 'PageContentController@cloneModules']);
Route::post('pg_page_contents_patterns', ['uses' => 'PageContentController@clonePatterns']);
Route::group(['prefix' => 'pg_page_contents'], function() {
    Route::get('', ['uses' => 'PageContentController@index']);
    Route::get('{id}', ['uses' => 'PageContentController@show']);
    Route::post('', ['uses' => 'PageContentController@store']);
    Route::put('{id}', ['uses' => 'PageContentController@update']);
    Route::post('{id}', ['uses' => 'PageContentController@update']);
    Route::patch('{id}', ['uses' => 'PageContentController@patch']);
    Route::delete('{id}', ['uses' => 'PageContentController@destroy']);
    Route::post('{id}/copy', ['uses' => 'PageContentController@copy']);
    Route::post('{id}/description', ['uses' => 'PageContentController@description']);
    Route::post('{id}/images', ['uses' => 'PageContentController@updateImages']);
    Route::post('{id}/pattern', ['uses' => 'PageContentController@pattern']);
});
Route::post('pg_page_modules_sort_order', ['uses' => 'PageModuleController@sortOrder']);
Route::post('pg_page_modules_modules', ['uses' => 'PageModuleController@cloneModules']);
Route::post('pg_page_modules_patterns', ['uses' => 'PageModuleController@clonePatterns']);
Route::group(['prefix' => 'pg_page_modules'], function() {
    Route::get('', ['uses' => 'PageModuleController@index']);
    Route::get('{id}', ['uses' => 'PageModuleController@show']);
    Route::post('', ['uses' => 'PageModuleController@store']);
    Route::put('{id}', ['uses' => 'PageModuleController@update']);
    Route::post('{id}', ['uses' => 'PageModuleController@update']);
    Route::patch('{id}', ['uses' => 'PageModuleController@patch']);
    Route::delete('{id}', ['uses' => 'PageModuleController@destroy']);
    Route::post('{id}/description', ['uses' => 'PageModuleController@description']);
    Route::post('{id}/images', ['uses' => 'PageModuleController@updateImages']);
    Route::post('{id}/pattern', ['uses' => 'PageModuleController@pattern']);
});
Route::get('pg_layouts_all', ['uses' => 'LayoutController@all']);
Route::group(['prefix' => 'pg_layouts'], function() {
    Route::get('', ['uses' => 'LayoutController@index']);
    Route::get('{id}', ['uses' => 'LayoutController@show']);
    Route::post('', ['uses' => 'LayoutController@store']);
    Route::put('{id}', ['uses' => 'LayoutController@update']);
    Route::post('{id}', ['uses' => 'LayoutController@update']);
    Route::delete('{id}', ['uses' => 'LayoutController@destroy']);
});
Route::post('pg_layout_modules_sort_order', ['uses' => 'LayoutModuleController@sortOrder']);
Route::post('pg_layout_modules_modules', ['uses' => 'LayoutModuleController@cloneModules']);
Route::post('pg_layout_modules_patterns', ['uses' => 'LayoutModuleController@clonePatterns']);
Route::group(['prefix' => 'pg_layout_modules'], function() {
    Route::get('', ['uses' => 'LayoutModuleController@index']);
    Route::get('{id}', ['uses' => 'LayoutModuleController@show']);
    Route::post('', ['uses' => 'LayoutModuleController@store']);
    Route::put('{id}', ['uses' => 'LayoutModuleController@update']);
    Route::post('{id}', ['uses' => 'LayoutModuleController@update']);
    Route::delete('{id}', ['uses' => 'LayoutModuleController@destroy']);
    //Route::post('{id}/description', ['uses' => 'LayoutModuleController@description']);
    Route::post('{id}/images', ['uses' => 'LayoutModuleController@updateImages']);
    Route::post('{id}/pattern', ['uses' => 'LayoutModuleController@pattern']);
});
Route::group(['prefix' => 'pg_layout_patterns'], function() {
    Route::get('', ['uses' => 'LayoutPatternController@index']);
    Route::get('{id}', ['uses' => 'LayoutPatternController@show']);
    Route::post('', ['uses' => 'LayoutPatternController@store']);
    Route::post('{id}', ['uses' => 'LayoutPatternController@update']);
    Route::delete('{id}', ['uses' => 'LayoutPatternController@destroy']);
    //Route::post('{id}/description', ['uses' => 'LayoutPatternController@description']);
    Route::post('{id}/images', ['uses' => 'LayoutPatternController@updateImages']);
});
Route::get('pg_settings_all', ['uses' => 'SettingController@all']);
Route::group(['prefix' => 'pg_settings'], function() {
    Route::get('{key}', ['uses' => 'SettingController@show']);
    Route::post('', ['uses' => 'SettingController@store']);
});
Route::get('pg_menus_all', ['uses' => 'MenuController@all']);
Route::get('pg_menus_nav', ['uses' => 'MenuController@nav']);
Route::group(['prefix' => 'pg_menus'], function() {
    Route::get('', ['uses' => 'MenuController@index']);
    Route::get('{id}', ['uses' => 'MenuController@show']);
    Route::post('', ['uses' => 'MenuController@store']);
    Route::post('{id}', ['uses' => 'MenuController@update']);
    Route::patch('{id}', ['uses' => 'MenuController@patch']);
    Route::delete('{id}', ['uses' => 'MenuController@destroy']);
    Route::post('{id}/description', ['uses' => 'MenuController@description']);
});
Route::group(['prefix' => 'pg_informations'], function() {
    Route::get('', ['uses' => 'InformationController@index']);
    Route::get('{id}', ['uses' => 'InformationController@show']);
    Route::post('', ['uses' => 'InformationController@store']);
    Route::put('{id}', ['uses' => 'InformationController@update']);
    Route::post('{id}', ['uses' => 'InformationController@update']);
    Route::delete('{id}', ['uses' => 'InformationController@destroy']);
    Route::post('{id}/description', ['uses' => 'InformationController@description']);
});
