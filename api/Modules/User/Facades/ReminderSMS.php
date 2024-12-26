<?php
namespace Modules\User\Facades;
use Illuminate\Support\Facades\Facade;

class ReminderSMS extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'reminder-sms'; // the IoC binding.
    }
}