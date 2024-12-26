<?php namespace Modules\Core\Repositories;

/**
 * Interface CoreRepository
 * @package Modules\Core\Repositories
 */
interface BaseRepository {
    /**
     * @param  int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($id);

    /**
     * Return a collection of all elements of the resource
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();

    /**
     * Create a resource
     * @param $data
     * @return mixed
     */
    public function create($data);

    /**
     * Update a resource
     * @param $model
     * @param  array $data
     * @return mixed
     */
    public function update($model, $data);

    /**
     * Destroy a resource
     * @param $model
     * @return mixed
     */
    public function destroy($model);

    /**
     * @return \Eloquent | \Illuminate\Database\Eloquent\Model;
     */
    public function getModel();

    /**
     * Find a resource by an array of attributes
     * @param  array $attributes
     * @return object
     */
    public function findByAttributes(array $attributes);

    /**
     * Return a collection of elements who's ids match
     * @param array $ids
     * @return mixed
     */
    public function findByMany(array $ids);

    /**
     * Clear the cache for this Repositories' Entity
     * @return bool
     */
    public function clearCache();

    /**
     * @param $attributes
     * @param bool $paging
     * @param integer $per_page
     * @return mixed
     */
    public function filterByAttribute($attributes, $paging = true, $per_page = null);

    public function paginate($perPage = null, $columns = null, $pageName = 'page', $page = null);

}
