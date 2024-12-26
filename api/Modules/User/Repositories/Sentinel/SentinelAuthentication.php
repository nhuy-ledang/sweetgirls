<?php namespace Modules\User\Repositories\Sentinel;

use ActivationSMS;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Cartalyst\Sentinel\Users\UserInterface;
use Hautelook\Phpass\PasswordHash;
use Modules\User\Facades\ReminderSMS;
use Modules\User\Exceptions\BannedUserException;
use Modules\User\Exceptions\InactivateUserException;
use Modules\User\Repositories\Authentication;
use Modules\User\Repositories\ReminderLogRepository;
use Sentinel;

class SentinelAuthentication implements Authentication {
    /**
     * @var \Cartalyst\Sentinel\Users\UserInterface
     */
    protected $currentUser;

    /**
     * The User repository.
     *
     * @var \Cartalyst\Sentinel\Users\UserRepositoryInterface
     */
    protected $users;

    /**
     * The Reminder repository.
     *
     * @var \Cartalyst\Sentinel\Reminders\ReminderRepositoryInterface
     */
    protected $reminders;

    /**
     * The Persistence repository.
     *
     * @var \Cartalyst\Sentinel\Persistences\PersistenceRepositoryInterface
     */
    protected $persistences;

    /**
     * The ActivationSMS repository.
     *
     * @var \Modules\User\Repositories\Sentinel\SentinelActivationSMSRepository
     */
    protected $activationSMS;

    /**
     * The ReminderLogRepository repository.
     *
     * @var \Modules\User\Repositories\ReminderLogRepository
     */
    protected $reminderlog_repository;

    /**
     * The expiration time in seconds.
     *
     * @var int
     */
    protected $expires = 300;

    public function __construct(ReminderLogRepository $reminderlog_repository) {
        $this->users = Sentinel::getUserRepository();
        $this->reminders = Sentinel::getReminderRepository();
        $this->persistences = Sentinel::getPersistenceRepository();
        $this->activationSMS = new SentinelActivationSMSRepository('Modules\User\Entities\ActivationSMS', config('cartalyst.sentinel.activations.expires',259200));
        $this->reminderlog_repository = $reminderlog_repository;
    }

    /**
     * Register a new user.
     * @param  array $user
     * @return bool
     */
    public function register(array $user) {
        return Sentinel::getUserRepository()->create((array)$user);
    }

    /**
     * Assign a role to the given user.
     * @param  \Modules\User\Repositories\UserRepository $user
     * @param  \Modules\User\Repositories\RoleRepository $role
     * @return mixed
     */
    public function assignRole($user, $role) {
        return $role->users()->attach($user);
    }

    /**
     * Activate the given used id
     * @param  int $userId
     * @param  string $code
     * @return mixed
     */
    public function activate($userId, $code) {
        $user = Sentinel::findById($userId);

        return Activation::complete($user, $code);
    }

    /**
     * Create an activation code for the given user
     * @param  \Modules\User\Repositories\UserRepository $user
     * @return mixed
     */
    public function createActivation($user) {
        return Activation::create($user)->code;
    }

    /**
     * Create a reminders code for the given user
     * @param  \Modules\User\Repositories\UserRepository $user
     * @return mixed
     */
    public function createReminder($user) {
        $reminder = Reminder::get($user) ?: Reminder::create($user);
        return $reminder->code;
    }

    public function createReminderSMS($user) {
        $reminder = ReminderSMS::get($user);
        $reminder = $reminder ? ReminderSMS::cycleCode($reminder) : ReminderSMS::create($user);
        return $reminder->code;
    }

    /**
     * Save data of reminder
     *
     * @param $user
     * @param $type
     * @param array $optional
     * @return mixed
     */
    public function createReminderSMSWithData($user, $type, array $optional) {
        $code = $this->createReminderSMS($user);
        $user_id = $user->getUserId();

        if (in_array($type, ['phone_number', 'password', 'email', 'device_token'])) {
            //=== Delete old log
            $this->reminderlog_repository->getModel()->where('user_id', $user_id)->delete();

            //=== Create new log
            $this->reminderlog_repository->create(['user_id' => $user_id, 'code' => $code, 'type' => $type, 'data' => $optional]);
        }

        return $code;
    }

