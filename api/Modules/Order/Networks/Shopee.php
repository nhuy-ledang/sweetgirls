<?php

namespace Modules\Order\Networks;

use EcomPHP\Shopee\Client;
use EcomPHP\Shopee\Resource;
use GuzzleHttp\RequestOptions;

class Shopee extends Resource {

    /**
     * @var string
     */
    protected $partner_key;

    /**
     * @var string
     */
    protected $partner_id;

    public function __construct($shop_id = '', $code = '') {
        $this->partner_key = config('shopee.partner_key', '7545544b54635674576e6f654856594b4e4c5349554679545841525477766a6a');
        $this->partner_id = config('shopee.partner_id', '2006937');
        $this->connect($shop_id, $code);
    }

    /**
     * Write Token To Log
     *
     * @param string $scope
     * @param string $token
     */
    protected function setLogToken($scope = 'shopee', $token) {
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
    protected function getLogToken($scope = 'shopee') {
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
     * @param string $code
     * @param string $shop_id
     * @param string $scope
     * @return Client
     */
    public function connect($shop_id = '', $code = '', $scope = 'shopee') {
        $client = new Client($this->partner_id, $this->partner_key);
        $tokenArr = json_decode($this->getLogToken($scope), true);
        $currentTime = time();

        if ($tokenArr) {
            $access_token = $tokenArr['access_token'];
            $expires_in = $tokenArr['expire_in'];
            $shop_id = $tokenArr['shop_id'];

            // Kiểm tra hạn sử dụng của access token
            if ($currentTime > $expires_in) {
                // Token đã hết hạn, gọi hàm refreshNewToken để lấy token mới
                $auth = $client->auth();
                $token = $auth->refreshNewToken($tokenArr['refresh_token'], $shop_id);
                $access_token = $token['access_token'];
                // Cập nhật thông tin token mới
                $tokenArr = array_merge($token, ['shop_id' => $shop_id]);
                $this->setLogToken($scope, json_encode($tokenArr, true));
            }
        }
        if (!$tokenArr) {
            $auth = $client->auth();
            if ($code && $shop_id) {
                try {
                    $token = $auth->getToken($code, $shop_id);
                    $access_token = $token['access_token'];
                    $refresh_token = $token['refresh_token'];
                    $expire_in = $token['expire_in'];
                } catch (EcomPHP\Shopee\Errors\TokenException $e) {
                    // Handle the exception (e.g., log the error, refresh the token, etc.)
                    throw new \InvalidArgumentException('Token Exception: ' . $e->getMessage());
                    // You may want to redirect the user to the authorization URL again or display an error message
                }
                $tokenArr = array_merge($token, ['shop_id' => $shop_id]);
                $this->setLogToken($scope, json_encode($tokenArr, true));
            } else {
                // Lấy URL xác thực
                $authUrl = $auth->createAuthRequest('/api/v1/backend/auth/shopee', false);
                throw new \InvalidArgumentException('Ủy quyền Shopee tại đây: ' . $authUrl);
            }
        }

        // Sử dụng API
        $client->setAccessToken($shop_id, $access_token);
        return $client;
    }

    public function searchItem($params = []) {
        $client = $this->connect();
        $this->useHttpClient($client->httpClient());

        $params = array_merge([
            'offset'           => '',
            'item_name'        => '',
            'attribute_status' => '',
            'item_sku'         => '',
            'page_size'        => 100,
        ], $params);

        return $this->call('GET', 'product/search_item', [
            RequestOptions::QUERY => $params,
        ]);
    }

    public function updateStock($params = []) {
        $client = $this->connect();
        $this->useHttpClient($client->httpClient());

        $stock_list = [
            'model_id'     => '',
            'seller_stock' => [['location_id' => '', 'stock' => 0]]
        ];

        $params = array_merge([
            'item_id'    => '',
            'stock_list' => $stock_list,
        ], $params);

        return $this->call('POST', 'product/update_stock', [
            RequestOptions::JSON => $params,
        ]);
    }
}
