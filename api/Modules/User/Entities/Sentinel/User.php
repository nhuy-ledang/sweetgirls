<?php namespace Modules\User\Entities\Sentinel;

use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Users\EloquentUser;
use Imagy;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Entities\UserInterface;
use Modules\Core\Common\EloquentHelperTrait;
use Modules\User\Exceptions\CannotDeleteSuperAdminException;

class User extends EloquentUser implements UserInterface {
    use EloquentHelperTrait;

    use SoftDeletes;

    protected $fillable = [
        'user_group_id', 'email', 'username', 'prefix', 'first_name', 'last_name', 'calling_code', 'phone_number', 'gender', 'birthday', 'address', 'avatar', 'avatar_url',
        'is_notify', 'is_sms', 'email_verified', 'phone_verified', 'spend', 'coins', 'coins_expired', 'points',
        'password', 'status', 'completed', 'completed_at', 'fullname',
        'device_id', 'device_platform', 'device_token', ' last_provider',
        'share_code', 'id_no', 'id_date', 'id_provider', 'id_address', 'id_front', 'id_behind', 'tax', 'card_holder', 'bank_number', 'bank_name', 'bank_id', 'bank_branch', 'bank_branch', 'paypal_number',
    ];

    protected $hidden = [
        'user_group_id', 'avatar',
        'password', 'password_failed', 'passwords', 'permissions',
        'updated_at', 'deleted_at', 'device_platform', 'device_token',
        'ip', 'latitude', 'longitude', 'last_provider',
        'is_notify', 'is_sms',
    ];

    protected $dates = ['deleted_at'];

    /**
     * {@inheritDoc}
     */
    protected $loginNames = ['email'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'             => 'integer',
        'user_group_id'  => 'integer',
        'is_notify'      => 'boolean',
        'is_sms'         => 'boolean',
        'completed'      => 'boolean',
        'prefix'         => 'integer',
        'gender'         => 'integer',
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',
        'spend'          => 'integer',
        'coins'          => 'integer',
        'points'         => 'integer',
        'address_id'     => 'integer',
    ];

    /**
     * @var array
     */
    protected $appends = ['no', 'display', 'avatar_url'];

    public function getNoAttribute() {
        return number_pad($this->id, 4);
    }

    public function getAvatarUrlAttribute($value) {
        if ($this->avatar) {
            //return media_url_file(Imagy::getThumbnail($this->avatar, 'small'));
            return media_url_file($this->avatar);
        } else if ($value) {
            return $value;
        } else {
            // Random avatar by first name
            return media_url_file('/avatars/200/' . strtoupper(substr(utf8_to_ascii($this->display), 0, 1)) . '.jpg');
        }
    }

    public function getCoverUrlAttribute() {
        return media_url_file($this->cover);
    }

    public function getDisplayAttribute() {
        // Thêm khoảng cách ở trước để nếu là tiếng Nhật thì không lỗi 'Malformed UTF-8 characters, possibly incorrectly encoded'
        $display = ' ' . trim(($this->first_name ? $this->first_name : '') . ' ' . ($this->last_name ? $this->last_name : ''));
        if ($display) {
            return $display;
        } else if ($this->username) {
            return $this->username;
        } else if ($this->email) {
            return $this->email;
        } else if ($this->phone_number) {
            return $this->phone_number;
        } else {
            return 'N/A';
        }
    }

    public function __construct(array $attributes = []) {
        $this->fillable = array_merge($this->fillable, config('user.users.fillable', []));

        parent::__construct($attributes);

        $this->loginNames = config('user.users.login-columns');

    }

    /**
     * Checks if a user belongs to the given Role ID
     *
     * @param  int $roleId
     *
     * @return bool
     */
    public function hasRoleId($roleId) {
        return $this->roles()->whereId($roleId)->count() >= 1;
    }

    /**
     * Checks if a user belongs to the given Role Name
     *
     * @param  string $name
     *
     * @return bool
     */
    public function hasRoleName($name) {
        return $this->roles()->whereName($name)->count() >= 1;
    }

