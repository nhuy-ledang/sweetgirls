<?php
namespace Modules\Notify\Services\SMS;


use Modules\Notify\Services\SMS\Contracts\SMSContract;
use Modules\Notify\Services\SMS\Contracts\SMSQueueContract;
use Modules\Notify\Services\SMS\Plivo\PlivoError;
use Modules\Notify\Services\SMS\Plivo\RestAPI;
use App;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SuperClosure\Serializer;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\Queue as QueueContract;

class Plivo implements SMSContract, SMSQueueContract {
    protected $auth_id;
    protected $auth_token;
    protected $api;

    /**
     * @return mixed
     */
    public function getAuthId() {
        return $this->auth_id;
    }

    /**
     * @param mixed $auth_id
     */
    public function setAuthId($auth_id) {
        $this->auth_id = $auth_id;
    }

    /**
     * @return mixed
     */
    public function getAuthToken() {
        return $this->auth_token;
    }

    /**
     * @param mixed $auth_token
     */
    public function setAuthToken($auth_token) {
        $this->auth_token = $auth_token;
    }

    protected $pretend;
    /**
     * The log writer instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The queue implementation.
     *
     * @var \Illuminate\Contracts\Queue\Queue
     */
    protected $queue;

    public function __construct() {

        $auth_id = config('asgard.notify.config.sms.gateway.plivo.auth_id');
        $auth_token = config('asgard.notify.config.sms.gateway.plivo.auth_token');
        if (app()->bound('Psr\Log\LoggerInterface')) {
            $this->setLogger(app()->make('Psr\Log\LoggerInterface'));
        }

        if (app()->bound('queue')) {
            $this->setQueue(app()['queue.connection']);
        }
        $this->setAuthId($auth_id);
        $this->setAuthToken($auth_token);

        $this->api($this->getAuthId(), $this->getAuthToken());


        $this->pretend = config('asgard.notify.config.sms.pretend');
    }

    public function api() {
        $url = "https://api.plivo.com";
        $version = "v1";
        return new RestAPI($this->getAuthId(), $this->getAuthToken());
    }


    public function createMessage($callback, $data = null) {
        $message = new Message();


        call_user_func($callback, $message);


        return $message;
    }

    protected function buildDataMessage(Message $message) {
        $data = [];
        $src = $message->getSrc();

        if (is_string($src)) {
            $src = str_replace("+", "", $src);
            $src = str_replace("-", "", $src);
            $src = str_replace(" ", "", $src);
        }

        if (is_array($src)) {
            foreach ($src as &$v) {
                $v = str_replace("+", "", $v);
                $v = str_replace("-", "", $v);
                $v = str_replace(" ", "", $v);
            }
        }

        $data['src'] = $src;
        $data['dst'] = $message->getDst();
        $data['text'] = $message->getText();


        return $data;
    }


    public function sendMessage(Message $message) {


        if (!$message->getSrc() || !$message->getDst() || !$message->getText()) {
            throw new InvalidArgumentException('Can not send message without content or target phone number');
        }

        if (!$this->pretend) {
            try {
                $rs = $this->api()->send_message($this->buildDataMessage($message));

            } catch (PlivoError $e) {

                throw new \Exception(json_encode($e->getMessage()));
            }
            $message->setResponseData($rs);

            event('sms.sending', $message);
            return $rs;
        } else {
            $message->setStatus(-2);
            event('sms.sending', $message);

            return true;
        }

    }

    public function raw($text, $callback) {
        return $this->send(['raw' => $text], [], $callback);
    }

    public function send($view, array $data, $callback) {

        if (is_string($view)) {
            $view = ['raw' => $view];
        }
        // First we need to parse the view, which could either be a string or an array
        // containing both an HTML and plain text versions of the view which should
        // be used when sending an notification. We will extract both of them out here.
        list($view, $plain, $raw) = $this->parseView($view);

        $message = $this->createMessage($callback, $data);

        if ($plain) {
            $message->setText($plain);
        } else if ($view) {
            $message->setText(view($view, $data)->render());
        } else {
            $message->setText($raw);
        }
        // Once we have retrieved the view content for the notification we will set the body
        // of this message using the HTML type, which will provide a simple wrapper
        // to creating view based emails that are able to receive arrays of data.

        return $this->sendMessage($message);
    }


