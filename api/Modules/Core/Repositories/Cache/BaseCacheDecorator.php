<?php namespace Modules\Core\Repositories\Cache;

use Illuminate\Pagination\Paginator;
use Modules\Core\Repositories\BaseRepository;

abstract class BaseCacheDecorator implements BaseRepository
{
    /**
     * @var \Modules\Core\Repositories\BaseRepository
     */
    protected $repository;
    /**
     * @var \Illuminate\Cache\Repository
     */
    protected $cache;
    /**
     * @var string The entity name
     */
    protected $entityName;
    /**
     * @var string The application locale
     */
    protected $locale;

    /**
     * @var int caching time
     */
    protected $cacheTime;

    public function __construct()
    {
        $this->cache = app('Illuminate\Cache\Repository');
        $this->cacheTime = app('Illuminate\Config\Repository')->get('cache.time', 60);
        $this->locale = app()->getLocale();
    }

    /**
     * @param  int   $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->cache
            ->tags($this->entityName, 'global')
            ->remember("{$this->locale}.{$this->entityName}.find.{$id}", $this->cacheTime,
                function () use ($id) {
                    return $this->repository->find($id);
                }
            );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->cache
            ->tags($this->entityName, 'global')
            ->remember("{$this->locale}.{$this->entityName}.all", $this->cacheTime,
                function () {
                    return $this->repository->all();
                }
            );
    }

    /**
     * Return all categories in the given language
     *
     * @param  string $lang
     * @return object
     */
    public function allTranslatedIn($lang)
    {
        return $this->cache
            ->tags($this->entityName, 'global')
            ->remember("{$this->locale}.{$this->entityName}.allTranslatedIn.{$lang}", $this->cacheTime,
                function () use ($lang) {
                    return $this->repository->allTranslatedIn($lang);
                }
            );
    }

    /**
     * Find a resource by the given slug
     * @param  string $slug
     * @return object
     */
    public function findBySlug($slug)
    {
        return $this->cache
            ->tags($this->entityName, 'global')
            ->remember("{$this->locale}.{$this->entityName}.findBySlug.{$slug}", $this->cacheTime,
                function () use ($slug) {
                    return $this->repository->findBySlug($slug);
                }
            );
    }

    /**
     * Create a resource
     *
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        $this->cache->tags($this->entityName)->flush();

        return $this->repository->create($data);
    }

    /**
     * Update a resource
     *
     * @param        $model
     * @param  array $data
     * @return mixed
     */
    public function update($model, $data)
    {
        $this->cache->tags($this->entityName)->flush();

        return $this->repository->update($model, $data);
    }

    /**
     * Destroy a resource
     *
     * @param $model
     * @return mixed
     */
    public function destroy($model)
    {
        $this->cache->tags($this->entityName)->flush();

        return $this->repository->destroy($model);
    }

    /**
     * @return \Eloquent;
     */
    public function getModel()
    {
        return $this->repository->getModel();
    }

    /**
     * Find a resource by an array of attributes
     * @param  array  $attributes
     * @return object
     */
    public function findByAttributes(array $attributes)
    {
        $tagIdentifier = json_encode($attributes);

        return $this->cache
            ->tags($this->entityName, 'global')
            ->remember("{$this->locale}.{$this->entityName}.findByAttributes.{$tagIdentifier}", $this->cacheTime,
                function () use ($attributes) {
                    return $this->repository->findByAttributes($attributes);
                }
            );
    }

    /**
     * Return a collection of elements who's ids match
     * @param array $ids
     * @return mixed
     */
    public function findByMany(array $ids)
    {
        $tagIdentifier = json_encode($ids);

        return $this->cache
            ->tags($this->entityName, 'global')
            ->remember("{$this->locale}.{$this->entityName}.findByMany.{$tagIdentifier}", $this->cacheTime,
                function () use ($ids) {
                    return $this->repository->findByMany($ids);
                }
            );
    }

    /**
     * Clear the cache for this Repositories' Entity
     * @return bool
     */
    public function clearCache()
    {
        return $this->cache->tags($this->entityName)->flush();
    }

    public function filterByAttribute($attributes,$paging = true, $per_page = null)
    {
        $tagIdentifier = json_encode($attributes).$paging.$per_page.$this->getModel()->getPerPage().Paginator::resolveCurrentPage();

        return $this->cache
            ->tags($this->entityName, 'global')
            ->remember("{$this->locale}.{$this->entityName}.filterByAttribute.{$tagIdentifier}", $this->cacheTime,
                function () use ($attributes,$paging,$per_page) {
                    return $this->repository->filterByAttribute($attributes,$paging,$per_page);
                }
            );
    }
    public function paginate($perPage = null, $columns = null, $pageName = 'page', $page = null)
    {
        $tagIdentifier = json_encode($columns).$pageName.$this->getModel()->getPerPage().Paginator::resolveCurrentPage();

        return $this->cache
            ->tags($this->entityName, 'global')
            ->remember("{$this->locale}.{$this->entityName}.filterByAttribute.{$tagIdentifier}", $this->cacheTime,
                function () use ($perPage, $columns, $pageName, $page) {
                    return $this->repository->paginate($perPage, $columns, $pageName, $page);
                }
            );
    }

    public function getUniqueSlug($title)
    {
        return $this->cache
            ->tags($this->entityName, 'global')
            ->remember("{$this->locale}.{$this->entityName}.getSlug.{$title}", $this->cacheTime,
                function () use ($title) {
                    return $this->repository->getUniqueSlug($title);
                }
            );
    }

}
