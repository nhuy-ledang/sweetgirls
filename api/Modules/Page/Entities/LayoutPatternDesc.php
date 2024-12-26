<?php namespace Modules\Page\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class LayoutPatternDesc extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pg__layout_pattern_desc';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idx';

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
    protected $fillable = ['id', 'lang', 'name', 'title', 'sub_title', 'short_description', 'description', 'table_contents', 'table_images', 'menu_text', 'btn_text', 'btn_link'];

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
        'idx'            => 'integer',
        'id'             => 'integer',
        'table_contents' => 'json',
        'table_images'   => 'json',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Relationship
     */
    public function pattern() {
        return $this->belongsTo('\Modules\Page\Entities\LayoutPattern', 'id', 'id');
    }
}
