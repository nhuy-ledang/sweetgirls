<?php namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Core\Repositories\SeoUrlRepository;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\Product\Repositories\ProductDescRepository;
use Modules\Product\Repositories\ProductRepository;

/**
 * Class ProductIncludedController
 * @package Modules\Product\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-06-02
 */
class ProductIncludedController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\ProductDescRepository
     */
    protected $product_desc_repository;

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
                                ProductDescRepository $product_desc_repository,
                                SeoUrlRepository $seo_url_repository,
                                FileRepository $file_repository,
                                FileService $fileService) {
        $this->model_repository = $product_repository;
        $this->product_desc_repository = $product_desc_repository;
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
     *   path="/backend/pd_included_products",
     *   summary="Get Included Products",
     *   operationId="pdGetIncludedProducts",
     *   tags={"BackendPdIncludedProducts"},
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
            $queries = ['and' => [['is_included', '=', 1]], 'orWhereRaw' => []];
            $data = $this->getRequestData();
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
            if ($q) $queries['whereRaw'][] = ["lower(`name`) like ?", "%$q%"];
            $results = $this->setUpQueryBuilder($this->model(), $queries)
                ->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'false' ? false : ($paging == 'true' ? true : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($results);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_included_products/{id}",
     *   summary="Get a Included Product",
     *   operationId="pdGetIncludedProduct",
     *   tags={"BackendPdIncludedProducts"},
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
            $queries = ['and' => [['is_included', '=', 1]]];
            $model = $this->setUpQueryBuilder($this->model(), $queries, false)->where('id', $id)->first();
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
        return [false, false];
    }

    /**
     * @OA\Post(
     *   path="/backend/pd_included_products",
     *   summary="Create Included Product",
     *   operationId="pdCreateIncludedProduct",
     *   tags={"BackendPdIncludedProducts"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="price", type="integer", example=0),
     *       @OA\Property(property="description", type="string", example=""),
     *       @OA\Property(property="status", type="integer", example=1),
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
            $input = $this->request->only(['name', 'price', 'short_description', 'description', 'status']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Check seo url exist
            $alias = $this->request->get('alias');
            if ($alias) {
                $seo_urls = $this->seo_url_repository->getSeoUrlsByKeyword($alias);
                if ($seo_urls->count() > 0) return $this->errorWrongArgs('keyword.exists');
            }
            $input['is_included'] = 1;
            // Create model
            $model = $this->model_repository->create($input);
            // Update seo url
            $input = [];
            list($errorKey, $seo_url) = $this->updateSeoUrl($model->id, 'vi', $input);
            if (!$errorKey && !empty($input)) $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/pd_included_products/{id}",
     *   summary="Update Included Product",
     *   operationId="pdUpdateIncludedProduct",
     *   tags={"BackendPdIncludedProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="price", type="integer", example=0),
     *       @OA\Property(property="description", type="string", example=""),
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
    public function update($id) {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'edit')) return $this->errorForbidden();
            $model = $this->model_repository->findByAttributes(['id' => $id, 'is_included' => 1]);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['name', 'price', 'short_description', 'description']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Update seo url
            list($errorKey, $seo_url) = $this->updateSeoUrl($id, 'vi', $input);
            if ($errorKey) return $this->errorWrongArgs($errorKey);
            // Update Model
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *   path="/backend/pd_included_products/{id}",
     *   summary="Update Included Product Partial",
     *   operationId="pdUpdateIncludedProductPartial",
     *   tags={"BackendPdIncludedProducts"},
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
            $model = $this->model_repository->findByAttributes(['id' => $id, 'is_included' => 1]);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['status']);
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/pd_included_products/{id}",
     *   summary="Delete Included Product",
     *   operationId="pdDeleteIncludedProduct",
     *   tags={"BackendPdIncludedProducts"},
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
            $model = $this->model_repository->findByAttributes(['id' => $id, 'is_included' => 1]);
            if (!$model) return $this->errorNotFound();
            $this->seo_url_repository->getModel()->where('query', "product_id=$id")->delete();
            $this->product_desc_repository->getModel()->where('id', $id)->delete();
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pd_included_products/{id}/description",
     *   summary="Update Product Description",
     *   operationId="pdUpdateIncludedProductDescription",
     *   tags={"BackendPdIncludedProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Id", example=1),
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
            // Check permission
            if (!$this->isCRUD('products', 'create')) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['lang', 'name', 'description']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, ['lang' => 'required', 'name' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $lang = $this->request->get('lang');
            list($errorKey, $seo_url) = $this->updateSeoUrl($id, $lang, $input);
            if ($errorKey) return $this->errorWrongArgs($errorKey);
            if ($lang == 'vi') {
                //$model = $this->model_repository->update($model, $input);
            } else {
                $modelDesc = $this->product_desc_repository->findByAttributes(['id' => $id, 'lang' => $lang]);
                if (!$modelDesc) {
                    $modelDesc = $this->product_desc_repository->create(array_merge($input, ['id' => $id, 'lang' => $lang]));
                } else {
                    $modelDesc = $this->product_desc_repository->update($modelDesc, $input);
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
}
