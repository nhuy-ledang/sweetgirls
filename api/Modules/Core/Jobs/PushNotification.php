<?php namespace Modules\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushNotification implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The maximum number of exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 1;

    /**
     * @var $adapter
     */
    protected $adapter;

    /**
     * @var $user
     */
    protected $user;

    /**
     * @var $data
     */
    protected $data;

    /**
     * Create a new job instance.
     * @param $adapter
     * @param $user
     * @param $data
     * @return void
     */
    public function __construct($adapter, $user, $data) {
        $this->adapter = $adapter;
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle() {
        //sleep(30);
        //$this->adapter = (new \Modules\Notify\Services\Notify())->mobileNotification();
        if (!$this->adapter) $this->adapter = new \Modules\Notify\Services\MobileNotification\OneSignal();
        $user = $this->user;
        $setting = $this->data['setting'];
        $pushMessage = $this->data['message'];
        $pushType = $this->data['type'];
        $pushData = $this->data['pushData'];
        $devices = [];
        $platforms = [];
        if (($setting === false || ($setting != false && !!$user->{$setting}))) {
            if (!empty($user->device_platform) && !empty($user->device_token)) {
                $devices[$user->device_platform][] = $user->device_token;
                $platforms[] = $user->device_platform;
            }
            foreach ($user->devices as $d) {
                if (empty($d->device_platform) || empty($d->device_token)) continue;
                if (!isset($devices[$d->device_platform])) $devices[$d->device_platform] = [];
                $devices[$d->device_platform][] = $d->device_token;
                $platforms[] = $d->device_platform;
            }
        }
        $platforms = array_unique($platforms);
        $target_devices = [];
        foreach ($platforms as $platform) {
            $target_devices[$platform] = array_unique($devices[$platform]);
        }
        // Create notification
        $notification = \Modules\Notify\Entities\Notification::create($this->data);
        $pushData['notification_id'] = $notification->id;
        // Push notification
        if (!empty($target_devices)) {
            $badgeCount = \Modules\Notify\Entities\Notification::where('user_id', $user->id)->where('is_read', 0)->count();
            $payload = new \Modules\Core\Helper\PushNotificationPayload($pushMessage, $pushType, $pushData, $badgeCount);
            $pushJson = $payload->toJson();
            $this->adapter->send($pushJson['alert'], function($message) use ($target_devices, $pushJson) {
                // $message->setTitle('Thông báo!');
                $message->setCustomData($pushJson);
                $message->setTargetDevices($target_devices);
            });
        }
    }
}
