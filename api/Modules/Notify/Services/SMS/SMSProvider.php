<?php
namespace Modules\Notify\Services\SMS;

use Modules\Segment\Services\Holler\NotifySMSService;

class SMSProvider {

    protected $provider;

    public function __construct() {

    }

    function getProvider() {
        if (config('asgard.notify.config.sms.driver') == 'holler') {
            $this->provider = new NotifySMSService();
        } else {
            $this->provider = new Plivo();
        }
        return $this->provider;
    }

    public function __call($method, $parameters) {
        if (!method_exists($this, $method)) {

            return call_user_func_array([$this->getProvider(),
                $method], $parameters);
        }

        return call_user_func_array([$this,
            $method], $parameters);
    }


}