<?php namespace Modules\Usr\Sentinel;

// Refer: Modules\User\Repositories\Sentinel\SentinelAuthentication
class AuthenticationBase {
    /**
     * @var \Modules\Usr\Sentinel\UserInterface
     */
    protected $currentUser;

    /**
     * The User repository.
     *
     * @var \Modules\Usr\Sentinel\UserRepositoryInterface
     */
    protected $users;

    /**
     * The Persistence repository.
     *
     * @var \Modules\Usr\Sentinel\PersistenceRepositoryInterface
     */
    protected $persistences;

    /**
     * The expiration time in seconds.
     *
     * @var int
     */
    protected $expires = 300;

    public function __construct(UserRepositoryInterface $users, PersistenceRepositoryInterface $persistences) {
        $this->users = $users;
        $this->persistences = $persistences;
    }

    /**
     * @param $code
     * @return \Modules\Usr\Sentinel\UserInterface
     */
    public function findByPersistenceCode($code) {
        return $this->users->findByPersistenceCode($code);
    }

    /**
     * {@inheritdoc}
     */
    public function recordLogin(UserInterface $user): bool {
        $user->last_login = Carbon::now();

        return (bool)$user->save();
    }

    /**
     * {@inheritdoc}
     */
    public function recordLogout(UserInterface $user): bool {
        return (bool)$user->save();
    }

    /**
     * Check User has deleted
     *
     * @param $code
     * @return bool
     */
    public function isCodeValid($code) {
        return !!$this->persistences->findByPersistenceCode($code);
    }

    /**
     * Check New Password
     * @param \Modules\Usr\Sentinel\UserInterface $user
     * @param $password
     * @return bool
     */
    public function newPassword($user, $password) {
        $credentials = compact('password');

        $this->users->hashPassword($credentials);

        $this->users->update($user, $credentials);

        return true;
    }
}
