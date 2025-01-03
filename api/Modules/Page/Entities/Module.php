<?php namespace Modules\Page\Entities;

use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

class Module extends CoreModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pg__modules';

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
    protected $fillable = ['translates', 'name', 'idx', 'code', 'title', 'sub_title', 'short_description', 'description', 'table_contents', 'table_images', 'properties', 'layout', 'tile', 'image', 'attach', 'menu_text', 'btn_text', 'btn_link', 'cf_data', 'user_guide', 'sort_order'];

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
        'translates'     => 'json',
        'table_contents' => 'json',
        'table_images'   => 'json',
        'properties'     => 'json',
        'cf_data'        => 'json',
        'sort_order'     => 'integer',
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
    public function descs() {
        return $this->hasMany('\Modules\Page\Entities\ModuleDesc', 'id', 'id');
    }
}
