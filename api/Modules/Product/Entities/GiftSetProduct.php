<?php namespace Modules\Product\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class GiftSetProduct
 *
 * @package Modules\Product\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class GiftSetProduct extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__gift_set_products';

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
    protected $fillable = ['gift_set_id', 'product_id', 'name', 'price', 'quantity'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'gift_set_id' => 'integer',
        'product_id'  => 'integer',
        'price'       => 'double',
        'quantity'    => 'integer',
    ];

    /**
     * Handle Data when eloquent return json data from database
     *
     * @return array
     */
    public function toArray() {
        $hidden = [];
        //if (is_null($this->image)) $hidden = ['thumb_url', 'small_url'];
        if (!empty($hidden)) $this->makeHidden($hidden);

        return parent::toArray();
    }
}
