<?php namespace Modules\Core\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

/***
 * Class Bank
 * @package Modules\Core\Entities

 
 */
class Bank extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'core__banks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
}
