<?php namespace Modules\User\Repositories\Sentinel;

use Carbon\Carbon;
use Cartalyst\Sentinel\Reminders\IlluminateReminderRepository;
use Cartalyst\Sentinel\Users\UserInterface;

class SentinelReminderSMSRepository extends IlluminateReminderRepository {
    /**
     * The Eloquent reminder model name.
     *
     * @var string
     */

    /**
     * The Eloquent reminder model name.
     *
     * @var string
     */
    protected $model = \Modules\User\Entities\ReminderSMS::class;

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

    /**
     * {@inheritDoc}
     */
    public function onlyComplete(UserInterface $user, $code) {
        $expires = $this->expires();

        $reminder = $this
            ->createModel()
            ->newQuery()
            ->where('user_id', $user->getUserId())
            ->where('code', $code)
            ->where('completed', false)
            ->where('created_at', '>', $expires)
            ->first();

        if ($reminder === null) {
            return false;
        }

        $reminder->fill([
            'completed'    => true,
            'completed_at' => Carbon::now(),
        ]);

        $reminder->save();

        return $reminder;
    }
}
