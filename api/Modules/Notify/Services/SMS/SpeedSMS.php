<?php namespace Modules\Notify\Services\SMS;

/***
 * Class SpeedSMS
 * @package Modules\Notify\Services\SMS

 * @reference https://speedsms.vn/sms-api-service/
 */
class SpeedSMS {
    private $smsAPI;

    /**
     * sms_type có các giá trị như sau:
     * 2: tin nhắn gửi bằng đầu số ngẫu nhiên
     * 3: tin nhắn gửi bằng brandname
     * 4: tin nhắn gửi bằng brandname mặc định (Verify hoặc Notify)
     * 5: tin nhắn gửi bằng app android
     */
    private $sms_type = 2;

    /**
     * SpeedSMS constructor.
     */
    public function __construct() {
        $this->smsAPI = new \Modules\Notify\Services\SMS\SpeedSMS\SpeedSMSAPI(config('notify.sms.gateway.speedsms.api_token'));
    }

    /***
     * Send an SMS
     * @param $phone
     * @param string $content
     * @param string $sender
     * @return mixed|null
     */
    public function send($phone, $content = 'test sms', $sender = 'HocDauVn') {
        if (config('notify.sms.gateway.speedsms.debug')) {
            return $content;
        } else {
            return $this->smsAPI->sendSMS([$phone], $content, $this->sms_type, $sender);
        }
    }

    /***
     * Check a number
     * @param $number
     * @return mixed|string
     */
    public function check($number) {
        return $number;
    }

    public function getUserInfo() {
        return $this->smsAPI->getUserInfo();
    }
}
