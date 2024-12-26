<?php namespace Modules\Notify\Services;

//use Modules\Notify\Services\Email\EmailProvider;
//use Modules\Notify\Services\MobileNotification\MobileNotificationProvider;
//use Modules\Notify\Services\SMS\SMSProvider;

class Notify {
    /**
     * @return \Modules\Notify\Services\SMS\SpeedSMS
     */
    public function sms() {
        //return new SMSProvider();
        //return new \Modules\Notify\Services\SMS\Nexmo();
        //return new \Modules\Notify\Services\SMS\SpeedSMS();
        return new \Modules\Notify\Services\SMS\FptSMS();
    }

    /**
     * @return \Modules\Notify\Services\Email\EmailDefault
     */
    public function email() {
        //return new EmailProvider();
        return new \Modules\Notify\Services\Email\EmailDefault();
    }

    /**
     * @return \Modules\Notify\Services\MobileNotification\OneSignal\Notifications
     */
    public function mobileNotification() {
        //return new MobileNotificationProvider();
        /*$api = new \Modules\Notify\Services\MobileNotification\OneSignalNorkunas();
        return $api->getNotifications();*/
        return new \Modules\Notify\Services\MobileNotification\OneSignal();
    }
}
