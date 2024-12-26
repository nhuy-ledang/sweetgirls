<?php namespace Modules\User\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\User\Entities\Sentinel\User;

class CreateInfusionsoftContact implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The maximum number of exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 100;

    /**
     * @var \Modules\User\Entities\Sentinel\User $user
     */
    protected $user;

    /**
     * Create a new job instance.
     * @param \Modules\User\Entities\Sentinel\User $user
     * @return void
     */
    public function __construct($user) {
        $this->user = $user;
    }

    /**
     * Write Infusionsoft Token
     * @param \Infusionsoft\Token $token
     */
    protected function setInfusionsoftToken($token) {
        $filepath = storage_path('app/infusionsoft_token.log');
        // Create file
        if (!file_exists($filepath)) {
            $handle = fopen($filepath, "w+");
            fclose($handle);
            chmod($filepath, 0777);
        }
        // Empty file
        $handle = fopen($filepath, 'w+');
        fclose($handle);
        // Write data
        $handle = fopen($filepath, 'a+');
        fwrite($handle, serialize($token));
        fclose($handle);
    }

    /**
     * Read Infusionsoft Token
     * @return \Infusionsoft\Token
     */
    protected function getInfusionsoftToken() {
        $filepath = storage_path('app/infusionsoft_token.log');
        // Create file
        if (!file_exists($filepath)) {
            $handle = fopen($filepath, "w+");
            fclose($handle);
            chmod($filepath, 0777);
        }
        $handle = fopen($filepath, 'r');
        flock($handle, LOCK_SH);
        $size = filesize($filepath);
        $token = $size > 0 ? unserialize(trim(fread($handle, $size))) : '';
        flock($handle, LOCK_UN);
        fclose($handle);

        return new \Infusionsoft\Token([
            'token_type'    => 'bearer',
            'scope'         => 'full|' . config('infusionsoft.auth.account') . '.infusionsoft.com',
            'access_token'  => $token && $token->accessToken ? $token->accessToken : config('infusionsoft.auth.accessToken'),
            'refresh_token' => $token && $token->refreshToken ? $token->refreshToken : config('infusionsoft.auth.refreshToken'),
            'expires_in'    => $token && $token->endOfLife ? $token->endOfLife : config('infusionsoft.auth.endOfLife'),
        ]);
    }

    /**
     * @param \Infusionsoft\Infusionsoft $infusionsoft
     * @param string $given_name
     * @param string $email
     * @param string $phone_number
     * @return \Infusionsoft\Api\Rest\ContactService
     */
    protected function createContact($infusionsoft, $given_name, $email, $phone_number) {
        $email1 = ['field' => 'EMAIL1', 'email' => $email];
        $infusionData = ['given_name' => $given_name, 'family_name' => '', 'email_addresses' => [$email1]];
        if ($phone_number) {
            $phone1 = ['field' => 'PHONE1', 'number' => $phone_number];
            $infusionData['phone_numbers'] = [$phone1];
        }

        return $infusionsoft->contacts()->create($infusionData);
    }

    /**
     * Execute the job.
     * @return mixed
     * @throws \Infusionsoft\InfusionsoftException
     */
    public function handle() {
//        $infusionsoft = new \Infusionsoft\Infusionsoft([
//            'clientId'     => config('infusionsoft.clientId'),
//            'clientSecret' => config('infusionsoft.clientSecret'),
//            'redirectUri'  => config('infusionsoft.redirectUri'),
//        ]);
//        $token = $this->getInfusionsoftToken();
//        $infusionsoft->setToken($token);
//        /*if (!$infusionsoft->getToken()) {
//            echo '<a href="' . $infusionsoft->getAuthorizationUrl() . '">Click here to authorize</a>'; exit();
//        }*/
//        try {
//            try {
//                $cid = $infusionsoft->contacts()->where('email', $this->user->email)->first();
//            } catch (\Infusionsoft\InfusionsoftException $e) {
//                $message = $e->getMessage();
//                if (strpos($message, 'keymanagement.service.access_token_expired')) {
//                    $infusionsoft->refreshAccessToken();
//                    $newToken = $infusionsoft->getToken();
//                    $this->setInfusionsoftToken($newToken);
//                    try {
//                        $cid = $infusionsoft->contacts()->where('email', $this->user->email)->first();
//                    } catch (\Infusionsoft\InfusionsoftException $e) {
//                        //var_dump('InfusionsoftException2', $e->getMessage()); exit();
//                        $cid = $this->createContact($infusionsoft, $this->user->display, $this->user->email, $this->user->phone_number);
//                    }
//                } else {
//                    //var_dump('InfusionsoftException1', $e->getMessage()); exit();
//                    $cid = $this->createContact($infusionsoft, $this->user->display, $this->user->email, $this->user->phone_number);
//                }
//            }
//        } catch (\Infusionsoft\TokenExpiredException $e) {
//            // If the request fails due to an expired access token, we can refresh
//            // the token and then do the request again.
//            //var_dump('TokenExpiredException', $e->getMessage()); exit();
//            $infusionsoft->refreshAccessToken();
//            $newToken = $infusionsoft->getToken();
//            $this->setInfusionsoftToken($newToken);
//            $cid = $this->createContact($infusionsoft, $this->user->display, $this->user->email, $this->user->phone_number);
//        }
//
//        // Update infusionsoft_id of user
//        User::where('id', $this->user->id)->update(['infusionsoft_id' => $cid->id]);
//
//        return $cid;
    }
}
