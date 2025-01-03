<?php namespace Modules\Usr\Http\Controllers\Api;

/**
 * Reference
 * https://github.com/sahat/satellizer
 * https://github.com/GeneaLabs/laravel-socialiter
 * https://github.com/GeneaLabs/laravel-sign-in-with-apple
 */

use Illuminate\Http\Request;
use Modules\System\Repositories\SettingRepository;
use Modules\Usr\Repositories\UserRepository;

/**
 * Class AuthController
 *
 * @package Modules\Usr\Http\Controllers\Api

 */
class AuthController extends ApiBaseModuleController {
    /**
     * @var \Modules\System\Repositories\SettingRepository;
     */
    protected $setting_repository;

    public function __construct(Request $request, UserRepository $user_repository, SettingRepository $setting_repository) {
        $this->model_repository = $user_repository;
        $this->setting_repository = $setting_repository;

        $this->_auth = app('Modules\Usr\Repositories\Authentication');

        $this->middleware('auth.usr')->except(['login']);

        parent::__construct($request);
    }

    private function updateDeviceToken($user) {
        $device_platform = (string)$this->request->get('device_platform');
        $device_token = (string)$this->request->get('device_token');
        if (empty($device_platform) || empty($device_token)) return;
        // Update user
        $user->device_platform = $device_platform;
        $user->device_token = $device_token;
        $user->save();
    }

