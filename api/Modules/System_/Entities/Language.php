<?php namespace Modules\System\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class Language
 * @package Modules\System\Entities

 
 */
class Language extends CoreModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'core__languages';

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
    protected $fillable = ['name', 'code', 'filename', 'locale', 'image', 'sort_order', 'status'];

    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'sort_order' => 'integer',
        'status'     => 'boolean',
    ];
}
