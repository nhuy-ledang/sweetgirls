<?php namespace Modules\Product\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class ProductSpecial extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__product_specials';

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
    protected $fillable = ['product_id', 'user_group_id', 'priority', 'price', 'is_flashsale', 'quantity', 'used', 'start_date', 'end_date'];

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
        'id'            => 'integer',
        'product_id'    => 'integer',
        'user_group_id' => 'integer',
        'priority'      => 'integer',
        'price'         => 'double',
        'is_flashsale'  => 'boolean',
        'quantity'      => 'integer',
        'used'          => 'integer',
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
    public function product() {
        return $this->belongsTo('\Modules\Product\Entities\Product');
    }
}
