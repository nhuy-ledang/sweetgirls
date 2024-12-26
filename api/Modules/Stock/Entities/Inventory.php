<?php namespace Modules\Stock\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class Inventory
 *
 * @package Modules\Stock\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class Inventory extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sto__inventories';

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
    protected $fillable = ['stock_id', 'owner_id', 'name', 'date', 'note', 'status', 'reviewer_id', 'approved_at'];

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
        'id'          => 'integer',
        'stock_id'    => 'integer',
        'owner_id'    => 'integer',
        'reviewer_id' => 'integer',
    ];

    /**
     * Relationship
     */
    public function stock() {
        return $this->belongsTo('\Modules\Stock\Entities\Stock', 'stock_id', 'id');
    }

    public function products() {
        return $this->hasMany('\Modules\Stock\Entities\InventoryProduct', 'inventory_id', 'id')
            ->leftJoin('pd__products as p', 'p.id', '=', 'sto__inventory_products.product_id')
            ->select(['sto__inventory_products.*', 'p.idx', 'p.name']);
    }
}
