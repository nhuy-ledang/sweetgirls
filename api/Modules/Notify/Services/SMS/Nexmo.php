<?php namespace Modules\Notify\Services\SMS;

/***
 * Class Nexmo
 * @package Modules\Notify\Services\SMS

 * @reference http://docs.guzzlephp.org/en/stable/quickstart.html#post-form-requests
 *            https://dashboard.nexmo.com/getting-started-guide
 * @errorCodes https://help.nexmo.com/hc/en-us/articles/204014733-Nexmo-SMS-Delivery-Error-Codes
 */
class Nexmo {
    private $api_key;
    private $api_secret;
    private $from = 'NEXMO';
    private $client;

    public function __construct() {
        $this->api_key = config('notify.sms.gateway.nexmo.api_key');
        $this->api_secret = config('notify.sms.gateway.nexmo.api_secret');

        $this->client = new \GuzzleHttp\Client();
    }

    /***
     * Send an SMS
     * curl -X POST  https://rest.nexmo.com/sms/json \
     * -d api_key=7c37f9a7 \
     * -d api_secret=46b1b1667902b87c \
     * -d to=84965047077 \
     * -d from="NEXMO" \
     * -d text="Hello from Nexmo"
     * @param $to
     * @param string $text
     * @param string $from
     * @return mixed|string
     */
    public function send($to, $text = '', $from = 'HocDauVn') {
        $body = array(
            'api_key'    => $this->api_key,
            'api_secret' => $this->api_secret,
            'to'         => $to,
            'from'       => $from ? $from : $this->from,
            'text'       => $text
        );

        $url = 'https://rest.nexmo.com/sms/json?' . http_build_query($body);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $data;
    }

    /***
     * Check a number
     * curl -X POST  https://api.nexmo.com/ni/advanced/json \
     * -d api_key=7c37f9a7 \
     * -d api_secret=46b1b1667902b87c \
     * -d number=84965047077
     * @param $number
     * @return mixed|string
     */
    public function check($number) {
        $url = 'https://api.nexmo.com/ni/advanced/json?' . http_build_query(array(
                'api_key'    => $this->api_key,
                'api_secret' => $this->api_secret,
                'number'     => $number
            ));
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $data;
    }
}
