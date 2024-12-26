<?php namespace Modules\Page\Http\Controllers\Api;

use Illuminate\Http\Request;
use Imagy;
use Modules\Core\Repositories\SeoUrlRepository;
use Modules\Page\Repositories\LayoutDescRepository;
use Modules\Page\Repositories\LayoutModuleDescRepository;
use Modules\Page\Repositories\LayoutModuleRepository;
use Modules\Page\Repositories\LayoutRepository;
use Modules\Page\Repositories\PageContentDescRepository;
use Modules\Page\Repositories\PageDescRepository;
use Modules\Page\Repositories\PageRepository;
use Modules\Page\Repositories\PageContentRepository;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;

/**
 * Class PageController
 *
 * @package Modules\Page\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2121-07-09
 */
class PageController extends ApiBaseModuleController {
    /**
     * @var \Modules\Page\Repositories\PageDescRepository
     */
    protected $page_desc_repository;

    /**
     * @var \Modules\Page\Repositories\PageContentRepository
     */
    protected $page_content_repository;

    /**
     * @var \Modules\Page\Repositories\PageContentDescRepository
     */
    protected $page_content_desc_repository;

    /**
     * @var \Modules\Page\Repositories\LayoutRepository
     */
    protected $layout_repository;

    /**
     * @var \Modules\Page\Repositories\LayoutDescRepository
     */
    protected $layout_desc_repository;

    /**
     * @var \Modules\Page\Repositories\LayoutModuleRepository
     */
    protected $layout_module_repository;

    /**
     * @var \Modules\Page\Repositories\LayoutModuleDescRepository
     */
    protected $layout_module_desc_repository;

