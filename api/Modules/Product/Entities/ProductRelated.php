<?php namespace Modules\Product\Entities;

use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class Category
 *
 * @package Modules\Product\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class ProductRelated extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__product_related';

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
    protected $fillable = ['product_id', 'related_id', 'sort_order'];

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
        'product_id' => 'integer',
//        'related_id' => 'integer',
        'sort_order' => 'integer',
    ];

    //

    /**
     * Relationship
     */

//    public function group() {
//        return $this->belongsTo('\Modules\Product\Entities\PropertyGroup', 'group_id', 'id');
//    }
}
