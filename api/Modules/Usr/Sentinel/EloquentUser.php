<?php namespace Modules\Usr\Sentinel;

use Cartalyst\Sentinel\Activations\EloquentActivation;
use Cartalyst\Sentinel\Persistences\EloquentPersistence;
use Cartalyst\Sentinel\Persistences\PersistableInterface;
use Cartalyst\Sentinel\Reminders\EloquentReminder;
use Cartalyst\Sentinel\Roles\EloquentRole;
use Cartalyst\Sentinel\Roles\RoleableInterface;
use Cartalyst\Sentinel\Throttling\EloquentThrottle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use IteratorAggregate;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Imagy;
use Modules\Core\Common\EloquentHelperTrait;
use Modules\Core\Entities\Eloquent\CoreModel;
use Modules\Core\Entities\Traits\CoreModelTrait;

//use Cartalyst\Sentinel\Users\EloquentUser;

// Refer: Cartalyst\Sentinel\Users\EloquentUser
class EloquentUser extends CoreModel implements PersistableInterface, RoleableInterface, UserInterface {
    use SoftDeletes, EloquentHelperTrait, CoreModelTrait;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'json',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'deleted_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $persistableKey = 'user_id';

    /**
     * {@inheritdoc}
     */
    protected $persistableRelationship = 'persistences';

    /**
     * Array of login column names.
     *
     * @var array
     */
    protected $loginNames = ['email', 'calling_code', 'phone_number'];

    /**
     * The Roles model FQCN.
     *
     * @var string
     */
    protected static $rolesModel = EloquentRole::class;

    /**
     * The Persistences model FQCN.
     *
     * @var string
     */
    protected static $persistencesModel = EloquentPersistence::class;

    /**
     * The Activations model FQCN.
     *
     * @var string
     */
    protected static $activationsModel = EloquentActivation::class;

    /**
     * The Reminders model FQCN.
     *
     * @var string
     */
    protected static $remindersModel = EloquentReminder::class;

    /**
     * The Throttling model FQCN.
     *
     * @var string
     */
    protected static $throttlingModel = EloquentThrottle::class;

    /**
     * Returns the activations relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activations(): HasMany {
        return $this->hasMany(static::$activationsModel, 'user_id');
    }

    /**
     * Returns the persistences relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function persistences(): HasMany {
        return $this->hasMany(static::$persistencesModel, 'user_id');
    }

    /**
     * Returns the reminders relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reminders(): HasMany {
        return $this->hasMany(static::$remindersModel, 'user_id');
    }

    /**
     * Returns the roles relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany {
        return $this->belongsToMany(static::$rolesModel, 'role_users', 'user_id', 'role_id')->withTimestamps();
    }

    /**
     * Returns the throttle relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function throttle(): HasMany {
        return $this->hasMany(static::$throttlingModel, 'user_id');
    }

    /**
     * Returns an array of login column names.
     *
     * @return array
     */
    public function getLoginNames(): array {
        return $this->loginNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): IteratorAggregate {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function inRole($role): bool {
        if ($role instanceof Model) {
            $roleId = $role->getRoleId();
        }

        foreach ($this->roles as $instance) {
            if ($role instanceof Model) {
                if ($instance->getRoleId() === $roleId) {
                    return true;
                }
            } else {
                if ($instance->getRoleId() == $role || $instance->getRoleSlug() == $role) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function inAnyRole(array $roles): bool {
        foreach ($roles as $role) {
            if ($this->inRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePersistenceCode(): string {
        return Str::random(32);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId(): int {
        return $this->getKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistableId(): string {
        return $this->getKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistableKey(): string {
        return $this->persistableKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setPersistableKey(string $key): void {
        $this->persistableKey = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistableRelationship(): string {
        return $this->persistableRelationship;
    }

    /**
     * {@inheritdoc}
     */
    public function setPersistableRelationship(string $persistableRelationship): void {
        $this->persistableRelationship = $persistableRelationship;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserLogin(): string {
        return $this->getAttribute($this->getUserLoginName());
    }

    /**
     * {@inheritdoc}
     */
    public function getUserLoginName(): string {
        return reset($this->loginNames);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserPassword(): string {
        return $this->password;
    }

    /**
     * Returns the roles model.
     *
     * @return string
     */
    public static function getRolesModel(): string {
        return static::$rolesModel;
    }

    /**
     * Sets the roles model.
     *
     * @param string $rolesModel
     * @return void
     */
    public static function setRolesModel(string $rolesModel): void {
        static::$rolesModel = $rolesModel;
    }

    /**
     * Returns the persistences model.
     *
     * @return string
     */
    public static function getPersistencesModel() {
        return static::$persistencesModel;
    }

    /**
     * Sets the persistences model.
     *
     * @param string $persistencesModel
     * @return void
     */
    public static function setPersistencesModel(string $persistencesModel): void {
        static::$persistencesModel = $persistencesModel;
    }

    public function getCreatedAtAttribute($value) {
        return $this->convertToTimezone($value);
    }

    public function getUpdatedAtAttribute($value) {
        return $this->convertToTimezone($value);
    }

    public function getAvatarUrlAttribute($value) {
        if ($this->avatar) {
            return media_url_file(Imagy::getThumbnail($this->avatar, 'small'));
        } else if ($value) {
            return $value;
        } else {
            // Random avatar by first name
            return media_url_file('/avatars/200/' . strtoupper(substr(utf8_to_ascii($this->display), 0, 1)) . '.jpg');
        }
    }

    public function getDisplayAttribute() {
        $display = trim($this->first_name . ' ' . $this->last_name);
        if (!$display) {
            if ($this->username) $display = $this->username;
        }
        if (!$display) $display = 'No name';
        return $display;
    }

    public function isBanned() {
        return $this->getAttribute('status') == 'banned';
    }

    public function isInactivate() {
        return $this->getAttribute('status') == 'inactivate';
    }

    /*public function toArray() {
        $visible = [];
        if (!empty($visible)) {
            $this->makeVisible($visible);
        }

        $hidden = [];
        if (!empty($hidden)) {
            $this->makeHidden($hidden);
        }

        return parent::toArray();
    }*/

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete() {
        $isSoftDeletable = property_exists($this, 'forceDeleting');

        $isSoftDeleted = $isSoftDeletable && !$this->forceDeleting;

        if ($this->exists && !$isSoftDeleted) {
            $this->activations()->delete();
            $this->persistences()->delete();
            $this->reminders()->delete();
            $this->roles()->detach();
            $this->throttle()->delete();
        }

        return parent::delete();
    }
}
