<?php namespace Modules\User\Http\Controllers\ApiPublic;

use Modules\Core\Http\Controllers\Api\ApiPublicController;

abstract class ApiBaseModuleController extends ApiPublicController {
    public $module_name = "user";

    // Method to send Get request to url
    protected function doCurl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $data;
    }

    /**
     * For login
     * @param $user
     */
    protected function complete($user) {
        if ($user && !$user->completed) {
            $user->fill([
                'completed'    => true,
                'completed_at' => Carbon::now(),
            ]);

            $user->save();
        }
    }

    /**
     * Response User
     * @param \Modules\User\Entities\Sentinel\User $user
     * @return array | null
     */
    protected function responseUser($user) {
        $newUser = null;
        if ($user) {
            $newUser = $user->makeVisible([
                // Default
                'birthday', 'phone_number', 'email', 'address',
                // Verify
                'email_verified', 'phone_verified',
                // Setting
                'status',
            ])->toArray();
        }

        return $newUser;
    }
}
