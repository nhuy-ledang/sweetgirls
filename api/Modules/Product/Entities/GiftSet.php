<?php namespace Modules\Product\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class GiftSet
 *
 * @package Modules\Product\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class GiftSet extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__gift_sets';

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
    protected $fillable = ['name', 'start_date', 'end_date', 'total', 'description', 'status'];

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
        'id'     => 'integer',
        'total'  => 'double',
        'status' => 'boolean',
    ];

    public function products() {
        return $this->hasMany('\Modules\Product\Entities\GiftSetProduct')->orderBy('name', 'asc');
        //->select(['*', \DB::raw('(select `price` from `pd__products` where `id` = `product_id`) as price')]);
    }
}
