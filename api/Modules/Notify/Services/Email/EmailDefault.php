<?php

namespace Modules\Notify\Services\Email;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Mail\MailQueue;
use Mail;

class EmailDefault implements Mailer, MailQueue {

    public function queue($view, $queue = null) {
        return Mail::queue($view, $queue);
    }

    public function later($delay, $view, $queue = null) {
        return Mail::later($delay, $view, $queue);
    }

    public function to($users) {
        return Mail::to($users);
    }

    public function bcc($users) {
        return Mail::bcc($users);
    }

    public function raw($text, $callback) {
        return Mail::raw($text, $callback);
    }

    public function send($view, array $data = [], $callback = null) {
        return Mail::send($view, $data, $callback);
    }

    public function failures() {
        return Mail::failures();
    }
}