<?php namespace Modules\Page\Entities;

use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

class Layout extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pg__layouts';

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
    protected $fillable = ['category_id', 'translates', 'name', 'description', 'image'];

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
    protected $appends = ['raw_url', 'thumb_url', 'small_url'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'category_id' => 'integer',
        'translates'  => 'json',
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

    public function category() {
        return $this->belongsTo('\Modules\Page\Entities\Category', 'category_id', 'id');
    }

    public function descs() {
        return $this->hasMany('\Modules\Page\Entities\LayoutDesc', 'id', 'id');
    }

    public function modules() {
        return $this->hasMany('\Modules\Page\Entities\LayoutModule')->orderBy('sort_order', 'asc');
    }
}
