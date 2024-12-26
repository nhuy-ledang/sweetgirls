<?php
/**
 * Created by PhpStorm.
 * User: nguyentantam
 * Date: 2/23/16
 * Time: 5:25 PM
 */

namespace Modules\User\Exceptions;


class UserException extends \Exception
{
    protected $user;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}