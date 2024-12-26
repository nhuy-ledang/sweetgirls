<?php namespace Modules\Business\Entities;

use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class SupplierContact
 *
 * @package Modules\Business\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class SupplierContact extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sup__supplier_contacts';

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
    protected $fillable = ['supplier_id', 'owner_id', 'type_id', 'fullname', 'contact_title', 'gender', 'email', 'phone_number', 'birthday', 'interests', 'personality', 'contact_tool', 'contact_time', 'address', 'marital_status', 'home_town', 'religion', 'dreams', 'favorite_activity', 'social_achievements', 'social_groups', 'social_network', 'avatar', 'note', 'rating_id', 'progress', 'status'];

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
    protected $appends = ['avatar_url', 'gender_name', 'marital_status_name'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'             => 'integer',
        'owner_id'       => 'integer',
        'supplier_id'    => 'integer',
        'type_id'        => 'integer',
        'gender'         => 'integer',
        'marital_status' => 'integer',
        'rating_id'      => 'integer',
        'status'         => 'boolean',
        'is_default'     => 'boolean',
        'progress'       => 'integer',
    ];

    public function getAvatarUrlAttribute() {
        return media_url_file(Imagy::getThumbnail($this->avatar, 'thumb'));
    }

    public function getGenderNameAttribute() {
        $list = ['0' => 'Không', '1' => 'Ông', '2' => 'Bà'];
        return !is_null($this->gender) && isset($list[$this->gender]) ? $list[$this->gender] : '';
    }

    public function getMaritalStatusNameAttribute() {
        $list = ['0' => 'Không rõ', '1' => 'Đã kết hôn', '2' => 'Chưa kết hôn'];
        return !is_null($this->marital_status) && isset($list[$this->marital_status]) ? $list[$this->marital_status] : '';
    }

    /*** Override exist method in trait to prevent ***/
    public function toArray() {
        $hidden = [];
        if (is_null($this->gender)) $hidden = array_merge($hidden, ['gender_name']);
        if (is_null($this->avatar)) $hidden = array_merge($hidden, ['avatar_url']);
        if (is_null($this->marital_status)) $hidden = array_merge($hidden, ['marital_status_name']);
        if (!empty($hidden)) $this->makeHidden($hidden);

        return parent::toArray();
    }

    /**
     * Relationship
     */
    public function owner() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'owner_id', 'id')->withTrashed();
    }

    public function supplier() {
        return $this->belongsTo('\Modules\Business\Entities\Supplier')->withTrashed();
    }

    public function type() {
        return $this->belongsTo('\Modules\Customer\Entities\Type');
    }

    public function rating() {
        return $this->belongsTo('\Modules\Customer\Entities\Rating');
    }
}
