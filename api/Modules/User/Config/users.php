<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Define which user driver to use.
    |--------------------------------------------------------------------------
    | Current default and only option : Sentinel
    | Sentinel option is outdated
    */
    'driver' => 'Sentinel',
    /*
    |--------------------------------------------------------------------------
    | Define which route to redirect to after a successful login
    |--------------------------------------------------------------------------
    */
    'redirect_route_after_login' => 'dashboard.index',
    /*
    |--------------------------------------------------------------------------
    | Login column(s)
    |--------------------------------------------------------------------------
    | Define which column(s) you'd like to use to login with, currently
    | only supported by the Sentinel user driver
    */
    'login-columns' => ['email', 'phone_number'],

    // declared
//    'is_active_when_register'=>true,

    /*
    |--------------------------------------------------------------------------
    | Fillable user fields
    |--------------------------------------------------------------------------
    | Set the fillable user fields, those fields will be mass assigned
    */
    'fillable' => [
        'email',
        'username',
        'password',
        'first_name',
        'last_name',
        'timezone',
        'phone_number'
    ],

    /*
    |--------------------------------------------------------------------------
    | Api
    |--------------------------------------------------------------------------
    */
    'api_key_name' => 'access_token',

];
