<?php namespace Modules\Core\Exceptions;

/**
 * Class ApiException
 * @package Modules\Core\Exceptions
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 7/19/2018 11:00 PM
 */
class ApiException extends CoreException {


    public function render() {
        $data = [
            'error'     => true,
            'code'      => $this->getCode(),
            'message'   => $this->getMessage(),
            'exception' => [
                'line' => $this->getLine(),
                'file' => $this->getFile(),
            ],
        ];
        $data['errors'] = $this->errors();

        return $data;
    }

}