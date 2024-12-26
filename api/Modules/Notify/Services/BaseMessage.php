<?php
namespace Modules\Notify\Services;

class BaseMessage {
    protected $attributes = [];

    public function __get($name) {
        if (!isset($this->attributes[$name])) {
            return null;
        }
        return $this->attributes[$name];
    }

    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }
}