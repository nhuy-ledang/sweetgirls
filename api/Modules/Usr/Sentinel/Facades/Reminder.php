<?php namespace Modules\Usr\Sentinel\Facades;

use Illuminate\Support\Facades\Facade;

class Reminder extends Facade {
    protected static function getFacadeAccessor() {
        return 'usr.sentinel.reminders';
    }
}
