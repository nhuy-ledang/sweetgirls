<?php
/**
 * Created by PhpStorm.
 * User: nguyentantam
 * Date: 12/30/15
 * Time: 1:53 PM
 */

namespace Modules\User\Events\Handlers;

use Modules\Core\Contracts\Authentication;
use Modules\User\Events\UserHasRegisteredEmail;
use Modules\User\Events\UserHasRegisteredSMS;
use Modules\User\Events\UserHasRegisteredOnApi;
use Activation;
use ActivationSMS;
use Setting;

class HandleUserHasRegisteredOnApi
{
    /**
     * @var AuthenticationRepository
     */
    private $auth;
    protected $setting;

    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    public function handle(UserHasRegisteredOnApi $event)
    {
        $user = $event->user;

        if(Activation::completed($user)){
            return true;
        }

        /*
         *
        'auto_active'=>'Auto Active',
        'manual_active'=>'Manual Active',
        'active_by_confirm_url'=>'Active by Confirm URL',
        'active_by_sms_verify_code'=>'Active by SMS Verify Code',
         */


        $handle_method_register_with_api_request = Setting::get('user::handle_method_register_with_api_request');
        if($handle_method_register_with_api_request == 'auto_active' && !Activation::completed($user)){
            $activation = Activation::create($user);
            Activation::complete($user,$activation->getCode());
        }elseif($handle_method_register_with_api_request == 'active_by_confirm_url'){
            event(new UserHasRegisteredEmail($user));
        }elseif($handle_method_register_with_api_request == 'active_by_sms_verify_code'){
            event(new UserHasRegisteredSMS($user));
        }else{ //manual_active
            // DO NOTHING
        }

    }
}