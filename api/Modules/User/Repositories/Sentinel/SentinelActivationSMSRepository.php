<?php namespace Modules\User\Repositories\Sentinel;

use Cartalyst\Sentinel\Activations\IlluminateActivationRepository;

class SentinelActivationSMSRepository extends IlluminateActivationRepository {
    protected $model = 'Modules\User\Entities\ActivationSMS';

    /**
     * Returns the random string used for the activation code.
     *
     * @return string
     */
    protected function generateActivationCode(): string
    {
        return sprintf("%06d", mt_rand(0, 999999));
    }
}