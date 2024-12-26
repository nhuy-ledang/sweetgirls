<?php
namespace Modules\Notify\Services\SMS\Plivo;


class Number extends Element {
    protected $nestables = array();

    protected $valid_attributes = array('sendDigits', 'sendOnPreanswer', 'sendDigitsMode');

    function __construct($body, $attributes = array()) {
        parent::__construct($body, $attributes);
        if (!$body) {
            throw new PlivoError("No number set for ".$this->getName());
        }
    }
}
