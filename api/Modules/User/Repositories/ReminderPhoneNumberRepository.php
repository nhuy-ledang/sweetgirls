<?php namespace Modules\User\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface ReminderPhoneNumberRepository extends BaseRepository {
    public function cycleCode($reminder);

    public function complete($reminder): bool;

    public function removeExpired(): bool;

    public function create($data);

    public function get($data, string $code = null);

    /**
     * Create a reminders code
     *
     * @param $data
     * @return mixed
     */
    public function createReminder($data);

    /**
     * Get a reminders code for the given user
     *
     * @param $data
     * @param null $code
     * @return mixed
     */
    public function getReminder($data, $code = null);
}
