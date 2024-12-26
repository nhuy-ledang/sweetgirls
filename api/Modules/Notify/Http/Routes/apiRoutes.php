<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Modules\Notify\Http\Controllers\Api')->group(function () {
    Route::group(['prefix' => 'notify'], function () {
        Route::get('notifications', ['uses' => 'NotificationController@index']);
        Route::get('notifications/{id}', ['uses' => 'NotificationController@show']);
        Route::delete('notifications/{id}', ['uses' => 'NotificationController@destroy']);

        Route::post('contacts', ['uses' => 'ContactController@store']);
        Route::post('recaptcha', ['uses' => 'RecaptchaController@store']);
        Route::post('feedbacks', ['uses' => 'FeedbackController@store']);
        Route::post('mail', ['uses' => 'MailController@store']);
        Route::post('mail/test', ['uses' => 'MailController@test']);

        Route::get('messages', ['uses' => 'MessageController@index']);
        Route::post('messages', ['uses' => 'MessageController@store']);
        Route::get('messages_unread', ['uses' => 'MessageController@getUnread']);
    });
});
