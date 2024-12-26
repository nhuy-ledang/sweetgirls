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

Route::group(['prefix' => 'auth'], function() {
    Route::post('login', ['uses' => 'AuthController@login']);
    Route::post('facebook', ['uses' => 'AuthController@facebook']);
    Route::post('facebook-token', ['uses' => 'AuthController@facebookByToken']);
    Route::post('google', ['uses' => 'AuthController@google']);
    Route::post('google-token', ['uses' => 'AuthController@googleByToken']);
    Route::post('apple', ['uses' => 'AuthController@apple']);
    /*Route::group(['prefix' => '/account-kit'], function () {
        Route::post('login', ['uses' => 'AuthController@accountKitLogin']);
        Route::post('check-phone', ['uses' => 'AuthController@accountKitCheckPhone']);
    });*/
    Route::post('register', ['uses' => 'AuthController@register']);
    Route::post('register-verify', ['uses' => 'AuthController@registerVerify']);
    Route::post('register-resend', ['uses' => 'AuthController@registerResend']);
    Route::post('register-email-resend', ['uses' => 'AuthController@registerEmailResend']);
    Route::post('register-email-verify', ['uses' => 'AuthController@registerEmailVerify']);
    Route::post('forgot', ['uses' => 'AuthController@forgot']);
    Route::post('forgot-checkotp', ['uses' => 'AuthController@forgotCheckOTP']);
    Route::post('forgot-newpw', ['uses' => 'AuthController@forgotNewPassword']);
    // Check auth
    Route::get('', ['uses' => 'AuthController@index']);
    Route::post('logout', ['uses' => 'AuthController@logout']);
    Route::post('pw-change', ['uses' => 'AuthController@passwordChange']);
    Route::post('profile-change', ['uses' => 'AuthController@profileChange']);
//    Route::post('phone-change', ['uses' => 'AuthController@phoneChange']);
//    Route::post('phone-verify', ['uses' => 'AuthController@phoneVerify']);
//    Route::post('email-change', ['uses' => 'AuthController@emailChange']);
//    Route::post('email-verify', ['uses' => 'AuthController@emailVerify']);
//    Route::post('email-check', ['uses' => 'AuthController@emailCheck']);
    Route::post('delete-account', ['uses' => 'AuthController@deleteAccount']);
    Route::post('delete-account-otp', ['uses' => 'AuthController@deleteAccountOTP']);
    Route::post('delete-account-confirm', ['uses' => 'AuthController@deleteAccountConfirm']);
    Route::post('create_share_code', ['uses' => 'AuthController@createShareCode']);
    Route::get('get_invite_history', ['uses' => 'AuthController@getInviteHistory']);
});

Route::get('addresses_all', ['uses' => 'AddressController@all']);
Route::group(['prefix' => 'addresses'], function() {
    Route::post('', ['uses' => 'AddressController@store']);
    Route::put('{id}', ['uses' => 'AddressController@update']);
    Route::delete('{id}', ['uses' => 'AddressController@destroy']);
});

Route::group(['prefix' => 'user_orders'], function() {
    Route::get('', ['uses' => 'OrderController@index']);
});

Route::group(['prefix' => 'user_reviews'], function() {
    Route::get('', ['uses' => 'ReviewController@index']);
});

Route::group(['prefix' => 'user_coins'], function() {
    Route::get('', ['uses' => 'CoinController@index']);
});

Route::get('user_vouchers', ['uses' => 'VoucherController@index']);

Route::post('notifies_mark_read', ['uses' => 'NotifyController@markRead']);
Route::post('notifies_destroys', ['uses' => 'NotifyController@destroys']);
Route::group(['prefix' => 'notifies'], function() {
    Route::get('', ['uses' => 'NotifyController@index']);
    Route::get('{id}', ['uses' => 'NotifyController@show']);
    Route::delete('{id}', ['uses' => 'NotifyController@destroy']);
});
Route::group(['prefix' => 'notifies_alert'], function() {
    Route::get('', ['uses' => 'NotifyController@getAlerts']);
    Route::post('', ['uses' => 'NotifyController@markAlerts']);
});
Route::get('notifies_unread_total', ['uses' => 'NotifyController@getUnreadTotal']);

