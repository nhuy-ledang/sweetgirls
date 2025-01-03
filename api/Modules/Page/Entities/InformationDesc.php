<?php namespace Modules\Page\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class InformationDesc extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pg__information_desc';

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
    protected $fillable = ['id', 'lang', 'title', 'meta_title', 'meta_description', 'meta_keyword', 'description', 'short_description', 'alias'];

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
        'idx' => 'integer',
        'id'  => 'integer',
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
    public function information() {
        return $this->belongsTo('\Modules\Page\Entities\Information', 'id', 'id');
    }
}
