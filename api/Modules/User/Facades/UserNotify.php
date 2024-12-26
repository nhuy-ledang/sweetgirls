<?php namespace Modules\User\Facades;

use Illuminate\Support\Facades\Facade;

class UserNotify extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'user-notify'; // the IoC binding.
    }
}
