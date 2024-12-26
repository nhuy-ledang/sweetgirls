<?php namespace Modules\User\Events;

class UserHasBegunResetProcessSMS
{
    public $user;
    public $code;

    public function __construct($user, $code)
    {
        $this->user = $user;
        $this->code = $code;
    }
}
