<?php namespace Modules\User\Http\Controllers\ApiPublic;

/**
 * Reference
 * https://github.com/sahat/satellizer
 * https://github.com/GeneaLabs/laravel-socialiter
 * https://github.com/GeneaLabs/laravel-sign-in-with-apple
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Modules\Media\Helpers\FileHelper;
use Modules\Notify\Repositories\NotificationRepository;
use Modules\Order\Repositories\OrderRepository;
use Modules\User\Repositories\Authentication;
use Modules\User\Repositories\UserRepository;
use Modules\System\Repositories\SettingRepository;
use Sentinel;

/**
 * Class AuthController
 *
 * @package Modules\User\Http\Controllers\ApiPublic

 * @OA\Info(title="Tedfast Swagger API", version="1.0.0")
 */
class AuthController extends ApiBaseModuleController {
    /**
     * @var \Modules\User\Repositories\Authentication
     */
    protected $authentication;

    /**
     * @var \Modules\Notify\Repositories\NotificationRepository
     */
    protected $notification_repository;

    /**
     * @var \Modules\System\Repositories\SettingRepository
     */
    protected $setting_repository;

    /**
     * @var \Modules\Order\Repositories\OrderRepository
     */
    protected $order_repository;

    public function __construct(Request $request,
                                Authentication $authentication,
                                UserRepository $user_repository,
                                NotificationRepository $notification_repository,
                                SettingRepository $setting_repository,
                                OrderRepository $order_repository) {
        $this->model_repository = $user_repository;
        $this->authentication = $authentication;
        $this->notification_repository = $notification_repository;
        $this->setting_repository = $setting_repository;
        $this->order_repository = $order_repository;

        $this->middleware('auth.user')->only(['index', 'logout', 'passwordChange', 'verifyPassword', 'phoneChange', 'phoneVerify', 'emailChange', 'emailVerify', 'profileChange', 'qrcode', 'createShareCode', 'getInviteHistory']);

        parent::__construct($request);
    }

    private function loginAfter(&$user) {
        // $this->setProductViewed($user->id);
    }

    /**
     * Respond Device Limit
     * @param $persistences
     * @return mixed
     */
    private function errorDeviceLimit($persistences) {
        $data = [
            'devices' => $persistences,
            'info'    => [
                'phone_number' => $this->setting_repository->findByKey('phone_number'),
            ],
        ];
        return $this->respondWithErrorKey('auth.device_limit', 403, '', [], $data);
    }

    // Check Email or Phone Number Valid
    protected function getLoginNames() {
        $email = $this->request->get('email');
        $validatorErrors = $this->getValidator(compact('email'), ['email' => 'required|email']);
        if (empty($validatorErrors)) {
            $email = trim(strtolower((string)$email));
            $phone_number = '';
            $by_email = true;
        } else {
            $phone_number = trim(strtolower((string)$email));
            $email = '';
            $by_email = false;
        }

        return [$email, $phone_number, $by_email];
    }

