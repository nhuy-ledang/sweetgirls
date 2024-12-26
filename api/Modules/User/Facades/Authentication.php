<?php
namespace Modules\User\Facades;
use Illuminate\Support\Facades\Facade;


class Authentication extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'user-authentication'; // the IoC binding.
    }
}