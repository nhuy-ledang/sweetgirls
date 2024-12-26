<?php namespace Modules\Usr\Sentinel;

use Modules\Core\Entities\Eloquent\CoreModel;

//use Cartalyst\Sentinel\Persistences\EloquentPersistence;

// Refer: Cartalyst\Sentinel\Persistences\EloquentPersistence;
class EloquentPersistence extends CoreModel implements PersistenceInterface {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'permissions',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'json',
    ];
}
