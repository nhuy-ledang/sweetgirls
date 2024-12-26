<?php namespace Modules\Usr\Sentinel;

use Cartalyst\Sentinel\Reminders\IlluminateReminderRepository;
use Cartalyst\Sentinel\Reminders\ReminderRepositoryInterface;

class ReminderRepository extends IlluminateReminderRepository implements ReminderRepositoryInterface {
    protected function generateReminderCode(): string {
        return sprintf("%06d", mt_rand(0, 999999));
    }
}
