<?php namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Core\Repositories\SeoUrlRepository;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\Order\Networks\Lazada;
use Modules\Order\Networks\Shopee;
use Modules\Order\Networks\Tiktok;
use Modules\Product\Repositories\CategoryRepository;
use Modules\Product\Repositories\OptionRepository;
use Modules\Product\Repositories\OptionValueRepository;
use Modules\Product\Repositories\ProductImageRepository;
use Modules\Product\Repositories\ProductOptionRepository;
use Modules\Product\Repositories\ProductOptionValueRepository;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductVariantRepository;

/**
 * Class ProductController
 * @package Modules\Product\Http\Controllers\Api
 */
class ProductController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\ProductDescRepository
     */
    protected $product_desc_repository;

    /**
     * @var \Modules\Product\Repositories\ProductImageRepository
     */
    protected $product_image_repository;

    /**
     * @var \Modules\Product\Repositories\ProductOptionRepository
     */
    protected $product_option_repository;

    /**
     * @var \Modules\Product\Repositories\ProductOptionValueRepository
     */
    protected $product_option_value_repository;

    /**
     * @var \Modules\Product\Repositories\ProductVariantRepository
     */
    protected $product_variant_repository;

    /**
     * @var \Modules\Product\Repositories\OptionRepository
     */
    protected $option_repository;

    /**
     * @var \Modules\Product\Repositories\OptionValueRepository
     */
    protected $option_value_repository;

    /**
     * @var \Modules\Product\Repositories\CategoryRepository
     */
    protected $category_repository;

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
                                ProductRepository $product_repository,
                                ProductImageRepository $product_image_repository,
                                ProductOptionRepository $product_option_repository,
                                ProductOptionValueRepository $product_option_value_repository,
                                ProductVariantRepository $product_variant_repository,
                                OptionRepository $option_repository,
                                OptionValueRepository $option_value_repository,
                                CategoryRepository $category_repository,
                                SeoUrlRepository $seo_url_repository,
                                FileRepository $file_repository,
                                FileService $fileService) {
        $this->model_repository = $product_repository;
        $this->product_image_repository = $product_image_repository;
        $this->product_option_repository = $product_option_repository;
        $this->product_option_value_repository = $product_option_value_repository;
        $this->product_variant_repository = $product_variant_repository;
        $this->option_repository = $option_repository;
        $this->option_value_repository = $option_value_repository;
        $this->category_repository = $category_repository;
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
     *   path="/backend/pd_products_all",
     *   summary="Get Products All",
     *   operationId="pdGetProductsAll",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function all() {
        try {
            $results = $this->model_repository->getModel()->orderBy('name', 'asc')->get();
            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_products",
     *   summary="Get Products",
     *   operationId="pdGetProducts",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
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
            // Check permission
            if (!$this->isCRUD('products', 'view')) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'id' : strtolower($sort);
            $order = (string)$this->request->get('order');
            $order = !$order ? 'asc' : strtolower($order);
            $queries = ['and' => [['is_included', '=', 0], ['is_free', '=', 0]], 'whereRaw' => [], 'orWhereRaw' => []];
            $data = $this->getRequestData();
            $filter_child = (isset($data->{'filter_child'}) && intval($data->{'filter_child'})) ? intval($data->{'filter_child'}) : false;
            if ($filter_child) {
                $queries['whereRaw'][] = ["(`master_id` is not null or `num_of_child` = 0)"];
            } else {
                $queries['whereRaw'][] = ["(`master_id` is null or `master_id` = 0)"];
            }
            $category_id = (isset($data->{'category_id'}) && intval($data->{'category_id'})) ? intval($data->{'category_id'}) : false;
            if ($category_id) {
                $queries['orWhereRaw'][] = ["(category_id = '" . $category_id . "' or categories = '" . $category_id . "' or categories like '" . $category_id . ",%' or categories like '%," . $category_id . ",%' or categories like '%," . $category_id . "')"];
            }
            // Query by keyword
            /*$q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(utf8_strtolower($q));
                $keys = ['name', 'description'];
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
            $q = trim(utf8_strtolower((isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : ''));
            if ($q) {
                $queries['orWhereRaw'][] = ["lower(`name`) like ?", "%$q%"];
                $queries['orWhereRaw'][] = ["lower(`long_name`) like ?", "%$q%"];
            }
            $tz = (int)$this->request->get('tz');
            $dateNow = $this->getDateLocalFromTz($tz);
            $fields = ['*'];
            $results = $this->setUpQueryBuilder($this->model(), $queries, false, $fields)->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
            $output = $results;
            /*foreach ($results as $item) {
                $output[] = $item;
                $childs = $this->model_repository->getModel()->where('master_id', $item->id)->orderBy('name', 'asc')->get();
                foreach ($childs as $child) {
                    $output[] = $child;
                }
            }*/
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'false' ? false : ($paging == 'true' ? true : (boolean)$paging);
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
     *   path="/backend/pd_products/{id}",
     *   summary="Get a Product",
     *   operationId="pdGetProduct",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Id", example=1),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
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
            $tz = (int)$this->request->get('tz');
            $dateNow = $this->getDateLocalFromTz($tz);
            $fields = ['*'];
            $queries = ['and' => [['is_included', '=', 0], ['is_free', '=', 0]]];
            $model = $this->setUpQueryBuilder($this->model(), $queries, false, $fields)->where('id', $id)->first();
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
     * @param $alias
     * @return array
     */
    private function updateSeoUrlByAlias($id, $lang, &$input, $alias) {
        $query = "product_id=$id";
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
            $seo_url = $this->seo_url_repository->create(['lang' => $lang, 'query' => $query, 'keyword' => $input['alias'], 'push' => "route=product/product&$query"]);
        }
        return [$errorKey, $seo_url];
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
        if ($alias) return $this->updateSeoUrlByAlias($id, $lang, $input, $alias);
        return [false, false];
    }

    /**
     * @OA\Post(
     *   path="/backend/pd_products",
     *   summary="Create Product",
     *   operationId="pdCreateProduct",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="category_id", type="integer", example=1),
     *       @OA\Property(property="manufacturer_id", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="long_name", type="string", example=""),
     *       @OA\Property(property="price", type="integer", example=0),
     *       @OA\Property(property="short_description", type="string", example=""),
     *       @OA\Property(property="description", type="string", example=""),
     *       @OA\Property(property="delivery", type="string", example=""),
     *       @OA\Property(property="stock_status", type="integer", example=""),
     *       @OA\Property(property="status", type="integer", example="1"),
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
            $input = $this->request->only('master_id', 'name', 'model', 'unit', 'price', 'image', 'sort_order', 'status', 'short_description', 'description', 'stock_status');
            $category_id = $this->request->get('category_id');
            $input['category_id'] = $category_id;
            $manufacturer_id = $this->request->get('manufacturer_id');
            if (!is_null($manufacturer_id) && intval($manufacturer_id)) $input['manufacturer_id'] = $manufacturer_id;
            $tag = $this->request->get('tag');
            if ($tag) {
                $tags = [];
                foreach (explode(',', $tag) as $tag) {
                    if (trim($tag)) $tags[] = trim($tag);
                }
                $input['tag'] = implode(',', array_unique($tags));
            }
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
                    $savedFile = $this->fileService->store($file, ['sub' => $this->module_name, 'object_id' => $model->id]);
                    if (!is_string($savedFile)) $model = $this->model_repository->update($model, ['image' => $savedFile->path]);
                }
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/pd_products/{id}",
     *   summary="Update Product",
     *   operationId="pdUpdateProduct",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="idx", type="string", example=""),
     *       @OA\Property(property="category_id", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="long_name", type="string", example=""),
     *       @OA\Property(property="price", type="integer", example=0),
     *       @OA\Property(property="short_description", type="string", example=""),
     *       @OA\Property(property="description", type="string", example=""),
     *       @OA\Property(property="delivery", type="string", example="year"),
     *       @OA\Property(property="stock_status", type="integer", example=""),
     *       @OA\Property(property="status", type="integer", example="1"),
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
    public function update($id) {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'edit')) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only('name', 'model', 'unit', 'price', 'image', 'sort_order', 'status', 'description', 'stock_status');
            $category_id = $this->request->get('category_id');
            $input['category_id'] = $category_id;
            $manufacturer_id = $this->request->get('manufacturer_id');
            if (!is_null($manufacturer_id) && intval($manufacturer_id)) $input['manufacturer_id'] = $manufacturer_id;
            $tag = $this->request->get('tag');
            if ($tag) {
                $tags = [];
                foreach (explode(',', $tag) as $tag) {
                    if (trim($tag)) $tags[] = trim($tag);
                }
                $input['tag'] = implode(',', array_unique($tags));
            }
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
                    //$oldFile = null;
                    //if ($model->image) $oldFile = $this->file_repository->findByAttributes(['object' => $this->module_name, 'path' => $model->image]);
                    // New image
                    $savedFile = $this->fileService->store($file, ['sub' => $this->module_name, 'object_id' => $model->id]);
                    if (!is_string($savedFile)) {
                        $input['image'] = $savedFile->path;
                        // Unlink old image
                        //if ($oldFile) $this->file_repository->destroy($oldFile);
                    }
                }
            }
            // Update Model
            $model = $this->model_repository->update($model, $input);
            // Update master
            if ($model->master_id && $master = $model->master) {
                $price_min = 0;
                $price_max = 0;
                $result = $this->model_repository->getModel()->where('master_id', $master->id)
                    ->select([\DB::raw('min(`pd__products`.`price`) as price_min, max(`pd__products`.`price`) as price_max, min(`ps`.`price`) as ps_price_min, max(`ps`.`price`) as ps_price_max')])->first();
                if ($result) {
                    $tmp = [];
                    $tmp[] = (float)$result->price_min;
                    $tmp[] = (float)$result->price_max;
                    if ($result->ps_price_min) $tmp[] = (float)$result->ps_price_min;
                    if ($result->ps_price_max) $tmp[] = (float)$result->ps_price_max;
                    $price_min = min($tmp);
                    $price_max = max($tmp);
                }
                $num_of_child = $master->childs->count();
                $this->model_repository->update($master, ['num_of_child' => $num_of_child, 'price_min' => $price_min, 'price_max' => $price_max]);
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *   path="/backend/pd_products/{id}",
     *   summary="Update Product Partial",
     *   operationId="pdUpdateProductPartial",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example=1),
     *       @OA\Property(property="top", type="integer", example=1),
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
            $model = $this->model_repository->findByAttributes(['id' => $id, 'is_included' => 0, 'is_free' => 0]);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['status', 'top']);
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/pd_products/{id}",
     *   summary="Delete Product",
     *   operationId="deleteProduct",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Id", example=1),
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
            $model = $this->model_repository->findByAttributes(['id' => $id, 'is_included' => 0, 'is_free' => 0]);
            if (!$model) return $this->errorNotFound();
            $this->seo_url_repository->getModel()->where('query', "product_id=$id")->delete();
            $this->product_desc_repository->getModel()->where('id', $id)->delete();
            $master = null;
            if ($model->master_id) $master = $model->master;
            // Destroy
            $this->model_repository->destroy($model);
            // Update master
            if ($master) {
                $price_min = 0;
                $price_max = 0;
                $result = $this->model_repository->getModel()->where('master_id', $master->id)
                    ->select([\DB::raw('min(`pd__products`.`price`) as price_min, max(`pd__products`.`price`) as price_max, min(`ps`.`price`) as ps_price_min, max(`ps`.`price`) as ps_price_max')])->first();
                if ($result) {
                    $tmp = [];
                    $tmp[] = (float)$result->price_min;
                    $tmp[] = (float)$result->price_max;
                    if ($result->ps_price_min) $tmp[] = (float)$result->ps_price_min;
                    if ($result->ps_price_max) $tmp[] = (float)$result->ps_price_max;
                    $price_min = min($tmp);
                    $price_max = max($tmp);
                }
                $num_of_child = $master->childs->count();
                $this->model_repository->update($master, ['num_of_child' => $num_of_child, 'price_min' => $price_min, 'price_max' => $price_max]);
            }

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_products/{id}/options",
     *   summary="Get Product Options",
     *   operationId="pdGetProductOptions",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Id", example=1),
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
            $results = $this->option_repository->getModel()->rightJoin('pd__product_options as po', 'option_id', 'pd__options.id')
                ->where('product_id', $id)->with('values')
                ->orderBy('pd__options.sort_order', 'asc')->select(['pd__options.id', 'pd__options.name', 'po.id as po_id', 'product_id'])->get();

            $output = [];
            foreach ($results as $result) {
                $result->option_id = $result->id;
                $result->id = $result->po_id;
                unset($result->po_id);
                $output[] = $result;
            }

            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_products/{id}/variants",
     *   summary="Get Product Variants",
     *   operationId="pdGetProductVariants",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function getVariants($id) {
        try {
            // select p.id, p.name, p.price, pv.option_id, pv.option_value_id from pd__products p
            // left join `pd__product_variants` pv on (pv.product_id=p.id) left join `pd__options` o on (o.id = pv.option_id)
            // left join `pd__option_values` ov on (ov.id = pv.option_value_id) where p.master_id = 432 order by o.sort_order desc, ov.sort_order asc
            $tz = (int)$this->request->get('tz');
            $dateNow = $this->getDateLocalFromTz($tz);
            $fields = [
                'pd__products.*', 'pv.option_id', 'pv.option_value_id'
            ];
            // Get products
            $results = $this->model_repository->getModel()->leftJoin('pd__product_variants as pv', 'pv.product_id', 'pd__products.id')->where('master_id', $id)->select($fields)->get();
            $variantObj = [];
            foreach ($results as $result) {
                $option_id = $result->option_id;
                $option_value_id = $result->option_value_id;
                $product_id = $result->id;
                unset($result->option_id);
                unset($result->option_value_id);
                $product = $result->toArray();
                if (!isset($variantObj[$product_id])) {
                    $product['variant_ids'] = [];
                    //$product['variants'] = [];
                    $variantObj[$product_id] = $product;
                }
                $variantObj[$product_id]['variant_ids'][] = "{$option_id}_$option_value_id";
                //$variantObj[$product_id]['variants'][] = ['option_id' => $option_id, 'option_value_id' => $option_value_id];
            }
            $products = array_values($variantObj);
            //return $this->respondWithSuccess($products);
            // Get variants
            $results = $this->option_repository->getModel()->rightJoin('pd__product_options as po', 'option_id', 'pd__options.id')->where('product_id', $id)->with('values')->orderBy('pd__options.sort_order', 'asc')->select(['pd__options.id', 'pd__options.name'])->get();
            //return $this->respondWithSuccess($results);
            $total = $results->count();
            $data = [];
            if ($total == 2) {
                $row1 = $results[1]; // Color
                $row2 = $results[0]; // Vol
                foreach ($row1->values as $val1) {
                    $tmp_1 = "{$val1->option_id}_$val1->id";
                    $data[$val1->id] = ['id' => $val1->id, 'option_id' => $val1->option_id, 'name' => $val1->name, 'values' => []];
                    foreach ($row2->values as $val2) {
                        $tmp_2 = "{$val2->option_id}_$val2->id";
                        $product = null;
                        foreach ($products as $p) {
                            if (in_array($tmp_1, $p['variant_ids']) && in_array($tmp_2, $p['variant_ids'])) {
                                $product = $p;
                                break;
                            }
                        }
                        $data[$val1->id]['values'][] = ['id' => $val2->id, 'option_id' => $val2->option_id, 'name' => $val2->name, 'product' => $product];
                    }
                }
                $data = array_values($data);
            } else if ($total == 1) {
                $row1 = $results[0]; // Vol
                foreach ($row1->values as $val1) {
                    $tmp_1 = "{$val1->option_id}_$val1->id";
                    $product = null;
                    foreach ($products as $p) {
                        if (in_array($tmp_1, $p['variant_ids'])) {
                            $product = $p;
                            unset($product['variant_ids']);
                            break;
                        }
                    }
                    $data[] = ['id' => $val1->id, 'option_id' => $val1->option_id, 'name' => $val1->name, 'product' => $product];
                }
                $data = array_values($data);
            }

            return $this->respondWithSuccess(['variants' => $results, 'values' => $data]);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @param $id
     * @return array
     */
    protected function getRequestOptions($id) {
        $options = [];
        $ids = [];
        $tmpOptions = (array)$this->request->get('options');
        if (!empty($tmpOptions)) foreach ($tmpOptions as $item) {
            $validatorErrors = $this->getValidator($item, ['option_id' => 'required|integer', 'option_value_id' => 'required|integer']);
            if (empty($validatorErrors)) {
                $find = $this->product_option_repository->getModel()->leftJoin('pd__option_values as ov', 'ov.option_id', 'pd__product_options.option_id')
                    ->where('product_id', $id)->where('pd__product_options.option_id', $item['option_id'])->where('ov.id', $item['option_value_id'])->first();
                if ($find) {
                    $options[] = ['option_id' => (int)$item['option_id'], 'option_value_id' => (int)$item['option_value_id']];
                    $ids[] = (int)$item['option_id'];
                }
            }
        }
        $ids = array_unique($ids);
        return (count($options) == count($ids)) ? $options : [];
    }

    /**
     * @OA\Post(
     *   path="/backend/pd_products/{id}/variants",
     *   summary="Create Product Variant",
     *   operationId="pdCreateProductVariant",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="options", type="string", example=""),
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
    public function createVariant($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model || ($model && $model->master_id)) return $this->errorNotFound();
            $optObj = [];
            $optIds = [];
            foreach ($model->options as $opt) {
                $optIds[] = $opt->option_id;
                $values = $this->option_value_repository->getModel()->where('option_id', $opt->option_id)->get();
                $valueIds = [];
                $valueObjs = [];
                foreach ($values as $value) {
                    $valueIds[] = $value->id;
                    $valueObjs[$value->id] = $value;
                }
                $opt->valueIds = $valueIds;
                $opt->valueObjs = $valueObjs;
                $optObj[$opt->option_id] = $opt;
            }
            if (empty($optIds)) $this->respondWithErrorKey('option_id.required');
            // Check options
            $options = $this->getRequestOptions($id);
            if (count($options) !== count($optIds)) return $this->errorWrongArgs();
            // Check variant exist
            $variant_tmp = $this->product_variant_repository->getModel()->leftJoin('pd__products as p', 'p.id', 'product_id')->where('p.master_id', $id)
                ->where(function($query) use ($options) {
                    $key = 0;
                    foreach ($options as $opt) {
                        if ($key === 0) {
                            $query = $query->where(function($query) use ($opt) {
                                $query->where('option_id', $opt['option_id'])->where('option_value_id', $opt['option_value_id']);
                            });
                        } else {
                            $query = $query->orWhere(function($query) use ($opt) {
                                $query->where('option_id', $opt['option_id'])->where('option_value_id', $opt['option_value_id']);
                            });
                        }
                        $key++;
                    }
                })->groupBy('pd__product_variants.product_id')->orderBy('total', 'desc')->limit(1)
                ->select(['pd__product_variants.product_id', \DB::raw('count(`pd__product_variants`.`product_id`) as total')])->first();
            if ($variant_tmp && $variant_tmp->total >= count($options)) return $this->respondWithErrorKey('option_id.exists');
            // Create variant data
            $fields = ['name', 'long_name', 'category_id', 'categories', 'manufacturer_id', 'price', 'weight', 'length', 'width', 'height', 'meta_title', 'meta_description', 'meta_keyword', 'image', 'stock_status'];
            $data = [];
            foreach ($fields as $field) $data[$field] = $model->{$field};
            $data['master_id'] = $model->id;
            $names = [];
            foreach ($options as $opt) $names[] = $optObj[$opt['option_id']]['valueObjs'][$opt['option_value_id']]['name'];
            $data['name'] .= ' - ' . implode(' - ', $names);
            $data['long_name'] .= ' - ' . implode(' - ', $names);
            $data['meta_title'] = $data['long_name'] ? $data['long_name'] : $data['name'];
            // Create variant
            $variant = $this->model_repository->create($data);
            // Update variant seo url
            $data = [];
            list($errorKey, $seo_url) = $this->updateSeoUrlByAlias($variant->id, 'vi', $data, $variant->long_name ? $variant->long_name : $variant->name);
            if (!$errorKey && !empty($data)) $model = $this->model_repository->update($variant, $data);
            // Create options
            foreach ($options as $opt) {
                // Create product variants
                $this->product_variant_repository->create(array_merge($opt, ['product_id' => $variant->id]));
                // Create product option values
                $attributes = array_merge($opt, ['product_id' => $model->id]);
                $tmp = $this->product_option_value_repository->findByAttributes($attributes);
                if (!$tmp) $this->product_option_value_repository->create($attributes);
            }

            return $this->respondWithSuccess($variant);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/pd_products/{id}/variants",
     *   summary="Update Product Variant",
     *   operationId="pdUpdateProductVariant",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Variant Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
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
    public function updateVariant($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model || ($model && !$model->master_id)) return $this->errorNotFound();
            $master = $model->master;
            if (!$master) return $this->errorNotFound();
            $input = $this->request->only('name', 'long_name', 'model', 'price', 'coins', 'weight', 'length', 'width', 'height', 'status', 'alias', 'meta_title', 'meta_description', 'meta_keyword', 'short_description', 'stock_status');
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Update Model
            $model = $this->model_repository->update($model, $input);
            // Update master
            $price_min = 0;
            $price_max = 0;
            $result = $this->model_repository->getModel()->where('master_id', $master->id)->first();
            if ($result) {
                $tmp = [];
                $tmp[] = (float)$result->price_min;
                $tmp[] = (float)$result->price_max;
                if ($result->ps_price_min) $tmp[] = (float)$result->ps_price_min;
                if ($result->ps_price_max) $tmp[] = (float)$result->ps_price_max;
                $price_min = min($tmp);
                $price_max = max($tmp);
            }
            $num_of_child = $master->childs->count();
            $this->model_repository->update($master, ['num_of_child' => $num_of_child, 'price_min' => $price_min, 'price_max' => $price_max]);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/pd_products/{id}/variants",
     *   summary="Delete Product Variant",
     *   operationId="pdDeleteProductVariant",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Variant Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroyVariant($id) {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'delete')) return $this->errorForbidden();
            $model = $this->model_repository->getModel()->where('id', $id)->whereNotNull('master_id')->first();
            if (!$model) return $this->errorNotFound();
            $this->seo_url_repository->getModel()->where('query', "product_id=$id")->delete();
            $this->product_desc_repository->getModel()->where('id', $id)->delete();
            $master = $model->master;
            // Destroy
            //$this->model_repository->destroy($model);
            $model->forceDelete();
            if ($master) {
                // Remove product options
                $product_options = $this->product_option_value_repository->getModel()->where('product_id', $master->id)->get();
                $product_option_ids = [];
                foreach ($product_options as $po) {
                    $find = $this->product_variant_repository->findByAttributes(['option_id' => $po->option_id, 'option_value_id' => $po->option_value_id]);
                    if (!$find) $product_option_ids[] = $po->id;
                }
                if (!empty($product_option_ids)) $this->product_option_value_repository->getModel()->whereIn('id', $product_option_ids)->delete();
                // Update master
                $price_min = 0;
                $price_max = 0;
                $result = $this->model_repository->getModel()->where('master_id', $master->id)
                    ->select([\DB::raw('min(`pd__products`.`price`) as price_min, max(`pd__products`.`price`) as price_max, min(`ps`.`price`) as ps_price_min, max(`ps`.`price`) as ps_price_max')])->first();
                if ($result) {
                    $tmp = [];
                    $tmp[] = (float)$result->price_min;
                    $tmp[] = (float)$result->price_max;
                    if ($result->ps_price_min) $tmp[] = (float)$result->ps_price_min;
                    if ($result->ps_price_max) $tmp[] = (float)$result->ps_price_max;
                    $price_min = min($tmp);
                    $price_max = max($tmp);
                }
                $num_of_child = $master->childs->count();
                $this->model_repository->update($master, ['num_of_child' => $num_of_child, 'price_min' => $price_min, 'price_max' => $price_max]);
            }

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
