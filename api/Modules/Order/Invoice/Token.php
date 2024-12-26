<?php

namespace Modules\Order\Invoice;

class Token {

    /**
     * @var string
     */
    public $accessToken;

    /**
     * @var string
     */
    public $tokenType = 'Bearer';

    /**
     * @var int
     */
    public $createdAt;

    /**
     * @var int
     */
    public $endOfLife;

    /**
     * @param array $data
     */
    function __construct($data = []) {
        if (isset($data['access_token'])) {
            $this->setAccessToken($data['access_token']);
        }
        $this->createdAt = isset($data['created_at']) ? $data['created_at'] : time();
        if (isset($data['expires_in'])) {
            $this->setEndOfLife($this->createdAt + $data['expires_in']);
        }
    }

    /**
     * @return string
     */
    public function getAccessToken() {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;
    }

    /**
     * @return int
     */
    public function getEndOfLife() {
        return $this->endOfLife;
    }

    /**
     * @param int $endOfLife
     */
    public function setEndOfLife($endOfLife) {
        $this->endOfLife = $endOfLife;
    }

    /**
     * Checks if the token is expired
     *
     * @return boolean
     */
    public function isExpired() {
        return ($this->getEndOfLife() < time());
    }
}
