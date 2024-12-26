<?php namespace Modules\User\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/**
 * Class UserRanks
 * @package Modules\User\Entities
 */
class UserRank extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user__ranks';

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
    protected $fillable = ['name', 'value', 'rank', 'status'];

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
        'id'       => 'integer',
        'value'    => 'integer',
        'rank'     => 'integer',
        'status'   => 'boolean',
    ];
}
