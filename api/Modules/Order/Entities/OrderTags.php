<?php namespace Modules\Order\Entities;

use Imagy;
use Modules\Core\Entities\Eloquent\CoreModel;

class OrderTags extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order__tags';

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
    protected $fillable = ['name'];

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
    protected $appends = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
    ];

    /**
     * Relationship
     */
}
