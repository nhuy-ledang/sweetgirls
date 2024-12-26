<?php

return [
    // use cache - more disk reads but less CPU power, masks and format templates are stored there
    'cacheable'        => true,
    // used when cacheable === true
    'cache_dir'        => module_path('Core') . DIRECTORY_SEPARATOR . 'QRcode' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
    // default error logs dir
    'log_dir'          => storage_path('QRcode') . DIRECTORY_SEPARATOR,

    // if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
    'find_best_mask'   => true,
    // if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
    'fine_from_random' => false,
    // when find_best_mask === false
    'default_mask'     => 2,

    // maximum allowed png image width (in pixels), tune to make sure GD and PHP can handle such big images
    'png_maximum_size' => 1024,
];
