<?php

namespace Modules\Order\Invoice\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerInterface;

class GuzzleHttpClient extends Client implements ClientInterface {
    public $debug;
    public $httpLogAdapter;

    public function __construct($debug, LoggerInterface $httpLogAdapter) {
        $this->debug = $debug;
        $this->httpLogAdapter = $httpLogAdapter;

        $config = ['timeout' => 60];
        if ($this->debug) {
            $config['handler'] = HandlerStack::create();
            $config['handler']->push(
                Middleware::log($this->httpLogAdapter, new MessageFormatter(MessageFormatter::DEBUG))
            );
        }

        parent::__construct($config);
    }

    /***
     * Sends a request to the given URI and returns the raw response.
     *
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return mixed|\Psr\Http\Message\StreamInterface
     * @throws HttpException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function call($method, $uri = null, array $options = []) {
        if (!isset($options['headers'])) {
            $options['headers'] = [];
        }

        if (!isset($options['body'])) {
            $options['body'] = null;
        }

        try {
            $request = new Request($method, $uri, $options['headers'], $options['body']);
            $response = $this->send($request);

            return $response->getBody();
        } catch (BadResponseException $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
