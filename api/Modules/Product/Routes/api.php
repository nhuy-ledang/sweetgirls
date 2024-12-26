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
    Route::post('{id}/description', ['uses' => 'CategoryController@description']);
    Route::post('{id}/properties', ['uses' => 'CategoryController@properties']);
    Route::post('{id}/options', ['uses' => 'CategoryController@options']);
});
Route::post('pd_category_modules_sort_order', ['uses' => 'CategoryModuleController@sortOrder']);
Route::post('pd_category_modules_modules', ['uses' => 'CategoryModuleController@cloneModules']);
Route::post('pd_category_modules_patterns', ['uses' => 'CategoryModuleController@clonePatterns']);
Route::group(['prefix' => 'pd_category_modules'], function() {
    Route::get('', ['uses' => 'CategoryModuleController@index']);
    Route::get('{id}', ['uses' => 'CategoryModuleController@show']);
    Route::post('', ['uses' => 'CategoryModuleController@store']);
    Route::put('{id}', ['uses' => 'CategoryModuleController@update']);
    Route::post('{id}', ['uses' => 'CategoryModuleController@update']);
    Route::patch('{id}', ['uses' => 'CategoryModuleController@patch']);
    Route::delete('{id}', ['uses' => 'CategoryModuleController@destroy']);
    Route::post('{id}/copy', ['uses' => 'CategoryModuleController@copy']);
    Route::post('{id}/description', ['uses' => 'CategoryModuleController@description']);
    Route::post('{id}/images', ['uses' => 'CategoryModuleController@updateImages']);
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
    Route::post('{id}/copy', ['uses' => 'ProductController@copy']);
    Route::post('{id}/description', ['uses' => 'ProductController@description']);
    Route::post('{id}/renew', ['uses' => 'ProductController@renew']);
    Route::get('{id}/options', ['uses' => 'ProductController@options']);
    Route::get('{id}/variants', ['uses' => 'ProductController@getVariants']);
    Route::post('{id}/variants', ['uses' => 'ProductController@createVariant']);
    Route::put('{id}/variants', ['uses' => 'ProductController@updateVariant']);
    Route::delete('{id}/variants', ['uses' => 'ProductController@destroyVariant']);
    Route::put('{id}/quantity', ['uses' => 'ProductController@updateQuantity']);
    Route::group(['prefix' => '{id}/specials'], function() {
        Route::get('', ['uses' => 'ProductSpecialController@indexSpecial']);
        Route::post('', ['uses' => 'ProductSpecialController@storeSpecial']);
        Route::put('{image_id}', ['uses' => 'ProductSpecialController@updateSpecial']);
        Route::post('{image_id}', ['uses' => 'ProductSpecialController@updateSpecial']);
        Route::delete('{image_id}', ['uses' => 'ProductSpecialController@destroySpecial']);
    });
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
Route::group(['prefix' => 'pd_included_products'], function() {
    Route::get('', ['uses' => 'ProductIncludedController@index']);
    Route::get('{id}', ['uses' => 'ProductIncludedController@show']);
    Route::post('', ['uses' => 'ProductIncludedController@store']);
    Route::put('{id}', ['uses' => 'ProductIncludedController@update']);
    Route::post('{id}', ['uses' => 'ProductIncludedController@update']);
    Route::patch('{id}', ['uses' => 'ProductIncludedController@patch']);
    Route::delete('{id}', ['uses' => 'ProductIncludedController@destroy']);
    Route::post('{id}/description', ['uses' => 'ProductIncludedController@description']);
});
Route::group(['prefix' => 'gift_products'], function() {
    Route::get('', ['uses' => 'ProductGiftController@index']);
    Route::get('{id}', ['uses' => 'ProductGiftController@show']);
    Route::post('', ['uses' => 'ProductGiftController@store']);
    Route::put('{id}', ['uses' => 'ProductGiftController@update']);
    Route::post('{id}', ['uses' => 'ProductGiftController@update']);
    Route::patch('{id}', ['uses' => 'ProductGiftController@patch']);
    Route::delete('{id}', ['uses' => 'ProductGiftController@destroy']);
    Route::post('{id}/description', ['uses' => 'ProductGiftController@description']);
});
Route::group(['prefix' => 'pd_product_latest'], function() {
    Route::get('', ['uses' => 'ProductLatestController@index']);
    Route::post('', ['uses' => 'ProductLatestController@store']);
    Route::delete('{id}', ['uses' => 'ProductLatestController@destroy']);
});
Route::group(['prefix' => 'pd_product_bestsellers'], function() {
    Route::get('', ['uses' => 'ProductBestsellerController@index']);
    Route::post('', ['uses' => 'ProductBestsellerController@store']);
    Route::delete('{id}', ['uses' => 'ProductBestsellerController@destroy']);
});
Route::group(['prefix' => 'pd_product_quantities'], function() {
    Route::get('', ['uses' => 'ProductQuantityController@index']);
    Route::get('{id}', ['uses' => 'ProductQuantityController@show']);
    Route::post('', ['uses' => 'ProductQuantityController@store']);
    Route::put('{id}', ['uses' => 'ProductQuantityController@update']);
    Route::delete('{id}', ['uses' => 'ProductQuantityController@destroy']);
});
Route::post('pd_product_modules_sort_order', ['uses' => 'ProductModuleController@sortOrder']);
Route::post('pd_product_modules_modules', ['uses' => 'ProductModuleController@cloneModules']);
Route::post('pd_product_modules_patterns', ['uses' => 'ProductModuleController@clonePatterns']);
Route::group(['prefix' => 'pd_product_modules'], function() {
    Route::get('', ['uses' => 'ProductModuleController@index']);
    Route::get('{id}', ['uses' => 'ProductModuleController@show']);
    Route::post('', ['uses' => 'ProductModuleController@store']);
    Route::put('{id}', ['uses' => 'ProductModuleController@update']);
    Route::post('{id}', ['uses' => 'ProductModuleController@update']);
    Route::patch('{id}', ['uses' => 'ProductModuleController@patch']);
    Route::delete('{id}', ['uses' => 'ProductModuleController@destroy']);
    Route::post('{id}/copy', ['uses' => 'ProductModuleController@copy']);
    Route::post('{id}/description', ['uses' => 'ProductModuleController@description']);
    Route::post('{id}/images', ['uses' => 'ProductModuleController@updateImages']);
});
Route::group(['prefix' => 'pd_product_related'], function() {
    Route::get('', ['uses' => 'ProductRelatedController@index']);
    Route::post('', ['uses' => 'ProductRelatedController@store']);
    Route::put('{id}', ['uses' => 'ProductRelatedController@update']);
    Route::post('{id}', ['uses' => 'ProductRelatedController@update']);
    Route::delete('{id}', ['uses' => 'ProductRelatedController@destroy']);
});
Route::group(['prefix' => 'pd_product_incombo'], function() {
    Route::get('', ['uses' => 'ProductIncomboController@index']);
    Route::post('', ['uses' => 'ProductIncomboController@store']);
    Route::put('{id}', ['uses' => 'ProductIncomboController@update']);
    Route::post('{id}', ['uses' => 'ProductIncomboController@update']);
    Route::delete('{id}', ['uses' => 'ProductIncomboController@destroy']);
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
Route::group(['prefix' => 'pd_product_specs'], function() {
    Route::get('', ['uses' => 'ProductSpecController@index']);
    Route::get('{id}', ['uses' => 'ProductSpecController@show']);
    Route::post('', ['uses' => 'ProductSpecController@store']);
    Route::put('{id}', ['uses' => 'ProductSpecController@update']);
    Route::delete('{id}', ['uses' => 'ProductSpecController@destroy']);
    Route::post('{id}/description', ['uses' => 'ProductSpecController@description']);
});
Route::get('pd_settings_all', ['uses' => 'SettingController@all']);
Route::group(['prefix' => 'pd_settings'], function() {
    Route::get('{key}', ['uses' => 'SettingController@show']);
    Route::post('', ['uses' => 'SettingController@store']);
});
Route::get('pd_stat_products_rank_exports', ['uses' => 'StatisticController@exportExcelProductRank']);
Route::group(['prefix' => 'pd_stat'], function() {
    Route::get('revenue_percent', ['uses' => 'StatisticController@revenuePercent']);
    Route::get('payment_methods', ['uses' => 'StatisticController@paymentMethods']);
    Route::get('revenues', ['uses' => 'StatisticController@revenues']);
    Route::get('orders', ['uses' => 'StatisticController@orders']);
    Route::get('overview', ['uses' => 'StatisticController@overview']);
    Route::get('users', ['uses' => 'StatisticController@users']);
    Route::get('bestseller', ['uses' => 'StatisticController@bestseller']);
    Route::get('top_revenue', ['uses' => 'StatisticController@topRevenue']);
    Route::get('products_rank', ['uses' => 'StatisticController@indexProductRank']);
});
Route::group(['prefix' => 'pd_product_reviews'], function() {
    Route::get('', ['uses' => 'ProductReviewController@index']);
});

Route::get('gift_sets_all', ['uses' => 'GiftSetController@all']);
Route::group(['prefix' => 'gift_sets'], function() {
    Route::get('', ['uses' => 'GiftSetController@index']);
    Route::get('{id}', ['uses' => 'GiftSetController@show']);
    Route::post('', ['uses' => 'GiftSetController@store']);
    Route::put('{id}', ['uses' => 'GiftSetController@update']);
    Route::patch('{id}', ['uses' => 'GiftSetController@patch']);
    Route::delete('{id}', ['uses' => 'GiftSetController@destroy']);
});

Route::get('pd_reviews_all', ['uses' => 'ProductReviewController@all']);
Route::group(['prefix' => 'pd_reviews'], function() {
    Route::get('', ['uses' => 'ProductReviewController@index']);
    Route::get('{id}', ['uses' => 'ProductReviewController@show']);
    Route::post('', ['uses' => 'ProductReviewController@store']);
    Route::put('{id}', ['uses' => 'ProductReviewController@update']);
    Route::post('{id}', ['uses' => 'ProductReviewController@update']);
    Route::delete('{id}', ['uses' => 'ProductReviewController@destroy']);
    Route::patch('{id}', ['uses' => 'ProductReviewController@patch']);

    Route::group(['prefix' => '{id}/images'], function() {
        Route::get('', ['uses' => 'ProductReviewImageController@indexImage']);
        Route::post('', ['uses' => 'ProductReviewImageController@storeImage']);
        Route::put('{image_id}', ['uses' => 'ProductReviewImageController@updateImage']);
        Route::post('{image_id}', ['uses' => 'ProductReviewImageController@updateImage']);
        Route::delete('{image_id}', ['uses' => 'ProductReviewImageController@destroyImage']);
    });
});

Route::get('gift_orders_all', ['uses' => 'GiftOrderController@all']);
Route::group(['prefix' => 'gift_orders'], function() {
    Route::get('', ['uses' => 'GiftOrderController@index']);
    Route::get('{id}', ['uses' => 'GiftOrderController@show']);
    Route::post('', ['uses' => 'GiftOrderController@store']);
    Route::put('{id}', ['uses' => 'GiftOrderController@update']);
    Route::patch('{id}', ['uses' => 'GiftOrderController@patch']);
    Route::delete('{id}', ['uses' => 'GiftOrderController@destroy']);
});

Route::get('pd_flashsales_values', ['uses' => 'FlashsaleController@indexValues']);
Route::group(['prefix' => 'pd_flashsales'], function() {
    Route::get('', ['uses' => 'FlashsaleController@index']);
    Route::get('{id}', ['uses' => 'FlashsaleController@show']);
    Route::post('', ['uses' => 'FlashsaleController@store']);
    Route::put('{id}', ['uses' => 'FlashsaleController@update']);
    Route::delete('{id}', ['uses' => 'FlashsaleController@destroy']);
});
