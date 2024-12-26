<?php namespace Modules\Usr\Sentinel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \Modules\Usr\Sentinel\Sentinel
 */
class Sentinel extends Facade {
    protected static function getFacadeAccessor() {
        return 'usr.sentinel';
    }
}
