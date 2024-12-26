<?php namespace Modules\Core\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Repositories\BaseRepository;

/**
 * Class EloquentCoreRepository
 *
 * @package Modules\Core\Repositories\Eloquent
 */
abstract class EloquentBaseRepository implements BaseRepository {
    /**
     * @var int
     */
    protected $cacheExpire = 720; // Minutes - 12 hour

    /**
     * @var int
     */
    protected $cacheExpireMin = 60; // Minutes - 1 hour

    /**
     * @var int
     */
    protected $cacheExpireMax = 10080; // Minutes -  1 week

    /**
     * @var \Illuminate\Database\Eloquent\Model An instance of the Eloquent Model
     */
    protected $model;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct($model) {
        $this->model = $model;

        $this->cacheExpire = config('app.debug') ? 2 : 720;
    }

    /**
     * @param  int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($id) {
        return $this->model->find($id);
    }

    /**
     * @param  int $id
     * @return object
     */
    public function findOrFail($id) {
        return $this->model->findOrFail($id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all() {
        return $this->model->get();
    }

    /**
     * @param  mixed $data
     * @return object
     */
    public function create($data) {
        return $this->model->create($data);
    }

    /**
     * @param $model
     * @param  array $data
     * @return object
     */
    public function update($model, $data) {
        $model->update($data);

        return $model;
    }

    /**
     * @param  Model $model
     * @return bool
     */
    public function destroy($model) {
        return $model->delete();
    }

    /**
     * Return a new instance of the Eloquent Model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel() {
        return $this->model->newInstance();
    }

    /**
     * Find a resource by an array of attributes
     * @param  array $attributes
     * @return object
     */
    public function findByAttributes(array $attributes) {
        $query = $this->model->query();

        foreach ($attributes as $field => $value) {
            $query = $query->where($field, $value);
        }

        return $query->first();
    }

    /**
     * Return a collection of elements who's ids match
     * @param array $ids
     * @return mixed
     */
    public function findByMany(array $ids) {
        $query = $this->model->query();

        return $query->whereIn("id", $ids)->get();
    }

    /**
     * Clear the cache for this Repositories' Entity
     * @return bool
     */
    public function clearCache() {
        return true;
    }

    public function filterByAttribute($attributes, $paging = false, $per_page = null) {
        $query = $this->model->query();

        foreach ($attributes as $field => $value) {
            if ($value instanceof \Closure) {
                $query = $query->where($value);
            } else {
                $query = $query->where($field, $value);
            }
        }

        if ($paging) {
            return $query->paginate($per_page);
        } else {
            return $query->get();
        }
    }

    public function paginate($perPage = null, $columns = null, $pageName = 'page', $page = null) {
        return $this->model->paginate($perPage, $columns, $pageName, $page);
    }
}
