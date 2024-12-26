<?php namespace Modules\Page\Entities;

use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

class Page extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pg__pages';

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
    protected $fillable = ['category_id', 'layout_id', 'translates', 'name', 'short_description', 'description', 'table_contents', 'properties', 'style', 'is_sub', 'is_land', 'home', 'bottom', 'image', 'icon', 'banner', 'sort_order', 'status', 'alias', 'meta_title', 'meta_description', 'meta_keyword'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['parent_id'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['raw_url', 'thumb_url', 'small_url', 'icon_url', 'banner_url', 'href'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'             => 'integer',
        'category_id'    => 'integer',
        'translates'     => 'json',
        'table_contents' => 'json',
        'properties'     => 'json',
        'is_sub'         => 'boolean',
        'is_land'        => 'boolean',
        'home'           => 'boolean',
        'bottom'         => 'boolean',
        'sort_order'     => 'integer',
        'status'         => 'boolean',
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
        if ($this->alias) {
            return config('app.url') . '/' . $this->alias;
        } else {
            return config('app.url') . '/page/page&page_id=' . $this->id;
        }
    }

    public function category() {
        return $this->belongsTo('\Modules\Page\Entities\Category', 'category_id', 'id');
    }

    public function descs() {
        return $this->hasMany('\Modules\Page\Entities\PageDesc', 'id', 'id');
    }

    public function contents() {
        return $this->hasMany('\Modules\Page\Entities\PageContent')->orderBy('sort_order', 'asc');
    }
}
