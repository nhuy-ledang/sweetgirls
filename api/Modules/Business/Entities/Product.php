<?php namespace Modules\Business\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class Product
 *
 * @package Modules\Business\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class Product extends CoreModel {
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bus__products';

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
    protected $fillable = ['idx', 'category_id', 'supplier_id', 'name', 'prd_type', 'price_im', 'pretax', 'vat', 'price', 'unit', 'weight', 'length', 'width', 'height', 'appraiser_id', 'approver_id', 'approved_at', 'status', 'image', 'short_description', 'description'];

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
    protected $appends = ['prd_type_name', 'unit_name', 'thumb_url'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'           => 'integer',
        'category_id'  => 'integer',
        'supplier_id'  => 'integer',
        'prd_type'     => 'integer',
        'price_im'     => 'double',
        'pretax'       => 'double',
        'vat'          => 'integer',
        'price'        => 'double',
        'weight'       => 'double',
        'length'       => 'double',
        'width'        => 'double',
        'height'       => 'double',
        'appraiser_id' => 'integer',
        'approver_id'  => 'integer',
        'status'       => 'boolean',
        'turn_on'      => 'integer',
        'turn_of'      => 'integer',
    ];

    public function getPrdTypeNameAttribute() {
        $list = [
            PROD_TYPE_MY_PRODUCT  => 'Sản phẩm sản xuất',
            PROD_TYPE_MY_SERVICE  => 'Dịch vụ tự thực hiện',
            PROD_TYPE_OUT_PRODUCT => 'Sản phẩm nhập',
            PROD_TYPE_OUT_SERVICE => 'Dịch vụ thuê ngoài',
        ];
        return !is_null($this->prd_type) && isset($list[$this->prd_type]) ? $list[$this->prd_type] : '';
    }

    public function getUnitNameAttribute() {
        $name = '';
        if ($this->unit) {
            $name = $this->unit;
            $temp = explode('-', $this->unit);
            if (count($temp) == 2) {
                list($custom, $type) = $temp;
                if ($type && in_array($type, ['year', 'month', 'day'])) {
                    $names = ['year' => 'năm', 'month' => 'tháng', 'day' => 'ngày'];
                    $name = $custom . ' ' . $names[$type];
                }
            }
        }

        return $name;
    }

    public function getThumbUrlAttribute() {
        return media_url_file(Imagy::getThumbnail($this->image, 'thumb'));
    }

    /*** Override exist method in trait to prevent ***/
    public function toArray() {
        $hidden = [];
        if (is_null($this->prd_type)) $hidden = array_merge($hidden, ['prd_type_name']);
        if (is_null($this->unit)) $hidden = array_merge($hidden, ['unit_name']);
        if (is_null($this->image)) $hidden = array_merge($hidden, ['thumb_url']);
        if (!empty($hidden)) $this->makeHidden($hidden);

        return parent::toArray();
    }

    /**
     * Relationship
     */
    public function category() {
        return $this->belongsTo('\Modules\Business\Entities\Category', 'category_id', 'id');
    }

    public function supplier() {
        return $this->belongsTo('\Modules\Business\Entities\Supplier', 'supplier_id', 'id')->withTrashed();
    }

    public function appraiser() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'appraiser_id', 'id')->withTrashed()
            ->leftJoin('st__users as st', 'st.usr_id', 'usrs.id')->select(['usrs.id', 'usrs.first_name', 'usrs.last_name', 'usrs.avatar', 'usrs.avatar_url', 'st.fullname', 'st.phone_number', 'st.email', 'st.position']);
    }

    public function approver() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'approver_id', 'id')->withTrashed()
            ->leftJoin('st__users as st', 'st.usr_id', 'usrs.id')->select(['usrs.id', 'usrs.first_name', 'usrs.last_name', 'usrs.avatar', 'usrs.avatar_url', 'st.fullname', 'st.phone_number', 'st.email', 'st.position']);
    }

    public function import() {
        return $this->hasOne('\Modules\Business\Entities\Import', 'product_id', 'id')->with('supplier');
//            ->with(['supplier', function($query) {
//                $query->select(['id', 'company']);
//            }]);
    }
}
