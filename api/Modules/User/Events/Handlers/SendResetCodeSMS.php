<?php namespace Modules\User\Events\Handlers;
// THIS IS THE EVENT THAT IS USED TO SEND THE SMS

use Notify;
use Modules\User\Events\UserHasBegunResetProcessSMS;

class SendResetCodeSMS
{
    public function handle(UserHasBegunResetProcessSMS $event)
    {
        $code = $event->code;
        $text = "Your reset password code is: ".$code;
        $phone = $event->user->phone_number;
       
        $phone_number[] = $phone;

        Notify::sms()->queue($text,[], function (\Modules\Notify\Services\SMS\Message $message) use($phone_number) {
            $message->setSrc("Reset");
            $message->setDst($phone_number);
        });
    }
}
