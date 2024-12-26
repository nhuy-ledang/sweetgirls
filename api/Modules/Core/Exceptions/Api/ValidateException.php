<?php namespace Modules\Core\Exceptions\Api;

use Illuminate\Support\MessageBag;
use Modules\Core\Exceptions\ApiException;
use Exception;

/**
 * Class ValidateException
 * @package Modules\Core\Exceptions\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 7/19/2018 10:59 PM
 */
class ValidateException extends ApiException {
    public function __construct($message, $code = 0, Exception $previous = null) {
        if ($message instanceof MessageBag) {
            $this->setErrors($message->getMessages());
        }
        parent::__construct("The given data failed to pass validation.", $code, $previous);
    }
}