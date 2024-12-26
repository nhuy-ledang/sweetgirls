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
use Modules\User\Repositories\DeviceTokenRepository;
use Modules\User\Repositories\ReminderPhoneNumberRepository;
use Modules\User\Repositories\SocialRepository;
use Modules\User\Repositories\UserRepository;
use Modules\System\Repositories\SettingRepository;
use Sentinel;

/**
 * Class AuthController
 *
 * @package Modules\User\Http\Controllers\ApiPublic
 * @author Huy D <huydang1920@gmail.com>
 * @OA\Info(title="Tedfast Swagger API", version="1.0.0")
 */
class AuthController extends ApiBaseModuleController {
    /**
     * @var \Modules\User\Repositories\Authentication
     */
    protected $authentication;

    /**
     * @var SocialRepository
     */
    protected $social_repository;

    /**
     * @var \Modules\User\Repositories\ReminderPhoneNumberRepository
     */
    protected $reminder_phone_number_repository;

    /**
     * @var DeviceTokenRepository
     */
    protected $device_token_repository;

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
                                SocialRepository $social_repository,
                                ReminderPhoneNumberRepository $reminder_phone_number_repository,
                                DeviceTokenRepository $device_token_repository,
                                NotificationRepository $notification_repository,
                                SettingRepository $setting_repository,
                                OrderRepository $order_repository) {
        $this->model_repository = $user_repository;
        $this->authentication = $authentication;
        $this->social_repository = $social_repository;
        $this->reminder_phone_number_repository = $reminder_phone_number_repository;
        $this->device_token_repository = $device_token_repository;
        $this->notification_repository = $notification_repository;
        $this->setting_repository = $setting_repository;
        $this->order_repository = $order_repository;
        $this->voucher_repository = $voucher_repository;

        $this->middleware('auth.user')->only(['index', 'logout', 'passwordChange', 'verifyPassword', 'phoneChange', 'phoneVerify', 'emailChange', 'emailVerify', 'profileChange', 'qrcode', 'createShareCode', 'getInviteHistory']);

        parent::__construct($request);
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

    private function loginAfter(&$user) {
        $this->setProductViewed($user->id);
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

    /**
     * @param $access_token
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function loginFacebookWithAccessToken($access_token) {
        $is_create_new = false;
        $client = new \GuzzleHttp\Client();
        // Step 2. Retrieve profile information about the current user.
        $fields = 'id,email,first_name,last_name,link,name,gender,picture.height(480),birthday';
        $profileResponse = $client->request('GET', 'https://graph.facebook.com/v10.0/me', ['query' => ['access_token' => $access_token, 'fields' => $fields]]);
        $profile = json_decode($profileResponse->getBody(), true);
        /*if(!isset($profile['email'])) {
            return 1;
        }*/
        // Step 3. If user is already signed in then link accounts.
        $social = $this->social_repository->findByAttributes(['provider' => 'facebook', 'social_id' => $profile['id']]);
        // Step 4. Get Params
        $fullname = isset($profile['name']) ? $profile['name'] : $profile['first_name'];
        $userInput = [
            'email'          => !empty($profile['email']) ? $profile['email'] : null,
            'email_verified' => true,
            'first_name'     => $fullname,
            //'first_name'    => $profile['first_name'],
            'last_name'      => '',
            //'last_name'     => $profile['last_name'],
            'fullname'       => $fullname,
            'last_provider'  => 'facebook',
        ];
        if (isset($profile['gender'])) $userInput['gender'] = $profile['gender'] == 'male' ? 1 : 2;
        if (isset($profile['birthday'])) $userInput['birthday'] = date('Y-m-d', strtotime($profile['birthday']));
        if (isset($profile['picture']) && isset($profile['picture']['data']) && isset($profile['picture']['data']['url'])) $userInput['avatar_url'] = $profile['picture']['data']['url'];
        // If facebook private
        if (empty($profile['email'])) {
            if ($social) {
                $user = $social->user;
            } else {
                $userInput['email'] = $profile['id'] . config('appsystems.mail.suffix');
                $userInput['email_verified'] = false;
                $userInput['username'] = $profile['id'] . $userInput['last_provider'];
                $userInput['password'] = Str::random(40);
                // Create user
                $user = $this->model_repository->createWithRoles($userInput, [USER_ROLE_USER], true);
                $is_create_new = true;
                // Create social
                $social = $this->social_repository->create(['user_id' => $user->id, 'social_id' => $profile['id'], 'provider' => 'facebook']);
            }
        } else {
            $user = $this->model_repository->findByAttributes(['email' => $profile['email']]);
        }
        if ($userInput['email'] && !isset($userInput['username']) && (!$user || ($user && !$user->username))) {
            $e = explode('@', $userInput['email']);
            $username = $e[0];
            if (!$this->model_repository->findByAttributes(['username' => $username])) {
                $userInput['username'] = $username;
            } else {
                $userInput['username'] = $username . $profile['id'];
            }
        }
        if (!$user) {
            $userInput['password'] = Str::random(40);
            // Create user
            $user = $this->model_repository->createWithRoles($userInput, [USER_ROLE_USER], true);
            $is_create_new = true;
            // Rollback social
            if ($social) {
                $social->delete();
                $social = null;
            }
        } else {
            if (!empty($profile['email']) && strpos($user->email, config('appsystems.mail.suffix')) > 0) $userInput['email'] = $profile['email'];
            if ($user->first_name) {
                unset($userInput['first_name']);
                unset($userInput['last_name']);
            }
            $user = $this->model_repository->update($user, $userInput);
            // Rollback social
            if ($social && $social->user_id != $user->id) {
                $social->delete();
                $social = null;
            }
        }
        if (!$social) {
            $socialInput = ['user_id' => $user->id, 'social_id' => $profile['id'], 'provider' => $userInput['last_provider']];
            if (isset($userInput['avatar_url'])) $socialInput['avatar'] = $userInput['avatar_url'];
            $this->social_repository->create($socialInput);
        } else {
            if (isset($userInput['avatar_url'])) {
                $social->avatar = $userInput['avatar_url'];
                $social->save();
            }
        }
        //=== Try login
        $credentials = ['last_provider' => $userInput['last_provider']];
        $this->getDeviceInfo($credentials);
        list($errorKey, $user, $persistences) = $this->_auth->loginByUser($user, $credentials);
        $access_token = !$errorKey ? $persistences : null;
        if (!$errorKey) {
            $this->complete($user);
            //=== Update device
            $this->updateDeviceToken($user, $credentials);
            //=== Get last persistence => the newest one for current login request
            /*$persistence = $user->persistences()->latest()->first();
            $user->{$this->apiKeyName} = $persistence->code;*/
            $user->{config('user.users.api_key_name')} = $access_token;

            if (config('app.debug')) $user->socialData = $profile;
        }

        return [$errorKey, $user, $persistences, $is_create_new];
    }

    /**
     * @OA\Post(
     *   path="/auth/facebook",
     *   summary="Login with Facebook",
     *   description="Login with Facebook",
     *   operationId="facebook",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Login with Facebook",
     *     @OA\JsonContent(
     *       required={"clientId","code","redirectUri"},
     *       @OA\Property(property="clientId", type="string", example="178860573004530"),
     *       @OA\Property(property="code", type="string", example="AQAzL3A8p_r8HN19Xr5MwrdC8lkLr3FZvuYx7-hGNbm2bAjaBbPqfDlxiF43_75TVVPA5Es32mRpheSGFOU7oEIEYqL4DENPZq6-pQDlUC7lTBrfiL2VSL6FoxLjQi-ZApdFugUzqxcAvtZSV1CT5b_GNk7Ki2Q_UXvNRmYWM0wwCAQezcYB6lFZI5hB6wmFKFrguPm4ss_N-9x68Xk0nymoMvfGXUJ-jVo5yx2GQxSWqdqhVcJg63F3lzlcaUMlL7GypKp3yC_XqFfVwmrGkh4cix5kt7gxdOCa5V9u1apZwx2NMbN3WYkuwlyp3oJ_z6vveMfvBws2pDXuKpyp9STtM7YQjnnfz8iwpfh0ubbxyg"),
     *       @OA\Property(property="redirectUri", type="string", example="http://localhost/startup/hocodau-web/oauth/oauthcallback.html"),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     * Login with Facebook.
     */
    public function facebook() {
        try {
            $app_env = app_env();
            $params = [
                'code'          => $this->request->get('code'),
                'client_id'     => $this->request->get('clientId'),
                'redirect_uri'  => $this->request->get('redirectUri'),
                'client_secret' => config("appsystems.$app_env.facebook.client_secret"),
            ];
            //==== Check Valid
            $validatorErrors = $this->getValidator($params, ['client_id' => 'required', 'code' => 'required', 'redirect_uri' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // $curl = new \Curl\Curl();
            // $curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
            $client = new \GuzzleHttp\Client();
            // Step 1. Exchange authorization code for access token.
            $accessTokenResponse = $client->request('GET', 'https://graph.facebook.com/v10.0/oauth/access_token', ['query' => $params]);
            $accessToken = json_decode($accessTokenResponse->getBody(), true);
            list($errorKey, $user, $persistences, $is_create_new) = $this->loginFacebookWithAccessToken($accessToken['access_token']);
            if ($errorKey) {
                if ($errorKey == 'auth.device_limit') { //=== Check three devices
                    return $this->errorDeviceLimit($persistences);
                } else if ($errorKey == 'auth.old_software') { //=== Update version for device_id
                    return $this->errorUnauthorized('auth.old_software');
                }
                return $this->errorWrongArgs($errorKey);
            } else if ($user === 1) {
                return $this->errorWrongArgs('email.required');
            } else if (!$user) {
                return $this->errorUnauthorized();
            }

            // Create Voucher
            if ($user && $is_create_new) $this->createVoucher($user);
            $this->loginAfter($user);

            // Create Infusionsoft Contact
            if ($is_create_new) dispatch(new \Modules\User\Jobs\CreateInfusionsoftContact($user));

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/facebook-token",
     *   summary="Login with Facebook By Token",
     *   description="Login with Facebook By Token",
     *   operationId="loginWithFacebookByToken",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Login with Facebook By Token",
     *     @OA\JsonContent(
     *       required={"access_token"},
     *       @OA\Property(property="access_token", type="string", example="EAANZAPiPvdKsBAP2pRTsmvafDsyEQLaMc3hzCJZBopaT7F8CXWy18rgQrVjosoZCWThTvmue4SNFth3TzuZC1qqTP774vAmDLL5aJXhnqSs5Bi9PIM2OZCdrAut2Qa37993sfUZBH8ekXIOKyAZAYLSWR1TyZCpgo9EYvnDqGZBhsAZBZC139nqC3cc"),
     *       @OA\Property(property="name", type="string", example="Galaxy J7"),
     *       @OA\Property(property="model", type="string", example="SM-J710F"),
     *       @OA\Property(property="device_id", type="string", example="8e43475dcc200477"),
     *       @OA\Property(property="App-Env", type="string", example="cms"),
     *       @OA\Property(property="Device-Platform", type="string", example="web"),
     *       @OA\Property(property="Device-Token", type="string", example="APA91bEZ4wyozxowl0g3Y2XBlpGRUOekBEs8hInUXtZgmYR8GGESyLGspjEeIlTVFBdJZ1cuKroKbi7w5S8TRXFAOJgRCZr0qNDZIhg3BLW52R4j10oFrsMXsrKVaqLcg3cbA-zn0j95")
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
    public function facebookByToken() {
        try {
            $access_token = $this->request->get('access_token');
            if (empty($access_token)) return $this->errorWrongArgs();
            list($errorKey, $user, $persistences, $is_create_new) = $this->loginFacebookWithAccessToken($access_token);
            if ($errorKey) {
                if ($errorKey == 'auth.device_limit') { //=== Check three devices
                    return $this->errorDeviceLimit($persistences);
                } else if ($errorKey == 'auth.old_software') { //=== Update version for device_id
                    return $this->errorUnauthorized('auth.old_software');
                }
                return $this->errorWrongArgs($errorKey);
            } else if ($user === 1) {
                return $this->errorWrongArgs('email.required');
            } else if (!$user) {
                return $this->errorUnauthorized();
            }

            $this->loginAfter($user);

            // Create Infusionsoft Contact
            if ($is_create_new) dispatch(new \Modules\User\Jobs\CreateInfusionsoftContact($user));

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
    //</editor-fold>

    //<editor-fold desc="Google">
    /**
     * @param $values
     * @param bool $primary
     * @return null
     */
    protected function getGoogleItemPrimary($values, $primary = true) {
        $data = null;
        foreach ($values as $v) {
            $data = $v;
            if ($primary && isset($v['metadata']['primary']) && $v['metadata']['primary'] == true) {
                break;
            }
        }
        return $data;
    }

    /***
     * @param $profileResponse
     * @return array
     */
    /*$profileResponse = [
        "resourceName"   => "people/104901635271098313415",
        "etag"           => "%EgoBAj0DBwgJPjcuGgQBAgUHIgxYNVZ6cXlCSUxoMD0=",
        "names"          => [
            [
                "metadata"             => [
                    "primary" => true,
                    "source"  => [
                        "type" => "PROFILE",
                        "id"   => "104901635271098313415"
                    ]
                ],
                "displayName"          => "Huy Đặng",
                "familyName"           => "Đặng",
                "givenName"            => "Huy",
                "displayNameLastFirst" => "Đặng, Huy",
                "unstructuredName"     => "Huy Đặng"
            ]
        ],
        "emailAddresses" => [
            [
                "metadata" => [
                    "primary"  => true,
                    "verified" => true,
                    "source"   => [
                        "type" => "ACCOUNT",
                        "id"   => "104901635271098313415"
                    ]
                ],
                "value"    => "huydang1920@gmail.com"
            ],
            [
                "metadata" => [
                    "verified" => true,
                    "source"   => [
                        "type" => "ACCOUNT",
                        "id"   => "104901635271098313415"
                    ]
                ],
                "value"    => "huydang1920@yahoo.com"
            ]
        ],
        "genders"        => [
            [
                "metadata"       => [
                    "primary" => true,
                    "source"  => [
                        "type" => "PROFILE",
                        "id"   => "104901635271098313415"
                    ]
                ],
                "value"          => "male",
                "formattedValue" => "Male"
            ]
        ],
        "birthdays"      => [
            [
                "metadata" => [
                    "primary" => true,
                    "source"  => [
                        "type" => "PROFILE",
                        "id"   => "104901635271098313415"
                    ]
                ],
                "date"     => [
                    "month" => 3,
                    "day"   => 20
                ]
            ],
            [
                "metadata" => [
                    "source" => [
                        "type" => "ACCOUNT",
                        "id"   => "104901635271098313415"
                    ]
                ],
                "date"     => [
                    "year"  => 1989,
                    "month" => 3,
                    "day"   => 20
                ]
            ]
        ],
        "photos"         => [
            [
                "metadata" => [
                    "primary" => true,
                    "source"  => [
                        "type" => "PROFILE",
                        "id"   => "104901635271098313415"
                    ]
                ],
                "url"      => "https=>//lh3.googleusercontent.com/a-/AOh14GizXrXVTPGiXgjkZGlPJbjNbqcvV4_Lk79x1vD6Fg=s100"
            ]
        ]
    ];*/
    protected function getGoogleOldProfile(array $profileResponse) {
        $profile = [];
        if (isset($profileResponse['resourceName'])) {
            $subs = explode('/', $profileResponse['resourceName']);
            $profile['sub'] = $subs[count($subs) - 1];
        }
        if (isset($profileResponse['emailAddresses'])) {
            $emailAddresses = $this->getGoogleItemPrimary($profileResponse['emailAddresses']);
            if ($emailAddresses) $profile['email'] = $emailAddresses['value'];
        }
        if (isset($profileResponse['names'])) {
            $names = $this->getGoogleItemPrimary($profileResponse['names']);
            if ($names) $profile['name'] = $names['displayName'];
        }
        if (isset($profileResponse['genders'])) {
            $genders = $this->getGoogleItemPrimary($profileResponse['genders']);
            if ($genders) $profile['gender'] = $genders['value'];
        }
        if (isset($profileResponse['photos'])) {
            $photos = $this->getGoogleItemPrimary($profileResponse['photos']);
            if ($photos) $profile['picture'] = str_replace('https=>//', 'https://', $photos['url']);
        }
        if (isset($profileResponse['birthdays'])) {
            $birthdays = $this->getGoogleItemPrimary($profileResponse['birthdays'], false);
            if ($birthdays) {
                $dates = $birthdays['date'];
                if (count($dates) == 3) $profile['birthday'] = $dates['year'] . '-' . $dates['month'] . '-' . $dates['day'];
            }
        }

        return $profile;
    }

    /**
     * @param array $profile
     * @return array
     */
    protected function loginGoogleWithAccessToken($profile = []) {
        $is_create_new = false;
        $userInput = [
            'email'          => $profile['email'],
            'email_verified' => true,
            'first_name'     => $profile['name'],
            //'first_name'     => $profile['given_name'],
            'last_name'      => '',
            //'last_name'      => $profile['family_name'],
            'fullname'       => $profile['name'],
            'last_provider'  => 'google',
        ];
        if (isset($profile['gender'])) {
            $userInput['gender'] = $profile['gender'] == 'male' ? 1 : 2;
        }
        if (isset($profile['birthday'])) {
            $userInput['birthday'] = date('Y-m-d', strtotime($profile['birthday']));
        }
        if (isset($profile['picture'])) {
            $userInput['avatar_url'] = str_replace('=s100', '=s480',
                str_replace('=s50', '=s480',
                    str_replace('/s50/', '/s480/', str_replace('sz=50', 'sz=480', $profile['picture']))
                )
            );
        }
        $social = $this->social_repository->findByAttributes(['provider' => 'google', 'social_id' => $profile['sub']]);
        $user = $this->model_repository->findByAttributes(['email' => $profile['email']]);
        if (!$user || ($user && !$user->username)) {
            $e = explode('@', $userInput['email']);
            $username = $e[0];
            if (!$this->model_repository->findByAttributes(['username' => $username])) {
                $userInput['username'] = $username;
            } else {
                $userInput['username'] = $username . $profile['sub'];
            }
        }
        if (!$user) {
            $userInput['password'] = Str::random(40);
            // Create user
            $user = $this->model_repository->createWithRoles($userInput, [USER_ROLE_USER], true);
            $is_create_new = true;
            // Rollback social
            if ($social) {
                $social->delete();
                $social = null;
            }
        } else {
            if ($user->first_name) {
                unset($userInput['first_name']);
                unset($userInput['last_name']);
            }
            $user = $this->model_repository->update($user, $userInput);
            // Rollback social
            if ($social && $social->user_id != $user->id) {
                $social->delete();
                $social = null;
            }
        }
        if (!$social) {
            $socialInput = [
                'user_id'   => $user->id,
                'social_id' => $profile['sub'],
                'provider'  => $userInput['last_provider'],
            ];
            if (isset($userInput['avatar_url'])) $socialInput['avatar'] = $userInput['avatar_url'];
            $this->social_repository->create($socialInput);
        } else {
            if (isset($userInput['avatar_url'])) {
                $social->avatar = $userInput['avatar_url'];
                $social->save();
            }
        }
        //=== Try login
        $credentials = ['last_provider' => $userInput['last_provider']];
        $this->getDeviceInfo($credentials);
        list($errorKey, $user, $persistences) = $this->_auth->loginByUser($user, $credentials);
        $access_token = !$errorKey ? $persistences : null;
        if (!$errorKey) {
            $this->complete($user);
            //=== Update device
            $this->updateDeviceToken($user, $credentials);
            //=== Get last persistence => the newest one for current login request
            /*$persistence = $user->persistences()->latest()->first();
            $user->{$this->apiKeyName} = $persistence->code;*/
            $user->{config('user.users.api_key_name')} = $access_token;

            if (config('app.debug')) $user->socialData = $profile;
        }

        return [$errorKey, $user, $persistences, $is_create_new];
    }

    /**
     * @OA\Post(
     *   path="/auth/google",
     *   summary="Login with Google",
     *   description="Login with Google",
     *   operationId="google",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Login with Google",
     *     @OA\JsonContent(
     *       required={"clientId","code","redirectUri"},
     *       @OA\Property(property="clientId", type="string", example="178860573004530"),
     *       @OA\Property(property="code", type="string", example="AQAzL3A8p_r8HN19Xr5MwrdC8lkLr3FZvuYx7-hGNbm2bAjaBbPqfDlxiF43_75TVVPA5Es32mRpheSGFOU7oEIEYqL4DENPZq6-pQDlUC7lTBrfiL2VSL6FoxLjQi-ZApdFugUzqxcAvtZSV1CT5b_GNk7Ki2Q_UXvNRmYWM0wwCAQezcYB6lFZI5hB6wmFKFrguPm4ss_N-9x68Xk0nymoMvfGXUJ-jVo5yx2GQxSWqdqhVcJg63F3lzlcaUMlL7GypKp3yC_XqFfVwmrGkh4cix5kt7gxdOCa5V9u1apZwx2NMbN3WYkuwlyp3oJ_z6vveMfvBws2pDXuKpyp9STtM7YQjnnfz8iwpfh0ubbxyg"),
     *       @OA\Property(property="redirectUri", type="string", example="http://localhost/startup/hocodau-web/oauth/oauthcallback.html")
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     * Login with Google.
     */
    public function google() {
        try {
            $app_env = app_env();
            $params = [
                'code'          => $this->request->get('code'),
                'client_id'     => $this->request->get('clientId'),
                'redirect_uri'  => $this->request->get('redirectUri'),
                'client_secret' => config("appsystems.$app_env.google.client_secret"),
                'grant_type'    => 'authorization_code',
            ];

            //==== Check Valid
            $validatorErrors = $this->getValidator($params, ['client_id' => 'required', 'code' => 'required', 'redirect_uri' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Step 1. Exchange authorization code for access token.
            // Call Google API
            $client = new \Google_Client();
//            $client->setApplicationName('Login to CodexWorld.com');
            $client->setClientId($params['client_id']);
            $client->setClientSecret($params['client_secret']);
            $client->setRedirectUri($params['redirect_uri']);
            // Auth
            $client->authenticate($params['code']);
            // Returns a Guzzle HTTP Client
            $httpClient = $client->authorize();
            // Step 2. Retrieve profile information about the current user.
            $profileResponse = $httpClient->get('https://people.googleapis.com/v1/people/me?personFields=emailAddresses,names,birthdays,genders,photos');
            //$profileResponse = $httpClient->get('https://www.googleapis.com/plus/v1/people/me/openIdConnect');
            $profile = json_decode($profileResponse->getBody(), true);
            // Check error
            if (isset($profile['error']) && isset($profile['error']['message'])) return $this->errorUnauthorized($profile['error']['message']);
            // Convert new version
            $profile = $this->getGoogleOldProfile($profile);
            list($errorKey, $user, $persistences, $is_create_new) = $this->loginGoogleWithAccessToken($profile);
            if ($errorKey) {
                if ($errorKey == 'auth.device_limit') { //=== Check three devices
                    return $this->errorDeviceLimit($persistences);
                } else if ($errorKey == 'auth.old_software') { //=== Update version for device_id
                    return $this->errorUnauthorized('auth.old_software');
                }
                return $this->errorWrongArgs($errorKey);
            } else if (!$user) {
                return $this->errorUnauthorized();
            }

            // Create Voucher
            if ($user && $is_create_new) $this->createVoucher($user);
            $this->loginAfter($user);

            // Create Infusionsoft Contact
            if ($is_create_new) dispatch(new \Modules\User\Jobs\CreateInfusionsoftContact($user));

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/google-token",
     *   summary="Login with Google By Token",
     *   description="Login with Google By Token",
     *   operationId="loginWithGoogleByToken",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Login with Google By Token",
     *     @OA\JsonContent(
     *       required={"access_token"},
     *       @OA\Property(property="access_token", type="string", example="EAANZAPiPvdKsBAP2pRTsmvafDsyEQLaMc3hzCJZBopaT7F8CXWy18rgQrVjosoZCWThTvmue4SNFth3TzuZC1qqTP774vAmDLL5aJXhnqSs5Bi9PIM2OZCdrAut2Qa37993sfUZBH8ekXIOKyAZAYLSWR1TyZCpgo9EYvnDqGZBhsAZBZC139nqC3cc"),
     *       @OA\Property(property="name", type="string", example="Galaxy J7"),
     *       @OA\Property(property="model", type="string", example="SM-J710F"),
     *       @OA\Property(property="device_id", type="string", example="8e43475dcc200477"),
     *       @OA\Property(property="App-Env", type="string", example="cms"),
     *       @OA\Property(property="Device-Platform", type="string", example="web"),
     *       @OA\Property(property="Device-Token", type="string", example="APA91bEZ4wyozxowl0g3Y2XBlpGRUOekBEs8hInUXtZgmYR8GGESyLGspjEeIlTVFBdJZ1cuKroKbi7w5S8TRXFAOJgRCZr0qNDZIhg3BLW52R4j10oFrsMXsrKVaqLcg3cbA-zn0j95")
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
    public function googleByToken() {
        try {
            $access_token = $this->request->get('access_token');
            if (empty($access_token)) return $this->errorWrongArgs();
            $client = new \GuzzleHttp\Client();
            // Step 2. Retrieve profile information about the current user.
            //$profileResponse = $client->request('GET', 'https://www.googleapis.com/plus/v1/people/me/openIdConnect', [
            $profileResponse = $client->request('GET', 'https://people.googleapis.com/v1/people/me?personFields=emailAddresses,names,birthdays,genders,photos', ['headers' => ['Authorization' => 'Bearer ' . $access_token]]);
            $profile = json_decode($profileResponse->getBody(), true);
            // Convert new version
            $profile = $this->getGoogleOldProfile($profile);
            list($errorKey, $user, $persistences, $is_create_new) = $this->loginGoogleWithAccessToken($profile);
            if ($errorKey) {
                if ($errorKey == 'auth.device_limit') { //=== Check three devices
                    return $this->errorDeviceLimit($persistences);
                } else if ($errorKey == 'auth.old_software') { //=== Update version for device_id
                    return $this->errorUnauthorized('auth.old_software');
                }
                return $this->errorWrongArgs($errorKey);
            } else if (!$user) {
                return $this->errorUnauthorized();
            }

            $this->loginAfter($user);

            // Create Infusionsoft Contact
            if ($is_create_new) dispatch(new \Modules\User\Jobs\CreateInfusionsoftContact($user));

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
    //</editor-fold">

    //<editor-fold desc="Apple">
    protected function fetchAppleData($url, $params = false) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($params) curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'User-Agent: curl', # Apple requires a user agent header at the token endpoint
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    /**
     * @param array $profile
     * @return array
     */
    protected function loginAppleAccessToken($profile = []) {
        $is_create_new = false;
        $userInput = ['email' => $profile['email'], 'email_verified' => true, 'last_provider' => 'apple'];
        if (isset($profile['gender'])) {
            $userInput['gender'] = $profile['gender'] == 'male' ? 1 : 2;
        }
        if (isset($profile['birthday'])) {
            $userInput['birthday'] = date('Y-m-d', strtotime($profile['birthday']));
        }
        $social = $this->social_repository->findByAttributes(['provider' => 'apple', 'social_id' => $profile['sub']]);
        $user = $this->model_repository->findByAttributes(['email' => $profile['email']]);
        if (!$user || ($user && !$user->username)) {
            $e = explode('@', $userInput['email']);
            $username = $e[0];
            if (!$this->model_repository->findByAttributes(['username' => $username])) {
                $userInput['username'] = $username;
            } else {
                $userInput['username'] = $username . $profile['sub'];
            }
        }
        if (!$user) {
            $userInput['password'] = Str::random(40);
            // Create user
            $user = $this->model_repository->createWithRoles($userInput, [USER_ROLE_USER], true);
            $is_create_new = true;
            // Rollback social
            if ($social) {
                $social->delete();
                $social = null;
            }
        } else {
            $user = $this->model_repository->update($user, $userInput);
            // Rollback social
            if ($social && $social->user_id != $user->id) {
                $social->delete();
                $social = null;
            }
        }
        if (!$social) {
            $socialInput = ['user_id' => $user->id, 'social_id' => $profile['sub'], 'provider' => $userInput['last_provider']];
            $this->social_repository->create($socialInput);
        }
        //=== Try login
        $credentials = ['last_provider' => $userInput['last_provider']];
        $this->getDeviceInfo($credentials);
        list($errorKey, $user, $persistences) = $this->_auth->loginByUser($user, $credentials);
        $access_token = !$errorKey ? $persistences : null;
        if (!$errorKey) {
            $this->complete($user);

            //=== Update device
            $this->updateDeviceToken($user, $credentials);

            //=== Get last persistence => the newest one for current login request
            /*$persistence = $user->persistences()->latest()->first();
            $user->{$this->apiKeyName} = $persistence->code;*/
            $user->{config('user.users.api_key_name')} = $access_token;

            if (config('app.debug')) $user->socialData = $profile;
        }

        return [$errorKey, $user, $persistences, $is_create_new];
    }

    /**
     * @OA\Post(
     *   path="/auth/apple",
     *   summary="Login with Apple",
     *   description="Login with Apple",
     *   operationId="apple",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Login with Apple",
     *     @OA\JsonContent(
     *       required={"clientId","code","redirectUri"},
     *       @OA\Property(property="clientId", type="string", example="devmanage.hocdau.vn"),
     *       @OA\Property(property="code", type="string", example=""),
     *       @OA\Property(property="redirectUri", type="string", example="http://local.sweetgirl.vn/oauth/acallback.html"),
     *       @OA\Property(property="name", type="string", example="Galaxy J7"),
     *       @OA\Property(property="model", type="string", example="SM-J710F"),
     *       @OA\Property(property="device_id", type="string", example="8e43475dcc200477"),
     *       @OA\Property(property="Device-Token", type="string", example="APA91bEZ4wyozxowl0g3Y2XBlpGRUOekBEs8hInUXtZgmYR8GGESyLGspjEeIlTVFBdJZ1cuKroKbi7w5S8TRXFAOJgRCZr0qNDZIhg3BLW52R4j10oFrsMXsrKVaqLcg3cbA-zn0j95")
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     * Login with Apple.
     */
    public function apple() {
        try {
            $app_env = app_env();
            $params = [
                'grant_type'    => 'authorization_code',
                'code'          => $this->request->get('code'),
                'client_id'     => $this->request->get('clientId'),
                'redirect_uri'  => $this->request->get('redirectUri'),
                'client_secret' => config("appsystems.$app_env.apple.client_secret"),
            ];
            //==== Check Valid
            $validatorErrors = $this->getValidator($params, [
                'client_id' => 'required',
                'code'      => 'required',
                //'redirect_uri' => 'required',
            ]);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            /*$client = new \GuzzleHttp\Client();
            // Step 1. Exchange authorization code for access token.
            $response = $client->request('GET', 'https://appleid.apple.com/auth/token', [
                'query' => $params,
            ]);
            $response = json_decode($response->getBody());*/
            // Token endpoint docs:
            // https://developer.apple.com/documentation/signinwithapplerestapi/generate_and_validate_tokens
            $response = $this->fetchAppleData('https://appleid.apple.com/auth/token', $params);
            if (!isset($response->access_token)) return $this->errorWrongArgs();
            $claims = explode('.', $response->id_token);
            $data = [
                'header'    => json_decode(base64_decode($claims[0]), true),
                'payload'   => json_decode(base64_decode($claims[1]), true),
                'signature' => $claims[2],
            ];
            $profile = [
                'sub'   => $data['payload']['sub'],
                'email' => $data['payload']['email'],
            ];
            list($errorKey, $user, $persistences, $is_create_new) = $this->loginAppleAccessToken($profile);
            if ($errorKey) {
                if ($errorKey == 'auth.device_limit') { //=== Check three devices
                    return $this->errorDeviceLimit($persistences);
                } else if ($errorKey == 'auth.old_software') { //=== Update version for device_id
                    return $this->errorUnauthorized('auth.old_software');
                }
                return $this->errorWrongArgs($errorKey);
            } else if (!$user) {
                return $this->errorUnauthorized();
            }

            $this->loginAfter($user);

            // Create Infusionsoft Contact
            if ($is_create_new) dispatch(new \Modules\User\Jobs\CreateInfusionsoftContact($user));

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
    //</editor-fold">

    //<editor-fold desc="AccountKit">
    /**
     * @OA\Post(
     *   path="/auth/account-kit/login",
     *   summary="Login with AccountKit",
     *   description="Login with AccountKit",
     *   operationId="accountKit",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Login with AccountKit",
     *     @OA\JsonContent(
     *       required={"authorizedToken"},
     *       @OA\Property(property="authorizedToken", type="string", example="AQCaKGK6kz9cFHGSBqYyNDXj_DWCtAEMYFhear6IpjQ8k74ZJYGJHTEmmqG7K82MaQ_MKfyXo4sJgeLMcV4UmkxA2elVpNjLTkaF6ptXeJc1wWjzYvXJL91rv94W_XPjQZIK_PLgEtUUtmRAOR9WDWOpjQzO_NHVijRtImy8rMjdxQKiMG215ZCCy5DTrSpkY4_OwWCldO5m2ZcLOKhsLzICVmD-cA3hVC_Ezn4q9dPpOKK5vFKGsnHoRb34H-7BLtDxPNjVyobU-GcmTQ8RybRfNNE7y-WH_bsGSSjM_D6vbA"),
     *       @OA\Property(property="name", type="string", example="Galaxy J7"),
     *       @OA\Property(property="model", type="string", example="SM-J710F"),
     *       @OA\Property(property="device_id", type="string", example="8e43475dcc200477"),
     *       @OA\Property(property="App-Env", type="string", example="cms"),
     *       @OA\Property(property="Device-Platform", type="string", example="web"),
     *       @OA\Property(property="Device-Token", type="string", example="APA91bEZ4wyozxowl0g3Y2XBlpGRUOekBEs8hInUXtZgmYR8GGESyLGspjEeIlTVFBdJZ1cuKroKbi7w5S8TRXFAOJgRCZr0qNDZIhg3BLW52R4j10oFrsMXsrKVaqLcg3cbA-zn0j95")
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     * Login with AccountKit.
     */
    public function accountKitLogin() {
        try {
            $is_create_new = false;
            $params = [
                'authorizedToken' => $this->request->get('authorizedToken'),
            ];
            //==== Check Valid
            $validatorErrors = $this->getValidator($params, ['authorizedToken' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Initialize variables
            $app_env = app_env();
            $app_env = 'default';
            $app_id = config("appsystems.$app_env.facebook.client_id");
            $secret = config("appsystems.$app_env.account_kit.client_secret");
            $version = 'v1.0';

            // Exchange authorization code for access token
            $token_exchange_url = 'https://graph.accountkit.com/' . $version . '/access_token?grant_type=authorization_code&code=' . $params['authorizedToken'] . "&access_token=AA|$app_id|$secret";
            $data = $this->doCurl($token_exchange_url);
            if (!isset($data['id'])) return $this->errorUnauthorized();
            $user_id = $data['id'];
            $user_access_token = $data['access_token'];
            $refresh_interval = $data['token_refresh_interval_sec'];

            // Get Account Kit information
            $me_endpoint_url = 'https://graph.accountkit.com/' . $version . '/me?access_token=' . $user_access_token;
            $data = $this->doCurl($me_endpoint_url);
            $phone = isset($data['phone']) ? $data['phone']['number'] : '';
            $country_prefix = isset($data['phone']) ? $data['phone']['country_prefix'] : '';
            $email = isset($data['email']) ? $data['email']['address'] : '';
            if (!$phone) return $this->errorUnauthorized();
            $user = $this->model_repository->getModel()->where('phone_number', '=', $phone)->orWhere('phone_number', '=', str_replace("+$country_prefix", "0", $phone))->first();
            if (!$user) {
                $userInput = [
                    'phone_number' => $phone,
                    //'password'     => str_random(8),
                ];
                $user = $this->model_repository->createWithRoles($userInput, [USER_ROLE_USER], true);
                $is_create_new = true;
            }
            $user->last_provider = 'accountkit';
            $user->phone_verified = true;
            $user->save();

            //=== Try login
            $credentials = ['last_provider' => 'accountkit'];
            $this->getDeviceInfo($credentials);
            list($errorKey, $user, $persistences) = $this->_auth->loginByUser($user, $credentials);
            $access_token = !$errorKey ? $persistences : null;
            if ($errorKey) {
                if ($errorKey == 'auth.device_limit') { //=== Check three devices
                    return $this->errorDeviceLimit($persistences);
                } else if ($errorKey == 'auth.old_software') { //=== Update version for device_id
                    return $this->errorUnauthorized('auth.old_software');
                }
                return $this->errorWrongArgs($errorKey);
            } else if (!$user) {
                return $this->errorUnauthorized();
            }

            $this->complete($user);

            //=== Update device
            $this->updateDeviceToken($user, $credentials);

            //=== Get last persistence => the newest one for current login request
            /*$persistence = $user->persistences()->latest()->first();
            $user->{$this->apiKeyName} = $persistence->code;*/
            $user->{config('user.users.api_key_name')} = $access_token;
            if (config('app.debug')) $user->smsData = $data;

            $this->loginAfter($user);

            // Create Infusionsoft Contact
            if ($is_create_new) dispatch(new \Modules\User\Jobs\CreateInfusionsoftContact($user));

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/account-kit/check-phone",
     *   summary="AccountKit Check Phone",
     *   description="AccountKit Check Phone",
     *   operationId="accountKitCheckPhone",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="AccountKit Check Phone",
     *     @OA\JsonContent(
     *       required={"authorizedToken"},
     *       @OA\Property(property="authorizedToken", type="string", example="AQCaKGK6kz9cFHGSBqYyNDXj_DWCtAEMYFhear6IpjQ8k74ZJYGJHTEmmqG7K82MaQ_MKfyXo4sJgeLMcV4UmkxA2elVpNjLTkaF6ptXeJc1wWjzYvXJL91rv94W_XPjQZIK_PLgEtUUtmRAOR9WDWOpjQzO_NHVijRtImy8rMjdxQKiMG215ZCCy5DTrSpkY4_OwWCldO5m2ZcLOKhsLzICVmD-cA3hVC_Ezn4q9dPpOKK5vFKGsnHoRb34H-7BLtDxPNjVyobU-GcmTQ8RybRfNNE7y-WH_bsGSSjM_D6vbA"),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     * CheckPhone with AccountKit.
     */
    public function accountKitCheckPhone() {
        try {
            $params = [
                'authorizedToken' => $this->request->get('authorizedToken'),
            ];
            //==== Check Valid
            $validatorErrors = $this->getValidator($params, ['authorizedToken' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);

            // Initialize variables
            $app_env = app_env();
            $app_env = 'default';
            $app_id = config("appsystems.$app_env.facebook.client_id");
            $secret = config("appsystems.$app_env.account_kit.client_secret");
            $version = 'v1.0';

            // Exchange authorization code for access token
            $token_exchange_url = 'https://graph.accountkit.com/' . $version . '/access_token?grant_type=authorization_code&code=' . $params['authorizedToken'] . "&access_token=AA|$app_id|$secret";
            $data = $this->doCurl($token_exchange_url);
            if (!isset($data['id'])) return $this->errorWrongArgs();

            $user_access_token = $data['access_token'];

            // Get Account Kit information
            $me_endpoint_url = 'https://graph.accountkit.com/' . $version . '/me?access_token=' . $user_access_token;
            $data = $this->doCurl($me_endpoint_url);
            $phone = isset($data['phone']) ? $data['phone']['number'] : '';

            if (!$phone) return $this->errorWrongArgs();

            return $this->respondWithSuccess($phone);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

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

    //<editor-fold desc="Register">

    /**
     * Activate And Send OTP
     * @param \Modules\User\Entities\Sentinel\User $user
     * @param string $last_provider
     * @return \Modules\User\Entities\Sentinel\User
     */
    protected function activateAndSendOTP(\Modules\User\Entities\Sentinel\User $user, $last_provider = 'phone_number') {
        if (in_array($last_provider, ['email', 'phone_number'])) {
            //=== Create Activation
            $optional = ['code' => $this->_auth->createActivationSMS($user)];
            if ($last_provider == 'email') {
                $optional['email'] = $user->email;
                dispatch(new \Modules\User\Jobs\SendVerifyRegister($this->email, $optional));
            } else {
                $optional['phone_number'] = phone2local('+' . $user->calling_code . $user->phone_number);
                // dispatch(new \Modules\User\Jobs\SendVerifyRegister($this->sms, $optional));

                $message = "CUASOVANG: Ma xac thuc tai ONLLEARNING cua ban la: {$optional['code']}";

                $this->sms->send($optional['phone_number'], $message);
            }
            if (config('app.debug')) $user->verify_code = $optional['code'];
        }

        return $user;
    }

    /**
     * Get code
     * @param $user_id
     * @return string
     */
    private function createVoucherCode($user_id) {
        $code = strtoupper(str_random_alpha_numeric(1, false, false) . str_random_not_cap(8));
        $find = $this->voucher_repository->getModel()->where('user_id', $user_id)->where('code', $code)->first();
        if (!$find) {
            return $code;
        } else {
            return $this->createVoucherCode($user_id);
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
            $this->getDeviceInfo($input);
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
                $rules['email'] = 'required|email';//'required|email|unique:users,email';
                $input['last_provider'] = 'email';
            }
            $validatorErrors = $this->getValidator($input, $rules);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            //=== Check code
            if ($phone_number) {
                $reminder = $this->reminder_phone_number_repository->getReminder(['ip' => $this->request->server->get('REMOTE_ADDR'), 'phone_number' => phone2local('+' . $calling_code . $phone_number),], $verify_code);
                if (!$reminder) return $this->respondWithErrorKey('code.invalid');
                $input['phone_verified'] = true;
            }
            if ($phone_number && $email) {
                $whereQuery = '((`calling_code` = ? and `phone_number` = ?) or `email` = ?)';
                $whereParams = [$calling_code, $phone_number, $email];
            } else if ($phone_number) {
                $whereQuery = '(`calling_code` = ? and `phone_number` = ?)';
                $whereParams = [$calling_code, $phone_number];
            } else { // $email
                $whereQuery = '(`email` = ?)';
                $whereParams = [$email];
            }
            $user = $this->model_repository->getModel()->whereRaw($whereQuery, $whereParams)->first();
            // Check user status
            if ($user) {
                if ((!$email || ($email && $email == $user->email)) && $user->status == USER_STATUS_STARTER) {
                    $user = $this->model_repository->activateUser($user, $input, [USER_ROLE_USER], true);
                } else if ($user->status == USER_STATUS_BANNED) {
                    return $this->respondWithErrorKey('auth.banned');
                }/* else if ($this->_auth->activateCompleted($user)) {
                    return $this->respondWithErrorKey('auth.activated');
                }*/ else {
                    return $this->respondWithErrorKey('register.exists');
                }
            }
            // Check rules
            if (!$user) {
                if ($email) {
                    $e = explode('@', $email);
                    $username = $e[0];
                    if ($this->model_repository->findByAttributes(['username' => $username])) {
                        $username = phone2local($calling_code . $phone_number);
                    }
                } else {
                    $username = phone2local($calling_code . $phone_number);
                }
                $input['username'] = $username;
                $user = $this->model_repository->createWithRoles($input, [USER_ROLE_USER], true);
                // Create Voucher
                if ($user) $this->createVoucher($user);
            }
            //=== Remove all token
            $user->persistences()->delete();
            //=== Try login
            list($errorKey, $user, $persistences) = $this->_auth->loginByUser($user, $input, true);
            $access_token = !$errorKey ? $persistences : null;
            if ($errorKey) {
                if ($errorKey == 'auth.device_limit') { //=== Check three devices
                    return $this->errorDeviceLimit($persistences);
                } else if ($errorKey == 'auth.old_software') { //=== Update version for device_id
                    return $this->errorUnauthorized('auth.old_software');
                }
                return $this->errorUnauthorized();
            }
            /*//=== Check platform and Send OTP
            $user = $this->activateAndSendOTP($user);*/
            //=== Update device
            $this->updateDeviceToken($user, $input);
            //=== Get last persistence => the newest one for current login request
            /*$persistence = $user->persistences()->latest()->first();
            $user->{$this->apiKeyName} = $persistence->code;*/
            $user->{config('user.users.api_key_name')} = $access_token;

            $this->loginAfter($user);

            // Create Infusionsoft Contact
            dispatch(new \Modules\User\Jobs\CreateInfusionsoftContact($user));

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

    /**
     * @OA\Post(
     *   path="/auth/register-resend",
     *   summary="Register Resend OTP",
     *   description="Register Resend OTP",
     *   operationId="registerResend",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Register Resend OTP",
     *     @OA\JsonContent(
     *       required={"access_token"},
     *       @OA\Property(property="access_token", type="string", example="VvAvwhahxgS2Imp6F7ElqUdzae6oYNJB"),
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
    public function registerResend() {
        try {
            $access_token = $this->request->get('access_token');
            if (!$access_token) return $this->respondWithErrorKey('access_denied.missing');

            $user = $this->_auth->findByPersistenceCode($access_token);
            if (!$user) return $this->respondWithErrorKey('access_denied.invalid');

            //=== Check platform and Send OTP
            $user = $this->activateAndSendOTP($user);

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/register-email-resend",
     *   summary="Register Email Resend By SmsCode",
     *   description="Register Email Resend By SmsCode",
     *   operationId="registerEmailResend",
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
    public function registerEmailResend() {
        try {
            $user = $this->auth();
            if ($user->email_verified) return $this->respondWithErrorKey('email.verified');
            //=== Check platform and Send OTP
            $email = $user->email;
            $code = $this->_auth->createReminderSMSWithData($user, 'email', ['email' => $email]);
            $verify_link = config('app.url') . '/_api/settings/emailverify?c=' . $code . '&e=' . $email;
            dispatch(new \Modules\User\Jobs\SendVerifyRegister($this->email, compact('code', 'email', 'verify_link')));

            if (config('app.debug')) $user->verify_code = $code;

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/register-email-verify",
     *   summary="Register Email Verify By SmsCode",
     *   description="Register Email Verify By SmsCode",
     *   operationId="registerEmailVerify",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Register Email Verify By SmsCode",
     *     @OA\JsonContent(
     *       required={"code","email"},
     *       @OA\Property(property="code", type="string", example="123456"),
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
    public function registerEmailVerify() {
        try {
            //=== Input
            $code = $this->request->get('code');
            $email = $this->request->get('email');
            // Check Valid
            $validatorErrors = $this->getValidator(compact('code', 'email'), ['code' => 'required', 'email' => 'required|email']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $model = $this->_auth->findByEmail($email);
            if (!$model) return $this->respondWithErrorKey('user_id.invalid');
            if ($model->email_verified) return $this->respondWithErrorKey('email.verified');
            $success = $this->_auth->completeChangeBySMS($model, 'email', $code);
            if ($success) {
                return $this->respondWithSuccess($success);
            } else {
                return $this->respondWithErrorKey('auth.reset_code_invalid');
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
    //</editor-fold>

    //<editor-fold desc="Forgot">
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
        if (!$user) return ['auth.invalidd', null, $by_email];

        return [null, $user, $by_email];
    }

    /**
     * @OA\Post(
     *   path="/auth/forgot",
     *   summary="Forgot Password",
     *   description="Forgot Password",
     *   operationId="forgotPassword",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Forgot Password",
     *     @OA\JsonContent(
     *       required={"email"},
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
            $code = $this->_auth->createReminderSMS($user);
            $reset_link = config('app.url') . '/account/password_recover?recover_code=' . $code . '&email=' . $user->email;
            $optional = ['code' => $code, 'reset_link' => $reset_link];
            if ($by_email) {
                $optional['email'] = $user->email;
                $optional['config_owner'] = $this->setting_repository->findByKey('config_owner');
                $addresses = $this->setting_repository->findByKey('config_address');
                $optional['setting'] = [
                    'email_support' => $this->setting_repository->findByKey('config_email_support'),
                    'hotline'       => $this->setting_repository->findByKey('config_hotline'),
                    'phone_number'  => $this->setting_repository->findByKey('config_telephone'),
                    'address'       => $addresses && is_array($addresses) && isset($addresses[$this->locale]) ? $addresses[$this->locale] : '',
                    'facebook_url'  => $this->setting_repository->findByKey('config_facebook_url'),
                    'instagram_url' => $this->setting_repository->findByKey('config_instagram_url'),
                    'zalo_url'      => $this->setting_repository->findByKey('config_zalo_url'),
                    'config_owner'  => $this->setting_repository->findByKey('config_owner'),
                ];
                dispatch(new \Modules\User\Jobs\SendResetPassword($this->email, $optional));
            } else {
                $optional['phone_number'] = phone2local('+' . $user->calling_code . $user->phone_number);
                // dispatch(new \Modules\User\Jobs\SendResetPassword($this->sms, $optional));
                $this->sms->send($optional['phone_number'], $optional['code']);
            }
            if (config('app.debug')) {
                $user->verify_code = $code;
                $user->reset_link = $reset_link;
            }
            $user->by_email = $by_email;

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/forgot-checkotp",
     *   summary="Forgot Check OTP",
     *   description="Forgot Check OTP",
     *   operationId="forgotCheckOTP",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Forgot Password",
     *     @OA\JsonContent(
     *       required={"email","code"},
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
     *   path="/auth/forgot-newpw",
     *   summary="Forgot New Password",
     *   description="Forgot New Password",
     *   operationId="forgotNewPassword",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Forgot New Password",
     *     @OA\JsonContent(
     *       required={"email","password","code"},
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
    //</editor-fold>

    //<editor-fold desc="ChangePassword">
    /**
     * @OA\Post(
     *   path="/auth/pw-change",
     *   summary="Password Change",
     *   description="Password Change",
     *   operationId="passwordChange",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Password Change",
     *     @OA\JsonContent(
     *       required={"current_password","password"},
     *       @OA\Property(property="current_password", type="string", example="12345678"),
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
            $user = $this->auth();
            //=== Input
            $current_password = $this->request->get('current_password');
            $new_password = $this->request->get('password');
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
            if ($new_password == $current_password) return $this->respondWithErrorKey('password.invalid');
            if (!$this->model_repository->checkPassword($current_password, $user->password)) return $this->respondWithErrorKey('password.current_failed');
            //if (!$this->model_repository->checkOldPassword($user, $new_password)) return $this->respondWithErrorKey('password.old');
            // Instead of verifyPassword function
            $changed = $this->_auth->newPassword($user, $new_password);
            if (!$changed) return $this->respondWithErrorKey('password.change_failed');

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    //</editor-fold>

    //<editor-fold desc="ChangeNumberPhone">
    /**
     * @OA\Post(
     *   path="/auth/phone-change",
     *   summary="Change Number Phone",
     *   description="Change Number Phone",
     *   operationId="changeNumberPhone",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Change Number Phone",
     *     @OA\JsonContent(
     *       required={"phone_number"},
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
     *   path="/auth/phone-verify",
     *   summary="Verify Number Phone By SmsCode",
     *   description="Verify Number Phone By SmsCode",
     *   operationId="verifyNumberPhone",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Verify Number Phone By SmsCode",
     *     @OA\JsonContent(
     *       required={"code"},
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
    //</editor-fold>

    //<editor-fold desc="ChangeEmail">
    /**
     * @OA\Post(
     *   path="/auth/email-change",
     *   summary="Change email",
     *   description="Change email",
     *   operationId="changeEmail",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Change email",
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
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
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
     *   path="/auth/email-verify",
     *   summary="Verify Email By SmsCode",
     *   description="Verify Email By SmsCode",
     *   operationId="verifyEmail",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Verify Email By SmsCode",
     *     @OA\JsonContent(
     *       required={"code"},
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
     *   path="/auth/email-check",
     *   summary="Email Check Exist",
     *   description="Email Check Exist",
     *   operationId="emailCheck",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Email Check Exist",
     *     @OA\JsonContent(
     *       required={"email"},
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
            $model = $this->model_repository->findByAttributes(['email' => $email]);
            if ($model) return $this->respondWithErrorKey('user_id.exists');

            return $this->respondWithSuccess(true);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
    //</editor-fold>

    /**
     * @OA\Post(
     *   path="/auth/profile-change",
     *   summary="Profile Change",
     *   description="Profile Change",
     *   operationId="profileChange",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=false,
     *     description="Profile Change",
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
            $input = $this->request->only(['prefix', 'avatar', 'first_name', 'fullname', 'gender', 'birthday', 'address', 'phone_number', 'is_notify', 'is_sms', 'id_no', 'id_date', 'id_provider', 'id_address', 'id_front', 'id_behind', 'tax', 'card_holder', 'bank_number', 'bank_name', 'bank_id', 'bank_branch', 'bank_branch', 'paypal_number']);
            //=== Check Valid
            /*$validatorErrors = $this->getValidator($input, $this->rulesForUpdate($model->id));
            if (!empty($validatorErrors)) {
                return $this->respondWithError($validatorErrors);
            }*/

            // Check avatar
            list($avatar, $errorKey) = $this->getRequestFile('avatar','image');
            if ($errorKey) return $this->errorWrongArgs($errorKey);
            if ($avatar) {
                $original_name = FileHelper::limit($avatar->getClientOriginalName(), 59);
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $file_mime = finfo_file($finfo, $avatar);
                finfo_close($finfo);
                $path = '/users/' . $user->id . '/' . time() . '-' . $original_name;
                $ok = \Storage::disk(config('filesystems.default'))
                    ->getDriver()->put($path, fopen($avatar, 'r+'), [
                            'visibility'  => 'public',
                            'ContentType' => $file_mime,
                        ]
                    );
                if (!$ok) return $this->respondWithErrorKey('file.upload_error');
                $input['avatar'] = $path;

                // Remove old avatar
                if ($this->auth->avatar) {
                    $oldFile = $this->model_repository->findByAttributes([
                        'id' => $this->auth->id,
                        'avatar'  => $this->auth->avatar
                    ]);
                    if ($oldFile && \Storage::exists($oldFile->avatar)) \Storage::delete($oldFile->avatar);
                }
            }

            // Check file front
            list($file_front, $errorKey) = $this->getRequestFile('file_front');
            //if ($errorKey) return $this->errorWrongArgs($errorKey);
            if ($file_front) {
                $path = $this->model_repository->uploadIdFile($user, 'id_front', $file_front);
                if ($path) $input['id_front'] = $path;
            }
            // Check file behind
            list($file_behind, $errorKey) = $this->getRequestFile('file_behind');
            //if ($errorKey) return $this->errorWrongArgs($errorKey);
            if ($file_behind) {
                $path = $this->model_repository->uploadIdFile($user, 'id_behind', $file_behind);
                if ($path) $input['id_behind'] = $path;
            }
            if (empty($input['fullname']) && !empty($input['first_name'])) $input['fullname'] = $input['first_name'];
            if (empty($input['first_name']) && !empty($input['fullname'])) $input['first_name'] = $input['fullname'];

            // Update Model
            $user = $this->model_repository->update($user, $input);
            // Update device table
            $credentials = $this->getDeviceInfo($input);
            if (!empty($credentials['device_platform']) && !empty($credentials['device_token'])) {
                $persistence = \Sentinel::getPersistenceRepository()->createModel()->newQuery()->where('user_id', $user->id)->where('code', $this->access_token)->first();
                if ($persistence) {
                    $optional = [];
                    foreach (['name', 'model', 'device_id', 'device_platform', 'device_token', 'ip'] as $field) {
                        if (isset($credentials[$field])) $optional[$field] = $credentials[$field];
                    }
                    foreach ($optional as $field => $value) {
                        $persistence->{$field} = $value;
                    }
                    $persistence->save();
                }
            }
            //=== Update device
            $this->updateDeviceToken($user, $credentials);

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
     *   path="/auth/delete-account",
     *   summary="Delete Account",
     *   operationId="deleteAccount",
     *   tags={"Auth"},
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
    public function deleteAccount() {
        try {
            //=== Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), ['password' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Check password
            $user = $this->auth();
            $password = $this->request->get('password');
            if (!$this->model_repository->checkPassword($password, $user->password)) return $this->respondWithErrorKey('password.current_failed');
            // Backup user info and delete account
            $user->activations()->delete();
            $user->persistences()->delete();
            $user->reminders()->delete();
            $user->roles()->detach();
            $user->throttle()->delete();
            $this->model_repository->destroy($user);
            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/delete-account-otp",
     *   summary="Delete Account OTP",
     *   operationId="deleteAccountOTP",
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
    public function deleteAccountOTP() {
        try {
            $user = $this->auth();
            //=== Create Reminder
            $code = $this->_auth->createReminderSMS($user);
            $optional = ['code' => $code];
            if ($user->email) {
                $optional['email'] = $user->email;
                dispatch(new \Modules\User\Jobs\SendVerifyDeleteAccount($this->email, $optional));
                /*$data = $optional;
                $this->email->send('user::mail.verifyDeleteAccount', $data, function($message) use ($data) {
                    $message->to($data['email'])->subject('Xác thực xóa tài khoản');
                });*/
            }
            if (config('app.debug')) $user->verify_code = $code;

            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/delete-account-confirm",
     *   summary="Delete Account Confirm",
     *   operationId="deleteAccountConfirm",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"code"},
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
    public function deleteAccountConfirm() {
        try {
            // Check Valid
            $code = $this->request->get('code');
            $validatorErrors = $this->getValidator(compact('code'), ['code' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $user = $this->auth();
            $reminder = $this->_auth->getReminderSMS($user, $code);
            if (!$reminder) return $this->errorWrongArgs('auth.reset_code_invalid');
            // Backup user info and delete account
            $user->activations()->delete();
            $user->persistences()->delete();
            $user->reminders()->delete();
            $user->roles()->detach();
            $user->throttle()->delete();
            $this->model_repository->destroy($user);
            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/remove-device",
     *   summary="Remove Device",
     *   description="Remove Device",
     *   operationId="removeDevice",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Remove Device",
     *     @OA\JsonContent(
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
    public function removeDevice() {
        try {
            $this->_auth->removeDevice($this->getDeviceInfo());

            return $this->respondWithSuccess(trans('Remove device Success'));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/remove-device/{id}",
     *   summary="Remove Device By Id",
     *   description="Remove Device By Id",
     *   operationId="removeDeviceById",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Persistences Id", example="1"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function removeDeviceById($id) {
        try {
            $this->_auth->removeDeviceById($id);

            return $this->respondWithSuccess(trans('Remove device Success'));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/remove-devices",
     *   summary="Remove Devices",
     *   description="Remove Devices",
     *   operationId="removeDevices",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Remove Devices",
     *     @OA\JsonContent(
     *       @OA\Property(property="ids", type="string", example="1,2,3"),
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
    public function removeDevices() {
        try {
            $tmpIds = (string)$this->request->get('ids');
            $ids = [];
            if (!empty($tmpIds)) {
                foreach (explode(',', $tmpIds) as $id) {
                    if (intval($id)) $ids[] = intval($id);
                }
                $ids = array_unique($ids);
            }
            if ($ids) $this->_auth->removeDevices($ids);

            return $this->respondWithSuccess(trans('Remove devices Success'));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/set-product-viewed",
     *   summary="Set Product Viewed",
     *   description="Set Product Viewed",
     *   operationId="setProductViewed",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Set Product Viewed",
     *     @OA\JsonContent(
     *       @OA\Property(property="user_id", type="integer", example="1"),
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
    public function setProductViewed($user_id) {
        try {
            DB::table('user__product_viewed')
                ->where('ip', $this->request->server->get('REMOTE_ADDR'))
                ->whereNull('user_id')
                ->update(['user_id' => $user_id]);

            return $this->respondWithSuccess(trans('Set Product Viewed Success'));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    protected function getShareCode($user) {
        $tmp = explode(' ', trim($user->display));
        $share_code = utf8_strtoupper(Str::limit(Str::limit(to_ascii(end($tmp)), 5, '') . str_random_not_cap(10), 11, ''));
        $find = $this->model_repository->getModel()->where('share_code', $share_code)->first();
        if (!$find) {
            return $share_code;
        } else {
            return $this->getShareCode($user);
        }
    }

    /**
     * @OA\Post(
     *   path="/auth/create_share_code",
     *   summary="Create Share Code",
     *   operationId="authCreateShareCode",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function createShareCode() {
        try {
            $user = $this->auth();
            if ($user->share_code) return $this->respondWithSuccess($user->share_code);
            $share_code = $this->getShareCode($user);
            $this->model_repository->update($user, ['share_code' => $share_code]);

            return $this->respondWithSuccess($share_code);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/auth/get_invite_history",
     *   summary="Get Invite History",
     *   operationId="authGetInviteHistory",
     *   tags={"Auth"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function getInviteHistory() {
        try {
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = 100;
            $sort = 'id';
            $order = 'desc';

            $user = $this->auth();
            $results = $this->order_repository->getModel()
                ->whereNotNull('referral_code')
                ->where('referral_code', $user->share_code)
                ->select('first_name', 'order_status', 'created_at')
                ->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();

            return $this->respondWithSuccess($results);

        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    public function createVoucher($user) {
        $dateNow = date('Y-m-d H:i:s');
        $end_date = '2023-08-17 23:59:59';
        if (strtotime($dateNow) < strtotime($end_date)) {
            // Tặng bộ voucher cho khách hàng mới đăng ký tài khoản
            $voucher_set = [
                // Voucher 20.000đ cho đơn từ 0đ
                [
                    'amount'    => 20000,
                    'total'     => 0,
                    'quantity'  => 10,
                ],
                // Voucher 50.000đ cho đơn từ 500.000đ
                [
                    'amount'    => 50000,
                    'total'     => 500000,
                    'quantity'  => 6,
                ],
                // Voucher 100.000đ cho hóa đơn từ 2.000.000đ
                [
                    'amount'    => 100000,
                    'total'     => 2000000,
                    'quantity'  => 5,
                ],
                // Voucher 200.000đ cho đơn từ 5.000.000đ
                [
                    'amount'    => 200000,
                    'total'     => 5000000,
                    'quantity'  => 5,
                ],
                // Voucher 500.000đ cho đơn từ 10.000.000đ
                [
                    'amount'    => 500000,
                    'total'     => 10000000,
                    'quantity'  => 2,
                ],
            ];
            foreach ($voucher_set as $voucher) {
                $code = $this->createVoucherCode($user->id);
                $end_date = date('Y-m-d', strtotime('+30 days'));
                $this->voucher_repository->create(['user_id' => $user->id, 'code' => $code, 'amount' => $voucher['amount'], 'total' => $voucher['total'], 'quantity' => $voucher['quantity'], 'end_date' => $end_date]);
            }
        }
    }
}
