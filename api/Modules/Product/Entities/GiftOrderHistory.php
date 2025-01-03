<?php namespace Modules\Product\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class GiftOrderHistory extends CoreModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__gift_order_histories';

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
    protected $fillable = ['gift_order_id', 'order_id', 'user_id'];

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
        'id'            => 'integer',
        'gift_order_id' => 'integer',
        'order_id'      => 'integer',
        'user_id'       => 'integer',
    ];
}
