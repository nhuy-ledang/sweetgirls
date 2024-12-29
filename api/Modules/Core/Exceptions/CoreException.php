<?php namespace Modules\Core\Exceptions;

use Exception;

/**
 * Class CoreException
 * @package Modules\Core\Exceptions
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