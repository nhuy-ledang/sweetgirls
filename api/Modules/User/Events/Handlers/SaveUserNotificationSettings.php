<?php namespace Modules\User\Events\Handlers;

use Illuminate\Http\Request;
use Modules\User\Events\SaveAdditionData;
use Modules\Event\Entities\UserNotification;
use Modules\User\Entities\NotificationUserSetting;

class SaveUserNotificationSettings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(SaveAdditionData $event)
    {
        $user = $event->user;

        $existedNotifications = $this->getNotificationSettings();
        $user->notificationSettings()->delete();

        $arrNotifications = [];
        foreach ($existedNotifications as $type => $text) {
            $arrNotifications[] = new NotificationUserSetting([
                                'type' => $type,
                                'active' => (int)$this->request->input('notificationSettings.'.$type, 1),
                            ]);
        }
        $user->notificationSettings()->saveMany($arrNotifications);
    }

    protected function getNotificationSettings()
    {
        $notificationSettings = [];
        $modules = \Module::collections();
        foreach ($modules as $module) {
            $notificationSettings = array_merge($notificationSettings, (array)config("asgard.{$module->getName()}.notifications.types"));
        }
        return $notificationSettings;
    }
}
