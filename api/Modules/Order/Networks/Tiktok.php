<?php

namespace Modules\Order\Networks;

use EcomPHP\TiktokShop\Client;

class Tiktok {

    /**
     * @var string
     */
    protected $app_key;

    /**
     * @var string
     */
    protected $app_secret;

    public function __construct($authorization_code = '') {
        $this->app_key = config('tiktokshop.app_key', '6aq4nd9d2a71n');
        $this->app_secret = config('tiktokshop.app_secret', 'f013b7f59ffd9e75d657ac115b5e9db6f0313e3a');
        $this->connect($authorization_code);
    }

    /**
     * Write Token To Log
     *
     * @param string $scope
     * @param string $token
     */
    protected function setLogToken($scope = 'tiktokshop', $token) {
        $filepath = storage_path("app/{$scope}.log");
        // Create file
        if (!file_exists($filepath)) {
            $handle = fopen($filepath, "w+");
            fclose($handle);
            chmod($filepath, 0777);
        }
        // Empty file
        $handle = fopen($filepath, 'w+');
        fclose($handle);
        // Write data
        $handle = fopen($filepath, 'a+');
        fwrite($handle, serialize($token));
        fclose($handle);
    }

    /**
     * Read Token From Log
     *
     * @param string $scope
     * @return mixed|string
     */
    protected function getLogToken($scope = 'tiktokshop') {
        $filepath = storage_path("app/{$scope}.log");
        // Create file
        if (!file_exists($filepath)) {
            $handle = fopen($filepath, "w+");
            fclose($handle);
            chmod($filepath, 0777);
        }
        $handle = fopen($filepath, 'r');
        flock($handle, LOCK_SH);
        $size = filesize($filepath);
        $token = $size > 0 ? unserialize(trim(fread($handle, $size))) : '';
        flock($handle, LOCK_UN);
        fclose($handle);

        return $token;
    }

    /**
     * @return Token
     */
    protected function getToken() {
        return $this->token;
    }

    /**
     * @param Token $token
     */
    protected function setToken($token) {
        $this->token = $token;
    }

    /**
     * Get access_token
     *
     * @param string $authorization_code
     * @param string $scope
     * @return Client
     * @throws \EcomPHP\TiktokShop\Errors\AuthorizationException
     */
    public function connect($authorization_code = '', $scope = 'tiktokshop') {
        $client = new Client($this->app_key, $this->app_secret);
        $tokenArr = json_decode($this->getLogToken($scope), true);
        $currentTime = time();

        if ($tokenArr) {
            $access_token = $tokenArr['access_token'];
            $expires_in = $tokenArr['access_token_expire_in'];
            $shop_cipher = $tokenArr['shops'][0]['cipher'];

            // Kiểm tra hạn sử dụng của access token
            if ($currentTime > $expires_in) {
                // Token đã hết hạn, gọi hàm refreshNewToken để lấy token mới
                $auth = $client->auth();
                $newToken = $auth->refreshNewToken($tokenArr['refresh_token']);
                $access_token = $newToken['access_token'];
                // Cập nhật thông tin token mới
                $client->setAccessToken($access_token);
                $authorizedShopList = $client->Authorization->getAuthorizedShop();
                $tokenArr = array_merge($newToken, $authorizedShopList);
                $this->setLogToken($scope, json_encode($tokenArr, true));
            }
        }
        if (!$tokenArr) {
            $auth = $client->auth();
            if ($authorization_code) {
                try {
                    $token = $auth->getToken($authorization_code);
                    $access_token = $token['access_token'];
                    $refresh_token = $token['refresh_token'];
                } catch (EcomPHP\TiktokShop\Errors\TokenException $e) {
                    // Handle the exception (e.g., log the error, refresh the token, etc.)
                    throw new \InvalidArgumentException('Token Exception: ' . $e->getMessage());
                    // You may want to redirect the user to the authorization URL again or display an error message
                }
                $client->setAccessToken($access_token);

                try {
                    $authorizedShopList = $client->Authorization->getAuthorizedShop();
                    $shop_cipher = $authorizedShopList['shops'][0]['cipher'];
                } catch (EcomPHP\TiktokShop\Errors\TokenException $e) {
                    // Handle the exception (e.g., log the error, refresh the token, etc.)
                    throw new \InvalidArgumentException('Token Exception: ' . $e->getMessage());
                    // You may want to redirect the user to the authorization URL again or display an error message
                }

                $tokenArr = array_merge($token, $authorizedShopList);
                $this->setLogToken($scope, json_encode($tokenArr, true));
            } else {
                // Lấy URL xác thực
                $authUrl = $auth->createAuthRequest(null, true);
                throw new \InvalidArgumentException('Ủy quyền Tiktok tại đây: ' . $authUrl);
            }
        }
        // Sử dụng API
        $client->setAccessToken($access_token);
        $client->setShopCipher($shop_cipher);
        return $client;
    }
}
