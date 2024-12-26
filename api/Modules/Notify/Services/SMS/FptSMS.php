<?php namespace Modules\Notify\Services\SMS;

use Modules\Notify\Services\SMS\FptSMS\Api\SendBrandname;
use Modules\Notify\Services\SMS\FptSMS\Api\SendBrandnameOtp;
use Modules\Notify\Services\SMS\FptSMS\Api\SendMtActive;
use Modules\Notify\Services\SMS\FptSMS\Auth\ClientCredentials;
use Modules\Notify\Services\SMS\FptSMS\Client;
use Modules\Notify\Services\SMS\FptSMS\Constant;
use Modules\Notify\Services\SMS\FptSMS\Exception as TechException;
use Modules\Notify\Services\SMS\FptSMS\Auth\AccessToken;

/***
 * Class FptSMS
 * @package Modules\Notify\Services\SMS
 * @author Huy D <huydang1920@gmail.com>
 * @reference https://speedsms.vn/sms-api-service/
 */
class FptSMS {
    /**
     * SpeedSMS constructor.
     * Config client and authorization grant type
     */
    public function __construct() {
        Constant::configs([
            'mode'            => config('notify.sms.gateway.fptsms.mode') == Constant::MODE_LIVE ? Constant::MODE_LIVE : Constant::MODE_SANDBOX,
            'connect_timeout' => 15,
            'enable_cache'    => config('notify.sms.gateway.fptsms.cache_enabled', false),
            'enable_log'      => config('notify.sms.gateway.fptsms.log_enabled', true),
            'log_path'        => storage_path('logs'),
        ]);
    }

    // config client and authorization grant type
    private function getTechAuthorization(array $scopes = ['send_brandname', 'send_brandname_otp']) {
        $client = new Client(
            config('notify.sms.gateway.fptsms.client_id', ''),
            config('notify.sms.gateway.fptsms.secret', ''),
            $scopes // ['send_mt_active'] // array('send_brandname', 'send_brandname_otp')
        );

        return new ClientCredentials($client);
    }

    /***
     * Send an SMS
     * @param $phone
     * @param string $content
     * @param string $scope
     * @return mixed|null
     */
    public function send($phone, $content = 'test sms', $scope = 'otp') {
        try {
            if (config('notify.sms.gateway.fptsms.debug')) {
                return $content;
            }

            // Lấy đối tượng Authorization để thực thi API
            if ($scope == 'otp') {
                $scopes = ['send_brandname_otp'];
            } else {
                $scopes = ['send_brandname'];
            }
            $oGrantType = $this->getTechAuthorization($scopes);

            if ($scope == 'otp') {
                $apiSendBrandname = new SendBrandnameOtp([
                    'Phone'     => ltrim($phone, '+'),
                    'BrandName' => 'CUASOVANG',
                    'Message'   => $content
                ]);
            } else {
                $apiSendBrandname = new SendBrandname([
                    'Phone'     => ltrim($phone, '+'),
                    'BrandName' => 'CUASOVANG',
                    'Message'   => $content
                ]);
            }
            /*$apiSendMtActive = new SendMtActive([
                'ServiceNum' => 8700,
                'Phone'      => ltrim($phone, '+'),
                'Message'    => $content
            ]);*/

            // Thực thi API
            $arrResponse = $oGrantType->execute($apiSendBrandname);

            // kiểm tra kết quả trả về có lỗi hay không
            if (!empty($arrResponse['error'])) {
                // Xóa cache access token khi có lỗi xảy ra từ phía server
                AccessToken::getInstance()->clear();

                // quăng lỗi ra, và ghi log
                throw new TechException($arrResponse['error_description'], $arrResponse['error']);
            }

            return $arrResponse;
        } catch (\Exception $ex) {
            return sprintf('Mã lỗi: %s - Mô tả lỗi: %s', $ex->getCode(), $ex->getMessage());
        }
    }
}
