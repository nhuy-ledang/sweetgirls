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

//Route::middleware('auth:api')->get('/order', function (Request $request) {
//    return $request->user();
//});

Route::group(['prefix' => 'carts'], function() {
    Route::get('', ['uses' => 'CartController@index']);
    Route::post('', ['uses' => 'CartController@store']);
    Route::put('{id}', ['uses' => 'CartController@update']);
    Route::delete('{id}', ['uses' => 'CartController@destroy']);
});
Route::post('carts_coins', ['uses' => 'CartController@addByCoin']);
Route::post('carts_includes', ['uses' => 'CartController@addIncludeProduct']);
Route::group(['prefix' => 'carts_products'], function() {
    Route::delete('{product_id}', ['uses' => 'CartController@removeProduct']);
});
Route::get('carts_totals', ['uses' => 'CartController@totals']);
Route::post('carts_coupon', ['uses' => 'CartController@addCoupon']);
Route::delete('carts_coupon', ['uses' => 'CartController@clearCoupon']);
Route::post('carts_voucher', ['uses' => 'CartController@addVoucher']);
Route::post('shipping_services', ['uses' => 'CartController@getShippingServices']);
Route::post('shipping_fee', ['uses' => 'CartController@getShippingFee']);

Route::group(['prefix' => 'orders'], function() {
    Route::post('', ['uses' => 'OrderController@store']);
    Route::post('guest', ['uses' => 'OrderController@storeGuest']);
    Route::get('onepay_callback', ['uses' => 'OrderController@onepayCallback']);
    Route::get('momo_callback', ['uses' => 'OrderController@momoCallback']);
    Route::put('{id}/cancel', ['uses' => 'OrderController@cancelOrder']);
});
Route::group(['prefix' => 'webhook'], function() {
    Route::get('onepay/ipn', ['uses' => 'WebhookController@onepayIPN']);
    Route::post('onepay/ipn', ['uses' => 'WebhookController@onepayIPN']);
    Route::get('viettelpost', ['uses' => 'WebhookController@viettelpost']);
    Route::post('viettelpost', ['uses' => 'WebhookController@viettelpost']);
    Route::get('onepay/ipn_fix/{id}', ['uses' => 'WebhookController@onepayIPNFix']);
    Route::get('update_viettelpost', ['uses' => 'WebhookController@updateViettelpost']);
});

// For public
Route::group(['prefix' => 'third_party'], function() {
    Route::get('orders', ['uses' => 'ThirdPartyOrderController@index']);
});

Route::group(['prefix' => 'auth'], function() {
    Route::get('tiktok', ['uses' => 'NetworkController@tiktok']);
    Route::get('shopee', ['uses' => 'NetworkController@shopee']);
    Route::get('lazada', ['uses' => 'NetworkController@lazada']);
});

Route::get('ord_order_shipping_histories', ['uses' => 'OrderShippingHistoryController@index']);
