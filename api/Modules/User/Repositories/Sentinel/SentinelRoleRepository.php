<?php namespace Modules\User\Repositories\Sentinel;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Modules\User\Events\RoleWasUpdated;
use Modules\User\Repositories\RoleRepository;
use Modules\Core\Repositories\Eloquent\NestedSetRepository;

class SentinelRoleRepository implements RoleRepository {
    use NestedSetRepository;

    /**
     * @var \Modules\User\Entities\Sentinel\Role
     */
    protected $model;

    public function __construct() {
        $this->model = Sentinel::getRoleRepository()->createModel();
    }

    /**
     * Find a role by its id
     * @param $id
     * @return mixed
     */
    public function find($id) {
        return $this->model->find($id);
    }

    /**
     * Update a role
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data) {
        $role = $this->model->find($id);

        $role->fill($data);

        $role->save();

        event(new RoleWasUpdated($role));
    }

    /**
     * Find a role by its name
     * @param  string $name
     * @return mixed
     */
    public function findByName($name) {
        return Sentinel::findRoleByName($name);
    }

    /**
     * @return \Modules\User\Entities\Sentinel\Role
     */
    public function getModel() {
        return Sentinel::getRoleRepository()->createModel();
    }

    public function removeUserFromRole($role_id, $user_ids) {
        $user_ids = is_array($user_ids) ?: [$user_ids];
        return $this->find($role_id)->users()->detach($user_ids);
    }
}
