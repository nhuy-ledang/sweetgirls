<?php namespace Modules\Stock\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class InventoryProduct
 *
 * @package Modules\Stock\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class InventoryProduct extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sto__inventory_products';

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
    protected $fillable = ['stock_id', 'inventory_id', 'product_id', 'unit', 'quantity', 'reality', 'note', 'reason'];

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
        'id'           => 'integer',
        'stock_id'     => 'integer',
        'inventory_id' => 'integer',
        'product_id'   => 'integer',
        'quantity'     => 'integer',
        'reality'      => 'integer',
    ];

    /**
     * Relationship
     */
    public function stock() {
        return $this->belongsTo('\Modules\Stock\Entities\Stock', 'stock_id', 'id');
    }

    public function product() {
        return $this->belongsTo('\Modules\Product\Entities\Product', 'product_id', 'id')->with('category')->withTrashed();
    }
}
