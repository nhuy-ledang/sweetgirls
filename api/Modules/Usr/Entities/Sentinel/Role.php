<?php namespace Modules\Usr\Entities\Sentinel;

use Modules\Usr\Sentinel\EloquentRole;

class Role extends EloquentRole {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usr__roles';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'permissions' => 'json',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];
}