    /**
     * @OA\Get(
     *   path="/auth",
     *   summary="Check Login",
     *   operationId="checkLogin",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function index() {
        try {
            $user = $this->auth();
            $this->loginAfter($user);

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/login",
     *   summary="Login",
     *   description="Login",
     *   operationId="login",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Login",
     *     @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="admin@gmail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="12345678"),
     *       @OA\Property(property="remember", type="boolean", example=true),
     *       @OA\Property(property="name", type="string", example="Galaxy J7"),
     *       @OA\Property(property="model", type="string", example="SM-J710F"),
     *       @OA\Property(property="App-Env", type="string", example="cms"),
     *       @OA\Property(property="Device-Platform", type="string", example="web"),
     *       @OA\Property(property="Device-Token", type="string", example="APA91bEZ4wyozxowl0g3Y2XBlpGRUOekBEs8hInUXtZgmYR8GGESyLGspjEeIlTVFBdJZ1cuKroKbi7w5S8TRXFAOJgRCZr0qNDZIhg3BLW52R4j10oFrsMXsrKVaqLcg3cbA-zn0j95"),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function login() {
        try {
            $input = $this->request->only(['password']);
            $this->getDeviceInfo($input);
            list($email, $phone_number, $by_email) = $this->getLoginNames();
            $data = $input;
            $rules = ['password' => 'required'];
            if ($by_email) {
                $input['email'] = $email;
                $data['email'] = $email;
                $rules['email'] = 'required|email';
            } else {
                $input['phone_number'] = $phone_number;
                list($calling_code, $new_phone_number) = calling2phone($phone_number);
                $data['phone_number'] = $new_phone_number;
                $rules['phone_number'] = 'required';
            }
            //==== Check Valid
            $validatorErrors = $this->getValidator($data, $rules);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            //=== Default params
            $input['last_provider'] = $by_email ? 'email' : 'phone_number';
            //=== Try login
            list($errorKey, $user, $persistences) = $this->_auth->login($input, (boolean)$this->request->get('remember'));
            $access_token = !$errorKey ? $persistences : null;
            if ($errorKey) {
                $data = [];
                if ($user) {
                    if ($errorKey == 'auth.device_limit') { //=== Check three devices
                        return $this->errorDeviceLimit($persistences);
                    } else if ($errorKey == 'auth.old_software') { //=== Update version for device_id
                        return $this->errorUnauthorized('auth.old_software');
                    } else if ($errorKey == 'auth.not_activated') { //=== Account not active
                        return $this->errorUnauthorized('auth.not_activated');
                    } else if ($errorKey == 'auth.login_not_activated') {
                        $data = $user;
                    } else {
                        $data['attempts_left'] = USER_PASSWORD_FAILED_MAX - $user->password_failed;
                    }
                } else {
                    $data = null;
                }

                return $this->respondWithErrorKey($errorKey, 400, '', [], $data);
            }

            //=== Check login
            if (!$user) return $this->errorUnauthorized();

            $this->complete($user);

            //=== Update device
            $this->updateDeviceToken($user, $input);

            //=== Get last persistence => the newest one for current login request
            /*$persistence = $user->persistences()->latest()->first();
            $user->{$this->apiKeyName} = $persistence->code;*/
            $user->{config('user.users.api_key_name')} = $access_token;

            //=== Set by
            $user->by_email = $by_email;

            /*//=== Check platform and Send OTP
            if ($user->device_platform != $input['device_platform'] || $user->device_token != $input['device_token']) {
                //=== Check platform and Send OTP
                $text = $this->_auth->createReminderSMSWithData($user, 'device_token', array(
                    'device_platform' => $input['device_platform'],
                    'device_token'    => $input['device_token'],
                ));
                $to = $user->phone_number;
                if (config('app.debug')) {
                    $user->sms_code = $text;
                }
                $user->sms_result = $this->sms->send($to, $text, 'Verify Device Token');

                return $this->respondWithErrorKey('system.change_device', 203, '', [], $user);
            } else {
                return $this->respondWithSuccess($user);
            }*/

            $this->loginAfter($user);

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    //<editor-fold desc="Facebook">

    //</editor-fold>

