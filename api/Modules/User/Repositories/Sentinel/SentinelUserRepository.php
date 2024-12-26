<?php namespace Modules\User\Repositories\Sentinel;

use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\User\Entities\UserInterface;
use Modules\User\Events\SaveAdditionData;
use Modules\User\Events\UserWasUpdated;
use Modules\User\Exceptions\UserNotFoundException;
use Modules\User\Repositories\UserRepository;
use DB;

class SentinelUserRepository extends EloquentBaseRepository implements UserRepository {
    /**
     * @var \Modules\User\Entities\Sentinel\User
     */
    protected $user;

    /**
     * @var \Cartalyst\Sentinel\Roles\EloquentRole
     */
    protected $role;

    public function __construct() {
        $this->user = Sentinel::getUserRepository()->createModel();
        $this->role = Sentinel::getRoleRepository()->createModel();
        parent::__construct(Sentinel::getUserRepository()->createModel());
    }

    /**
     * Returns all the users
     * @return object
     */
    public function all() {
        return $this->user->all();
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
        $activation->fill([
            'completed'    => true,
            'completed_at' => Carbon::now(),
        ]);
        $activation->save();

        return $user;
    }

    /**
     * Create Or Update activation
     *
     * @param UserInterface $user
     * @return UserInterface
     */
    private function createOrUpdateActivation(UserInterface $user) {
        $activation = Activation::exists($user);
        if (!$activation) {
            $activation = Activation::create($user);
        }

        //Activation::complete($user, $activation->code);
        $activation->fill([
            'completed'    => true,
            'completed_at' => Carbon::now(),
        ]);
        $activation->save();

        return $user;
    }

