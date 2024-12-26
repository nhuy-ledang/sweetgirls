<?php namespace Modules\Usr\Repositories;

interface Authentication {
    /**
     * Authenticate a user
     *
     * @param  array $credentials
     * @param  bool $remember Remember the user
     * @return array
     */
    public function login(array $credentials, $remember = false);

    /***
     * @param \Modules\Usr\Entities\Sentinel\User $user
     * @param array $credentials
     * @return array
     */
    public function loginByUser($user, $credentials = []);

    /**
     * Log the user out of the application.
     *
     * @param $access_token
     * @return bool|mixed
     */
    public function logout($access_token = null);

    /**
     * Check if the user is logged in
     *
     * @return mixed
     */
    public function check();

    /**
     * Sets the user associated with Sentinel (does not log in).
     *
     * @param \Modules\Usr\Sentinel\UserInterface $user
     * @return void
     */
    public function setUser($user);

    /**
     * @param $code
     * @return \Modules\Usr\Sentinel\UserInterface
     */
    public function findByPersistenceCode($code);

    /**
     * Check User has deleted
     *
     * @param $code
     * @return bool
     */
    public function isCodeValid($code);

    /**
     * Check New Password
     * @param \Modules\Usr\Sentinel\UserInterface $user
     * @param $password
     * @return mixed
     */
    public function newPassword($user, $password);

    /**
     * Check is Super Admin
     *
     * @param \Modules\Usr\Entities\Sentinel\User $user
     * @return bool
     */
    public function isSuperAdmin($user);

    /**
     * Check is Supper or Admin
     *
     * @param \Modules\Usr\Entities\Sentinel\User $user
     * @return bool
     */
    public function isAdmin($user);

    /**
     * Check is Manager
     *
     * @param \Modules\Usr\Entities\Sentinel\User $user
     * @return bool
     */
    public function isManager($user);

    /**
     * Check is Accountant
     *
     * @param \Modules\Usr\Entities\Sentinel\User $user
     * @return bool
     */
    public function isAccountant($user);

    /**
     * Check is Sales
     *
     * @param \Modules\Usr\Entities\Sentinel\User $user
     * @return bool
     */
    public function isSales($user);

    /**
     * Check is User
     *
     * @param \Modules\Usr\Entities\Sentinel\User $user
     * @return bool
     */
    public function isUser($user);
}
