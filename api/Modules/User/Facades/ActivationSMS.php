<?php
namespace Modules\User\Facades;
use Illuminate\Support\Facades\Facade;


class ActivationSMS extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'activation-sms'; // the IoC binding.
    }
}