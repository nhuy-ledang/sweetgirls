<?php namespace Modules\Core\Helper;
/***
 * Class ErrorFormat
 *
 * @package Modules\Core\Helper

 */
class ErrorFormat {
    public $errorCode;
    public $errorMessage;
    public $errorKey;

    public function __construct($errorCode, $errorKey = '') {
        $this->errorCode = $errorCode[0];
        $this->errorMessage = $errorCode[1];
        $this->errorKey = $errorKey;
    }
}
