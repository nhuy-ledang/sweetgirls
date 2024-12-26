<?php namespace Modules\Stock\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class StockProduct
 *
 * @package Modules\Stock\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class StockRole extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sto__stock_roles';

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
    protected $fillable = ['stock_id', 'staff_id', 'role'];

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
        'id'       => 'integer',
        'stock_id' => 'integer',
        'staff_id' => 'integer',
    ];

    /**
     * Relationship
     */
    public function stock() {
        return $this->belongsTo('\Modules\Stock\Entities\Stock', 'stock_id', 'id');
    }

    public function staff() {
        return $this->belongsTo('\Modules\Usr\Entities\Sentinel\User', 'staff_id', 'id');
    }
}
