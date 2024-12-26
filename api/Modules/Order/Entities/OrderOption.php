<?php namespace Modules\Order\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class OrderOption extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order__options';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['order_id', 'order_product_id', 'product_option_id', 'product_option_value_id', 'name', 'value', 'type'];

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
        'id'                      => 'integer',
        'order_id'                => 'integer',
        'order_product_id'        => 'integer',
        'product_option_id'       => 'integer',
        'product_option_value_id' => 'integer',
    ];

    /**
     * Relationship
     */
    public function order() {
        return $this->belongsTo('\Modules\Order\Entities\Order', 'order_id', 'id');
    }

    public function product() {
        return $this->belongsTo('\Modules\Product\Entities\Product', 'product_id', 'id');
    }
}
