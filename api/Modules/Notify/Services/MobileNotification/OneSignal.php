<?php namespace Modules\Notify\Services\MobileNotification;

use Curl\Curl;
//use OneSignal\Resolver\NotificationResolver;

class OneSignal {
    public $app_id;
    public $api_key;
    public $android_channel_id;
    public $curl;

    /***
     * OneSignal constructor.
     */
    public function __construct() {
        $this->app_id = config('notify.mobile-notification.gateway.onesignal.application_id');
        $this->api_key = config('notify.mobile-notification.gateway.onesignal.application_auth_key');
        $this->android_channel_id = config('notify.mobile-notification.gateway.onesignal.android_channel_id');
    }

    protected function buildOneSignalDataMessage(Message $message) {
        $data = ['app_id' => $this->app_id];
        $lang = 'en'; //App::getLocale();
        if (!is_null($message->getTitle())) {
            $data['headings'] = [$lang => $message->getTitle()];
        }
        $data['contents'] = [$lang => $message->getContent()];
        $data['data'] = $message->getCustomData();
        $data['send_after'] = $message->getSendTime();
        $target_devices = $message->getTargetDevices();
        if (array_key_exists('ios', $target_devices)) $data['include_ios_tokens'] = $target_devices['ios'];
        if (array_key_exists('android', $target_devices)) $data['include_android_reg_ids'] = $target_devices['android'];
        if (array_key_exists('player', $target_devices)) $data['include_player_ids'] = $target_devices['player'];
        foreach ($data as $k => $v) if (is_null($v)) unset($data[$k]);
        if (isset($data['data']['badgeType'])) {
            $data['ios_badgeType'] = $data['data']['badgeType'];
            unset($data['data']['badgeType']);
        }
        if (isset($data['data']['badgeCount'])) {
            $data['ios_badgeCount'] = $data['data']['badgeCount'];
            unset($data['data']['badgeCount']);
        }
        if (isset($data['data']['chrome_web_icon'])) {
            $data['chrome_web_icon'] = $data['data']['chrome_web_icon'];
            unset($data['data']['chrome_web_icon']);
        }
        $data['ios_sound'] = 'notification.wav';
        $data['android_sound'] = 'notification';
        if ($this->android_channel_id) $data['android_channel_id'] = $this->android_channel_id;

        return $data;
    }

    public function send($content, $callback) {
        $message = new Message();

        if ($message->getFrom() == null) {
            $message->setFrom(['Notify Module']);
        }

        call_user_func($callback, $message);

        $message->setContent($content);

        if (!$message->getContent() || !$message->getTo()) {
            throw new \InvalidArgumentException('Can not send message without content or target devices');
        }

        $data = $this->buildOneSignalDataMessage($message);

        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->curl->setOpt(CURLOPT_HEADER, false);
        $this->curl->setOpt(CURLOPT_POST, true);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->setHeader('Content-Type', 'application/json; charset=utf-8');
        $this->curl->setHeader('Authorization', 'Basic ' . $this->api_key);
        $this->curl->post('https://onesignal.com/api/v1/notifications', json_encode($data));
        $response = $this->curl->error_code === 0 ? json_decode($this->curl->response) : $this->curl->error_message;
        $this->curl->close();
        /*$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $this->api_key
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);*/

        $message->setResponseData($response);
        event('mobile-notification.sending', $message);

        return $response;
    }
}
