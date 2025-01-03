<?php namespace Modules\Product\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class ProductLike extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__product_likes';

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
    protected $fillable = ['product_id', 'user_id', 'ip', 'liked'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'user_id'    => 'integer',
        'product_id' => 'integer',
        'liked'      => 'boolean',
    ];

    /**
     * Relationship
     */
    public function customer() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User', 'user_id', 'id');
    }

    public function product() {
        return $this->belongsTo('Modules\Product\Entities\Product', 'product_id');
    }
}