    /**
     * Checks if a user belongs to the given Group ID
     *
     * @param  int $groupId
     *
     * @return bool
     */
    public function hasGroupId($groupId) {
        return $this->groups()->whereGroupId($groupId)->count() >= 1;
    }

    /**
     * Checks if a user belongs to the given Group Name
     *
     * @param  string $name
     *
     * @return bool
     */
    public function hasGroupName($name) {
        return $this->groups()->whereName($name)->count() >= 1;
    }

    /**
     * Check if the current user is activated
     * @return bool
     */
    public function isActivated() {
        if ($activation = Activation::completed($this)) {
            return true;
        }

        return false;
    }

    public function isBanned() {
        return $this->getAttribute('status') == 'banned';
    }

    public function isInactivate() {
        return $this->getAttribute('status') == 'inactivate';
    }

    public function group() {
        return $this->belongsTo('Modules\User\Entities\Group');
    }

    public function source() {
        return $this->belongsTo('Modules\User\Entities\LeadSource', 'source_id');
    }

    public function userBanned() {
        return $this->hasMany('Modules\User\Entities\UserBanned', 'user_id');
    }

    /**
     * Get the devices by a user .
     */
    public function devices() {
        return $this->hasMany('Modules\User\Entities\DeviceToken', 'user_id')->select(['app', 'device_platform', 'device_token']);
    }

    public function country() {
        return $this->belongsTo('Modules\Location\Entities\Country');
    }

    public function timezone() {
        return $this->belongsTo('Modules\Location\Entities\Timezone');
    }

    public function tz() {
        return $this->belongsTo('Modules\Location\Entities\Timezone', 'timezone_id');
    }

    public function scopeOnlyBackendUser($query) {
        return $query->where(function($q) {
            $q->whereHas("roles", function($q2) {
                $q2->where("slug", "<>", "api-user");
                $q2->where("slug", "<>", "guest");
            });
        });
    }

    public function scopeOnlyApiUser($query) {
        $query->where(function($q) {
            $q->orWhereHas("roles", function($q2) {
                $q2->where("slug", "=", "api-user");
            });
            $q->orWhereHas("roles", function($q2) {
            }, "=", 0);
        });
    }


    /**
     * Override exist method in trait to prevent delete admin user\
     * @param $ids
     * @return int
     * @throws CannotDeleteSuperAdminException
     */
    public function multiDelete($ids) {
        $records = $this->with('roles')->whereIn($this->getKeyName(), $ids)->get();
        foreach ($records as $record) {
            if ($this->inRole('super-admin')) {
                throw new CannotDeleteSuperAdminException('You can not delete super-admin in multi-delete function');
            }

            $record->delete();
        }
        return $records->count();
    }

    public function delete() {
        $time = time();
        $this->email = $this->getOriginal('email') . '-' . 'deleted' . $time;
        $this->username = $this->getOriginal('username') . '-' . 'deleted' . $time;
        $this->save();
        // Laravel auto softDelete
        /*if($this->forceDeleting){*/
        return parent::delete();
        /*}else{
            $this->runSoftDelete();
        }*/
    }

    public function toArray() {
        $visible = [];
        if (!empty($visible)) $this->makeVisible($visible);
        $hidden = [];
        if (!empty($hidden)) $this->makeHidden($hidden);

        return parent::toArray();
    }

    public function setTimezoneIdAttribute($timezone_id) {
        $this->attributes['timezone_id'] = $timezone_id !== '' ? $timezone_id : null;
    }

    public function deviceTokens() {
        return $this->hasMany(config('asgard.user.notifications.device_model'));
    }

    public function socials() {
        return $this->hasMany('Modules\User\Entities\Social');
    }

    public function passwords() {
        return $this->hasMany('Modules\User\Entities\Password');
    }

    public function contact() {
        return $this->hasOne('Modules\User\Entities\Contact');
    }
}
