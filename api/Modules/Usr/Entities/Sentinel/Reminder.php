<?php namespace Modules\Usr\Entities\Sentinel;

use Modules\Usr\Sentinel\EloquentReminder;

class Reminder extends EloquentReminder {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usr__reminders';
}
