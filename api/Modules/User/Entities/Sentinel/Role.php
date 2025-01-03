<?php namespace Modules\User\Entities\Sentinel;

use Cartalyst\Sentinel\Roles\EloquentRole;
use Modules\Core\Traits\NodeTreeTrait;

/**
 * Class Role
 * @package Modules\User\Entities\Sentinel
 */
class Role extends EloquentRole {
    use NodeTreeTrait;

    protected $fillable = [
        'name',
        'slug',
        'permissions',
        'parent_id',
        'level',
        'lft',
        'rgt',
    ];

    public function getPermissionsArrayAttribute() {
        if (!$this->attributes['permissions']) {
            return [];
        }
        return json_decode($this->attributes['permissions'], true);
    }

    public function __call($method, $parameters) {


        if (!method_exists($this, $method)) {
            foreach (app('modules')->getOrdered() as $enabledModule) {
                $relations = config(strtolower('asgard.' . $enabledModule->getName()) . '.relations.' . get_class($this));
                if (!is_null($relations) || !is_null($relations = config(strtolower('asgard.' . $enabledModule->getName()) . '.relations.' . class_basename($this)))) {
                    foreach ($relations as $relationName => $relationValue) {
                        if ($relationName == $method) {
                            return $relationValue($this);
                        }
                    }
                }
            }
        }


        #i: No relation found, return the call to parent (Eloquent) to handle it.
        return parent::__call($method, $parameters);
    }


}
