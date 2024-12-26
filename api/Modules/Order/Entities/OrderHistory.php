<?php namespace Modules\Order\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;
use Modules\Order\Traits\OrderStatusTrait;

class OrderHistory extends CoreModel {
    use OrderStatusTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order__histories';

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
    protected $fillable = ['order_id', 'status', 'order_status', 'payment_status', 'notify', 'comment'];

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
    protected $appends = ['order_status_name', 'payment_status_name'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'       => 'integer',
        'order_id' => 'integer',
    ];

    /**
     * Relationship
     */
    public function order() {
        return $this->belongsTo('\Modules\Order\Entities\Order', 'order_id', 'id');
    }
}
