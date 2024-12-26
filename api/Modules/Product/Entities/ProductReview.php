<?php namespace Modules\Product\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class ProductReview
 *
 * @package Modules\Order\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class ProductReview extends CoreModel {
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'pd__product_reviews';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['user_id', 'product_id', 'rating', 'review', 'link', 'status', 'approved_at'];

    /**
     * The attributes that should be hidden for serialization.
     * @var array
     */
    protected $hidden = [];

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = [];

    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'user_id'    => 'integer',
        'product_id' => 'integer',
        'rating'     => 'integer',
        'status'     => 'boolean',
    ];

    /**
     * Relationship
     */
    public function user() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User', 'user_id', 'id');
    }

    public function product() {
        return $this->belongsTo('Modules\Product\Entities\Product', 'product_id');
    }

    public function images() {
        return $this->hasMany('\Modules\Product\Entities\ProductReviewImage', 'review_id');
    }

    public function likes() {
        return $this->hasMany('\Modules\Product\Entities\ProductReviewLike', 'review_id')->where('like', 1);
    }

    public function comments() {
        return $this->hasMany('\Modules\Product\Entities\ProductReviewComment', 'review_id');
    }
}
