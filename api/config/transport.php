<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Viettel Post
    |--------------------------------------------------------------------------
    */
    'viettelpost' => [
        'mode' => env('VIETTELPOST_MODE', 'sandbox'),
        'username' => env('VIETTELPOST_USERNAME', ''),
        'password' => env('VIETTELPOST_PASSWORD', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | J&T
    |--------------------------------------------------------------------------
    */
    'j2t' => [
        'mode' => env('J2T_MODE', 'sandbox'),
        'username' => env('J2T_USERNAME', ''),
        'password' => env('J2T_PASSWORD', ''),
    ],
];
