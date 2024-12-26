<?php
namespace Modules\Notify\Services\SMS\Plivo;


class PreAnswer extends Element {
    protected $nestables = array('Play', 'Speak', 'GetDigits', 'Wait', 'Redirect', 'Message', 'DTMF');

    protected $valid_attributes = array();

    function __construct($attributes = array()) {
        parent::__construct(null, $attributes);
    }
}
