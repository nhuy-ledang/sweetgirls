<?php namespace Modules\Notify\Services\MobileNotification;

use Modules\Notify\Services\MobileNotification\OneSignal\Notifications;
use OneSignal\Config;
use Symfony\Component\HttpClient\Psr18Client;
use Nyholm\Psr7\Factory\Psr17Factory;
use OneSignal\Resolver\ResolverFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class OneSignalNorkunas extends \OneSignal\OneSignal {
    public function __construct(Config $config = null, ClientInterface $httpClient = null, RequestFactoryInterface $requestFactory = null, StreamFactoryInterface $streamFactory = null) {
        $config = new Config(
            config('notify.mobile-notification.gateway.onesignal.application_id'),
            config('notify.mobile-notification.gateway.onesignal.application_auth_key'),
            config('notify.mobile-notification.gateway.onesignal.auth_key')
        );
        $httpClient = new Psr18Client();
        $requestFactory = $streamFactory = new Psr17Factory();

        parent::__construct($config, $httpClient, $requestFactory, $streamFactory);
    }

    public function getNotifications() {
        $resolverFactory = new ResolverFactory($this->getConfig());
        return new Notifications($this, $resolverFactory);
    }
}
