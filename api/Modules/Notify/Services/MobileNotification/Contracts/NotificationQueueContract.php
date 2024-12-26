<?php

namespace Modules\Notify\Services\MobileNotification\Contracts;


interface NotificationQueueContract {
    /**
     * Queue a new notification message for sending.
     *
     * @param  string|array $view
     * @param  array $data
     * @param  \Closure|string $callback
     * @param  string $queue
     * @return mixed
     */
    public function queue($view, array $data, $callback, $queue = null);

    /**
     * Queue a new notification message for sending after (n) seconds.
     *
     * @param  int $delay
     * @param  string|array $view
     * @param  array $data
     * @param  \Closure|string $callback
     * @param  string $queue
     * @return mixed
     */
    public function later($delay, $view, array $data, $callback, $queue = null);
}