    /**
     * Queue a new notification message for sending.
     *
     * @param  string|array $view
     * @param  array $data
     * @param  \Closure|string $callback
     * @param  string|null $queue
     * @return mixed
     */
    public function queue($view, array $data, $callback, $queue = null) {
        $callback = $this->buildQueueCallable($callback);

        return $this->queue->push(get_class($this) . '@handleQueuedMessage', compact('view', 'data', 'callback'), $queue);
    }

    /**
     * Queue a new notification message for sending on the given queue.
     *
     * @param  string $queue
     * @param  string|array $view
     * @param  array $data
     * @param  \Closure|string $callback
     * @return mixed
     */
    public function queueOn($queue, $view, array $data, $callback) {
        return $this->queue($view, $data, $callback, $queue);
    }

    /**
     * Queue a new notification message for sending after (n) seconds.
     *
     * @param  int $delay
     * @param  string|array $view
     * @param  array $data
     * @param  \Closure|string $callback
     * @param  string|null $queue
     * @return mixed
     */
    public function later($delay, $view, array $data, $callback, $queue = null) {
        $callback = $this->buildQueueCallable($callback);

        return $this->queue->later($delay, get_class($this) . '@handleQueuedMessage', compact('view', 'data', 'callback'), $queue);
    }

    /**
     * Queue a new notification message for sending after (n) seconds on the given queue.
     *
     * @param  string $queue
     * @param  int $delay
     * @param  string|array $view
     * @param  array $data
     * @param  \Closure|string $callback
     * @return mixed
     */
    public function laterOn($queue, $delay, $view, array $data, $callback) {
        return $this->later($delay, $view, $data, $callback, $queue);
    }

    /**
     * Build the callable for a queued notification job.
     *
     * @param  mixed $callback
     * @return mixed
     */
    protected function buildQueueCallable($callback) {
        if (!$callback instanceof Closure) {
            return $callback;
        }

        return (new Serializer)->serialize($callback);
    }

    /**
     * Handle a queued notification message job.
     *
     * @param  \Illuminate\Contracts\Queue\Job $job
     * @param  arra
     * y  $data
     * @return void
     */
    public function handleQueuedMessage($job, $data) {

        try {

            \Notify::sms()->getProvider()->send($data['view'], $data['data'], $this->getQueuedCallable($data));

        } catch (\Exception $e) {
            \Log::error(__METHOD__, $e);
        }
        $job->delete();
    }

    /**
     * Get the true callable for a queued notification message.
     *
     * @param  array $data
     * @return mixed
     */
    protected function getQueuedCallable(array $data) {
        if (Str::contains($data['callback'], 'SerializableClosure')) {
            return unserialize($data['callback'])->getClosure();
        }

        return $data['callback'];
    }

    /**
     * Set the log writer instance.
     *
     * @param  \Psr\Log\LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Set the queue manager instance.
     *
     * @param  \Illuminate\Contracts\Queue\Queue $queue
     * @return $this
     */
    public function setQueue(QueueContract $queue) {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Set the IoC container instance.
     *
     * @param  \Illuminate\Contracts\Container\Container $container
     * @return void
     */
    public function setContainer(Container $container) {
        $this->container = $container;
    }

    protected function parseView($view) {
        if (is_string($view)) {
            return [$view,
                null,
                null];
        }

        // If the given view is an array with numeric keys, we will just assume that
        // both a "pretty" and "plain" view were provided, so we will return this
        // array as is, since must should contain both views with numeric keys.
        if (is_array($view) && isset($view[0])) {
            return [$view[0],
                $view[1],
                null];
        }

        // If the view is an array, but doesn't contain numeric keys, we will assume
        // the the views are being explicitly specified and will extract them via
        // named keys instead, allowing the developers to use one or the other.
        if (is_array($view)) {
            return [Arr::get($view, 'html'),
                Arr::get($view, 'text'),
                Arr::get($view, 'raw'),];
        }

        throw new InvalidArgumentException('Invalid view.');
    }
}