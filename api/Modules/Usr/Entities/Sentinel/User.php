<?php namespace Modules\Usr\Entities\Sentinel;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Usr\Sentinel\EloquentUser;
use Modules\Usr\Sentinel\UserInterface;

class User extends EloquentUser implements UserInterface {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usrs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id', 'email', 'username', 'first_name', 'last_name', 'calling_code', 'phone_number', 'email_verified', 'phone_verified',
        'password', 'status', 'completed', 'completed_at',
        'gender', 'birthday', 'address', 'avatar', 'avatar_url', 'device_platform', 'device_token',
        'is_notify', 'is_sms',
    ];

    /**
     * Array of login column names.
     *
     * @var array
     */
    protected $loginNames = ['email'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['password', 'password_failed', 'deleted_at', 'ip', 'completed_at', 'device_platform', 'device_token'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['display'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'             => 'integer',
        'is_notify'      => 'boolean',
        'is_sms'         => 'boolean',
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',
        'gender'         => 'integer',
    ];

    /**
     * The Roles model FQCN.
     *
     * @var string
     */
    protected static $rolesModel = Role::class;

    /**
     * The Persistences model FQCN.
     *
     * @var string
     */
    protected static $persistencesModel = Persistence::class;

    /**
     * The Activations model FQCN.
     *
     * @var string
     */
    protected static $activationsModel = Activation::class;

    /**
     * The Reminders model FQCN.
     *
     * @var string
     */
    protected static $remindersModel = Reminder::class;

    /**
     * The Throttling model FQCN.
     *
     * @var string
     */
    protected static $throttlingModel = Throttle::class;

    /**
     * Returns the roles relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany {
        return $this->belongsToMany(static::$rolesModel, 'usr__role_users', 'user_id', 'role_id')->withTimestamps();
    }

    public function group() {
        return $this->belongsTo('Modules\Usr\Entities\Group');
    }
}
