<?php
namespace Modules\Notify\Services\SMS\Plivo;


class DTMF extends Element {
    protected $nestables = array();

    protected $valid_attributes = array('async');

    function __construct($body, $attributes = array()) {
        parent::__construct($body, $attributes);
        if (!$body) {
            throw new PlivoError("No digits set for ".$this->getName());
        }
    }
}