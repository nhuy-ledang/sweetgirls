<?php
namespace Modules\Notify\Services\SMS\Plivo;


class User extends Element {
    protected $nestables = array();

    protected $valid_attributes = array('sendDigits', 'sendOnPreanswer', 'sipHeaders');

    function __construct($body, $attributes = array()) {
        parent::__construct($body, $attributes);
        if (!$body) {
            throw new PlivoError("No user set for ".$this->getName());
        }
    }
}