/*Route::group(['prefix' => 'user_payment'], function() {
    // For user
    Route::get('transactions', ['uses' => 'PaymentController@index']);
    Route::post('payments', ['uses' => 'PaymentController@store']);
    Route::post('payments/create_by_admin', ['uses' => 'PaymentController@storeByAdmin']);

    // For admin
    Route::post('transactions/{id}/confirm', ['uses' => 'PaymentController@confirm']);
    Route::get('transactions/exports', ['uses' => 'PaymentController@export']);

    // Public
    Route::get('onepay_credit/callback', ['uses' => 'PaymentController@onepayCreditCallback']);
    Route::get('onepay_atm/callback', ['uses' => 'PaymentController@onepayATMCallback']);
});*/

/*Route::group(['prefix' => '/user_collection'], function () {
    Route::get('collections', ['uses' => 'CollectionController@index']);
    Route::post('collections', ['uses' => 'CollectionController@store']);
    Route::get('listPlacesSaved', ['uses' => 'CollectionController@listPlacesSaved']);

    Route::get('folders', ['uses' => 'CollectionFolderController@index']);
    Route::post('folders', ['uses' => 'CollectionFolderController@store']);
    Route::put('folders/{id}', ['uses' => 'CollectionFolderController@update']);
    Route::delete('folders/{id}', ['uses' => 'CollectionFolderController@destroy']);
});

Route::post('/logger', ['uses' => 'LoggerController@store']);

Route::group(['prefix' => '/user_history'], function () {
    Route::get('histories', ['uses' => 'HistoryController@index']);
    Route::post('histories', ['uses' => 'HistoryController@store']);
    Route::delete('histories/{id}', ['uses' => 'HistoryController@destroy']);
    Route::get('viewed', ['uses' => 'HistoryController@viewed']);
    Route::get('activities', ['uses' => 'HistoryController@activities']);
});

Route::group(['prefix' => '/user_follow'], function () {
    Route::get('follows', ['uses' => 'FollowController@index']);
    Route::post('follows', ['uses' => 'FollowController@store']);
    Route::get('follows/{id}', ['uses' => 'FollowController@getFollowsBy']);
    Route::delete('follows/{id}', ['uses' => 'FollowController@destroy']);
});

Route::group(['prefix' => '/user_friend'], function () {
    Route::get('friends', ['uses' => 'FriendController@index']);
    Route::post('friends', ['uses' => 'FriendController@store']);
    Route::get('friends/{id}', ['uses' => 'FriendController@getFriendsBy']);
    Route::delete('friends/{id}', ['uses' => 'FriendController@destroy']);
    Route::get('requests', ['uses' => 'FriendController@requests']);
});

Route::group(['prefix' => '/user_picture'], function () {
    Route::get('pictures', ['uses' => 'PictureController@index']);
    Route::get('places', ['uses' => 'PictureController@statPlaces']);
    Route::get('places/{id}', ['uses' => 'PictureController@statPlaceById']);
});

Route::group(['prefix' => '/user_calendar'], function () {
    Route::get('calendars', ['uses' => 'CalendarController@index']);
    Route::get('stats', ['uses' => 'CalendarController@stats']);
    Route::get('tuition', ['uses' => 'CalendarController@tuition']);
    Route::get('history', ['uses' => 'CalendarController@history']);
    Route::get('holiday', ['uses' => 'CalendarController@holiday']);
});

Route::group(['prefix' => 'qr-code'], function () {
    Route::post('', ['uses' => 'QRcodeController@store']);
});

Route::group(['prefix' => 'tests'], function () {
    Route::get('push', ['uses' => 'TestController@push']);
    Route::get('all', ['uses' => 'TestController@all']);
});*/

/*Route::group(['prefix' => 'user_contact'], function() {
    Route::get('contacts', ['uses' => 'ContactController@index']);
    Route::post('contacts', ['uses' => 'ContactController@store']);
    Route::get('contacts/{id}/qrcode', ['uses' => 'ContactController@qrcode']);
    Route::post('like', ['uses' => 'ContactLikeController@store']);
});*/
