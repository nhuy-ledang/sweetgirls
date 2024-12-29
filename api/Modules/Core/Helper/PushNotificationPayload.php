<?php namespace Modules\Core\Helper;
/***
 * Class PushNotificationPayload
 *
 * @package Modules\Core\Helpers

 */
class PushNotificationPayload {
    // message to show on app
    public $alert = 'MESSAGE';
    // optional
    public $badgeType = 'SetTo';
    public $badgeCount = 1;
    // push notification type, to distinct with others
    public $type = 'TYPE';
    // bid json object that included vendor infos
    public $data = 'DATA';
    // https://documentation.onesignal.com/docs/web-push-notification-icons
    public $chrome_web_icon = 'http://hocdau.vn/image/logo/logo-hocdau.png';

    public function __construct($message, $type, $data, $badgeCount = false) {
        $this->alert = $message;
        $this->type = $type;
        $this->data = $data;
        if ($badgeCount !== false) $this->badgeCount = $badgeCount;
        $this->chrome_web_icon = env('ONESIGNAL_APPLICATION_ICON', 'http://hocdau.vn/image/logo/logo-hocdau.png');
    }

    public function toJson() {
        return json_decode(json_encode($this), true);
    }
}
