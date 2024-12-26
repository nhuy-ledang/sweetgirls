<?php namespace Modules\Media\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Folder extends CoreModel {
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'media__folders';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['parent_id', 'name'];

    /**
     * The attributes that should be hidden for serialization.
     * @var array
     */
    protected $hidden = ['updated_at'];

    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [
        'id'        => 'integer',
        'parent_id' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = [];
}
