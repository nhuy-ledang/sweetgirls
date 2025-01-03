<?php namespace Modules\Page\Entities;

use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

class Menu extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pg__menus';

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
    protected $fillable = ['parent_id', 'page_id', 'translates', 'name', 'icon', 'image', 'source', 'link', 'is_sub', 'is_redirect', 'is_sidebar', 'is_header', 'is_footer', 'sort_order', 'status'];

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
        'id'          => 'integer',
        'parent_id'   => 'integer',
        'page_id'     => 'integer',
        'translates'  => 'json',
        'is_sub'      => 'boolean',
        'is_sidebar'  => 'boolean',
        'is_header'   => 'boolean',
        'is_footer'   => 'boolean',
        'is_redirect' => 'boolean',
        'sort_order'  => 'integer',
        'status'      => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['source_name', 'raw_url', 'thumb_url', 'small_url'];

    public function getSourceNameAttribute() {
        $list = ['product' => 'Sản phẩm', 'project' => 'Dự án', 'news' => 'Tin tức', 'library' => 'Thư viện'];
        return !is_null($this->source) && isset($list[$this->source]) ? $list[$this->source] : $this->source;

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

    public function childs() {
        return $this->hasMany('\Modules\Page\Entities\Menu', 'parent_id', 'id')->orderBy('sort_order', 'asc');
    }

    public function page() {
        return $this->belongsTo('\Modules\Page\Entities\Page', 'page_id', 'id');
    }

    public function descs() {
        return $this->hasMany('\Modules\Page\Entities\MenuDesc', 'id', 'id');
    }
}
