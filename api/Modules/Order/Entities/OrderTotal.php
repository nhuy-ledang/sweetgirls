<?php namespace Modules\Order\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class OrderTotal extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order__totals';

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
    protected $fillable = ['order_id', 'code', 'title', 'value', 'sort_order'];

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
        'id'         => 'integer',
        'order_id'   => 'integer',
        'value'      => 'double',
        'sort_order' => 'integer',
    ];

    /**
     * Relationship
     */
    public function order() {
        return $this->belongsTo('\Modules\Order\Entities\Order', 'order_id', 'id');
    }
}