    /**
     * Get remember log
     * @param $user
     * @param $type
     * @return bool
     */
    public function getReminderSMSWithData($user, $type) {
        if (in_array($type, ['phone_number', 'password', 'email', 'device_token'])) {
            return $this->reminderlog_repository->getModel()->where('user_id', $user->id)->where('type', $type)->orderBy('id', 'desc')->first();
        }

        return false;
    }

    /**
     * Completes the reset password process
     * @param $user
     * @param  string $code
     * @param  string $password
     * @return bool
     */
    public function completeResetPassword($user, $code, $password) {
        if (Reminder::complete($user, $code, $password)) {
            $reminderSMS = ReminderSMS::get($user);
            if ($reminderSMS) $reminderSMS->delete();

            $user->password_failed = 0;
            $user->save();

            return true;
        }
        return false;
    }

    public function completeResetPasswordSMS($user, $code, $password) {
        if (ReminderSMS::complete($user, $code, $password)) {
            $reminder = Reminder::get($user);
            if ($reminder) $reminder->delete();

            $user->password_failed = 0;
            $user->save();

            return true;
        }
        return false;
    }

    /**
     * Get a reminders code for the given user
     *
     * @param $user
     * @param null $code
     * @return mixed
     */
    public function getReminderSMS($user, $code = null) {
        ReminderSMS::removeExpired();

        return ReminderSMS::get($user, $code);
    }

