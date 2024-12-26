<?php namespace Modules\Usr\Sentinel;

use Cartalyst\Support\Traits\EventTrait;
use Illuminate\Contracts\Events\Dispatcher;

// Refer: Cartalyst\Sentinel\Sentinel
class Sentinel {
    use EventTrait;

    /**
     * The current cached, logged in user.
     */
    protected $user;

    /**
     * The Persistences repository instance.
     *
     * @var PersistenceRepositoryInterface
     */
    protected $persistences;

    /**
     * The Users repository instance.
     *
     * @var UserRepositoryInterface
     */
    protected $users;

    /**
     * The Roles repository.
     *
     * @var RoleRepositoryInterface
     */
    protected $roles;

    /**
     * The Throttling repository instance.
     *
     * @var ThrottleRepositoryInterface
     */
    protected $throttle;

    /**
     * Array that holds all the enabled checkpoints.
     *
     * @var array
     */
    protected $checkpoints = [];

    /**
     * Flag for the checkpoint status.
     *
     * @var bool
     */
    protected $checkpointsStatus = true;

    /**
     * Constructor.
     *
     * @param PersistenceRepositoryInterface $persistences
     * @param UserRepositoryInterface $users
     * @param RoleRepositoryInterface $roles
     * @param Dispatcher $dispatcher
     * @return void
     */
    public function __construct(
        PersistenceRepositoryInterface $persistences,
        UserRepositoryInterface $users,
        RoleRepositoryInterface $roles,
        Dispatcher $dispatcher
    ) {
        $this->users = $users;

        $this->roles = $roles;

        $this->dispatcher = $dispatcher;

        $this->persistences = $persistences;
    }

    /**
     * Checks to see if a user is logged in.
     *
     * @return bool|UserInterface
     */
    public function check() {
        if ($this->user !== null) {
            return $this->user;
        }
    }

    /**
     * Authenticates a user, with "remember" flag.
     *
     * @param array|\Cartalyst\Sentinel\Users\UserInterface $credentials
     * @param bool $remember
     * @param bool $login
     * @return bool|\Cartalyst\Sentinel\Users\UserInterface
     */
    public function authenticate($credentials, bool $remember = false, bool $login = true) {
        $response = $this->fireEvent('sentinel.authenticating', [$credentials], true);

        if ($response === false) {
            return false;
        }

        if ($credentials instanceof UserInterface) {
            $user = $credentials;
        } else {
            $user = $this->users->findByCredentials($credentials);

            $valid = $user !== null ? $this->users->validateCredentials($user, $credentials) : false;

            if (!$valid) {
                $this->cycleCheckpoints('fail', $user, false);

                return false;
            }
        }

        if (!$this->cycleCheckpoints('login', $user)) {
            return false;
        }

        if ($login) {
            if (!$user = $this->login($user, $remember)) {
                return false;
            }
        }

        $this->fireEvent('sentinel.authenticated', $user);

        return $this->user = $user;
    }

    /**
     * Persists a login for the given user.
     *
     * @param \Modules\Usr\Sentinel\UserInterface $user
     * @param bool $remember
     * @return bool|\Modules\Usr\Sentinel\UserInterface
     */
    public function login(UserInterface $user, bool $remember = false) {
        $this->fireEvent('sentinel.logging-in', $user);

        $this->persistences->persist($user, $remember);

        $response = $this->users->recordLogin($user);

        if (!$response) {
            return false;
        }

        $this->fireEvent('sentinel.logged-in', $user);

        return $this->user = $user;
    }

    /**
     * Logs the current user out.
     *
     * @param \Modules\Usr\Sentinel\UserInterface|null $user
     * @param bool $everywhere
     * @return bool
     */
    public function logout(UserInterface $user = null, bool $everywhere = false): bool {
        $currentUser = $this->check();

        $this->fireEvent('sentinel.logging-out', $user);

        if ($user && $user !== $currentUser) {
            $this->persistences->flush($user, false);

            $this->fireEvent('sentinel.logged-out', $user);

            return true;
        }

        $user = $user ?: $currentUser;

        //if ($user === false) {
        if (!$user) {
            $this->fireEvent('sentinel.logged-out', $user);

            return true;
        }

        $method = $everywhere === true ? 'flush' : 'forget';

        $this->persistences->{$method}($user);

        $this->user = null;

        $this->fireEvent('sentinel.logged-out', $user);

        return $this->users->recordLogout($user);
    }

    /**
     * Cycles through all the registered checkpoints for a user. Checkpoints
     * may throw their own exceptions, however, if just one returns false,
     * the cycle fails.
     *
     * @param string $method
     * @param \Modules\Usr\Sentinel\UserInterface $user
     * @param bool $halt
     * @return bool
     */
    protected function cycleCheckpoints(string $method, UserInterface $user = null, bool $halt = true): bool {
        if (!$this->checkpointsStatus) {
            return true;
        }

        foreach ($this->checkpoints as $checkpoint) {
            $response = $checkpoint->{$method}($user);

            if (!$response && $halt) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the currently logged in user, lazily checking for it.
     *
     * @param bool $check
     * @return \Modules\Usr\Sentinel\UserInterface|null
     */
    public function getUser(bool $check = true): ?UserInterface {
        if ($check && $this->user === null) {
            $this->check();
        }

        return $this->user;
    }

    /**
     * Sets the user associated with Sentinel (does not log in).
     *
     * @param \Modules\Usr\Sentinel\UserInterface $user
     * @return void
     */
    public function setUser(UserInterface $user) {
        $this->user = $user;
    }

    /**
     * Returns the user repository.
     *
     * @return UserRepositoryInterface
     */
    public function getUserRepository(): UserRepositoryInterface {
        return $this->users;
    }

    /**
     * Returns the role repository.
     *
     * @return RoleRepositoryInterface
     */
    public function getRoleRepository(): RoleRepositoryInterface {
        return $this->roles;
    }

    /**
     * Returns the persistences repository.
     *
     * @return PersistenceRepositoryInterface
     */
    public function getPersistenceRepository(): PersistenceRepositoryInterface {
        return $this->persistences;
    }

    /**
     * Returns the throttle repository.
     *
     * @return ThrottleRepositoryInterface
     */
    public function getThrottleRepository(): ThrottleRepositoryInterface {
        return $this->throttle;
    }

    /**
     * Sets the throttle repository.
     *
     * @param ThrottleRepositoryInterface $throttle
     * @return void
     */
    public function setThrottleRepository(ThrottleRepositoryInterface $throttle): void {
        $this->throttle = $throttle;
    }
}
