<?php namespace Modules\User\Events;

class UserHasRegisteredEmail
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}
