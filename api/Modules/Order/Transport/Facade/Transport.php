<?php namespace Modules\Order\Transport\Facade;

use Illuminate\Support\Facades\Facade;

class Transport extends Facade {
    protected static function getFacadeAccessor() {
        return 'transport';
    }
}
