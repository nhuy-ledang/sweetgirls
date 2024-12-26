<?php namespace Modules\Product\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class ProductReview
 *
 * @package Modules\Product\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class ProductReviewComment extends CoreModel {
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'pd__product_review_comments';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['user_id', 'review_id', 'comment'];

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
        'id'        => 'integer',
        'user_id'   => 'integer',
        'review_id' => 'integer',
    ];

    /**
     * Relationship
     */
    public function user() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User', 'user_id', 'id');
    }

    public function review() {
        return $this->belongsTo('Modules\Product\Entities\ProductReview', 'review_id');
    }
}
