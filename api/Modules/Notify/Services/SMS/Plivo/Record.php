<?php
namespace Modules\Notify\Services\SMS\Plivo;


class Record extends Element {
    protected $nestables = array();

    protected $valid_attributes = array(
        'action',
        'method',
        'timeout',
        'finishOnKey',
        'maxLength',
        'playBeep',
        'recordSession',
        'startOnDialAnswer',
        'redirect',
        'fileFormat',
        'callbackUrl',
        'callbackMethod',
        'transcriptionType',
        'transcriptionUrl',
        'transcriptionMethod'
    );

    function __construct($attributes = array()) {
        parent::__construct(null, $attributes);
    }
}
