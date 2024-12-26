<?php namespace Modules\User\Events;

class UserHasRegisteredSMS
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}