    /**
     * Respond Device Limit
     *
     * @param $persistences
     * @return mixed
     */
    private function errorDeviceLimit($persistences) {
        $data = ['devices' => $persistences];
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
     *   path="/backend/usr_auth",
     *   summary="Check Login",
     *   operationId="usrAuthCheckLogin",
     *   tags={"BackendUsrAuth"},
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
            $user->getRoles()->makeHidden(['pivot', 'created_at', 'updated_at']);
            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_auth/login",
     *   summary="Login",
     *   operationId="usrAuthLogin",
     *   tags={"BackendUsrAuth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
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
            // Set email
            $by_email = true;
            $input['email'] = $email;
            $data['email'] = $email;
            $rules['email'] = 'required|email';
            /*if ($by_email) {
                $input['email'] = $email;
                $data['email'] = $email;
                $rules['email'] = 'required|email';
            } else {
                $input['phone_number'] = $phone_number;
                list($calling_code, $new_phone_number) = calling2phone($phone_number);
                $data['phone_number'] = $new_phone_number;
                $rules['phone_number'] = 'required';
            }*/
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
            //=== Update device
            $this->updateDeviceToken($user);
            //=== Get last persistence => the newest one for current login request
            $user->{config('user.users.api_key_name')} = $access_token;
            //=== Set by
            $user->by_email = $by_email;
            $user->getRoles()->makeHidden(['pivot', 'created_at', 'updated_at']);

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_auth/logout",
     *   summary="Sign out",
     *   operationId="usrAuthSignOut",
     *   tags={"BackendUsrAuth"},
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

    protected function findByCredentials() {
        list($email, $phone_number, $by_email) = $this->getLoginNames();
        if (!$by_email) {
            list($calling_code, $phone_number) = calling2phone($phone_number);
            if (!($calling_code && $phone_number)) return ['phone_number.required', null, $by_email];
            $credentials = ['calling_code' => $calling_code, 'phone_number' => $phone_number];
        } else {
            $credentials = ['email' => $email];
        }
        $user = $this->_auth->findByCredentials($credentials);
        if (!$user) return ['user.invalid', null, $by_email];

        return [null, $user, $by_email];
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_auth/pw-change",
     *   summary="Password Change",
     *   operationId="useAuthPasswordChange",
     *   tags={"BackendUsrAuth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="password", type="string", example="12345678"),
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
    public function passwordChange() {
        try {
            //=== Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), [
                'password' => [
                    'required',
                    'min:6',
                    //'regex:/^.*(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])|(?=.*[a-z])(?=.*[A-Z])(?=.*[ !\"#$%&\'()*+,-.\/:;<=>?@[\]^_`{|}~])|(?=.*[a-z])(?=.*[0-9])(?=.*[ !\"#$%&\'()*+,-.\/:;<=>?@[\]^_`{|}~])|(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"#$%&\'()*+,-.\/:;<=>?@[\]^_`{|}~]).*$/',
                ],
                //'password_confirmation' => 'required|same:password'
            ]);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $user = $this->auth();
            $new_password = $this->request->get('password');
            //$this->_auth->newPassword($user, $new_password);
            $this->model_repository->update($user, ['password' => $new_password]);
            $user->persistences()->where('user_id',$user->id)->where('code', '<>', $this->access_token)->delete();

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_auth/forgot",
     *   summary="Forgot Password",
     *   operationId="usrAuthForgotPassword",
     *   tags={"BackendUsrAuth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="email", type="string", example="admin@gmail.com"),
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
    public function forgot() {
        try {
            list($errorKey, $user, $by_email) = $this->findByCredentials();
            if ($errorKey) return $this->respondWithErrorKey($errorKey);
            //=== Check has activated
            if ($user->status != USER_STATUS_ACTIVATED) return $this->respondWithErrorKey('auth.login_not_activated');
            //=== Check user activation
            if (!$this->_auth->activateCompleted($user)) return $this->respondWithErrorKey('auth.not_activated');
            //=== Create Reminder
            $optional = ['code' => $this->_auth->createReminderSMS($user)];
            if ($by_email) {
                $optional['email'] = $user->email;
                $optional['config_owner'] = $this->setting_repository->findByKey('config_owner');
                dispatch(new \Modules\User\Jobs\SendResetPassword($this->email, $optional));
            } else {
                $optional['phone_number'] = phone2local('+' . $user->calling_code . $user->phone_number);
                // dispatch(new \Modules\User\Jobs\SendResetPassword($this->sms, $optional));
                $this->sms->send($optional['phone_number'], $optional['code']);
            }
            if (config('app.debug')) $user->verify_code = $optional['code'];

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_auth/forgot-checkotp",
     *   summary="Forgot Check OTP",
     *   operationId="usrAuthForgotCheckOTP",
     *   tags={"BackendUsrAuth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="email", type="string", example="admin@gmail.com"),
     *       @OA\Property(property="code", type="string", example="123456"),
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
    public function forgotCheckOTP() {
        try {
            // Check Valid
            $code = $this->request->get('code');
            $validatorErrors = $this->getValidator(compact('code'), ['code' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            list($errorKey, $user, $by_email) = $this->findByCredentials();
            if ($errorKey) return $this->respondWithErrorKey($errorKey);
            $reminder = $this->_auth->getReminderSMS($user, $code);
            if (!$reminder) return $this->errorWrongArgs('auth.reset_code_invalid');

            return $this->respondWithSuccess(true);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_auth/forgot-newpw",
     *   summary="Forgot New Password",
     *   operationId="usrAuthForgotNewPassword",
     *   tags={"BackendUsrAuth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="email", type="string", format="email", example="admin@gmail.com"),
     *       @OA\Property(property="password", type="string", example="12345678"),
     *       @OA\Property(property="code", type="string", example="123456"),
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
    public function forgotNewPassword() {
        try {
            $code = $this->request->get('code');
            $password = $this->request->get('password');
            // Check Valid
            $validatorErrors = $this->getValidator(compact('code', 'password'), ['password' => 'required', 'code' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            list($errorKey, $user, $by_email) = $this->findByCredentials();
            if ($errorKey) return $this->respondWithErrorKey($errorKey);
            /*if ($this->model_repository->checkOldPassword($user, $input['password'])) {
                return response(['data' => 'Your new password cannot be the same as your previous 14 Passwords. Please choose a new password.'], 403);
            }*/
            if (!$this->_auth->getReminderSMS($user)) return $this->errorWrongArgs('auth.reset_code_invalid');
            $user = $this->_auth->completeResetPasswordSMS($user, $code, $password);
            if (!$user) return $this->errorWrongArgs('auth.reset_failed');
            //$this->model_repository->addLastPassword($user, $input['password']);

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_auth/profile-change",
     *   summary="Profile Change",
     *   operationId="usrAuthProfileChange",
     *   tags={"BackendUsrAuth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="prefix", type="integer", example=0),
     *       @OA\Property(property="first_name", type="string", example="First Name"),
     *       @OA\Property(property="gender", type="integer", example="male:1, female:2"),
     *       @OA\Property(property="birthday", type="string", example="1989-04-01"),
     *       @OA\Property(property="address", type="string", example=""),
     *       @OA\Property(property="is_notify", type="integer", example=0),
     *       @OA\Property(property="is_sms", type="integer", example=0),
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
    public function profileChange() {
        try {
            $user = $this->auth();
            //=== Input
            $input = $this->request->only(['prefix', 'first_name', 'gender', 'birthday', 'address', 'is_notify', 'is_sms']);
            //=== Check Valid
            /*$validatorErrors = $this->getValidator($input, $this->rulesForUpdate($model->id));
            if (!empty($validatorErrors)) {
                return $this->respondWithError($validatorErrors);
            }*/
            if (isset($input['first_name'])) $input['last_name'] = '';
            //=== Change number phone
            $phone_number = $this->request->get('phone_number');
            if ($phone_number) {
                list($calling_code, $phone_number) = calling2phone($phone_number);
                if (!$phone_number) return $this->errorWrongArgs('phone_number.invalid');
                //=== Is change ?
                if (!($user->calling_code == $calling_code && $user->phone_number == $phone_number)) {
                    //=== Check phone exist
                    $findExist = $this->model_repository->getModel()->where('id', '<>', $user->id)->whereRaw("(`calling_code` = ? and `phone_number` = ?)", [$calling_code, $phone_number])->first();
                    if ($findExist) {
                        return $this->errorWrongArgs('phone_number.exists');
                    } else {
                        $input['calling_code'] = $calling_code;
                        $input['phone_number'] = $phone_number;
                    }
                }
            }
            // Update Model
            $user = $this->model_repository->update($user, $input);
            //=== Update device
            $this->updateDeviceToken($user);
            // Update device table
            $credentials = $this->getDeviceInfo($input);
            if (!empty($credentials['device_platform']) && !empty($credentials['device_token'])) {
                $persistence = \Sentinel::getPersistenceRepository()->createModel()->newQuery()->where('user_id', $user->id)->where('code', $this->access_token)->first();
                if ($persistence) {
                    $optional = [];
                    foreach (['name', 'model', 'device_platform', 'device_token', 'ip'] as $field) {
                        if (isset($credentials[$field])) $optional[$field] = $credentials[$field];
                    }
                    foreach ($optional as $field => $value) $persistence->{$field} = $value;
                    $persistence->save();
                }
                return $this->respondWithSuccess($persistence);
            }
            // Logger
            /*if (isset($input['cover']) && !empty($input['cover'])) {
                History::logger($this->auth->id, LOGGER_USER_COVER_CHANGED, $user->cover_url);
            }
            if (isset($input['avatar']) && !empty($input['avatar'])) {
                History::logger($this->auth->id, LOGGER_USER_AVATAR_CHANGED, $user->avatar_url);
            }*/

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_auth/phone-change",
     *   summary="Change Number Phone",
     *   operationId="usrAuthChangeNumberPhone",
     *   tags={"BackendUsrAuth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="phone_number", type="string", example="0979750599")
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
    public function phoneChange() {
        try {
            $model = $this->auth();
            $phone_number = $this->request->get('phone_number');
            // Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), ['phone_number' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            list($calling_code, $phone_number) = calling2phone($phone_number);
            if (!$phone_number) return $this->errorWrongArgs('phone_number.invalid');
            //=== Not change
            if ($model->calling_code == $calling_code && $model->phone_number == $phone_number) return $this->errorWrongArgs('phone_number.not_change');
            //=== Check phone exist
            $findExist = $this->model_repository->getModel()->where('id', '<>', $model->id)->whereRaw("(`calling_code` = ? and `phone_number` = ?)", [$calling_code, $phone_number])->first();
            if ($findExist) return $this->errorWrongArgs('phone_number.exists');
            //=== Check platform and Send OTP
            $reminder = \ReminderSMS::join('reminder__logs AS log', function($join) {
                $join->on('log.user_id', '=', 'reminder__sms.user_id')->where('log.code', '=', \DB::raw('`reminder__sms`.`code`'));
            })->where('reminder__sms.user_id', $model->id)
                ->where('log.type', 'phone_number')->where('reminder__sms.completed', 0)->where('log.created_at', '>', date('Y-m-d H:i:s', strtotime('-10 minute')))
                ->select(['reminder__sms.code'])
                ->first();
            $global_phone_number = phone2local('+' . $calling_code . $phone_number);
            if ($reminder) {
                $code = $reminder->code;
            } else {
                $code = $this->_auth->createReminderSMSWithData($model, 'phone_number', ['phone_number' => $global_phone_number]);
            }
            $this->sms->send($global_phone_number, "Ma xac thuc: $code");
            if (config('app.debug')) $model->sms_code = $code;

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_auth/phone-verify",
     *   summary="Verify Number Phone By SmsCode",
     *   operationId="usrAuthVerifyNumberPhone",
     *   tags={"BackendUsrAuth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="code", type="string", example="123456")
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
    public function phoneVerify() {
        try {
            // Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), ['code' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $success = $this->_auth->completeChangeBySMS($this->auth(), 'phone_number', $this->request->get('code'));
            if ($success) {
                return $this->respondWithSuccess($success);
            } else {
                return $this->respondWithErrorKey('phone_number.change_failed');
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_auth/email-change",
     *   summary="Change email",
     *   operationId="usrAuthChangeEmail",
     *   tags={"BackendUsrAuth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"email"},
     *       @OA\Property(property="email", type="string", format="email", example="admin@gmail.com")
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
    public function emailChange() {
        try {
            $user = $this->auth();
            $email = $this->request->get('email');
            //=== Check Valid
            $validatorErrors = $this->getValidator(compact('email'), ['email' => "required|email"]);
            if (!empty($validatorErrors)) {
                return $this->respondWithError($validatorErrors);
            }
            $email = strtolower(trim((string)$email));
            //=== Check Valid
            $validatorErrors = $this->getValidator(compact('email'), ['email' => "unique:users,email," . $user->id . ",id"]);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            if ($email == $user->email) return $this->respondWithErrorKey('email.not_change');
            if (strpos($user->email, config('appsystems.mail.suffix')) !== false) return $this->respondWithErrorKey('email.denied');
            //=== Check platform and Send OTP
            $code = $this->_auth->createReminderSMSWithData($user, 'email', ['email' => $email]);
            dispatch(new \Modules\User\Jobs\SendChangeEmail($this->email, $user, compact('code')));
            if (config('app.debug')) $user->sms_code = $code;

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_auth/email-verify",
     *   summary="Verify Email By SmsCode",
     *   operationId="usrAuthVerifyEmail",
     *   tags={"BackendUsrAuth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="code", type="string", example="123456")
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
    public function emailVerify() {
        try {
            $user = $this->auth();
            //=== Input
            $code = $this->request->get('code');
            // Check Valid
            $validatorErrors = $this->getValidator(compact('code'), ['code' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $success = $this->_auth->completeChangeBySMS($user, 'email', $code);
            if ($success) {
                return $this->respondWithSuccess($success);
            } else {
                return $this->respondWithErrorKey('email.change_failed');
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_auth/email-check",
     *   summary="Email Check Exist",
     *   operationId="usrAuthEmailCheck",
     *   tags={"BackendUsrAuth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="email", type="string", format="email", example="admin@gmail.com"),
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
    public function emailCheck() {
        try {
            //=== Input
            $email = $this->request->get('email');
            // Check Valid
            $validatorErrors = $this->getValidator(compact('email'), ['email' => 'required|email']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $model = $this->model_repository->findByAttributes(['email' => $email]);
            if ($model) return $this->respondWithErrorKey('user_id.exists');

            return $this->respondWithSuccess(true);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