    /**
     * @OA\Post(
     *   path="/auth/logout",
     *   summary="Sign out",
     *   description="Sign out",
     *   operationId="signOut",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Sign out",
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example="Galaxy J7"),
     *       @OA\Property(property="model", type="string", example="SM-J710F"),
     *       @OA\Property(property="App-Env", type="string", example="cms"),
     *       @OA\Property(property="Device-Platform", type="string", example="web"),
     *       @OA\Property(property="Device-Token", type="string", example="APA91bEZ4wyozxowl0g3Y2XBlpGRUOekBEs8hInUXtZgmYR8GGESyLGspjEeIlTVFBdJZ1cuKroKbi7w5S8TRXFAOJgRCZr0qNDZIhg3BLW52R4j10oFrsMXsrKVaqLcg3cbA-zn0j95"),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function logout() {
        try {
            $device_platform = detect_platform();
            $device_token = detect_token();

            if ($device_platform && $device_token && $auth = $this->isLogged()) {
                // Remove device
                if ($auth->device_token == $device_token) {
                    $auth->device_platform = null;
                    $auth->device_token = null;
                    $auth->save();
                }

                // Remove device table
                //$this->device_token_repository->getModel()->where('user_id', $auth->id)->where('device_token', $device_token)->delete();
            }

            $this->_auth->logout($this->access_token);

            return $this->respondWithSuccess(trans('Signout Success'));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/register",
     *   summary="Register By Email",
     *   description="Register By Email",
     *   operationId="registerByEmail",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Register By Email",
     *     @OA\JsonContent(
     *       required={"phone_number","password"},
     *       @OA\Property(property="email", type="string", format="email", example="admin@gmail.com"),
     *       @OA\Property(property="phone_number", type="string", example="0979750599"),
     *       @OA\Property(property="password", type="string", example="12345678"),
     *       @OA\Property(property="name", type="string", example="Galaxy J7"),
     *       @OA\Property(property="model", type="string", example="SM-J710F"),
     *       @OA\Property(property="device_id", type="string", example="8e43475dcc200477"),
     *       @OA\Property(property="referral_code", type="string", example=""),
     *       @OA\Property(property="App-Env", type="string", example="cms"),
     *       @OA\Property(property="Device-Platform", type="string", example="web"),
     *       @OA\Property(property="Device-Token", type="string", example="APA91bEZ4wyozxowl0g3Y2XBlpGRUOekBEs8hInUXtZgmYR8GGESyLGspjEeIlTVFBdJZ1cuKroKbi7w5S8TRXFAOJgRCZr0qNDZIhg3BLW52R4j10oFrsMXsrKVaqLcg3cbA-zn0j95"),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function register() {
        try {
            $input = $this->request->only('password', 'prefix', 'first_name', 'last_name', 'gender', 'birthday', 'address');
            $verify_code = $this->request->get('code');
            list($calling_code, $phone_number) = calling2phone($this->request->get('phone_number'));

            $rules = ['password' => 'required'];
            if ($phone_number) {
                $rules['phone_number'] = 'required';
                $rules['code'] = 'required';
                $input['calling_code'] = $calling_code;
                $input['phone_number'] = $phone_number;
                $input['code'] = $verify_code;
                $input['last_provider'] = 'phone_number';
            }

            $email = $this->request->get('email');
            if ($email) {
                $email = trim(strtolower((string)$email));
                $input['email'] = $email;
            }
            if ($email || !$phone_number) {
                $rules['email'] = 'required|email';
                $input['last_provider'] = 'email';
            }

            $validatorErrors = $this->getValidator($input, $rules);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);

            $username = $email ? explode('@', $email)[0] : phone2local($calling_code . $phone_number);
            $input['username'] = $username;

            $user = $this->model_repository->createWithRoles($input, [USER_ROLE_USER], true);

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/register-verify",
     *   summary="Register Verify (First Login) By SmsCode",
     *   description="Register Verify (First Login) By SmsCode",
     *   operationId="registerVerify",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Register Verify (First Login) By SmsCode",
     *     @OA\JsonContent(
     *       required={"code","access_token"},
     *       @OA\Property(property="code", type="integer", example="123456"),
     *       @OA\Property(property="access_token", type="string", example="VvAvwhahxgS2Imp6F7ElqUdzae6oYNJB"),
     *       @OA\Property(property="name", type="string", example="Galaxy J7"),
     *       @OA\Property(property="model", type="string", example="SM-J710F"),
     *       @OA\Property(property="device_id", type="string", example="8e43475dcc200477"),
     *       @OA\Property(property="App-Env", type="string", example="cms"),
     *       @OA\Property(property="Device-Platform", type="string", example="web"),
     *       @OA\Property(property="Device-Token", type="string", example="APA91bEZ4wyozxowl0g3Y2XBlpGRUOekBEs8hInUXtZgmYR8GGESyLGspjEeIlTVFBdJZ1cuKroKbi7w5S8TRXFAOJgRCZr0qNDZIhg3BLW52R4j10oFrsMXsrKVaqLcg3cbA-zn0j95"),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function registerVerify() {
        try {
            $input = $this->request->only('code', 'access_token');
            $this->getDeviceInfo($input);
            // Check Valid
            $validatorErrors = $this->getValidator($input, ['code' => 'required', 'access_token' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $user = $this->_auth->findByPersistenceCode($input['access_token']);
            if (!$user) return $this->respondWithErrorKey('access_denied.invalid');
            if ($this->_auth->activateSMS($user, $input['code'])) {
                //=== Remove all token
                $user->persistences()->delete();
                //=== Try login
                $input['last_provider'] = 'phone_number';
                list($errorKey, $user, $persistences) = $this->_auth->loginByUser($user, $input, true);
                $access_token = !$errorKey ? $persistences : null;
                if (!$errorKey) {
                    //=== Update device
                    $this->updateDeviceToken($user, $input);
                    //=== Get last persistence => the newest one for current login request
                    /*$persistence = $user->persistences()->latest()->first();
                    $user->{$this->apiKeyName} = $persistence->code;*/
                    $user->{config('user.users.api_key_name')} = $access_token;
                }
                $phone_number = phone2local('+' . $user->calling_code . $user->phone_number);
                // Send email notify
                if ($user->email) {
                    $optional = [
                        'display'      => $user->display,
                        'email'        => $user->email,
                        'phone_number' => $phone_number,
                    ];
                    dispatch(new \Modules\User\Jobs\SendRegisterSuccess($this->email, $optional));
                }
                // Send sms notify
                if ($phone_number) $this->sms->send($phone_number, Lang::get('messages.sms_content.register_success'));

                return $this->respondWithSuccess($user);
            }

            return $this->respondWithErrorKey('code.invalid');
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    protected function getDeviceInfo(&$input = []) {
        $input['ip'] = $this->request->server->get('REMOTE_ADDR');
        $name = $this->request->get('name');
        if ($name) $input['name'] = $name;
        $model = $this->request->get('model');
        if ($model) $input['model'] = $model;
        $device_id = $this->request->get('device_id');
        if ($device_id) $input['device_id'] = $device_id;
        $device_platform = detect_platform();
        if ($device_platform) $input['device_platform'] = $device_platform;
        $device_token = detect_token();
        if ($device_token) $input['device_token'] = $device_token;

        return $input;
    }

    /**
     * Update Device token
     * @param $user
     * @param $credentials
     */
    private function updateDeviceToken($user, $credentials = []) {
        if (empty($credentials)) $this->getDeviceInfo($credentials);
        //=== Get persistence optional
        $optional = [];
        if (isset($credentials['last_provider'])) $optional['provider'] = $credentials['last_provider'];
        foreach (['name', 'model', 'device_id', 'device_platform', 'device_token', 'ip'] as $field) {
            if (isset($credentials[$field])) $optional[$field] = $credentials[$field];
        }
        // Update device token
        $env = detect_env();
        if ($env == 'app' && !empty($optional['device_platform']) && !empty($optional['device_token'])) {
            $data = ['device_platform' => $optional['device_platform'], 'device_token' => $optional['device_token']];
            if (!empty($optional['device_id'])) $data['device_id'] = $optional['device_id'];
            // Update user
            $this->model_repository->getModel()->where('id', $user->id)->update($data);
            // Update device table
            $device = $this->device_token_repository->findByAttributes(['user_id' => $user->id, 'device_platform' => $optional['device_platform'], 'device_token' => $optional['device_token']]);
            if (!$device) {
                $this->device_token_repository->create(array_merge(['user_id' => $user->id], $optional));
            } else {
                $this->device_token_repository->update($device, $optional);
            }
        };
    }
}
