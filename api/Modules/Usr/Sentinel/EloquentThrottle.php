<?php namespace Modules\Usr\Sentinel;

use Modules\Core\Entities\Eloquent\CoreModel;

//use Cartalyst\Sentinel\Throttling\EloquentThrottle;

// Refer: Cartalyst\Sentinel\Throttling\EloquentThrottle
class EloquentThrottle extends CoreModel {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ip',
        'type',
    ];
}
