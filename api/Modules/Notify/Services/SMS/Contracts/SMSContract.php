<?php

namespace Modules\Notify\Services\SMS\Contracts;


use Modules\Notify\Services\SMS\Message;

interface SMSContract {
    public function createMessage($callback, $data = null);


    public function sendMessage(Message $message);

    /**
     * Send a new message when only a raw text part.
     *
     * @param  string $text
     * @param  \Closure|string $callback
     * @return int
     */
    public function raw($text, $callback);

    /**
     * Send a new message using a view.
     *
     * @param  string|array $view
     * @param  array $data
     * @param  \Closure|string $callback
     * @return void
     */
    public function send($view, array $data, $callback);
}