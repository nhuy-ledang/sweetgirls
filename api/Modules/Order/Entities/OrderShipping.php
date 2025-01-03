<?php namespace Modules\Order\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class OrderShipping extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order__shipping';

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
    protected $fillable = ['order_id', 'order_number', 'money_collection', 'exchange_weight', 'money_total', 'money_total_fee', 'money_fee', 'money_collection_fee', 'money_other_fee', 'money_vas', 'money_vat', 'kpi_ht', 'receiver_province', 'receiver_district', 'receiver_wards', 'params', 'data'];

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
    protected $appends = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                   => 'integer',
        'order_id'             => 'integer',
        'money_collection'     => 'double',
        'exchange_weight'      => 'double',
        'money_total'          => 'double',
        'money_total_fee'      => 'double',
        'money_fee'            => 'double',
        'money_collection_fee' => 'double',
        'money_other_fee'      => 'double',
        'money_vas'            => 'double',
        'money_vat'            => 'double',
        'kpi_ht'               => 'double',
        'receiver_province'    => 'integer',
        'receiver_district'    => 'integer',
        'receiver_wards'       => 'integer',
        'params'               => 'json',
        'data'                 => 'json',
    ];

    /**
     * Relationship
     */
    public function order() {
        return $this->belongsTo('\Modules\Order\Entities\Order');
    }
}
