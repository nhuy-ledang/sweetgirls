<?php namespace Modules\Product\Entities;

use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

class CategoryModule extends CoreModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pd__category_modules';

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
    protected $fillable = ['category_id', 'module_id', 'translates', 'name', 'code', 'title', 'sub_title', 'short_description', 'description', 'table_contents', 'table_images', 'properties', 'is_overwrite', 'layout', 'tile', 'image', 'attach', 'menu_text', 'btn_text', 'btn_link', 'sort_order', 'status'];

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
    protected $appends = ['raw_url', 'thumb_url', 'small_url', 'attach_url'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'             => 'integer',
        'category_id'    => 'integer',
        'module_id'      => 'integer',
        'translates'     => 'json',
        'table_contents' => 'json',
        'table_images'   => 'json',
        'properties'     => 'json',
        'is_overwrite'   => 'boolean',
        'sort_order'     => 'integer',
        'status'         => 'boolean',
        'cf_data'        => 'json',
    ];

    public function getRawUrlAttribute() {
        return media_url_file($this->image);
    }

    public function getThumbUrlAttribute() {
        return media_url_file(Imagy::getThumbnail($this->image, 'thumb'));
    }

    public function getSmallUrlAttribute() {
        return media_url_file(Imagy::getThumbnail($this->image, 'small'));
    }

    public function getAttachUrlAttribute() {
        return media_url_file($this->attach);
    }

    /**
     * Relationship
     */
    public function category() {
        return $this->belongsTo('\Modules\Product\Entities\Category', 'category_id', 'id');
    }

    public function descs() {
        return $this->hasMany('\Modules\Product\Entities\CategoryModuleDesc', 'id', 'id');
    }

    public function module() {
        return $this->belongsTo('\Modules\Page\Entities\Module', 'code', 'code');
    }
}