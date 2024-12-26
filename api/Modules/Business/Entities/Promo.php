<?php namespace Modules\Business\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Entities\Eloquent\CoreModel;

class Promo extends CoreModel {
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bus__promos';

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
    protected $fillable = ['group_id', 'staff_id', 'name', 'code', 'type', 'discount', 'total', 'start_date', 'end_date', 'uses_total', 'uses_customer', 'description', 'customer', 'guide', 'status', 'approve_at'];

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
    protected $appends = [/*'discount_value',*/
        'group_name'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'discount'      => 'double',
        'total'         => 'double',
        'uses_total'    => 'integer',
        'uses_customer' => 'integer',
        'status'        => 'boolean',
    ];

    /*public function getDiscountValueAttribute() {
        if ($this->type == 'P') {
            return $this->discount . '%';
        } else if ($this->type == 'F') {
            return number_format($this->discount, 0, ',', '.');
        }
        return '';
    }*/

    public function getGroupNameAttribute() {
        $list = [
            1 => 'Thành viên',
            2 => 'Chiến dịch',
            4 => 'Trọn gói',
            5 => 'Số lượng lớn',
        ];
        return !empty($this->group_id) && isset($list[$this->group_id]) ? $list[$this->group_id] : '';
    }

    /*** Override exist method in trait to prevent ***/
    public function toArray() {
        $hidden = [];
        if (is_null($this->group_id)) $hidden = array_merge($hidden, ['group_name']);
        if (!empty($hidden)) $this->makeHidden($hidden);

        return parent::toArray();
    }

    /**
     * Relationship
     */
    public function staff() {
        return $this->belongsTo('\Modules\Staff\Entities\User', 'staff_id', 'id')->withTrashed();
    }
}
