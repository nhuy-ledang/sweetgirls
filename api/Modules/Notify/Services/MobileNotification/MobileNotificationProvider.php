<?php

namespace Modules\Notify\Services\MobileNotification;


use Modules\Notify\Services\NotifyProvider;
use Modules\Segment\Services\Holler\NotifyPushNotification;


class MobileNotificationProvider extends NotifyProvider {

    protected $provider;

    public function __construct() {

    }

    function getProvider() {
        if (config('asgard.notify.config.mobile-notification.driver') == 'holler') {
            $this->provider = new NotifyPushNotification();
        } else {
            $api = new OneSignal();

            $this->provider = $api->notifications();
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