    /**
     * Create a user resource
     * @param $data
     * @return mixed
     */
    public function create($data) {
        DB::beginTransaction();
        try {
            $user = $this->user->create((array)$data);
//            event(new SaveAdditionData($user));
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return $user;
    }

    /**
     * Create a user and assign roles to it
     *
     * @param array $data
     * @param array $roles
     * @param bool $activated
     * @param bool $status_activated
     * @return bool|mixed
     * @throws DontHavePermissionExceptions
     * @throws \Exception
     */
    public function createWithRoles($data, $roles, $activated = false, $status_activated = true) {
        $this->hashPassword($data);

        DB::beginTransaction();
        $user = $this->create((array)$data);

        if (isset($user) && !empty($user)) {
            if (!empty($roles)) {
                $this->validateInputRoles($roles);
                $user->roles()->attach($roles);
            }
            if ($activated) {
                $user = $this->createActivation($user);
                //=== User Complete
                if ($status_activated) {
                    $user->fill([
                        'status'       => USER_STATUS_ACTIVATED,
                        'completed'    => true,
                        'completed_at' => Carbon::now(),
                    ]);
                    $user->save();
                }
            }

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
     * @param bool $status_activated
     * @return mixed|UserInterface
     */
    public function activateUser($user, $data, $roles, $activated = false, $status_activated = true) {
        DB::beginTransaction();
        $user = $this->update($user, (array)$data);
        if (!empty($roles)) {
            $user->roles()->detach();
            $this->validateInputRoles($roles);
            $user->roles()->attach($roles);
        }
        if ($activated) {
            $user = $this->createOrUpdateActivation($user);
            //=== User Complete
            if ($status_activated) {
                $user->fill([
                    'status'       => USER_STATUS_ACTIVATED,
                    'completed'    => true,
                    'completed_at' => Carbon::now(),
                ]);
                $user->save();
            }
        }

        DB::commit();
        return $user;
    }

    public function associateWithGroups($user, $groups) {
        if (!empty($groups)) {
            $user->groups()->attach($groups);
        }
    }

    /**
     * Update a user
     * @param $user
     * @param $data
     * @return mixed
     */
    public function update($user, $data) {
        /*if (isset($data['password']) && $data['password']) {
            $this->addLastPassword($user, $data['password']);
        }*/

        $this->hashPassword($data);

        $user->update($data);

        event(new SaveAdditionData($user));

        event(new UserWasUpdated($user));

        return $user;
    }

    /**
     * Create a user
     *
     * @param UserInterface $user
     * @param $data
     * @return UserInterface
     */
    public function updateAndActivation(UserInterface $user, $data) {
        $this->update($user, $data);

        $user = $this->createOrUpdateActivation($user);

        //=== User Complete
        $user->fill([
            'status'       => USER_STATUS_ACTIVATED,
            'completed'    => true,
            'completed_at' => Carbon::now(),
        ]);

        $user->save();

        return $user;
    }

    /**
     * @param $userId
     * @param $data
     * @param $roles
     * @internal param $user
     * @return mixed
     */
    public function updateAndSyncRoles($userId, $data, $roles) {
        $user = $this->user->find($userId);

        $this->hashPassword($data);

        $user = $user->fill($data);
        $user->save();

        event(new SaveAdditionData($user));

        event(new UserWasUpdated($user));

        if (!empty($roles)) {
            $this->validateInputRoles($roles);
            $user->roles()->sync($roles);
        }

        return $user;
    }

    /**
     * @param $userId
     * @param $newPassword
     * @param $oldPassword
     * @internal param $user
     * @return boolean
     */
    public function updatePassword($userId, $newPassword, $oldPassword = '') {
        $user = $this->user->find($userId);

        if (!empty($oldPassword) && !Hash::check($oldPassword, $user->password)) {
            throw new \Exception(trans('user::messages.old password not match'));
        }

        $data = [
            'password' => $newPassword,
        ];
        $this->hashPassword($data);

        $user = $user->fill($data);
        $user->save();

        event(new UserWasUpdated($user));

        return true;
    }

    public function updateGroups($user, $groups) {
        if (!empty($groups)) {
            $user->groups()->sync($groups);
        }

        return $user;
    }

    /**
     * Deletes a user
     * @param $id
     * @throws UserNotFoundException
     * @return mixed
     */
    public function delete($id) {
        if ($user = $this->user->find($id)) {
            return $user->delete();
        };

        throw new UserNotFoundException();
    }

    /**
     * Find a user by its ID
     * @param $id
     * @return mixed
     */
    public function find($id) {
        return $this->user->find($id);
    }

    /**
     * Find a user by its credentials
     * @param  array $credentials
     * @return mixed
     */
    public function findByCredentials(array $credentials) {
        return Sentinel::findByCredentials($credentials);
    }

    /**
     * @return new \Modules\User\Entities\Sentinel\User
     */
    public function getModel() {
        return Sentinel::getUserRepository()->createModel();
    }

    protected function validateInputRoles($roles_check) {
        $user = Sentinel::getUser();
        $containsSearch = true;
        if ($user && $user->roles) {
            //Get current user role
            $user_roles = $user->roles;
            if ($user->inRole('super-admin')) {
                return true;
            } else {
                $roles = $this->role->getChildrenNodes($user_roles);
                $roles_id = $roles->lists('id')->toArray();

                $containsSearch = count(array_intersect($roles_check, $roles_id)) == count($roles_check);

                if (!$containsSearch) {
                    throw new DontHavePermissionExceptions("Can not assign roles that you don't have permission on them");
                }
            }
        }

        return true;
    }

    /**
     * Add password to history
     * @param $user
     * @param $password
     * @param bool $forced
     * @return bool
     */
    public function addLastPassword($user, $password, $forced = false) {
        $model = new \Modules\User\Entities\Password();

        if ($forced) {
            $model->create([
                'user_id'  => $user->id,
                'password' => Hash::make($password),
            ]);

            return true;
        } else {
            $password_total = 14 - 1;

            $ps = $model->where('user_id', $user->id)->orderBy('id', 'desc')->get();

            if ($ps->count() > $password_total) {
                $ids = [];
                $k = 1;
                foreach ($ps as $p) {
                    if ($k <= $password_total) {
                        $ids[] = $p->id;
                    }
                    $k++;
                }
                $model->newQuery()->whereNotIn('id', $ids)->delete();
            }

            $model->create([
                'user_id'  => $user->id,
                'password' => Hash::make($password),
            ]);

            return true;
        }
    }

    /**
     * Check password
     *
     * @param $password
     * @param $password_hash
     * @return bool
     */
    public function checkPassword($password, $password_hash) {
        if (Hash::check($password, $password_hash)) {
            return true;
        }

        return false;
    }

    /**
     * Hash the password key
     * @param array $data
     */
    private function hashPassword(array &$data) {
        if (isset($data['password'])) {
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make($data['password']);
            }
        }
    }

    /**
     * Check old password
     * @param $user
     * @param $password
     * @return bool
     */
    public function checkOldPassword($user, $password) {
        $passwords = $user->passwords;

        foreach ($passwords as $pw) {
            if (Hash::check($password, $pw->password)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update Identity Image
     * @param $user
     * @param $prop : id_front || id_behind
     * @param \Illuminate\Http\UploadedFile $file
     * @return string|null
     */
    public function uploadIdFile($user, $prop, UploadedFile $file) {
        if ($user->{$prop}) {
            $path = $user->{$prop};
            if ($path && \Storage::exists($path)) \Storage::delete($path);
        }
        $mimeType = $file->getClientMimeType();
        $path = "/identities/{$user->id}-" . str_random_not_cap(6) . '-' . time() . "." . strtolower($file->guessClientExtension());
        $ok = \Storage::disk(config('filesystems.default'))->getDriver()->put($path, fopen($file, 'r+'), ['visibility' => 'public', 'ContentType' => $mimeType]);
        if (!$ok) return null;
        return $path;
    }
}
