<?php namespace Modules\User\Repositories;

use Illuminate\Http\UploadedFile;
use Modules\Core\Repositories\BaseRepository;

/**
 * Interface UserRepository
 * @package Modules\User\Repositories
 */
interface UserRepository extends BaseRepository {
    /**
     * Returns all the users
     * @return object
     */
    public function all();

    /**
     * Create a user resource
     * @param  array $data
     * @return mixed
     */
    public function create($data);

    /**
     * Create a user and assign roles to it
     * @param array $data
     * @param array $roles
     * @param bool $activated
     * @param bool $status_activated
     */
    public function createWithRoles($data, $roles, $activated = false, $status_activated = true);

    /**
     * Activate a user stater
     *
     * @param UserInterface $user
     * @param $data
     * @param $roles
     * @param bool $activated
     * @param bool $status_activated
     * @return mixed|UserInterface
     */
    public function activateUser($user, $data, $roles, $activated = false, $status_activated = true);

    /**
     * Associate created user with groups
     * @param array $data
     * @param array $roles
     * @param bool $activated
     */
    public function associateWithGroups($user, $groups);

    /**
     * Find a user by its ID
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Update a user
     * @param $user
     * @param $data
     * @return mixed
     */
    public function update($user, $data);

    /**
     * Update a user and sync its roles
     * @param  int $userId
     * @param $data
     * @param $roles
     * @return mixed
     */
    public function updateAndSyncRoles($userId, $data, $roles);

    /**
     * Update groups associated with the user
     * @param  int $userId
     * @param $data
     * @param $roles
     * @return mixed
     */
    public function updateGroups($user, $groups);

    /**
     * Deletes a user
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * Find a user by its credentials
     * @param  array $credentials
     * @return mixed
     */
    public function findByCredentials(array $credentials);

    /**
     * @return \Modules\User\Entities\Sentinel\User;
     */
    public function getModel();

    /**
     * Update Identity Image
     * @param $user
     * @param $prop : id_front || id_behind
     * @param \Illuminate\Http\UploadedFile $file
     * @return string|null
     */
    public function uploadIdFile($user, $prop, UploadedFile $file);
}
