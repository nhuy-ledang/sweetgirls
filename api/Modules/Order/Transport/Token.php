<?php namespace Modules\Order\Transport;

class Token {

    /**
     * @var string
     */
    public $token;

    /**
     * @var int
     */
    public $endOfLife;

    /**
     * @param array $data
     */
    function __construct($data = []) {
        $this->setToken($data['token']);
        $this->setEndOfLife($data['expired']);
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token) {
        $this->token = $token;
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
