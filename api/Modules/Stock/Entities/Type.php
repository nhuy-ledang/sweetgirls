<?php namespace Modules\Stock\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class Stock
 *
 * @package Modules\Stock\Entities
 * @author TNT <nhattruong.tedfast@gmail.com>
 * @copyright (c) Tedfast
 */
class Type extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sto__types';

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
    protected $fillable = ['name', 'colour', 'image', 'description', 'uses', 'sort_order', 'status'];

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
        'sort_order' => 'integer',
        'status'     => 'boolean',
    ];
}
