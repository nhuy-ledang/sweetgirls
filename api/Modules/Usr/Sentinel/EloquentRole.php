<?php namespace Modules\Usr\Sentinel;

use Modules\Core\Entities\Eloquent\CoreModel;

//use Cartalyst\Sentinel\Roles\EloquentRole;

// Refer: Cartalyst\Sentinel\Roles\EloquentRole
class EloquentRole extends CoreModel {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'permissions'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'permissions' => 'json',
    ];

    /**
     * {@inheritdoc}
     */
    public function getRoleId(): int {
        return $this->getKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getRoleSlug(): string {
        return $this->slug;
    }
}
