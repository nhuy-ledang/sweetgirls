<?php namespace Modules\Usr\Sentinel\Facades;

use Illuminate\Support\Facades\Facade;

class Activation extends Facade {
    protected static function getFacadeAccessor() {
        return 'usr.sentinel.activations';
    }
}
