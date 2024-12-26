<?php namespace Modules\User\Events\Handlers;

use Illuminate\Mail\Message;
use Modules\Core\Contracts\Authentication;
use Modules\User\Events\UserHasRegisteredSMS;
use Notify;

class SendRegistrationConfirmationSMS
{
    /**
     * @var AuthenticationRepository
     */
    private $auth;

    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    public function handle(UserHasRegisteredSMS $event)
    {

        $user = $event->user;

        $code = $this->auth->createActivationSMS($user);

        // $data = [
        //     'user' => $user,
        //     'activationcode' => $activationCode,
        // ];
        $text = "Your verify code is: ".$code;
        $phone = $event->user->phone_number;
        $phone_number[] = $phone; 

        Notify::sms()->queue($text,[], function (\Modules\Notify\Services\SMS\Message $message) use($phone) {
            $message->setSrc("Verify");
            $message->setDst($phone);
        });
    }
}
