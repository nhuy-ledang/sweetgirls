<?php namespace Modules\Usr\Sentinel;

use Modules\Core\Repositories\BaseRepository;

interface UserRepositoryInterface extends BaseRepository {
    /**
     * Finds a user by the given primary key.
     *
     * @param int $id
     * @return \Modules\Usr\Sentinel\UserInterface|null
     */
    public function findById(int $id): ?UserInterface;

    /**
     * Finds a user by the given credentials.
     *
     * @param array $credentials
     * @return \Modules\Usr\Sentinel\UserInterface|null
     */
    public function findByCredentials(array $credentials): ?UserInterface;

    /**
     * Finds a user by the given persistence code.
     *
     * @param string $code
     * @return \Modules\Usr\Sentinel\UserInterface|null
     */
    public function findByPersistenceCode(string $code): ?UserInterface;

    /**
     * Records a login for the given user.
     *
     * @param \Modules\Usr\Sentinel\UserInterface $user
     * @return bool
     */
    public function recordLogin(UserInterface $user): bool;

    /**
     * Records a logout for the given user.
     *
     * @param \Modules\Usr\Sentinel\UserInterface $user
     * @return bool
     */
    public function recordLogout(UserInterface $user): bool;

    /**
     * @param UserInterface $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials): bool;

    /**
     * Hash the password key
     *
     * @param array $data
     */
    public function hashPassword(array &$data);

    /**
     * Create a user and assign roles to it
     *
     * @param $data
     * @param array $roles
     * @param bool $activated
     * @return bool|\Illuminate\Database\Eloquent\Model|mixed
     * @throws \Exception
     */
    public function createWithRoles($data, $roles = [], $activated = false);

    /**
     * Activate a user stater
     *
     * @param UserInterface $user
     * @param $data
     * @param $roles
     * @param bool $activated
     * @return mixed|UserInterface
     */
    public function activateUser($user, $data, $roles, $activated = false);
}
