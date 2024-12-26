<?php namespace Modules\Stock\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class StoProduct
 *
 * @package Modules\Stock\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class StoProduct extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sto__products';

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
    protected $fillable = ['stock_id', 'ticket_id', 'product_id', 'content', 'quantity', 'price', 'total', 'shipment', 'due_date', 'code', 'type', 'status'];

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
        'ticket_id'    => 'integer',
        'stock_id'     => 'integer',
        'product_id'   => 'integer',
        'quantity'     => 'integer',
        'price'        => 'double',
        'total'        => 'double',
        'status'       => 'boolean',
    ];

    /*** Override exist method in trait to prevent ***/
    public function toArray() {
        $hidden = [];
        // if (is_null($this->unit)) $hidden = array_merge($hidden, ['unit_name']);
        if (!empty($hidden)) $this->makeHidden($hidden);
        return parent::toArray();
    }

    /**
     * Relationship
     */
    public function stock() {
        return $this->belongsTo('\Modules\Stock\Entities\Stock', 'stock_id', 'id');
    }
}
