<?php namespace Modules\Core\Exceptions;

/**
 * Class ApiException
 * @package Modules\Core\Exceptions
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