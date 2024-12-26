<?php
namespace Modules\Notify\Services\SMS\Plivo;


class Message extends Element {
    protected $nestables = array();

    protected $valid_attributes = array('src', 'dst', 'type', 'callbackMethod', 'callbackUrl');

    function __construct($body, $attributes = array()) {
        parent::__construct($body, $attributes);
        if (!$body) {
            throw new PlivoError("No text set for ".$this->getName());
        }
    }
}
