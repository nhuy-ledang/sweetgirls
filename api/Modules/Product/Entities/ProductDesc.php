<?php namespace Modules\Product\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class ProductDesc extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__product_desc';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idx';

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
    protected $fillable = ['id', 'lang', 'name', 'long_name', 'short_description', 'description', 'properties', 'user_guide', 'tag', 'meta_title', 'meta_description', 'meta_keyword', 'delivery', 'warranty', 'image', 'image_alt', 'alias'];

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
        'idx' => 'integer',
        'id'  => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    public function getDescriptionAttribute($value) {
        return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Relationship
     */
    public function product() {
        return $this->belongsTo('\Modules\Product\Entities\Product', 'id', 'id');
    }
}
