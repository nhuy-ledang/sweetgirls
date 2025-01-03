<?php namespace Modules\Product\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class ProductFaq extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__product_faqs';

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
    protected $fillable = ['product_id', 'name', 'description', 'sort_order'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'product_id' => 'integer',
        'translates' => 'json',
        'sort_order' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Relationship
     */
    public function descs() {
        return $this->hasMany('\Modules\Product\Entities\ProductFaqDesc', 'id', 'id');
    }
}
