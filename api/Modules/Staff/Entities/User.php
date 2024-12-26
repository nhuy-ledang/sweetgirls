<?php namespace Modules\Staff\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class User
 *
 * @package Modules\Staff\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class User extends CoreModel {
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'st__users';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['idx', 'department_id', 'usr_id', 'email', 'calling_code', 'phone_number', 'fullname', 'gender', 'birthday', 'address', 'fixed_address', 'bank_id', 'bank_name', 'start_date', 'end_date', 'position', 'mission', 'description', 'avatar', 'salary', 'real', 'method', 'status'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['avatar_url', 'status_name'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'department_id' => 'integer',
        'usr_id'        => 'integer',
        'gender'        => 'integer',
        'salary'        => 'double',
        'real'          => 'double',
        'status'        => 'integer',
    ];

    public function getAvatarUrlAttribute($value) {
        if ($this->avatar) {
            return media_url_file(Imagy::getThumbnail($this->avatar, 'small'));
        } else if ($value) {
            return $value;
        } else {
            // Random avatar by first name
            return media_url_file('/avatars/200/' . strtoupper(substr(utf8_to_ascii($this->fullname), 0, 1)) . '.jpg');
        }
    }

    public function getStatusNameAttribute() {
        $list = [
            1 => 'Đang công tác',
            2 => 'Đã thôi việc',
            3 => 'Nghỉ thai sản',
        ];
        if (!empty($this->status) && isset($list[$this->status])) {
            return $list[$this->status];
        } else {
            return 'N/A';
        }
    }

    /**
     * Relationship
     */
    public function department() {
        return $this->belongsTo('\Modules\Staff\Entities\Department', 'department_id', 'id');
    }

    public function usr() {
        //return $this->hasOne('\Modules\Usr\Entities\Sentinel\User', 'email', 'email');
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'usr_id', 'id');
    }
}