    /**
     * @var \Modules\Core\Repositories\SeoUrlRepository
     */
    protected $seo_url_repository;

    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    protected $file_repository;

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    public function __construct(Request $request,
                                PageRepository $page_repository,
                                PageDescRepository $page_desc_repository,
                                PageContentRepository $page_content_repository,
                                PageContentDescRepository $page_content_desc_repository,
                                LayoutRepository $layout_repository,
                                LayoutDescRepository $layout_desc_repository,
                                LayoutModuleRepository $layout_module_repository,
                                LayoutModuleDescRepository $layout_module_desc_repository,
                                SeoUrlRepository $seo_url_repository,
                                FileRepository $file_repository,
                                FileService $fileService) {
        $this->model_repository = $page_repository;
        $this->page_desc_repository = $page_desc_repository;
        $this->page_content_repository = $page_content_repository;
        $this->page_content_desc_repository = $page_content_desc_repository;
        $this->layout_repository = $layout_repository;
        $this->layout_desc_repository = $layout_desc_repository;
        $this->layout_module_repository = $layout_module_repository;
        $this->layout_module_desc_repository = $layout_module_desc_repository;
        $this->seo_url_repository = $seo_url_repository;
        $this->file_repository = $file_repository;
        $this->fileService = $fileService;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreate() {
        return [
            'name' => 'required',
        ];
    }

    /**
     * Get the validation rules for update.
     *
     * @param int $id
     * @return array
     */
    protected function rulesForUpdate($id) {
        return [
            'name' => 'required',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/pg_pages_all",
     *   summary="Get Page All",
     *   operationId="getPageAll",
     *   tags={"BackendPages"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function all() {
        try {
            $results = $this->model_repository->all();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pg_pages",
     *   summary="Get Pages",
     *   operationId="getPages",
     *   tags={"BackendPages"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="App-Env", in="query", required=false, description="ENV", example="cms"),
     *   @OA\Parameter(name="paging", in="query", required=false, description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", required=false, description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", required=false, description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", required=false, description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", required=false, description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", required=false, description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example="%7B%22subject_id%22%3A0%2C%22course_id%22%3A0%2C%22q%22%3A%22%22%7D"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function index() {
        try {
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $order = (string)$this->request->get('order');
            $sort = !$sort ? 'id' : strtolower($sort);
            $order = !$order ? 'asc' : strtolower($order);
            $queries = [
                'and'      => [],
                'whereRaw' => [],
            ];
            $fields = ['*'];
            $results = $this->setUpQueryBuilder($this->model(), $queries, false, $fields)->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();

            $output = [];
            foreach ($results as $result) {
                $properties = $result->properties;
                if ($properties && !empty($properties['banner'])) {
                    $properties['banner_thumb_url'] = media_url_file(Imagy::getThumbnail($properties['banner'], 'thumb'));
                }
                $result->properties = $properties;
                $output[] = $result;
            }

            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging === 'true' ? true : ($paging === 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($output);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();
                return $this->respondWithPaging($output, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pg_pages/{id}",
     *   summary="Get Page",
     *   operationId="getPage",
     *   tags={"BackendPages"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="App-Env", in="query", required=false, description="ENV", example="cms"),
     *   @OA\Parameter(name="id", in="path", required=true, description="Page Id", example="1"),
     *   @OA\Parameter(name="data", in="query", required=false, description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function show($id) {
        try {
            $fields = ['*'];
            $model = $this->setUpQueryBuilder($this->model(), [], false, $fields)->where('id', $id)->first();
            if (!$model) return $this->errorNotFound();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * Update Seo Url
     * @param $id
     * @param $lang
     * @param $input
     * @return bool|\Illuminate\Support\Facades\Response|mixed
     */
    private function updateSeoUrl($id, $lang, &$input) {
        $alias = $this->request->get('alias');
        if ($alias) {
            $query = "page_id=$id";
            $seo_urls = $this->seo_url_repository->getSeoUrlsByKeyword($alias);
            $errorKey = '';
            foreach ($seo_urls as $item) {
                if (!($item->query == $query)) {
                    $errorKey = 'keyword.exists';
                    break;
                }
            }
            if ($errorKey) return [$errorKey, false];
            $seo_url = $this->seo_url_repository->getSeoUrlByQuery($query, $lang);
            if ($seo_url) {
                if ($seo_url->keyword != $alias && $seo_url->keyword != seo_url($alias)) {
                    $input['alias'] = seo_url($alias);
                    $this->seo_url_repository->getModel()->where('id', $seo_url->id)->update(['keyword' => $input['alias']]);
                } else {
                    $input['alias'] = $seo_url->keyword;
                }
            } else {
                $input['alias'] = seo_url($alias);
                $seo_url = $this->seo_url_repository->create(['lang' => $lang, 'query' => $query, 'keyword' => $input['alias'], 'push' => "route=page/page&$query"]);
            }
            return [$errorKey, $seo_url];
        }
        return [false, false];
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_pages",
     *   summary="Create Page",
     *   operationId="createPage",
     *   tags={"BackendPages"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="category_id", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="description", type="string", example=""),
     *       @OA\Property(property="sort_order", type="integer", example=0),
     *       @OA\Property(property="status", type="integer", example="1"),
     *     ),
     *   ),
     *   @OA\Parameter(name="App-Env", in="query", description="ENV", example="cms"),
     *   @OA\Parameter(name="Device-Platform", in="query", description="ENV", example="web"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function store() {
        try {
            $input = $this->request->all();
            $layout_id = (int)$this->request->get('layout_id');
            $input['layout_id'] = $layout_id ? $layout_id : null;
            $category_id = $this->request->get('category_id');
            if (!is_null($category_id) && intval($category_id)) $input['category_id'] = $category_id;
            $table_contents = $this->request->get('table_contents');
            $input['table_contents'] = [];
            if ($table_contents) {
                if (is_string($table_contents)) $table_contents = json_decode($table_contents, true);
                $input['table_contents'] = $table_contents;
            }
            $properties = $this->request->get('properties');
            $input['properties'] = [];
            if ($properties) {
                if (is_string($properties)) $properties = json_decode($properties, true);
                $input['properties'] = $properties;
            }
            // For properties
            $prop_banner = '';
            $file_path = $this->request->get('filepath_0');
            if ($file_path) {
                $prop_banner = $file_path;
            } else {
                list($file, $errorKey) = $this->getRequestFile('file_0');
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => trim($this->module_prefix, '_')]);
                    if (!is_string($savedFile)) $prop_banner = $savedFile->path;
                }
            }
            if ($prop_banner) $input['properties']['banner'] = $prop_banner;
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Check seo url exist
            $alias = $this->request->get('alias');
            if ($alias) {
                $seo_urls = $this->seo_url_repository->getSeoUrlsByKeyword($alias);
                if ($seo_urls->count() > 0) return $this->errorWrongArgs('keyword.exists');
            }
            // Create model
            $model = $this->model_repository->create($input);
            // Update seo url
            $input = [];
            list($errorKey, $seo_url) = $this->updateSeoUrl($model->id, 'vi', $input);
            if (!$errorKey && !empty($input)) $model = $this->model_repository->update($model, $input);
            // Upload image
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $model = $this->model_repository->update($model, ['image' => $file_path]);
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                //if ($errorKey) return $this->errorWrongArgs($errorKey);
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => 'pg', 'object_id' => $model->id]);
                    if (!is_string($savedFile)) $model = $this->model_repository->update($model, ['image' => $savedFile->path]);
                }
            }
            // Upload icon
            $ic_path = $this->request->get('ic_path');
            if ($ic_path) {
                $model = $this->model_repository->update($model, ['icon' => $ic_path]);
            } else {
                list($ic_file, $errorKey) = $this->getRequestFile('ic_file');
                if ($ic_file) {
                    $savedFile = $this->fileService->store($ic_file, ['sub' => 'icons']);
                    if (!is_string($savedFile)) $model = $this->model_repository->update($model, ['icon' => $savedFile->path]);
                }
            }
            // Upload banner
            $bn_path = $this->request->get('bn_path');
            if ($bn_path) {
                $model = $this->model_repository->update($model, ['banner' => $bn_path]);
            } else {
                list($bn_file, $errorKey) = $this->getRequestFile('bn_file');
                if ($bn_file) {
                    $savedFile = $this->fileService->store($bn_file, ['sub' => 'banners']);
                    if (!is_string($savedFile)) $model = $this->model_repository->update($model, ['banner' => $savedFile->path]);
                }
            }
            // Clone layout
            if ($layout_id) {
                $layout = $this->layout_repository->find($layout_id);
                foreach ($layout->modules as $k => $module) {
                    $this->page_content_repository->create(array_merge($module->toArray(), ['page_id' => $model->id, 'style' => $module->code, 'sort_order' => $k + 1]));
                }
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/pg_pages/{id}",
     *   summary="Update Page",
     *   operationId="updatePage",
     *   tags={"BackendPages"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Page Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="category_id", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="description", type="string", example=""),
     *       @OA\Property(property="sort_order", type="integer", example=0),
     *       @OA\Property(property="status", type="integer", example="1"),
     *     ),
     *   ),
     *   @OA\Parameter(name="App-Env", in="query", description="ENV", example="cms"),
     *   @OA\Parameter(name="Device-Platform", in="query", description="ENV", example="web"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function update($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->all();
            if (isset($input['layout_id'])) unset($input['layout_id']);
            $category_id = $this->request->get('category_id');
            if (!is_null($category_id) && intval($category_id)) $input['category_id'] = $category_id;
            $table_contents = $this->request->get('table_contents');
            $input['table_contents'] = [];
            if ($table_contents) {
                if (is_string($table_contents)) $table_contents = json_decode($table_contents, true);
                $input['table_contents'] = $table_contents;
            }
            $properties = $this->request->get('properties');
            $input['properties'] = [];
            if ($properties) {
                if (is_string($properties)) $properties = json_decode($properties, true);
                $input['properties'] = $properties;
            }
            // For properties
            $prop_banner = '';
            $file_path = $this->request->get('filepath_0');
            if ($file_path) {
                $prop_banner = $file_path;
            } else {
                list($file, $errorKey) = $this->getRequestFile('file_0');
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => trim($this->module_prefix, '_')]);
                    if (!is_string($savedFile)) $prop_banner = $savedFile->path;
                }
            }
            if ($prop_banner) $input['properties']['banner'] = $prop_banner;
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Update seo url
            list($errorKey, $seo_url) = $this->updateSeoUrl($id, 'vi', $input);
            if ($errorKey) return $this->errorWrongArgs($errorKey);
            // Upload image
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $input['image'] = $file_path;
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                if ($errorKey) return $this->errorWrongArgs($errorKey);
                if ($file) {
                    // Unlink old image
                    $oldFile = null;
                    //if ($model->image) $oldFile = $this->file_repository->findByAttributes(['object' => 'pgc', 'path' => $model->image]);
                    // New image
                    $savedFile = $this->fileService->store($file, ['sub' => 'pg', 'object_id' => $model->id]);
                    if (!is_string($savedFile)) {
                        $input['image'] = $savedFile->path;
                        // Unlink old image
                        //if ($oldFile) $this->file_repository->destroy($oldFile);
                    }
                }
            }
            // Upload icon
            $ic_path = $this->request->get('ic_path');
            if ($ic_path) {
                $input['icon'] = $ic_path;
            } else {
                list($ic_file, $errorKey) = $this->getRequestFile('ic_file');
                if ($ic_file) {
                    $savedFile = $this->fileService->store($ic_file, ['sub' => 'icons']);
                    if (!is_string($savedFile)) $input['icon'] = $savedFile->path;
                }
            }
            // Upload banner
            $bn_path = $this->request->get('bn_path');
            if ($bn_path) {
                $input['banner'] = $bn_path;
            } else {
                list($bn_file, $errorKey) = $this->getRequestFile('bn_file');
                if ($bn_file) {
                    $savedFile = $this->fileService->store($bn_file, ['sub' => 'banners']);
                    if (!is_string($savedFile)) $input['banner'] = $savedFile->path;
                }
            }
            // Update Model
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *   path="/backend/pg_pages/{id}",
     *   summary="Update Page Partial",
     *   operationId="UpdatePagePartial",
     *   tags={"BackendPages"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Page Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example=1),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function patch($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['status', 'home', 'bottom']);
            if (isset($input['home']) && !is_null($input['home'])) {
                $input['home'] = (boolean)$input['home'];
                if ($input['home'] === true) {
                    $this->model_repository->getModel()->where('id', '<>', $model->id)->update(['home' => false]);
                }
            }
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/pg_pages/{id}",
     *   summary="Delete Page",
     *   operationId="deletePage",
     *   tags={"BackendPages"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Page Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_pages/{id}/copy",
     *   summary="Copy Page",
     *   operationId="copyPage",
     *   tags={"BackendPages"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="category_id", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="description", type="string", example=""),
     *       @OA\Property(property="sort_order", type="integer", example=0),
     *       @OA\Property(property="status", type="integer", example="1"),
     *     ),
     *   ),
     *   @OA\Parameter(name="App-Env", in="query", description="ENV", example="cms"),
     *   @OA\Parameter(name="Device-Platform", in="query", description="ENV", example="web"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function copy($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $contents = $model->contents;
            $input = $this->request->all();
            $input = array_merge($model->toArray(), $input, ['home' => false]);
            $category_id = $this->request->get('category_id');
            if (!is_null($category_id) && intval($category_id)) $input['category_id'] = $category_id;
            $table_contents = $this->request->get('table_contents');
            if ($table_contents) {
                if (is_string($table_contents)) $table_contents = json_decode($table_contents, true);
                $input['table_contents'] = $table_contents;
            }
            $properties = $this->request->get('properties');
            if ($properties) {
                if (is_string($properties)) $properties = json_decode($properties, true);
                $input['properties'] = $properties;
            }
            // For properties
            $prop_banner = '';
            $file_path = $this->request->get('filepath_0');
            if ($file_path) {
                $prop_banner = $file_path;
            } else {
                list($file, $errorKey) = $this->getRequestFile('file_0');
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => trim($this->module_prefix, '_')]);
                    if (!is_string($savedFile)) $prop_banner = $savedFile->path;
                }
            }
            if ($prop_banner) $input['properties']['banner'] = $prop_banner;
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Create model
            $model = $this->model_repository->create($input);
            // Update seo url
            $input = [];
            list($errorKey, $seo_url) = $this->updateSeoUrl($model->id, 'vi', $input);
            if (!$errorKey && !empty($input)) $model = $this->model_repository->update($model, $input);
            // Upload image
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $model = $this->model_repository->update($model, ['image' => $file_path]);
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                //if ($errorKey) return $this->errorWrongArgs($errorKey);
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => 'pg', 'object_id' => $model->id]);
                    if (!is_string($savedFile)) $model = $this->model_repository->update($model, ['image' => $savedFile->path]);
                }
            }
            // Upload icon
            $ic_path = $this->request->get('ic_path');
            if ($ic_path) {
                $model = $this->model_repository->update($model, ['icon' => $ic_path]);
            } else {
                list($ic_file, $errorKey) = $this->getRequestFile('ic_file');
                if ($ic_file) {
                    $savedFile = $this->fileService->store($ic_file, ['sub' => 'icons']);
                    if (!is_string($savedFile)) $model = $this->model_repository->update($model, ['icon' => $savedFile->path]);
                }
            }
            // Upload banner
            $bn_path = $this->request->get('bn_path');
            if ($bn_path) {
                $model = $this->model_repository->update($model, ['banner' => $bn_path]);
            } else {
                list($bn_file, $errorKey) = $this->getRequestFile('bn_file');
                if ($bn_file) {
                    $savedFile = $this->fileService->store($bn_file, ['sub' => 'banners']);
                    if (!is_string($savedFile)) $model = $this->model_repository->update($model, ['banner' => $savedFile->path]);
                }
            }
            // Clone page contents
            foreach ($contents as $k => $content) {
                $this->page_content_repository->create(array_merge($content->toArray(), ['page_id' => $model->id, 'sort_order' => $k + 1]));
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_pages/{id}/description",
     *   summary="Update Page Description",
     *   operationId="updatePagedescription",
     *   tags={"BackendPages"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Page Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="lang", type="string", example="en"),
     *       @OA\Property(property="meta_title", type="string", example=""),
     *       @OA\Property(property="meta_description", type="string", example=""),
     *       @OA\Property(property="meta_keyword", type="string", example=""),
     *       @OA\Property(property="alias", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Parameter(name="App-Env", in="query", description="ENV", example="cms"),
     *   @OA\Parameter(name="Device-Platform", in="query", description="ENV", example="web"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function description($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['lang', 'name', 'meta_title', 'meta_description', 'meta_keyword', 'description']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, ['lang' => 'required', 'name' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $lang = $this->request->get('lang');
            list($errorKey, $seo_url) = $this->updateSeoUrl($id, $lang, $input);
            if ($errorKey) return $this->errorWrongArgs($errorKey);
            if ($lang == 'vi') {
                //$model = $this->model_repository->update($model, $input);
            } else {
                $modelDesc = $this->page_desc_repository->findByAttributes(['id' => $id, 'lang' => $lang]);
                if (!$modelDesc) {
                    $modelDesc = $this->page_desc_repository->create(array_merge($input, ['id' => $id, 'lang' => $lang]));
                } else {
                    $modelDesc = $this->page_desc_repository->update($modelDesc, $input);
                }
                $translates = $model->translates ? $model->translates : [];
                $translates[] = $lang;
                $translates = array_unique($translates);
                $model->translates = $translates;
                $model->save();
            }
            $model->descs;
            //$model->makeVisible(['category_id', 'categories', 'meta_title', 'meta_description', 'meta_keyword', 'description', 'image', 'translates', 'alias', 'status']);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_pages/{id}/layout",
     *   summary="Copy Page To Layout",
     *   operationId="copyPageToLayout",
     *   tags={"BackendPages"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Page Id", example="1"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function layout($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Create layout
            $layout = $this->layout_repository->create($model->toArray());
            foreach ($model->descs as $desc) {
                $this->layout_desc_repository->create(array_merge($desc->toArray(), ['id' => $layout->id]));
            }
            // Clone layout modules
            foreach ($model->contents as $k => $item) {
                $module = $this->layout_module_repository->create(array_merge($item->toArray(), ['layout_id' => $layout->id, 'sort_order' => $k + 1]));
                foreach ($item->descs as $desc) {
                    $this->layout_module_desc_repository->create(array_merge($desc->toArray(), ['id' => $module->id]));
                }
            }

            return $this->respondWithSuccess($layout);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_pages_layouts",
     *   summary="Clone Page From Layouts",
     *   operationId="clonePageFromLayouts",
     *   tags={"BackendPages"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="ids", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function cloneLayouts() {
        try {
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $ids = $this->request->get('ids');
            $layout_ids = [];
            if (is_array($ids)) {
                $layout_ids = $ids;
            } else if ($ids) {
                foreach (explode(',', $ids) as $id) {
                    if (intval($id)) $layout_ids[] = (int)$id;
                }
            }
            $layout_ids = array_unique($layout_ids);
            if (!$layout_ids) return $this->errorWrongArgs();
            $layouts = $this->layout_repository->getModel()->whereIn('id', $layout_ids)->get();
            if (!$layouts) return $this->errorWrongArgs();
            $models = [];
            foreach ($layouts as $layout) {
                $data = array_merge($layout->toArray(), ['status' => false]);
                // Create model
                $model = $this->model_repository->create($data);
                // Create description
                foreach ($layout->descs as $desc) {
                    $this->page_desc_repository->create(array_merge($desc->toArray(), ['id' => $model->id]));
                }
                // Create modules
                foreach ($layout->modules as $k => $item) {
                    $module = $this->page_content_repository->create(array_merge($item->toArray(), ['page_id' => $model->id, 'sort_order' => $k + 1]));
                    foreach ($item->descs as $desc) {
                        $this->page_content_desc_repository->create(array_merge($desc->toArray(), ['id' => $module->id]));
                    }
                }
                $models[] = $model;
            }

            return $this->respondWithSuccess($models);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
