<?php

return [

    /*
    |--------------------------------------------------------------------------
    | App Systems
    |--------------------------------------------------------------------------
    |
    */
    'drivers' => ['default'],

    'default' => [
        'google' => [
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        ],
        'facebook' => [
            'client_id' => env('FACEBOOK_CLIENT_ID'),
            'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        ],
        'apple' => [
            'client_secret' => env('APPLE_CLIENT_SECRET'),
        ],
        'account_kit' => [
            'client_secret' => env('APPLE_CLIENT_SECRET'),
        ],
    ],

    'mail' => [
        'suffix' => env('MAIL_SUFFIX', '@hocdau.vn'),
    ],
];
