<?php namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class GiftOrder
 *
 * @package Modules\Product\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class GiftOrder extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__gift_orders';

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
    protected $fillable = ['name', 'total', 'amount', 'description', 'start_date', 'end_date', 'limited', 'uses_total', 'status'];

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
        'total'      => 'double',
        'amount'     => 'double',
        'limited'    => 'integer',
        'uses_total' => 'integer',
        'status'     => 'boolean',
    ];

    public function products() {
        return $this->hasMany('\Modules\Product\Entities\GiftOrderProduct')->leftJoin('pd__products as p', 'p.id', 'product_id')
            ->select(['pd__gift_order_products.*', 'p.name as name_display'])
            ->orderBy('pd__gift_order_products.id', 'asc');
    }
}
