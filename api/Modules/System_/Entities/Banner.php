<?php

namespace Modules\System\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Banner extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'media__banners';

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
    protected $fillable = ['name', 'status'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'     => 'integer',
        'status' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    public function banners() {
        return $this->hasMany('\Modules\System\Entities\BannerImage')->orderBy('sort_order', 'asc');
    }
}
