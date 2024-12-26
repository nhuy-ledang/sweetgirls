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
Route::get('ord_orders_cron', ['uses' => 'OrderController@destroyInProcessExpired']);
Route::post('ord_orders_create_shipping', ['uses' => 'OrderController@createShippingOrders']);
Route::post('ord_orders_create_requests', ['uses' => 'OrderController@createRequests']);
Route::post('ord_orders_change_payments_status', ['uses' => 'OrderController@changePaymentsStatus']);
Route::get('ord_orders_get_shipping_fee', ['uses' => 'OrderController@getShippingFee']);
Route::post('ord_orders_pos', ['uses' => 'OrderController@storePos']);
Route::group(['prefix' => 'ord_orders'], function() {
    Route::get('', ['uses' => 'OrderController@index']);
    Route::get('{id}', ['uses' => 'OrderController@show']);
    Route::post('', ['uses' => 'OrderController@store']);
    Route::delete('{id}', ['uses' => 'OrderController@destroy']);
    Route::get('{id}/products', ['uses' => 'OrderController@products']);
    Route::post('{id}/order_status', ['uses' => 'OrderController@changeOrderStatus']);
    Route::post('{id}/payment_status', ['uses' => 'OrderController@changePaymentStatus']);
    Route::post('{id}/shipping_status', ['uses' => 'OrderController@changeShippingStatus']);
    Route::post('{id}/create_shipping', ['uses' => 'OrderController@createShipping']);
    Route::post('{id}/invoiced', ['uses' => 'OrderController@invoiced']);
    //Route::post('{id}/supervisor', ['uses' => 'OrderController@supervisor']);
    Route::get('{id}/print', ['uses' => 'OrderController@getPrint']);
    Route::put('{id}', ['uses' => 'OrderController@update']);
    Route::put('{id}/address', ['uses' => 'OrderController@updateAddress']);
});
Route::group(['prefix' => 'ord_orders_products'], function() {
    Route::get('', ['uses' => 'OrderController@indexProductOrder']);
    Route::get('{id}/print', ['uses' => 'OrderController@getProductPrint']);
});
Route::get('ord_orders_exports', ['uses' => 'OrderController@exportExcel']);
Route::get('ord_orders_exports_details', ['uses' => 'OrderController@exportExcelDetail']);
Route::get('ord_orders_products_exports', ['uses' => 'OrderController@exportExcelProduct']);
Route::get('ord_orders_products_export_details', ['uses' => 'OrderController@exportExcelProductDetail']);
//Route::get('ord_qrcode/{filename}', ['uses' => 'OrderController@getQrcode']);
Route::get('ord_stats', ['uses' => 'OrderController@stats']);
Route::get('ord_stats_export_by_date', ['uses' => 'StatisticController@exportExcelOrderByDate']);
Route::get('ord_order_shipping_histories', ['uses' => 'OrderShippingHistoryController@index']);
Route::group(['prefix' => 'ord_order_histories'], function() {
    Route::get('', ['uses' => 'OrderHistoryController@index']);
    Route::get('{id}', ['uses' => 'OrderHistoryController@show']);
    Route::post('', ['uses' => 'OrderHistoryController@store']);
    Route::put('{id}', ['uses' => 'OrderHistoryController@update']);
    Route::delete('{id}', ['uses' => 'OrderHistoryController@destroy']);
});
Route::group(['prefix' => 'ord_order_tags'], function() {
    Route::get('', ['uses' => 'OrderTagsController@index']);
    Route::get('{id}', ['uses' => 'OrderTagsController@show']);
    Route::post('', ['uses' => 'OrderTagsController@store']);
    Route::put('{id}', ['uses' => 'OrderTagsController@update']);
    Route::delete('{id}', ['uses' => 'OrderTagsController@destroy']);
});
Route::group(['prefix' => 'ord_report'], function() {
    Route::get('orders', ['uses' => 'ReportController@orders']);
    Route::get('products', ['uses' => 'ReportProductController@index']);
    Route::get('customer_groups', ['uses' => 'ReportController@customerGroups']);
    Route::get('customers', ['uses' => 'ReportController@customers']);
    Route::get('discounts', ['uses' => 'ReportDiscountController@index']);
    Route::get('discount-details', ['uses' => 'ReportDiscountController@details']);

    Route::get('products_all', ['uses' => 'ReportProductController@all']);
    Route::get('staffs', ['uses' => 'ReportStaffController@staffs']);
    Route::get('revenues', ['uses' => 'ReportRevenueController@revenues']);
    Route::get('handling_staffs', ['uses' => 'ReportStaffController@handlingStaffs']);
    Route::get('processed_staffs', ['uses' => 'ReportStaffController@processedStaffs']);
});
Route::group(['prefix' => 'ord_stats'], function() {
    Route::get('revenue_percent', ['uses' => 'StatisticController@revenuePercent']);
    Route::get('payment_methods', ['uses' => 'StatisticController@paymentMethods']);
    Route::get('revenues', ['uses' => 'StatisticController@revenues']);
    Route::get('orders', ['uses' => 'StatisticController@orders']);
    Route::get('users', ['uses' => 'StatisticController@users']);
    Route::get('overview', ['uses' => 'StatisticController@overview']);
});
Route::group(['prefix' => 'ord_webhook'], function() {
    Route::get('', ['uses' => 'WebhookController@index']);
    Route::get('{id}', ['uses' => 'WebhookController@show']);
    Route::delete('{id}', ['uses' => 'WebhookController@destroy']);
});
Route::group(['prefix' => 'ord_networks'], function() {
    Route::get('overview', ['uses' => 'NetworkController@overview']);
});
Route::get('ord_settings_all', ['uses' => 'SettingController@all']);
Route::group(['prefix' => 'ord_settings'], function () {
    Route::get('{key}', ['uses' => 'SettingController@show']);
    Route::post('', ['uses' => 'SettingController@store']);
});
Route::group(['prefix' => 'ord_invoices'], function() {
    //Route::post('{id}/vat', ['uses' => 'InvoiceVatController@createVATPaviet']);
    Route::post('{id}/vat', ['uses' => 'InvoiceVatController@createVATMisa']);
});

Route::get('ord_test', ['uses' => 'TestController@index']);
