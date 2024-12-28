<?php namespace Modules\System\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class Translate
 * @package Modules\System\Entities
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class Translate extends CoreModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'core__translates';

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
    protected $fillable = ['key', 'lang', 'value', 'translate'];

    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];
}
