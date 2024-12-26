<?php
namespace Modules\Notify\Services\SMS\Plivo;


class Wait extends Element {
    protected $nestables = array();

    protected $valid_attributes = array('length', 'silence', 'min_silence', 'minSilence', 'beep');

    function __construct($attributes = array()) {
        parent::__construct(null, $attributes);
    }
}
