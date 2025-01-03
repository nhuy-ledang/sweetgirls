<?php namespace Modules\Staff\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class Department
 *
 * @package Modules\Staff\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class Department extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'st__departments';

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
    protected $fillable = ['idx', 'name', 'description'];

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
        'id' => 'integer',
    ];
}
