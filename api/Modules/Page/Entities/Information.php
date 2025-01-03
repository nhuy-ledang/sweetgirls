<?php namespace Modules\Page\Entities;

use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

class Information extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pg__informations';

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
    protected $fillable = ['parent_id', 'translates', 'title', 'short_description', 'description', 'meta_title', 'meta_description', 'meta_keyword', 'image', /*'avatar', 'icon', 'top', 'menu', 'bottom',*/
        'sort_order', 'status', 'alias'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'parent_id'  => 'integer',
        'translates' => 'json',
        'status'     => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['raw_url', 'thumb_url', 'small_url', 'href'];

    public function getRawUrlAttribute() {
        return media_url_file($this->image);
    }

    public function getThumbUrlAttribute() {
        return media_url_file(Imagy::getThumbnail($this->image, 'thumb'));
    }

    public function getSmallUrlAttribute() {
        return media_url_file(Imagy::getThumbnail($this->image, 'small'));
    }

    public function getDescriptionAttribute($value) {
        return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }

    public function getHrefAttribute() {
        return config('app.url') . ($this->alias ? ('/' . $this->alias) : "/page/information?information_id={$this->id}");
    }

    public function descs() {
        return $this->hasMany('\Modules\Page\Entities\InformationDesc', 'id', 'id');
    }
}
