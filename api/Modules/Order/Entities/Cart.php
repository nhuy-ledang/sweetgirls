<?php namespace Modules\Order\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class Cart
 * @package Modules\Order\Entities
 * type: T:Trade (default), G:Gift (by coin), I:Included Products
 */
class Cart extends CoreModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crt__carts';

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
    protected $fillable = ['user_id', 'product_id', 'session_id', 'type', 'quantity'];

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
        'user_id'    => 'integer',
        'product_id' => 'integer',
        'quantity'   => 'integer',
    ];

    /**
     * Relationship
     */
    public function user() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User');
    }

    public function product() {
        return $this->belongsTo('\Modules\Product\Entities\Product');
    }

    public function session() {
        return $this->hasOne('\Modules\Order\Entities\Session', 'session_id', 'session_id');
    }
}
