<?php namespace Modules\Usr\Sentinel;

use Carbon\Carbon;
use DB;
use Modules\Usr\Sentinel\Facades\Activation;

//use Modules\User\Repositories\Sentinel\SentinelUserRepository;

// Refer: Modules\User\Repositories\Sentinel\SentinelUserRepository
class UserRepository extends UserRepositoryBase implements \Modules\Usr\Repositories\UserRepository {
    /**
     * Hash the password key
     *
     * @param array $data
     */
    public function hashPassword(array &$data) {
        if (isset($data['password'])) {
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                //$data['password'] = Hash::make($data['password']);
                $data['password'] = $this->hasher->hash($data['password']);
            }
        }
    }

    /**
     * Create activation
     *
     * @param UserInterface $user
     * @return UserInterface
     */
    private function createActivation(UserInterface $user) {
        $activation = Activation::create($user);

        //Activation::complete($user, $activation->code);
        $activation->fill(['completed' => true, 'completed_at' => Carbon::now()]);
        $activation->save();

        $user->fill(['status' => USER_STATUS_ACTIVATED, 'completed_at' => Carbon::now()]);
        $user->save();

        return $user;
    }

    /**
     * Create a user
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model|mixed
     * @throws \Exception
     */
    public function create($data) {
        try {
            $user = parent::create((array)$data);
//            event(new SaveAdditionData($user));
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $user;
    }

    /**
     * Create a user and assign roles to it
     *
     * @param $data
     * @param array $roles
     * @param bool $activated
     * @return bool|\Illuminate\Database\Eloquent\Model|mixed
     * @throws \Exception
     */
    public function createWithRoles($data, $roles = [], $activated = false) {
        $this->hashPassword($data);
        DB::beginTransaction();
        $user = $this->create((array)$data);
        if (isset($user) && !empty($user)) {
            if (!empty($roles)) $user->roles()->attach($roles);
            if ($activated) $user = $this->createActivation($user);

            DB::commit();
            return $user;
        } else {
            return false;
        }
    }

    /**
     * Activate a user stater
     *
     * @param UserInterface $user
     * @param $data
     * @param $roles
     * @param bool $activated
     * @return mixed|UserInterface
     */
    public function activateUser($user, $data, $roles, $activated = false) {
        DB::beginTransaction();
        $user = $this->update($user, (array)$data);
        if (!empty($roles)) {
            $valid = $this->validateInputRoles($roles);
            if ($valid) $user->roles()->sync($roles);
        }
        if ($activated) {
            $activation = Activation::exists($user);
            if (!$activation) $this->createActivation($user);
        }

        DB::commit();
        return $user;
    }

    /**
     * Update a user
     * /**
     *
     * @param \Illuminate\Database\Eloquent\Model $user
     * @param  array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($user, $data) {
        //if (isset($data['password']) && $data['password']) $this->addLastPassword($user, $data['password']);

        $this->hashPassword($data);

        $user = parent::update($user, $data);

//        event(new SaveAdditionData($user));
//
//        event(new UserWasUpdated($user));

        return $user;
    }

    /**
     * ValidateInputRoles
     *
     * @param array $roles_check
     * @return bool
     */
    public function validateInputRoles($roles_check = []) {
        $role_ids = \Modules\Usr\Sentinel\Facades\Sentinel::getRoleRepository()->getModel()->pluck('id')->toArray();

        return count(array_intersect($roles_check, $role_ids)) == count($roles_check);
    }

    /**
     * Sync Roles
     *
     * @param $user
     * @param $roles
     * @return bool
     */
    public function syncRoles($user, $roles) {
        $activation = Activation::exists($user);
        if (!$activation) $this->createActivation($user);

        $valid = $this->validateInputRoles($roles);
        if ($valid) return $user->roles()->sync($roles);
        return false;
    }
}
