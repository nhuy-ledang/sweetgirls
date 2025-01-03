<?php namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

class Product extends CoreModel {
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__products';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['master_id', 'category_id', 'categories', 'manufacturer_id', 'name', 'long_name', 'model', 'num_of_child', 'is_gift', 'is_coin_exchange', 'is_free', 'is_included', 'no_cod', 'price', 'coins', 'weight', 'length', 'width', 'height', 'price_min', 'price_max', 'gift_set_id', 'image', 'banner', 'top', 'sort_order', 'status', 'alias', 'meta_title', 'meta_description', 'meta_keyword', 'short_description', 'description', 'tag', 'link', 'properties', 'user_guide', 'stock_status', 'quantity', 'unit'];

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
        'id'           => 'integer',
        'master_id'    => 'integer',
        'category_id'  => 'integer',
        'translates'   => 'json',
        'liked'        => 'boolean',
        'price'        => 'double',
        'special'      => 'double',
        'num_of_child' => 'integer',
        'is_gift'      => 'boolean',
        'is_coin_exchange' => 'boolean',
        'is_free'      => 'boolean',
        'is_included'  => 'boolean',
        'no_cod'       => 'boolean',
        'coins'        => 'integer',
        'weight'       => 'double',
        'length'       => 'double',
        'width'        => 'double',
        'height'       => 'double',
        'price_min'    => 'double',
        'price_max'    => 'double',
        'gift_set_id'  => 'integer',
        'quantity'     => 'integer',
        'sort_order'   => 'integer',
        'top'          => 'boolean',
        'status'       => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['idx', 'raw_url', 'thumb_url', 'small_url', 'banner_url', 'href'];

    public function getRawUrlAttribute() {
        return media_url_file($this->image);
    }

    public function getThumbUrlAttribute() {
        return media_url_file(Imagy::getThumbnail($this->image, 'thumb'));
    }

    public function getSmallUrlAttribute() {
        return media_url_file(Imagy::getThumbnail($this->image, 'small'));
    }

    public function getBannerUrlAttribute() {
        return media_url_file($this->banner);
    }

    public function getDescriptionAttribute($value) {
        return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }

    public function getIdxAttribute() {
        return $this->model ? $this->model : '';
    }

    public function getHrefAttribute() {
        return config('app.url') . ($this->alias ? ('/' . $this->alias) : "/product/product?product_id={$this->id}");
    }

    /**
     * Handle Data when eloquent return json data from database
     *
     * @return array
     */
    public function toArray() {
        $hidden = [];
        if (is_null($this->image)) $hidden = ['raw_url', 'thumb_url', 'small_url'];
        if (is_null($this->banner)) $hidden[] = 'banner_url';
        if (!empty($hidden)) $this->makeHidden($hidden);

        return parent::toArray();
    }

    public function master() {
        return $this->belongsTo('\Modules\Product\Entities\Product', 'master_id', 'id')->withTrashed();
    }

    public function childs() {
        return $this->hasMany('\Modules\Product\Entities\Product', 'master_id', 'id');//->withTrashed();
    }

    public function descs() {
        return $this->hasMany('\Modules\Product\Entities\ProductDesc', 'id', 'id');
    }

    public function category() {
        return $this->belongsTo('\Modules\Product\Entities\Category', 'category_id', 'id');
    }

    public function gift_set() {
        return $this->belongsTo('\Modules\Product\Entities\GiftSet');
    }

    public function images() {
        return $this->hasMany('\Modules\Product\Entities\ProductImage', 'product_id');
    }

    public function options() {
        return $this->hasMany('\Modules\Product\Entities\ProductOption', 'product_id', 'id')
            ->leftJoin('pd__options as opt', 'opt.id', '=', 'option_id')
            ->select(['pd__product_options.*', 'opt.name as name']);
    }

    public function variants() {
        return $this->hasMany('\Modules\Product\Entities\ProductVariant', 'product_id');
    }
}
