<?php
namespace Modules\Notify\Services\SMS\Plivo;

class GetDigits extends Element {
    protected $nestables = array('Speak', 'Play', 'Wait');

    protected $valid_attributes = array(
        'action',
        'method',
        'timeout',
        'digitTimeout',
        'numDigits',
        'retries',
        'invalidDigitsSound',
        'validDigits',
        'playBeep',
        'redirect',
        "finishOnKey",
        'digitTimeout',
        'log'
    );

    function __construct($attributes = array()) {
        parent::__construct(null, $attributes);
    }
}
