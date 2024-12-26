<?php

Route::group(['middleware' => 'web', 'prefix' => 'notify', 'namespace' => 'Modules\Notify\Http\Controllers'], function()
{
    Route::get('/', 'NotifyController@index');
});
