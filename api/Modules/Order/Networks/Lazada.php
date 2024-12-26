<?php

namespace Modules\Order\Networks;

use Lazada\LazopClient;
use Lazada\LazopRequest;

class Lazada {

    /**
     * @var string
     */
    protected $lazadaUrl;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $apiSecret;

    protected $token;

    protected $callback_url;

    protected $client;

    public function __construct($code = '') {
        $this->lazadaUrl = config('lazada.lazada_url', 'https://api.lazada.vn/rest');
        $this->apiKey = config('lazada.api_key', '127681');
        $this->apiSecret = config('lazada.api_secret', 's7FU4ujsceffMnciY9r6OYJUCKIQcpWn');
        $this->callback_url = config('app.url') . "/api/v1/auth/lazada";

        $this->client = new LazopClient($this->lazadaUrl, $this->apiKey, $this->apiSecret);
        $this->connect($code);
    }

    /**
     * Write Token To Log
     *
     * @param string $scope
     * @param string $token
     */
    protected function setLogToken($scope = 'lazada', $token) {
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
    protected function getLogToken($scope = 'lazada') {
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
     * @param string $code
     * @param string $scope
     * @return LazopClient
     * @throws \Exception
     */
    public function connect($code = '', $scope = 'lazada') {
        $tokenArr = json_decode($this->getLogToken($scope), true);
        $currentTime = time();

        if ($tokenArr) {
            $access_token = $tokenArr['access_token'];
            $expires_in = $tokenArr['expires_in'];
            $created_at = $tokenArr['created_at'];
            // Kiểm tra hạn sử dụng của access token
            if ($currentTime > ($expires_in + $created_at)) {
                // Token đã hết hạn, gọi hàm refreshNewToken để lấy token mới
                $request = new LazopRequest('/auth/token/refresh');
                $request->addApiParam('refresh_token',$tokenArr['refresh_token']);
                $token = $this->client->execute($request);
                $token = json_decode($token, true);
                $access_token = $token['access_token'];
                // Cập nhật thông tin token mới
                $tokenArr = array_merge($token, ['created_at' => time()]);
                $this->setLogToken($scope, json_encode($tokenArr, true));
            }
        }
        if (!$tokenArr) {
            if ($code) {
                try {
                    $request = new LazopRequest('/auth/token/createWithOpenId');
                    $request->addApiParam('code',$code);
                    $token = $this->client->execute($request);
                    $token = json_decode($token, true);
                    $access_token = $token['access_token'];
                    $refresh_token = $token['refresh_token'];
                    $expire_in = $token['expires_in'];
                } catch (EcomPHP\Lazada\Errors\TokenException $e) {
                    // Handle the exception (e.g., log the error, refresh the token, etc.)
                    throw new \InvalidArgumentException('Token Exception: ' . $e->getMessage());
                    // You may want to redirect the user to the authorization URL again or display an error message
                }
                $tokenArr = array_merge($token, ['created_at' => time()]);
                $this->setLogToken($scope, json_encode($tokenArr, true));
            } else {
                // Lấy URL xác thực
                throw new \InvalidArgumentException('Ủy quyền Lazada tại đây: ' . "https://auth.lazada.com/oauth/authorize?response_type=code&force_auth=true&redirect_uri={$this->callback_url}&client_id={$this->apiKey}");
            }
        }
        $this->setToken($access_token);
        return $this->client;
    }

    public function getOrders($params = []) {
        $request = new LazopRequest('/orders/get', 'GET');
        foreach ($params as $key => $value) {
            $request->addApiParam($key, $value);
        }

        // Process API
        return $this->client->execute($request, $this->token);
    }

    public function getProducts($params = []) {
        $request = new LazopRequest('/products/get', 'GET');
        $request->addApiParam('offset', 0);
        $request->addApiParam('limit', 50);
        foreach ($params as $key => $value) {
            $request->addApiParam($key, $value);
        }

        // Process API
        return $this->client->execute($request, $this->token);
    }

    public function updateProductPriceQuantity($payload = '') {
        $request = new LazopRequest('/product/price_quantity/update', 'POST');
        $request->addApiParam('payload', $payload);

        // Process API
        return $this->client->execute($request, $this->token);
    }
}
