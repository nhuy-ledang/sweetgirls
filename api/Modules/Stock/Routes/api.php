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

Route::get('sto_stocks_all', ['uses' => 'StockController@all']);
Route::group(['prefix' => 'sto_stocks'], function() {
    Route::get('', ['uses' => 'StockController@index']);
    Route::get('{id}', ['uses' => 'StockController@show']);
    Route::post('', ['uses' => 'StockController@store']);
    Route::post('{id}', ['uses' => 'StockController@update']);
    Route::put('{id}', ['uses' => 'StockController@update']);
    Route::delete('{id}', ['uses' => 'StockController@destroy']);
    Route::get('{id}/products', ['uses' => 'StockController@products']);
});
Route::group(['prefix' => 'sto_tickets'], function() {
    Route::get('', ['uses' => 'TicketController@index']);
    Route::get('{id}', ['uses' => 'TicketController@show']);
//    Route::post('', ['uses' => 'TicketController@store']);
//    Route::put('{id}', ['uses' => 'TicketController@update']);
//    Route::delete('{id}', ['uses' => 'TicketController@destroy']);
//    Route::post('{id}/status', ['uses' => 'TicketController@status']);
    Route::get('{id}/files', ['uses' => 'TicketFileController@files']);
    Route::post('{id}/files', ['uses' => 'TicketFileController@fileUploads']);
    Route::delete('{id}/files/{file_id}', ['uses' => 'TicketFileController@fileDestroy']);
});
Route::group(['prefix' => 'sto_requests'], function() {
    Route::get('', ['uses' => 'RequestController@index']);
    Route::get('{id}', ['uses' => 'RequestController@show']);
    Route::post('', ['uses' => 'RequestController@store']);
    Route::put('{id}', ['uses' => 'RequestController@update']);
    Route::delete('{id}', ['uses' => 'RequestController@destroy']);
//    Route::post('{id}/status', ['uses' => 'RequestController@status']);
});
Route::get('sto_in_requests_products', ['uses' => 'InRequestController@products']);
Route::group(['prefix' => 'sto_in_requests'], function() {
    Route::get('', ['uses' => 'InRequestController@index']);
    Route::get('{id}', ['uses' => 'InRequestController@show']);
    Route::post('', ['uses' => 'InRequestController@store']);
    Route::put('{id}', ['uses' => 'InRequestController@update']);
    Route::delete('{id}', ['uses' => 'InRequestController@destroy']);
    Route::post('{id}/status', ['uses' => 'InRequestController@status']);
    Route::get('{id}/files', ['uses' => 'TicketFileController@files']);
    Route::post('{id}/files', ['uses' => 'TicketFileController@fileUploads']);
    Route::delete('{id}/files/{file_id}', ['uses' => 'TicketFileController@fileDestroy']);
});
Route::get('sto_out_requests_products', ['uses' => 'OutRequestController@products']);
Route::group(['prefix' => 'sto_out_requests'], function() {
    Route::get('', ['uses' => 'OutRequestController@index']);
    Route::get('{id}', ['uses' => 'OutRequestController@show']);
    Route::post('', ['uses' => 'OutRequestController@store']);
    Route::put('{id}', ['uses' => 'OutRequestController@update']);
    Route::delete('{id}', ['uses' => 'OutRequestController@destroy']);
    Route::post('{id}/status', ['uses' => 'OutRequestController@status']);
    Route::get('{id}/files', ['uses' => 'TicketFileController@files']);
    Route::post('{id}/files', ['uses' => 'TicketFileController@fileUploads']);
    Route::delete('{id}/files/{file_id}', ['uses' => 'TicketFileController@fileDestroy']);
});
Route::group(['prefix' => 'sto_in_tickets'], function() {
    Route::get('', ['uses' => 'InTicketController@index']);
    Route::get('{id}', ['uses' => 'InTicketController@show']);
    Route::post('', ['uses' => 'InTicketController@store']);
    Route::put('{id}', ['uses' => 'InTicketController@update']);
    Route::delete('{id}', ['uses' => 'InTicketController@destroy']);
    Route::post('{id}/status', ['uses' => 'InTicketController@status']);
    Route::get('{id}/files', ['uses' => 'TicketFileController@files']);
    Route::post('{id}/files', ['uses' => 'TicketFileController@fileUploads']);
    Route::delete('{id}/files/{file_id}', ['uses' => 'TicketFileController@fileDestroy']);
});
Route::get('sto_out_tickets_products', ['uses' => 'OutTicketController@products']);
Route::group(['prefix' => 'sto_out_tickets'], function() {
    Route::get('', ['uses' => 'OutTicketController@index']);
    Route::get('{id}', ['uses' => 'OutTicketController@show']);
    Route::post('', ['uses' => 'OutTicketController@store']);
    Route::put('{id}', ['uses' => 'OutTicketController@update']);
    Route::delete('{id}', ['uses' => 'OutTicketController@destroy']);
    Route::post('{id}/status', ['uses' => 'OutTicketController@status']);
    Route::get('{id}/files', ['uses' => 'TicketFileController@files']);
    Route::post('{id}/files', ['uses' => 'TicketFileController@fileUploads']);
    Route::delete('{id}/files/{file_id}', ['uses' => 'TicketFileController@fileDestroy']);
});
Route::group(['prefix' => 'sto_req_tickets'], function() {
    Route::get('', ['uses' => 'ReqTicketController@index']);
    Route::get('{id}', ['uses' => 'ReqTicketController@show']);
    Route::put('{id}', ['uses' => 'ReqTicketController@update']);
    //Route::delete('{id}', ['uses' => 'ReqTicketController@destroy']);
    Route::post('{id}/status', ['uses' => 'ReqTicketController@status']);
    Route::get('{id}/files', ['uses' => 'TicketFileController@files']);
    Route::post('{id}/files', ['uses' => 'TicketFileController@fileUploads']);
    Route::delete('{id}/files/{file_id}', ['uses' => 'TicketFileController@fileDestroy']);
});
Route::group(['prefix' => 'sto_inventories'], function() {
    Route::get('', ['uses' => 'InventoryController@index']);
    Route::get('{id}', ['uses' => 'InventoryController@show']);
    Route::post('', ['uses' => 'InventoryController@store']);
    Route::put('{id}', ['uses' => 'InventoryController@update']);
    Route::delete('{id}', ['uses' => 'InventoryController@destroy']);
});
Route::post('sto_inventory_products_import_check', ['uses' => 'InventoryProductController@importCheck']);
Route::post('sto_inventory_products_import', ['uses' => 'InventoryProductController@import']);
Route::group(['prefix' => 'sto_inventory_products'], function() {
    Route::get('', ['uses' => 'InventoryProductController@index']);
    Route::get('{id}', ['uses' => 'InventoryProductController@show']);
    Route::post('', ['uses' => 'InventoryProductController@store']);
    Route::put('{id}', ['uses' => 'InventoryProductController@update']);
    Route::delete('{id}', ['uses' => 'InventoryProductController@destroy']);
});

Route::get('sto_types_all', ['uses' => 'TypeController@all']);
Route::group(['prefix' => 'sto_types'], function() {
    Route::get('', ['uses' => 'TypeController@index']);
    Route::get('{id}', ['uses' => 'TypeController@show']);
    Route::post('', ['uses' => 'TypeController@store']);
    Route::put('{id}', ['uses' => 'TypeController@update']);
    Route::delete('{id}', ['uses' => 'TypeController@destroy']);
});
