<?php namespace Modules\Business\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class ImportHistory
 *
 * @package Modules\Business\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class ImportHistory extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bus__import_histories';

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
    protected $fillable = ['product_id', 'idx_im', 'supplier_id', 'price_im', 'operating_costs', 'expected_profit', 'quantity', 'earning_ratio', 'pretax', 'vat', 'price', 'appraiser_id', 'approver_id', 'approved_at', 'status', 'note'];

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
    protected $appends = ['status_name'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'              => 'integer',
        'product_id'      => 'integer',
        'supplier_id'     => 'integer',
        'price_im'        => 'double',
        'operating_costs' => 'double',
        'expected_profit' => 'double',
        'quantity'        => 'integer',
        'earning_ratio'   => 'double',
        'pretax'          => 'double',
        'vat'             => 'integer',
        'price'           => 'double',
        'appraiser_id'    => 'integer',
        'approver_id'     => 'integer',
        'status'          => 'integer',
    ];

    public function getStatusNameAttribute() {
        $list = ['0' => 'Đang tính giá', '1' => 'Đã tính xong', '2' => 'Hiệu lực phát hành', '3' => 'Ngưng phát hành'];
        return !is_null($this->status) && isset($list[$this->status]) ? $list[$this->status] : '';
    }

    /*** Override exist method in trait to prevent ***/
    public function toArray() {
        $hidden = [];
        if (is_null($this->status)) $hidden = array_merge($hidden, ['status_name']);
        if (!empty($hidden)) $this->makeHidden($hidden);

        return parent::toArray();
    }

    /**
     * Relationship
     */
    public function supplier() {
        return $this->belongsTo('\Modules\Business\Entities\Supplier', 'supplier_id', 'id')->withTrashed();
    }

    public function appraiser() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'appraiser_id', 'id')->withTrashed();
    }

    public function approver() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'approver_id', 'id')->withTrashed();
    }
}
