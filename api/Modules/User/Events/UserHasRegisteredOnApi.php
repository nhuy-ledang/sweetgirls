<?php namespace Modules\User\Events;

class UserHasRegisteredOnApi
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}
