<?php namespace Modules\Core\Exceptions;

use Exception;

/**
 * Class NotFoundRevisionExceptions
 * @package Modules\Core\Exceptions
 */
class NotFoundRevisionExceptions extends Exception {
    protected $entity_id;

    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function setEntityID($entity_id) {
        $this->entity_id = $entity_id;

        return $this;
    }

    public function getEntityID() {
        return $this->entity_id;
    }
}