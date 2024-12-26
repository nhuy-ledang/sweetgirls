<?php

return [
    'name' => 'Notify',
    'sms' => [
        'driver' => env("NOTIFY_SMS_GATEWAY", "holler"),//'plivo',
        'gateway' => [
            'plivo' => [
                'auth_id' => env("PLIVO_AUTH_ID", ""),//'SAM2E1ZJYXYTG4NDAWNZ',
                'auth_token' => env("PLIVO_AUTH_TOKEN", ""),//'ODM2Y2Q4OTJkYTk0YWJmYWEyNTJhOTZmNGM3ZTc2',
            ],
            'nexmo' => [
                'api_key' => env('NEXMO_API_KEY', ''),
                'api_secret' => env('NEXMO_API_SECRET', '')
            ],
            'speedsms' => [
                'debug' => env('SPEEDSMS_DEBUG', false),
                'api_token' => env('SPEEDSMS_API_TOKEN', 'Your access token'),
            ],
            'fptsms' => [
                'debug' => env('FPTSMS_DEBUG', false),
                'mode' => env('FPTSMS_MODE', 'sandbox'),
                'client_id' => env('FPTSMS_CLIENT_ID', ''),
                'secret' => env('FPTSMS_SECRET', ''),
                'log_enabled' => env('FPTSMS_LOG_ENABLED', false),
                'cache_enabled' => env('FPTSMS_CACHE_ENABLED', true),
            ]
        ],
        'pretend' => env("NOTIFY_SMS_PRETEND",false),//false,
    ],
    'mobile-notification' => [
        'driver'  => env("NOTIFY_MOBILE_NOTIFICATION_GATEWAY", "onesignal"),
        'gateway' => [
            'onesignal' => [
                'application_id' => env("ONESIGNAL_APPLICATION_ID", ""),//'af7d4205-354d-44be-b4b6-3db67977c568',
                'application_auth_key' => env("ONESIGNAL_APPLICATION_AUTH_KEY", ""),//'NjRiZDI4ZmMtYTIyMS00OWVhLWFiZmMtZDkzZjlmNzU0Mzdh',
                'auth_key' => env("ONESINGAL_AUTH_KEY", ""),//'OGE3N2E4NDctOWUwYy00YWZmLTllMzAtNjVmNDZhZWEwMjUw',
                'android_channel_id' => env("ONESINGAL_ANDROID_CHANNEL_ID", ''),
            ],
            'fcm' => [
                'server_key'       => env('FCM_SERVER_KEY', 'Your FCM server key'),
                'sender_id'        => env('FCM_SENDER_ID', 'Your sender id'),
                'server_send_url'  => 'https://fcm.googleapis.com/fcm/send',
                'server_group_url' => 'https://android.googleapis.com/gcm/notification',
                'log_enabled'      => env('FCM_LOG_ENABLED', false),
                'timeout'          => 30.0, // in second
            ]
        ],
        'pretend' => env("NOTIFY_MOBILE_NOTIFICATION_PRETEND",false),//false,
    ],
    'email' => [
        'driver'=>env("NOTIFY_EMAIL_GATEWAY",""),//'default',
    ],
    'message_status' => [
        -2 => 'pretend',
        -1 => 'canceled',
        0  => 'queue',
        1  => 'delivery'
    ]
];
