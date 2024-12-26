<?php

namespace Modules\User\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Social extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user__socials';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'social_id',
        'avatar',
        'provider',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    public function user() {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User');
    }

    public static function getAvatarByUserId($user_id) {
        $key = "social_avatar_$user_id";
        if (self::hasCache($key)) {
            return self::getCache($key);
        } else {
            $s = self::where('user_id', $user_id)->orderBy('provider', 'asc')->first();
            if ($s) {
                $value = $s->avatar;
                self::setCache($key, $value);
                return $value;
            } else {
                self::setCache($key, null);
                return null;
            }
        }
    }
}
