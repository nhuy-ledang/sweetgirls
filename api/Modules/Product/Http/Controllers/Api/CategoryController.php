<?php namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\SeoUrlRepository;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\Product\Repositories\CategoryDescRepository;
use Modules\Product\Repositories\CategoryRepository;
use Modules\Product\Repositories\ProductRepository;

/**
 * Class CategoryController
 *
 * @package Modules\Product\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2021-10-28
 */
class CategoryController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\CategoryDescRepository
     */
    protected $category_desc_repository;

    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

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
                                CategoryRepository $category_repository,
                                CategoryDescRepository $category_desc_repository,
                                SeoUrlRepository $seo_url_repository,
                                ProductRepository $product_repository,
                                FileRepository $file_repository,
                                FileService $fileService) {
        $this->model_repository = $category_repository;
        $this->category_desc_repository = $category_desc_repository;
        $this->seo_url_repository = $seo_url_repository;
        $this->product_repository = $product_repository;
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
     *   path="/backend/pd_categories_all",
     *   summary="GetProductCategoryAll",
     *   operationId="GetProductCategoryAll",
     *   tags={"BackendPdCategories"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function all() {
        try {
            $results = $this->model_repository->getModel()->where('parent_id', 0)->orderBy('sort_order', 'asc')->with('childs')->get();
            $output = [];
            foreach ($results as $result) {
                $output[] = $result;
                if ($result->childs) foreach ($result->childs as $child) {
                    $child->name = $result->name . ' -> ' . $child->name;
                    $output[] = $child;
                }
            }

            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_categories",
     *   summary="Get Product Categories",
     *   operationId="getProductCategories",
     *   tags={"BackendPdCategories"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example="0"),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="name"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="asc"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields, extend_fields: avgReview} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function index() {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'view')) return $this->errorForbidden();
            /*$page = (int)$this->request->get('page');
            $pageSize = (int)$this->request->get('pageSize');
            if (!$page) $page = 1;
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $order = (string)$this->request->get('order');
            $sort = !$sort ? 'name' : strtolower($sort);
            $order = !$order ? 'asc' : strtolower($order);*/
            $sort = 'sort_order';
            $order = 'asc';
            $queries = ['and' => [['parent_id', '=', 0]]];
            /*$data = $this->getRequestData();
            // Query by keyword
            $q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(utf8_strtolower($q));
                $keys = ['name'];
                foreach ($keys as $key) {
                    $iQ = [];
                    $iB = [];
                    foreach ($arrQ as $i) {
                        $iQ[] = "lower(`$key`) like ?";
                        $iB[] = "%$i%";
                    }
                    $queries['orWhereRaw'][] = ['(' . implode(' and ', $iQ) . ')', $iB];
                }
            }*/
            $results = $this->setUpQueryBuilder($this->model(), $queries, false)->orderBy($sort, $order)
                //->take($pageSize)->skip($pageSize * ($page - 1))
                ->get();
            $output = [];
            foreach ($results as $result) {
                if (isset($result['options']) and !empty($result['options'])) {
                    $prop = [];
                    foreach (explode(',', $result['options']) as $property) {
                        $optionss = DB::table('pd__options')->where('id', $property)->first();
                        $prop[] = $optionss;
                    }
                    $result['optionss'] = $prop;
                }

                $output[] = $result;
                $queries['and'] = [['parent_id', '=', $result->id]];
                $childs = $this->setUpQueryBuilder($this->model(), $queries, false)->orderBy($sort, $order)->get();
                foreach ($childs as $child) {
                    if (isset($child['options']) and !empty($child['options'])) {
                        $prop = [];
                        foreach (explode(',', $child['options']) as $property) {
                            $optionss = DB::table('pd__options')->where('id', $property)->first();
                            $prop[] = $optionss;
                        }
                        $child['optionss'] = $prop;
                    }

                    //$child->name = $result->name . ' -> ' . $child->name;
                    $child->parent = ['name' => $result['name']];
                    $output[] = $child;
                }
            }
            return $this->respondWithSuccess($output);
            /*$paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'false' ? false : ($paging == 'true' ? true : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($results);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }*/
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
            $query = "category_id=$id";
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
                $seo_url = $this->seo_url_repository->create(['lang' => $lang, 'query' => $query, 'keyword' => $input['alias'], 'push' => "route=product/category&$query"]);
            }
            return [$errorKey, $seo_url];
        }
        return [false, false];
    }

    /**
     * @OA\Post(
     *   path="/backend/pd_categories",
     *   summary="Create Product Category",
     *   operationId="createProductCategory",
     *   tags={"BackendPdCategories"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function store() {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'create')) return $this->errorForbidden();
            $input = $this->request->all();
            $parent_id = (int)$this->request->get('parent_id');
            $input['parent_id'] = $parent_id ? $parent_id : 0;
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
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => 'pd_cat', 'object_id' => $model->id]);
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

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pd_categories/{id}",
     *   summary="Update Product Category",
     *   operationId="updateProductCategory",
     *   tags={"BackendPdCategories"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Category Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
     *     )
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function update($id) {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'edit')) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->all();
            $parent_id = (int)$this->request->get('parent_id');
            if (!$parent_id) $parent_id = 0;
            if ($parent_id == $id) return $this->errorWrongArgs();
            $input['parent_id'] = $parent_id;
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
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
                // Upload image
                if ($file) {
                    // Unlink old image
                    //$oldFile = null;
                    //if ($model->image) $oldFile = $this->file_repository->findByAttributes(['object' => 'pd_cat', 'path' => $model->image]);
                    // New image
                    $savedFile = $this->fileService->store($file, ['sub' => 'pd_cat', 'object_id' => $model->id]);
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
     *   path="/backend/pd_categories/{id}",
     *   summary="Update Product Category Partial",
     *   operationId="UpdateProductCategoryPartial",
     *   tags={"BackendPdCategories"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Category Id", example="1"),
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
            // Check permission
            if (!$this->isCRUD('products', 'edit')) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['status', 'show']);
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/pd_categories/{$id}",
     *   summary="Delete Product Category",
     *   operationId="deleteProductCategory",
     *   tags={"BackendPdCategories"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Category Id", example=1),
     *   @OA\Parameter(name="category_id", description="Category Id", in="path", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'delete')) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();

            $find = $this->product_repository->findByAttributes(['category_id' => $model->id]);
            if ($find) return $this->respondWithErrorKey('system.forbidden', 400, '', [], $find);

            $this->seo_url_repository->getModel()->where('query', "category_id=$id")->delete();
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pd_categories/{id}/description",
     *   summary="Update Product Category Description",
     *   operationId="updateProductCategorydescription",
     *   tags={"BackendPdCategories"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Category Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="lang", type="string", example="en"),
     *       @OA\Property(property="meta_title", type="string", example=""),
     *       @OA\Property(property="meta_description", type="string", example=""),
     *       @OA\Property(property="meta_keyword", type="string", example=""),
     *       @OA\Property(property="alias", type="string", example=""),
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
                $modelDesc = $this->category_desc_repository->findByAttributes(['id' => $id, 'lang' => $lang]);
                if (!$modelDesc) {
                    $modelDesc = $this->category_desc_repository->create(array_merge($input, ['id' => $id, 'lang' => $lang]));
                } else {
                    $modelDesc = $this->category_desc_repository->update($modelDesc, $input);
                }
                $translates = $model->translates ? $model->translates : [];
                $translates[] = $lang;
                $translates = array_unique($translates);
                $model->translates = $translates;
                $model->save();
            }
            $model->descs;

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *   path="/backend/pd_categories/{id}/properties",
     *   summary="Update Product Category Properties Partial",
     *   operationId="UpdateProductCategoryPropertiesPartial",
     *   tags={"BackendPdCategories"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Category Id", example="1"),
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
    public function properties($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['properties']);
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *   path="/backend/pd_categories/{id}/options",
     *   summary="Update Product Category Options Partial",
     *   operationId="UpdateProductCategoryOptionsPartial",
     *   tags={"BackendPdCategories"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Category Id", example="1"),
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
    public function options($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['options']);
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
