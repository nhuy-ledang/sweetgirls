<?php
namespace Modules\Notify\Services\SMS\Plivo;


class Play extends Element {
    protected $nestables = array();

    protected $valid_attributes = array('loop');

    function __construct($body, $attributes = array()) {
        parent::__construct($body, $attributes);
        if (!$body) {
            throw new PlivoError("No url set for ".$this->getName());
        }
    }
}
