<?php
/**
 * Created by PhpStorm.
 * User: kei
 * Date: 12/22/2016
 * Time: 11:38 AM
 */

namespace Modules\User\Events;


class UserHasBeenLogoutOnApi
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}