    /**
     * Determines if the current user has access to given permission
     * @param $permission
     * @return bool
     */
    public function hasAccess($permission) {
        if ($this->isSuperUser()) {
            return true;
        }
        try {
            if (!$this->check()) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return Sentinel::hasAccess($permission);
    }

    /**
     * Check user is super admin or not
     * @return true | false
     */
    public function isSuperUser() {
        try {
            $user = $this->check();
        } catch (\Exception $e) {
            $user = false;
        }
        if (!$user) {
            return false;
        }
        return $user->inRole('super-admin');
    }

    /**
     * Check if the user is logged in
     * @return mixed
     */
    public function forceCheck() {
        return $this->currentUser = Sentinel::forceCheck();
    }

    /**
     * Get the ID for the currently authenticated user
     * @return int
     */
    public function id() {
        try {
            if (!$user = $this->check()) {
                return;
            }
            return $user->id;
        } catch (\Exception $e) {

        }
        return null;
    }

    public function findById($user_id) {
        return Sentinel::getUserRepository()->findById($user_id);
    }

    public function findByEmail($email) {
        return Sentinel::getUserRepository()->where('email', $email)->first();
    }

    public function findByCredentials($credentials) {
        return Sentinel::getUserRepository()->findByCredentials($credentials);
    }

    /**
     * Validate the password of the given user.
     *
     * @param array $credentials
     * @return array
     */
    private function validateCredentials(array $credentials) {
        //=== Step 1: Find user
        $user = $this->users->findByCredentials($credentials);
        if ($user === null) {
            return ['auth.invalid', $user];
        }
        //=== Step 2: Check password
        $valid = $this->users->validateCredentials($user, $credentials);
        if ($valid === false) {
            $passwordHasher = new PasswordHash(8,true);
            $passwordMatch = $passwordHasher->CheckPassword($credentials['password'], $user['password']);
            if ($passwordMatch === false) {
                return ['auth.invalid_password', $user];
            }
        }
        return [null, $user];
    }

    /**
     * Validate the password of the given user.
     *
     * @param UserInterface $user
     * @param array $credentials
     * @return bool|\Cartalyst\Sentinel\Users\UserInterface
     */
    private function validateCredentialsByUser(UserInterface $user, array $credentials) {
        //=== Check password
        $valid = $this->users->validateCredentials($user, $credentials);
        if ($valid === false) {
            //=== Update password_failed number
            $user->password_failed++;
            $user->save();

            return false;
        }

        return $user;
    }

    /**
     * Authenticate a user
     * @param  array $credentials
     * @param  bool $remember Remember the user
     * @return array
     */
    public function login(array $credentials, $remember = false) {
        try {
            // Login by phone number
            if (isset($credentials['phone_number'])) {
                list($calling_code, $phone_number) = calling2phone($credentials['phone_number']);
                $credentials['phone_number'] = $phone_number;
                $credentials['calling_code'] = $calling_code;
            }

            list($errorKey, $user) = $this->validateCredentials($credentials);
            if ($errorKey) {
                return [$errorKey, null, null];
            } else if ($user->status != USER_STATUS_ACTIVATED) {
                $errorKey = $user->status == USER_STATUS_BANNED ? 'auth.banned' : 'auth.login_not_activated';
                return [$errorKey, $user, null];
            }/* else if ($user->password_failed >= USER_PASSWORD_FAILED_MAX) {
                return ['auth.locked', $user];
            }*/

            //=== Check user activation
            if (!$this->activateCompleted($user)) {
                return ['auth.not_activated', $user, null];
            }

            //=== Get persistence optional
            $optional = [];
            if (isset($credentials['last_provider'])) $optional['provider'] = $credentials['last_provider'];
            foreach (['name', 'model', 'device_platform', 'device_token', 'ip'] as $field) {
                if (isset($credentials[$field])) $optional[$field] = $credentials[$field];
            }

            //=== Check device
            /*$env = detect_env();
            if (!($env == 'cms' || $env == 'web')) {
                $this->removeDevice($optional);

                //=== Check three devices
                $persistences = $user->persistences()->latest()->get();
                if ($persistences->count() >= config('user.users.device_limit', 3)) {
                    return ['auth.device_limit', $user, $persistences];
                }
            }*/

            //=== Update user
            foreach (['device_platform', 'device_token', 'ip', 'last_provider'] as $field) {
                if (isset($credentials[$field])) $user->{$field} = $credentials[$field];
            }
            // $user->save();
            if ($currentUser = Sentinel::authenticate($user, $remember)) {
                $this->currentUser = $currentUser;

                //=== Get last persistence => Update persistence => the newest one for current login request
                $persistence = $this->currentUser->persistences()->latest()->first();
                foreach ($optional as $field => $value) {
                    $persistence->{$field} = $value;
                }
                $persistence->save();

                /*//=== Reset count
                if ($this->currentUser->password_failed != 0) {
                    $this->currentUser->password_failed = 0;
                    $this->currentUser->save();
                }*/

                return [null, $this->currentUser, $persistence->code];
            }

            /*//=== Update password_failed number
            $user->password_failed++;
            $user->save();

            //=== Recheck
            if ($user->password_failed >= USER_PASSWORD_FAILED_MAX) {
                return ['auth.locked', $user];
            }*/

            return ['auth.invalid_password', null, null];
        } catch (NotActivatedException $e) {
            return ['auth.not_activated', null, null];
        } catch (InactivateUserException $e) {
            return ['auth.inactivate', null, null];
        } catch (BannedUserException $e) {
            return ['auth.banned', null, null];
        } catch (ThrottlingException $e) {
            //$delay = $e->getDelay();
            return ['auth.blocked', null, null];
        }
    }

    /***
     * @param \Modules\User\Entities\Sentinel\User $user
     * @param array $credentials
     * @param boolean $limitSkip
     * @return array
     */
    public function loginByUser($user, $credentials = [], $limitSkip = false) {
        //=== Get persistence optional
        $optional = [];
        if (isset($credentials['last_provider'])) $optional['provider'] = $credentials['last_provider'];
        foreach (['name', 'model', 'device_platform', 'device_token', 'ip'] as $field) {
            if (isset($credentials[$field])) $optional[$field] = $credentials[$field];
        }
        /*if (!$limitSkip) {
            //=== Check device
            $env = detect_env();
            if (!($env == 'cms' || $env == 'web')) {
                $this->removeDevice($optional);

                //=== Check three devices
                $persistences = $user->persistences()->latest()->get();
                if ($persistences->count() >= config('user.users.device_limit', 3)) {
                    return ['auth.device_limit', $user, $persistences];
                }
            }
        }*/

        //=== Update user
        foreach (['device_platform', 'device_token', 'ip', 'last_provider'] as $field) {
            if (isset($credentials[$field])) $user->{$field} = $credentials[$field];
        }
        $currentUser = Sentinel::login($user);
        event('sentinel.authenticated', $user);

        $this->currentUser = $currentUser;

        //=== Get last persistence => Update persistence => the newest one for current login request
        $persistence = $this->currentUser->persistences()->latest()->first();
        foreach ($optional as $field => $value) {
            $persistence->{$field} = $value;
        }
        $persistence->save();

        // $this->currentUser->{config('user.users.api_key_name')} = $persistence->code;

        return [null, $this->currentUser, $persistence->code];
    }

    /**
     * Log the user out of the application.
     * @param $access_token
     * @return bool|mixed
     */
    public function logout($access_token = null) {
        if (!$this->currentUser) {
            $this->check();
        }
        Sentinel::logout($this->currentUser ? $this->currentUser : null, false);
        if ($access_token) {
            $this->persistences->remove($access_token);
        }

        $this->currentUser = null;

        return true;
    }

    /**
     * Check if the user is logged in
     * @return mixed
     */
    public function check() {
        return $this->currentUser = Sentinel::check();
    }

    /**
     * Returns the currently logged in user, lazily checking for it.
     *
     * @return \Cartalyst\Sentinel\Users\UserInterface
     */
    public function getUser() {
        return $this->currentUser ? $this->currentUser : Sentinel::getUser();
    }

    /**
     * Sets the user associated with Sentinel (does not log in).
     *
     * @param \Cartalyst\Sentinel\Users\UserInterface $user
     */
    public function setUser($user) {
        Sentinel::setUser($user);
        $this->currentUser = $user;
    }

    /**
     * Check user active
     * @param UserInterface $user
     * @return mixed
     */
    public function activateCompleted(UserInterface $user) {
        return Activation::completed($user);
    }

    /**
     * @param \Modules\User\Entities\Sentinel\User $user
     * @param $code
     * @return bool|mixed
     */
    public function activateSMS($user, $code) {
        if ($this->activationSMS->complete($user, $code)) {

            $code = $this->createActivation($user);
            Activation::complete($user, $code);

            //=== User Complete
            $data = [
                'status'       => USER_STATUS_ACTIVATED,
                'completed'    => true,
                'completed_at' => Carbon::now(),
            ];
            if (in_array($user->last_provider, ['phone_number', 'email'])) {
                if ($user->last_provider == 'phone_number') {
                    $data['phone_verified'] = true;
                } else {
                    $data['email_verified'] = true;
                }
            }

            $user->fill($data);
            $user->save();

            return $code;
        }

        return false;
    }

    /**
     * @param \Modules\User\Entities\Sentinel\User $user
     * @param $code
     * @return bool|mixed
     */
    public function statusActivateSMS($user, $code) {
        if ($this->activationSMS->complete($user, $code)) {

            //=== User Complete
            $user->fill([
                'status'       => USER_STATUS_ACTIVATED,
                'completed'    => true,
                'completed_at' => Carbon::now(),
            ]);
            $user->save();

            return $user;
        }

        return false;
    }

    public function createActivationSMS($user) {
        //Activation::createModel()->newQuery()->where('user_id', $user->getUserId())->delete();
        $this->activationSMS->createModel()->newQuery()->where('user_id', $user->getUserId())->delete();

        return $this->activationSMS->create($user)->code;
    }

    public function completeChangeBySMS($user, $fileName, $code) {
        $user_id = $user->getUserId();

        $reminderlog = $this->reminderlog_repository->findByAttributes(['user_id' => $user_id, 'code' => $code, 'type' => $fileName]);
        if (!$reminderlog) {
            return false;
        }
        $data = $reminderlog->data;

        if (!isset($data[$fileName]) || empty($data[$fileName])) {
            return false;
        }

        $fieldValue = $data[$fileName];

        if ($fileName == 'phone_number' && (!isset($data['phone_number']) || empty($data['phone_number']))) {
            return false;
        } else if ($fileName == 'device_token' && (!isset($data['device_platform']) || empty($data['device_platform']))) {
            return false;
        }

        $reminder = $this->getReminderSMS($user, $code);
        /*ReminderSMS::removeExpired();
        $reminder = ReminderSMS::createModel()->newQuery()
            ->where('user_id', $user_id)
            ->where('code', $code)
            ->where('completed', false)
            ->first();*/

        if ($reminder === null) return false;

        if ($fileName == 'phone_number') {
            $user->phone_verified = true;
            list($calling_code, $phone_number) = calling2phone($fieldValue);
            $user->calling_code = $calling_code;
            $user->phone_number = $phone_number;
            $user->save();
        } else {
            if ($fileName == 'email') {
                $user->email_verified = true;
            } else if ($fileName == 'device_token') {
                $user->device_platform = $data['device_platform'];
            } else if ($fileName == 'password') {
                $user->password_failed = 0;
            }
            $user->{$fileName} = $fieldValue;
            $user->save();
        }

        $reminder->fill([
            'completed'    => true,
            'completed_at' => Carbon::now(),
        ]);

        $reminder->save();
        $reminderlog->delete();

        //=== Remove Reminder
        $reminder = $this->reminders->exists($user);
        if ($reminder) $reminder->delete();

        return $user;
    }

    /**
     * @param $code
     * @return \Cartalyst\Sentinel\Users\UserInterface
     */
    public function findByPersistenceCode($code) {
        return $this->users->findByPersistenceCode($code);
    }

    /**
     * Check user has deleted
     * @param $code
     * @return bool
     */
    public function isCodeValid($code) {
        return !!$this->persistences->findByPersistenceCode($code);
    }

    /**
     * Check user expires
     * @param $code
     * @return bool
     */
    public function isExpires($code) {
        $expires = Carbon::now()->subSeconds($this->expires);

        $persistence = $this->persistences->createModel()
            ->newQuery()
            ->where('code', $code)
            ->where('updated_at', '>', $expires)
            ->first();

        if ($persistence) {
            $renewal = Carbon::now()->addSecond($this->expires);

            $persistence->updated_at = $renewal;
            $persistence->save();
        }

        return !$persistence;
    }

    /**
     * Check New Password
     * @param \Cartalyst\Sentinel\Users\UserInterface $user
     * @param $password
     * @return bool
     */
    public function newPassword(UserInterface $user, $password) {
        $credentials = compact('password');

        $valid = $this->users->validForUpdate($user, $credentials);

        if ($valid === false) {
            return false;
        }

        $this->users->update($user, $credentials);

        return true;
    }

    /**
     * @param array $deviceInfo
     * @return mixed
     */
    public function removeDevice($deviceInfo = []) {
        $optional = [];
        foreach (['name', 'model', 'device_platform', 'device_token'] as $field) {
            if (isset($deviceInfo[$field])) $optional[$field] = $deviceInfo[$field];
        }
        if (!empty($optional['device_platform']) && !empty($optional['device_token'])) {
            $this->persistences->createModel()->newQuery()->where('device_platform', $optional['device_platform'])->where('device_token', $optional['device_token'])->delete();
        }
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function removeDeviceById($id) {
        $this->persistences->createModel()->newQuery()->where('id', $id)->delete();
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function removeDevices($ids) {
        $this->persistences->createModel()->newQuery()->whereIn('id', $ids)->delete();
    }
}
