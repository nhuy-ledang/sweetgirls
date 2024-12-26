<?php namespace Modules\User\Repositories\Eloquent;

use Carbon\Carbon;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\User\Repositories\ReminderPhoneNumberRepository;

class EloquentReminderPhoneNumberRepository extends EloquentBaseRepository implements ReminderPhoneNumberRepository {
    /**
     * The expiration time in seconds.
     *
     * @var int
     */
    protected $expires = 600;

    /**
     * Returns the expiration date.
     *
     * @return \Carbon\Carbon
     */
    protected function expires(): Carbon {
        return Carbon::now()->subSeconds($this->expires);
    }

    /**
     * Returns the random string used for the reminder code.
     *
     * @return string
     */
    protected function generateReminderCode(): string {
        return sprintf("%06d", mt_rand(0, 999999));
    }

    public function cycleCode($reminder) {
        $code = sprintf("%06d", mt_rand(0, 999999));
        $reminder->code = $code;
        $reminder->save();
        return $reminder;
    }

    public function complete($reminder): bool {
        $reminder->fill([
            'completed'    => true,
            'completed_at' => Carbon::now(),
        ]);

        $reminder->save();

        return true;
    }

    public function removeExpired(): bool {
        $expires = $this->expires();

        return $this->getModel()
            ->where('completed', false)
            ->where('created_at', '<', $expires)
            ->delete();
    }

    public function create($data) {
        $data['code'] = $this->generateReminderCode();

        return parent::create($data);
    }

    public function get($data, string $code = null) {
        $expires = $this->expires();

        $reminder = $this->getModel()
            ->where('phone_number', $data['phone_number'])
            ->where('ip', $data['ip'])
            ->where('completed', false)
            ->where('created_at', '>', $expires);

        if ($code) {
            $reminder->where('code', $code);
        }

        return $reminder->first();
    }

    /**
     * Create a reminders code
     *
     * @param $data
     * @return mixed
     */
    public function createReminder($data) {
        $reminder = $this->findByAttributes($data) ?: $this->create($data);
        return $reminder->code;
    }

    /**
     * Get a reminders code for the given user
     *
     * @param $data
     * @param null $code
     * @return mixed
     */
    public function getReminder($data, $code = null) {
        $this->removeExpired();

        return $this->get($data, $code);
    }
}
