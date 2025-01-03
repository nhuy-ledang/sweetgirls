<?php namespace Modules\Product\Entities;

use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class Category
 *
 * @package Modules\Product\Entities

 
 */
class Category extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__categories';

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
    protected $fillable = ['parent_id', 'translates', 'name', 'meta_title', 'meta_description', 'meta_keyword', 'short_description', 'description', 'image', 'icon', 'banner', 'layout', 'sort_order', 'status', 'show', 'alias', 'properties', 'options'];

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
    protected $appends = ['raw_url', 'thumb_url', 'small_url', 'icon_url', 'banner_url', 'preview', 'previews'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'parent_id'  => 'integer',
        'translates' => 'json',
        'sort_order' => 'integer',
        'status'     => 'boolean',
        'show'       => 'boolean',
    ];

    public function getDescriptionAttribute($value) {
        return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }

    public function getRawUrlAttribute() {
        return media_url_file($this->image);
    }

    public function getThumbUrlAttribute() {
        return media_url_file(Imagy::getThumbnail($this->image, 'thumb'));
    }

    public function getSmallUrlAttribute() {
        return media_url_file(Imagy::getThumbnail($this->image, 'small'));
    }

    public function getIconUrlAttribute() {
        return media_url_file($this->icon);
    }

    public function getBannerUrlAttribute() {
        return media_url_file($this->banner);
    }

    public function getHrefAttribute() {
        return config('app.url') . ($this->alias ? ('/' . $this->alias) : "/product/category?path={$this->id}");
    }

    public function getPreviewAttribute() {
        return config('app.url') . "/assets/templates/product/cat-" . ($this->layout ? $this->layout : 'layout1') . ".jpg";
    }

    public function getPreviewsAttribute() {
        return [
            'layout1' => config('app.url') . "/assets/templates/product/cat-layout1.jpg",
            'layout2' => config('app.url') . "/assets/templates/product/cat-layout2.jpg",
            'layout3' => config('app.url') . "/assets/templates/product/cat-layout2.jpg",
        ];
    }

    /**
     * Relationship
     */
    public function descs() {
        return $this->hasMany('\Modules\Product\Entities\CategoryDesc', 'id', 'id');
    }

    public function childs() {
        return $this->hasMany('\Modules\Product\Entities\Category', 'parent_id', 'id');
    }
}
