<?php namespace Modules\Page\Entities;

use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

class PageContent extends CoreModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pg__page_contents';

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
    protected $fillable = ['page_id', 'parent_id', 'module_id', 'translates', 'name', 'style', 'code', 'title', 'sub_title', 'short_description', 'description', 'table_contents', 'table_images', 'properties', 'is_overwrite', 'layout', 'tile', 'image', 'attach', 'menu_text', 'btn_text', 'btn_link', 'sort_order', 'status'];

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
        'page_id'        => 'integer',
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
    public function page() {
        return $this->belongsTo('\Modules\Page\Entities\Page', 'page_id', 'id');
    }

    public function descs() {
        return $this->hasMany('\Modules\Page\Entities\PageContentDesc', 'id', 'id');
    }

    public function module() {
        return $this->belongsTo('\Modules\Page\Entities\Module', 'code', 'code');
    }

    public function getWidget() {
        $temp = explode('_', $this->tile);
        $code = $temp[0];
        $class_id = count($temp) > 1 ? (int)$temp[1] : 0;
        $widget = \Modules\Page\Entities\Widget::where('code', $code)->first();
        /*if ($widget && !empty($widget->cf_data['classes'])) {
            foreach ($widget->cf_data['classes'] as $cl) {
                if ((int)$cl['id'] == $class_id) {
                    $widget->class_id = $class_id;
                    $widget->preview = $cl['preview'];
                    break;
                }
            }
        }*/
        return $widget;
    }
}