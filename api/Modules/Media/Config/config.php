<?php

return [
    'name' => 'Media',

    /*
    |--------------------------------------------------------------------------
    | //TODO this small issue can be fix but it take too much time
    | The path where the media files will be uploaded, include public folder also
    |--------------------------------------------------------------------------
    */
    'files-path'     => '/assets/',
    /*
    |--------------------------------------------------------------------------
    | Specify all the allowed file extensions a user can upload on the server
    |--------------------------------------------------------------------------
    */
    'allowed-types'  => '.jpg,.jpeg,.png,.gif,.svg,.mp4',
    /*
    |--------------------------------------------------------------------------
    | Determine the max file size upload rate
    | Defined in MB
    |--------------------------------------------------------------------------
    */
    'max-file-size'  => '20',

    /*
    |--------------------------------------------------------------------------
    | Determine the max total media folder size
    |--------------------------------------------------------------------------
    | Expressed in bytes
    */
    'max-total-size' => 8000000, //8 megabyte,
    'video-max-total-size' => 100000000, //100 megabyte,
    'file-max-total-size' => 100000000, //100 megabyte,

    /*
    |--------------------------------------------------------------------------
    | Thumbnails
    |--------------------------------------------------------------------------
    */
    'thumbnails' => [
        'subject'=> [
            'fit' => [
                'width'       => 60,
                'height'      => 60,
                'aspectRatio' => true,
                'upsize'      => true,
            ],
        ]
    ]
];
