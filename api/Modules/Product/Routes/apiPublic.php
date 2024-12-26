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

Route::group(['prefix' => 'products'], function() {
    Route::post('{id}/like', ['uses' => 'ProductLikeController@like']);
    Route::get('{id}/reviews', ['uses' => 'ProductReviewController@getReviews']);
    Route::post('{id}/reviews', ['uses' => 'ProductReviewController@createReview']);

    Route::put('{id}/share', ['uses' => 'ProductReviewController@createShareAction']);
});

Route::get('pd_review_all', ['uses' => 'ProductReviewController@all']);

Route::group(['prefix' => 'reviews'], function() {
    Route::post('{id}/likes', ['uses' => 'ProductReviewLikeController@createLike']);
    Route::get('{id}/likes', ['uses' => 'ProductReviewLikeController@getLikes']);

    Route::post('{id}/comments', ['uses' => 'ProductReviewCommentController@createComment']);
    Route::get('{id}/comments', ['uses' => 'ProductReviewCommentController@getComments']);
});

Route::post('pd_review_link', ['uses' => 'ProductReviewController@createLink']);

