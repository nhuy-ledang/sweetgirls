<?php namespace Modules\User\Repositories;

use Cartalyst\Sentinel\Users\UserInterface;

interface Authentication
{
    /**
     * Authenticate a user
     * @param  array $credentials
     * @param  bool $remember Remember the user
     * @return array
     */
    public function login(array $credentials, $remember = false);

    /**
     * Register a new user.
     * @param  array $user
     * @return bool
     */
    public function register(array $user);

    /**
     * Activate the given used id
     * @param  int $userId
     * @param  string $code
     * @return mixed
     */
    public function activate($userId, $code);

    /**
     * Assign a role to the given user.
     * @param  \Modules\User\Repositories\UserRepository $user
     * @param  \Modules\User\Repositories\RoleRepository $role
     * @return mixed
     */
    public function assignRole($user, $role);

    /**
     * Log the user out of the application.
     * @param $access_token
     * @return mixed
     */
    public function logout($access_token = null);

    /**
     * Create an activation code for the given user
     * @param $user
     * @return mixed
     */
    public function createActivation($user);

    public function createActivationSMS($user);

    /**
     * Create a reminders code for the given user
     * @param $user
     * @return mixed
     */
    public function createReminder($user);

    public function createReminderSMS($user);

    /**
     * Save data of reminder
     *
     * @param $user
     * @param $type
     * @param array $optional
     * @return mixed
     */
    public function createReminderSMSWithData($user, $type, array $optional);

    /**
     * Get remember log
     * @param $user
     * @param $type
     * @return bool
     */
    public function getReminderSMSWithData($user, $type);

    /**
     * Get a reminders code for the given user
     *
     * @param $user
     * @param null $code
     * @return mixed
     */
    public function getReminderSMS($user, $code = null);

    /**
     * Completes the reset password process
     * @param $user
     * @param  string $code
     * @param  string $password
     * @return bool
     */
    public function completeResetPassword($user, $code, $password);

    /**
     * Determines if the current user has access to given permission
     * @param $permission
     * @return bool
     */
    public function hasAccess($permission);

    /**
     * Check if the user is logged in
     * @return \Modules\User\Entities\Sentinel\User | false
     */
    public function check();

    /**
     * FORCE Check if the user is logged in
     * @return \Modules\User\Entities\Sentinel\User | false
     */
    public function forceCheck();

    /**
     * Get the ID for the currently authenticated user
     * @return int
     */
    public function id();

    /**
     * Check user is super admin or not
     * @return true | false
     */
    public function isSuperUser();

    public function findById($user_id);

    public function findByEmail($email);

    public function findByCredentials($credentials);

    /***
     * @param \Modules\User\Entities\Sentinel\User $user
     * @param array $credentials
     * @param boolean $limitSkip
     * @return array
     */
    public function loginByUser($user, $credentials = [], $limitSkip = false);

    /**
     * @param $code
     * @return \Cartalyst\Sentinel\Users\UserInterface
     */
    public function findByPersistenceCode($code);

    /**
     * @param \Modules\User\Entities\Sentinel\User $user
     * @param $code
     * @return bool|mixed
     */
    public function activateSMS($user, $code);

    /**
     * @param \Modules\User\Entities\Sentinel\User $user
     * @param $code
     * @return bool|mixed
     */
    public function statusActivateSMS($user, $code);

    /**
     * Returns the currently logged in user, lazily checking for it.
     *
     * @return \Cartalyst\Sentinel\Users\UserInterface
     */
    public function getUser();

    /**
     * Sets the user associated with Sentinel (does not log in).
     *
     * @param \Cartalyst\Sentinel\Users\UserInterface $user
     */
    public function setUser($user);

    /**
     * Check user has deleted
     * @param $code
     * @return bool
     */
    public function isCodeValid($code);

    /**
     * Check user expires
     * @param $code
     * @return bool
     */
    public function isExpires($code);

    /**
     * Check New Password
     * @param \Cartalyst\Sentinel\Users\UserInterface $user
     * @param $password
     * @return bool
     */
    public function newPassword(UserInterface $user, $password);

    /**
     * @param array $deviceInfo
     * @return mixed
     */
    public function removeDevice($deviceInfo = []);

    /**
     * @param integer $id
     * @return mixed
     */
    public function removeDeviceById($id);

    /**
     * @param array $ids
     * @return mixed
     */
    public function removeDevices($ids);
}
