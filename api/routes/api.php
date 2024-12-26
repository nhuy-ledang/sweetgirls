<?php

use Modules\Core\Common\Utilhelper\Utilhelper;

if ($path = Utilhelper::load_route('Notify')) {
    require $path;
}

