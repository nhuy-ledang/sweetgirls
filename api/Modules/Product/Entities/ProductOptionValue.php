<?php namespace Modules\Product\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class ProductOptionValue extends CoreModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__product_option_values';

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
    protected $fillable = ['product_id', 'option_id', 'option_value_id'];

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
        'id'              => 'integer',
        'product_id'      => 'integer',
        'option_id'       => 'integer',
        'option_value_id' => 'integer',
    ];

    /**
     * Relationship
     */
    public function product() {
        return $this->belongsTo('\Modules\Product\Entities\Product');
    }

    public function option() {
        return $this->belongsTo('\Modules\Product\Entities\Option');
    }

    public function option_value() {
        return $this->belongsTo('\Modules\Product\Entities\OptionValue');
    }
}
