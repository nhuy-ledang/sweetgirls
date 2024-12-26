<?php

return [

    /*
    |--------------------------------------------------------------------------
    | One Pay
    |--------------------------------------------------------------------------
    | Quốc tế: vpc_CardList=INTERNATIONAL
    | Nội địa: vpc_CardList=DOMESTIC
    | QR: vpc_CardList=QR
    */
    'onepay' => [
        'live' => [
            'url' => env('ONEPAY_URL_PAYGATE', 'https://onepay.vn/paygate/vpcpay.op'),
            'merchantid' => env('ONEPAY_MERCHANTID', 'TESTONEPAY'),
            'accesscode' => env('ONEPAY_ACCESSCODE', '6BEB2546'),
            'secure_secret' => env('ONEPAY_SECURE_SECRET', '6D0870CDE5F24F34F3915FB0045120DB'),
        ],
        'sandbox' => [
            'url' => env('ONEPAY_SANDBOX_URL_PAYGATE', 'https://mtf.onepay.vn/paygate/vpcpay.op'),
            'merchantid' => env('ONEPAY_SANDBOX_MERCHANTID', 'TESTONEPAY'),
            'accesscode' => env('ONEPAY_SANDBOX_ACCESSCODE', '6BEB2546'),
            'secure_secret' => env('ONEPAY_SANDBOX_SECURE_SECRET', '6D0870CDE5F24F34F3915FB0045120DB'),
        ],
        /*'credit' => [
            'url_paygate' => env('ONEPAY_CREDIT_URL_PAYGATE', 'https://mtf.onepay.vn/vpcpay/vpcpay.op'),
            'merchantid' => env('ONEPAY_CREDIT_MERCHANTID', 'TESTONEPAY'),
            'accesscode' => env('ONEPAY_CREDIT_ACCESSCODE', '6BEB2546'),
            'secure_secret' => env('ONEPAY_CREDIT_SECURE_SECRET', '6D0870CDE5F24F34F3915FB0045120DB'),
        ],
        'atm' => [
            'url_paygate' => env('ONEPAY_ATM_URL_PAYGATE', 'https://mtf.onepay.vn/onecomm-pay/vpc.op'),
            'merchantid' => env('ONEPAY_ATM_MERCHANTID', 'ONEPAY'),
            'accesscode' => env('ONEPAY_ATM_ACCESSCODE', 'D67342C2'),
            'secure_secret' => env('ONEPAY_ATM_SECURE_SECRET', 'A3EFDFABA8653DF2342E8DAC29B51AF0'),
        ],*/
    ],

    'momo' => [
        'live' => [
            'url' => env('MOMO_URL_PAYGATE', 'https://payment.momo.vn/v2/gateway/api/create'),
            'partner_code' => env('MOMO_PARTNERCODE', 'MOMOBKUN20180529'),
            'access_key' => env('MOMO_ACCESSKEY', 'klm05TvNBzhg7h7j'),
            'secret_key' => env('MOMO_SECRETKEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'),
        ],
        'sandbox' => [
            'url' => env('MOMO_URL_PAYGATE', 'https://test-payment.momo.vn/v2/gateway/api/create'),
            'partner_code' => env('MOMO_PARTNERCODE', 'MOMOBKUN20180529'),
            'access_key' => env('MOMO_ACCESSKEY', 'klm05TvNBzhg7h7j'),
            'secret_key' => env('MOMO_SECRETKEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'),
        ],
    ],

];
