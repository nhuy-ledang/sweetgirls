<?php namespace Modules\User\Events\Handlers;

use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Contracts\Authentication;
use Modules\User\Events\UserHasRegisteredEmail;

class SendRegistrationConfirmationEmail
{
    /**
     * @var Authentication
     */
    private $auth;

    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    public function handle(UserHasRegisteredEmail $event)
    {
        $user = $event->user;

        $activationCode = $this->auth->createActivation($user);

        $data = [
            'user' => $user,
            'activationcode' => $activationCode,
        ];

        Mail::queue('user::emails.welcome', $data,
            function (Message $m) use ($user) {
                $m->to($user->email)->subject(trans('user::users.register user'));
            }
        );
    }
}
