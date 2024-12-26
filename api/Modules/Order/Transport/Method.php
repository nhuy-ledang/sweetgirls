<?php namespace Modules\Order\Transport;

use Modules\Order\Transport\Http\ArrayLogger;
use Psr\Log\LoggerInterface;

abstract class Method {
    const MODE_LIVE = 'live';
    const MODE_SANDBOX = 'sandbox';

    /**
     * @var string
     */
    protected static $mode = self::MODE_LIVE;

    /**
     * @var string Base URL of all API requests
     */
    protected $url = '';

    /**
     * @var string
     */
    protected $username = '';

    /**
     * @var string
     */
    protected $password = '';

    /**
     * @var boolean Determines if API calls should be logged
     */
    protected $debug = false;

    /**
     * @var Http\ClientInterface
     */
    protected $httpClient;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $httpLogAdapter;

    /**
     * @var Token
     */
    protected $token;

    public function __construct() {
    }

    /**
     * Set mode
     *
     * @param string $mode
     */
    protected static function _setMode($mode) {
        if ($mode == self::MODE_LIVE || $mode == self::MODE_SANDBOX) {
            self::$mode = $mode;
        }
    }

    /**
     * Is live mode
     *
     * @return boolean
     */
    public static function isLive() {
        return self::$mode === self::MODE_LIVE;
    }

    /**
     * Is sandbox mode
     *
     * @return boolean
     */
    public static function isSandbox() {
        return self::$mode === self::MODE_SANDBOX;
    }

    /**
     * Get mode
     *
     * @return string
     */
    public static function getMode() {
        return self::$mode;
    }

    /**
     * Write Token To Log
     *
     * @param string $dataToken
     * @param string $method
     */
    protected function setLogToken($dataToken, $method) {
        $filepath = storage_path("app/$method.log");
        // Create file
        if (!file_exists($filepath)) {
            $handle = fopen($filepath, 'w+');
            fclose($handle);
            chmod($filepath, 0777);
        }
        // Empty file
        $handle = fopen($filepath, 'w+');
        fclose($handle);
        // Write data
        $handle = fopen($filepath, 'a+');
        fwrite($handle, serialize($dataToken));
        fclose($handle);
    }

    /**
     * Read Token From Log
     *
     * @param string $method
     * @return mixed|string
     */
    protected function getLogToken($method) {
        $filepath = storage_path("app/$method.log");
        // Create file
        if (!file_exists($filepath)) {
            $handle = fopen($filepath, 'w+');
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
     * @return LoggerInterface
     */
    protected function getHttpLogAdapter() {
        // If a log adapter hasn't been set, we default to the null adapter
        if (!$this->httpLogAdapter) $this->httpLogAdapter = new ArrayLogger();

        return $this->httpLogAdapter;
    }

    /**
     * @return Http\ClientInterface
     */
    protected function getHttpClient() {
        if (!$this->httpClient) return new Http\GuzzleHttpClient($this->debug, $this->getHttpLogAdapter());

        return $this->httpClient;
    }

    /**
     * Checks if the current token is null or expired
     *
     * @return boolean
     */
    protected function isTokenExpired() {
        $token = $this->getToken();

        if (!is_object($token)) return true;

        return $token->isExpired();
    }
}
