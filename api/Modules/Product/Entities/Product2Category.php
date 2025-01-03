<?php namespace Modules\Product\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Product2Category extends CoreModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__product2category';

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
    protected $fillable = ['product_id', 'category_id'];

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
        'product_id'  => 'integer',
        'category_id' => 'integer',
    ];
}
