<?php namespace Modules\Core\Exceptions;

use Exception;

/**
 * Class CoreException
 * @package Modules\Core\Exceptions
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 7/19/2018 11:00 PM
 */
class CoreException extends Exception
{
    protected $errors;
    public function errors()
    {
        return $this->errors;
    }

    /**
     * @param mixed $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }
}