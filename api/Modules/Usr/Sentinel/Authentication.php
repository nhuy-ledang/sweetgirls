<?php namespace Modules\Usr\Sentinel;

use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Modules\User\Exceptions\BannedUserException;
use Modules\User\Exceptions\InactivateUserException;
use Modules\Usr\Sentinel\Facades\Activation;
use Modules\Usr\Sentinel\Facades\Sentinel;

class Authentication extends AuthenticationBase implements \Modules\Usr\Repositories\Authentication {
    public function __construct() {
        parent::__construct(
            Sentinel::getUserRepository(),
            Sentinel::getPersistenceRepository()
        );
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
            return ['auth.invalid_password', $user];
        }
        return [null, $user];
    }

    /**
     * Authenticate a user
     *
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
     * @param \Modules\Usr\Entities\Sentinel\User $user
     * @param array $credentials
     * @return array
     */
    public function loginByUser($user, $credentials = []) {
        //=== Get persistence optional
        $optional = [];
        if (isset($credentials['last_provider'])) $optional['provider'] = $credentials['last_provider'];
        foreach (['name', 'model', 'device_platform', 'device_token', 'provider', 'ip'] as $field) {
            if (isset($credentials[$field])) $optional[$field] = $credentials[$field];
        }

        //=== Update user
        foreach (['device_platform', 'device_token', 'ip', 'last_provider'] as $field) {
            if (isset($credentials[$field])) $user->{$field} = $credentials[$field];
        }
        $currentUser = Sentinel::login($user);

        $this->currentUser = $currentUser;

        //=== Get last persistence => Update persistence => the newest one for current login request
        $persistence = $this->currentUser->persistences()->latest()->first();
        foreach ($optional as $field => $value) {
            $persistence->{$field} = $value;
        }
        $persistence->save();

        return [null, $this->currentUser, $persistence->code];
    }

    /**
     * Log the user out of the application.
     *
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
     *
     * @return mixed
     */
    public function check() {
        return $this->currentUser = Sentinel::check();
    }

    /**
     * Returns the currently logged in user, lazily checking for it.
     *
     * @return UserInterface
     */
    public function getUser() {
        return $this->currentUser ? $this->currentUser : Sentinel::getUser();
    }

    /**
     * Sets the user associated with Sentinel (does not log in).
     *
     * @param \Modules\Usr\Sentinel\UserInterface $user
     * @return void
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
     * Check is Supper or Admin
     *
     * @param \Modules\Usr\Entities\Sentinel\User $user
     * @return bool
     */
    public function isSuperAdmin($user) {
        return $user && $user->inRole('super-admin');
    }

    /**
     * Check is Super Admin
     *
     * @param \Modules\Usr\Entities\Sentinel\User $user
     * @return bool
     */
    public function isAdmin($user) {
        return $user && ($user->inAnyRole(['super-admin', 'admin']));
    }

    /**
     * Check is Manager
     *
     * @param \Modules\Usr\Entities\Sentinel\User $user
     * @return bool
     */
    public function isManager($user) {
        return $user && $user->inRole('manager');
    }

    /**
     * Check is Accountant
     *
     * @param \Modules\Usr\Entities\Sentinel\User $user
     * @return bool
     */
    public function isAccountant($user) {
        return $user && $user->inRole('accountant');
    }

    /**
     * Check is Sales
     *
     * @param \Modules\Usr\Entities\Sentinel\User $user
     * @return bool
     */
    public function isSales($user) {
        return $user && $user->inRole('sales');
    }

    /**
     * Check is User
     *
     * @param \Modules\Usr\Entities\Sentinel\User $user
     * @return bool
     */
    public function isUser($user) {
        return $user && $user->inRole('user');
    }
}
