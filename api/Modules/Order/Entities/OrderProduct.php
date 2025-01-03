<?php namespace Modules\Order\Entities;

use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

class OrderProduct extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order__products';

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
    protected $fillable = ['order_id', 'product_id', 'name', 'model', 'type', 'quantity', 'priceo', 'price', 'total', 'coins', 'message'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['image', 'alias'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['thumb_url', 'href'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'order_id'   => 'integer',
        'product_id' => 'integer',
        'quantity'   => 'integer',
        'priceo'     => 'double',
        'price'      => 'double',
        'total'      => 'double',
        'totalo'     => 'double',
        'order_num'  => 'integer',
        'coins'      => 'integer',
        'weight'     => 'double',
    ];

    public function getThumbUrlAttribute() {
        return media_url_file(Imagy::getThumbnail($this->image, 'thumb'));
    }

    public function getHrefAttribute() {
        return config('app.url') . ($this->alias ? ('/' . $this->alias) : "/product/product?product_id={$this->product_id}");
    }

    /**
     * Handle Data when eloquent return json data from database
     *
     * @return array
     */
    public function toArray() {
        $hidden = [];
        if (is_null($this->image)) $hidden = ['thumb_url'];
        if (is_null($this->type) || ($this->type && $this->type == 'I')) $hidden = ['href'];
        if (!empty($hidden)) $this->makeHidden($hidden);

        return parent::toArray();
    }

    /**
     * Relationship
     */
    public function order() {
        return $this->belongsTo('\Modules\Order\Entities\Order', 'order_id', 'id');
    }

    public function product() {
        return $this->belongsTo('\Modules\Product\Entities\Product', 'product_id', 'id');
    }

    public function shipping() {
        return $this->belongsTo('\Modules\Order\Entities\OrderShipping', 'order_id', 'order_id');
    }

    public function options() {
        return $this->hasMany('\Modules\Product\Entities\OrderOption', ['order_id', 'id'], ['order_id', 'order_product_id']);
    }
}
