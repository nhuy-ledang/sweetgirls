<?php namespace Modules\Business\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Business\Repositories\CategoryRepository;
use Modules\Business\Repositories\ImportHistoryRepository;
use Modules\Business\Repositories\ImportRepository;
use Modules\Business\Repositories\ProductRepository;
use Modules\Business\Repositories\SupplierRepository;
use Modules\Media\Services\FileService;

/**
 * Class OutProductController
 *
 * @package Modules\Business\Http\Controllers\Api
 * @author Huy D <huydang1920@gmail.com>
 * Date: 2023-04-06
 */
class OutProductController extends ApiBaseModuleController {
    /**
     * @var string
     */
    protected $module_id = 'business';

    /**
     * @var string
     */
    protected $prd_type = PROD_TYPE_OUT_PRODUCT;

    /**
     * @var \Modules\Business\Repositories\ImportHistoryRepository
     */
    protected $import_history_repository;

    /**
     * @var \Modules\Business\Repositories\ProductRepository
     */
    protected $product_repository;

    /**
     * @var \Modules\Business\Repositories\CategoryRepository
     */
    protected $category_repository;

    /**
     * @var \Modules\Business\Repositories\SupplierRepository
     */
    protected $supplier_repository;

    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    protected $file_repository;

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    public function __construct(Request $request,
                                ImportRepository $import_repository, ImportHistoryRepository $import_history_repository,
                                ProductRepository $product_repository, CategoryRepository $category_repository,
                                SupplierRepository $supplier_repository, FileService $fileService) {
        $this->model_repository = $import_repository;
        $this->import_history_repository = $import_history_repository;
        $this->product_repository = $product_repository;
        $this->category_repository = $category_repository;
        $this->supplier_repository = $supplier_repository;
        $this->fileService = $fileService;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreateProduct() {
        return [
            'category_id'     => 'integer|exists:bus__categories,id',
            'name'            => 'required',
            'price_im'        => 'required|numeric',
            'operating_costs' => 'required|numeric',
            'expected_profit' => 'required|numeric',
            'quantity'        => 'integer',
            'earning_ratio'   => 'required|numeric',
            'pretax'          => 'required|numeric',
            'vat'             => 'required|numeric',
            'price'           => 'required|numeric',
        ];
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreate() {
        return [
            'product_id'      => 'required|integer|exists:bus__products,id',
            'price_im'        => 'required|numeric',
            'operating_costs' => 'required|numeric',
            'expected_profit' => 'required|numeric',
            'quantity'        => 'integer',
            'earning_ratio'   => 'required|numeric',
            'pretax'          => 'required|numeric',
            'vat'             => 'required|numeric',
            'price'           => 'required|numeric',
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
            'price_im'        => 'required|numeric',
            'operating_costs' => 'required|numeric',
            'expected_profit' => 'required|numeric',
            'quantity'        => 'integer',
            'earning_ratio'   => 'required|numeric',
            'pretax'          => 'required|numeric',
            'vat'             => 'required|numeric',
            'price'           => 'required|numeric',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/bus_out_products",
     *   summary="Get Imports",
     *   operationId="busGetImports",
     *   tags={"BackendBusOutProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields, extend_fields: Extend fields query} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
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
            //if (!($this->isView() || $this->isViewOwn())) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $sort = $sort ? strtolower($sort) : 'id';
            $order = (string)$this->request->get('order');
            $order = $order ? strtolower($order) : 'asc';
            $queries = [
                'and'        => [
                    ['p.prd_type', '=', $this->prd_type],
                ],
                'whereRaw'   => [
                    ['`p`.`deleted_at` is null'],
                ],
                'orWhereRaw' => [],
            ];
            $data = $this->getRequestData();
            $category_id = (isset($data->{'category_id'}) && !is_null($data->{'category_id'}) && $data->{'category_id'} !== '') ? (int)$data->{'category_id'} : false;
            if ($category_id) $queries['and'][] = ['category_id', '=', $category_id];
            // Query by keyword
            $q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                //$arrQ = $this->parseToArray(trim(utf8_strtolower($q)));
                $arrQ = [trim(utf8_strtolower($q))];
                $keys = ['`name`'];
                foreach ($keys as $key) {
                    $iQ = [];
                    $iB = [];
                    foreach ($arrQ as $i) {
                        $iQ[] = "lower($key) like ?";
                        $iB[] = "%$i%";
                    }
                    $queries['orWhereRaw'][] = ['(' . implode(' and ', $iQ) . ')', $iB];
                }
            }
            $fields = [
                'bus__imports.*',
                'p.id as prd__id',
                'p.idx as prd__idx',
                'p.category_id as prd__category_id',
                'p.name as prd__name',
                'p.prd_type as prd__prd_type',
                'p.unit as prd__unit',
                'p.image as prd__image',
                'p.short_description as prd__short_description',
                'p.price_im as prd__price_im',
                'p.pretax as prd__pretax',
                'p.vat as prd__vat',
                'p.price as prd__price',
            ];
            $results = $this->setUpQueryBuilder($this->model(), $queries, false, $fields)
                ->rightJoin('bus__products as p', 'product_id', 'p.id')
                ->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
            $categoryIds = [];
            $catObj = [];
            foreach ($results as $item) {
                if ($item->prd__category_id) $categoryIds[] = (int)$item->prd__category_id;
            }
            $categoryIds = array_unique($categoryIds);
            if ($categoryIds) {
                $categories = $this->category_repository->getModel()->whereIn('id', $categoryIds)->select(['id', 'name'])->get();
                foreach ($categories as $category) $catObj[$category->id] = $category;
            }
            $output = [];
            foreach ($results as $item) {
                $newItem = $this->parseToRespond($item->toArray());
                $product = new \Modules\Business\Entities\Product($newItem['prd']);
                $product->id = $item->prd__id;
                $product->category = $item->prd__category_id && isset($catObj[$item->prd__category_id]) ? $catObj[$item->prd__category_id] : null;
                $newItem['product'] = $product;
                unset($newItem['prd']);
                $output[] = $newItem;
            }
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'true' ? true : ($paging == 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($output);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)
                    ->rightJoin('bus__products as p', 'product_id', 'p.id')
                    ->count();
                return $this->respondWithPaging($output, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/bus_out_products/{id}",
     *   summary="Get a Import",
     *   operationId="busGetImport",
     *   tags={"BackendBusOutProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Import Id", example=1),
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
            // Check permission
            if (!($this->isView() || $this->isViewOwn())) return $this->errorForbidden();
            $model = $this->setUpQueryBuilder($this->model(), ['and' => [['product_id', '=', $id]]])->first();
            if (!$model) return $this->errorNotFound();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/bus_out_products_products",
     *   summary="Create Import Product",
     *   operationId="busCreateImportProduct",
     *   tags={"BackendBusProducts"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="idx", type="string", example=""),
     *       @OA\Property(property="category_id", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="price", type="integer", example=0),
     *       @OA\Property(property="short_description", type="string", example=""),
     *       @OA\Property(property="description", type="string", example=""),
     *       @OA\Property(property="unit", type="string", example="year"),
     *       @OA\Property(property="status", type="integer", example="1"),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function create() {
        try {
            // Check permission
            if (!$this->isCreate()) return $this->errorForbidden();
            $input = $this->request->only(['idx_shared', 'name', 'price_im', 'operating_costs', 'expected_profit', 'quantity', 'earning_ratio', 'pretax', 'vat', 'price', 'short_description', 'unit', 'weight', 'description', 'note']);
            $category_id = $this->request->get('category_id');
            if (!is_null($category_id) && intval($category_id)) $input['category_id'] = $category_id;
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreateProduct());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $supplier_id = (int)$this->request->get('supplier_id');
            $supplier = $supplier_id ? $this->supplier_repository->find($supplier_id) : null;
            $input['supplier_id'] = $supplier ? $supplier_id : null;
            $idx = $this->request->get('idx');
            if ($idx) $idx = to_idx($idx);
            // Sản phẩm nhập && Dịch vụ thuê ngoài
            if ($this->prd_type == PROD_TYPE_OUT_PRODUCT || $this->prd_type == PROD_TYPE_OUT_SERVICE) {
                $idx_im = $this->request->get('idx_im');
                if ($idx_im) $idx_im = to_idx($idx_im);
                if ($idx_im) {
                    $temp = $this->model_repository->getModel()->where('idx_im', $idx_im)->first();
                    if ($temp) return $this->respondWithErrorKey('idx.exists');
                    $input['idx_im'] = $idx_im;
                }
            }
            $idx_shared = (boolean)$this->request->get('idx_shared');
            if ($idx_shared && !empty($input['idx_im'])) $idx = $input['idx_im'];
            if ($idx) {
                $temp = $this->product_repository->getModel()->where('idx', $idx)->first();
                if ($temp) return $this->respondWithErrorKey('idx.exists');
                $input['idx'] = $idx;
            }
            $input['prd_type'] = $this->prd_type;
            // Create product model
            $product = $this->product_repository->create($input);
            // Upload product image
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $product = $this->product_repository->update($product, ['image' => $file_path]);
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                //if ($errorKey) return $this->errorWrongArgs($errorKey);
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => MEDIA_SUB_AVATAR, 'object_id' => $product->id]);
                    if (!is_string($savedFile)) $product = $this->product_repository->update($product, ['image' => $savedFile->path]);
                }
            }
            // Create model
            $model = $this->model_repository->create(array_merge($input, ['product_id' => $product->id, 'appraiser_id' => $this->auth->id]));

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/bus_out_products",
     *   summary="Create Import",
     *   operationId="busCreateImport",
     *   tags={"BackendBusOutProducts"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="product_id", type="integer", example=1),
     *       @OA\Property(property="idx_im", type="string", example=""),
     *       @OA\Property(property="supplier_id", type="integer", example=1),
     *       @OA\Property(property="price_im", type="integer", example=0),
     *       @OA\Property(property="operating_costs", type="integer", example=0),
     *       @OA\Property(property="expected_profit", type="integer", example=0),
     *       @OA\Property(property="quantity", type="integer", example=1),
     *       @OA\Property(property="earning_ratio", type="integer", example=1),
     *       @OA\Property(property="pretax", type="integer", example=0),
     *       @OA\Property(property="vat", type="integer", example=0),
     *       @OA\Property(property="price", type="integer", example=0),
     *       @OA\Property(property="note", type="string", example=""),
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
            if (!$this->isCreate()) return $this->errorForbidden();
            $input = $this->request->only(['idx_shared', 'product_id', 'supplier_id', 'price_im', 'operating_costs', 'expected_profit', 'quantity', 'earning_ratio', 'pretax', 'vat', 'price', 'note']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $idx = $this->request->get('idx');
            if ($idx) $idx = to_idx($idx);
            $idx_im = '';
            // Sản phẩm nhập && Dịch vụ thuê ngoài
            if ($this->prd_type == PROD_TYPE_OUT_PRODUCT || $this->prd_type == PROD_TYPE_OUT_SERVICE) {
                $idx_im = $this->request->get('idx_im');
                if ($idx_im) $idx_im = to_idx($idx_im);
            }
            // Create model
            $model = $this->model_repository->find($input['product_id']);
            if (!$model) { // Create model
                // Sản phẩm nhập && Dịch vụ thuê ngoài
                if ($this->prd_type == PROD_TYPE_OUT_PRODUCT || $this->prd_type == PROD_TYPE_OUT_SERVICE) {
                    if ($idx_im) {
                        $temp = $this->model_repository->getModel()->where('idx_im', $idx_im)->first();
                        if ($temp) return $this->respondWithErrorKey('idx.exists');
                        $input['idx_im'] = $idx_im;
                    }
                }
                $idx_shared = (boolean)$this->request->get('idx_shared');
                if ($idx_shared && !empty($input['idx_im'])) $idx = $input['idx_im'];
                if ($idx) {
                    $temp = $this->product_repository->getModel()->where('id', '<>', $model->product_id)->where('idx', $idx)->first();
                    if ($temp) return $this->respondWithErrorKey('idx.exists');
                    $input['idx'] = $idx;
                }
                $model = $this->model_repository->create($input);
            } else { // Update model
                // Sản phẩm nhập && Dịch vụ thuê ngoài
                if ($this->prd_type == PROD_TYPE_OUT_PRODUCT || $this->prd_type == PROD_TYPE_OUT_SERVICE) {
                    if ($idx_im && $idx_im != $model->idx_im) {
                        $temp = $this->model_repository->getModel()->where('idx_im', $idx_im)->first();
                        if ($temp) return $this->respondWithErrorKey('idx.exists');
                        $input['idx_im'] = $idx_im;
                    }
                }
                $idx_shared = (boolean)$this->request->get('idx_shared');
                if ($idx_shared && !empty($input['idx_im'])) $idx = $input['idx_im'];
                if ($idx) {
                    $temp = $this->product_repository->getModel()->where('id', '<>', $model->product_id)->where('idx', $idx)->first();
                    if ($temp) return $this->respondWithErrorKey('idx.exists');
                    $input['idx'] = $idx;
                }
                $model = $this->model_repository->update($model, array_merge($input, ['status' => 0, 'appraiser_id' => $this->auth->id, 'approver_id' => null, 'approved_at' => null]));
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/bus_out_products/{id}",
     *   summary="Update Import",
     *   operationId="busUpdateImport",
     *   tags={"BackendBusOutProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Import Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="idx_im", type="string", example=""),
     *       @OA\Property(property="supplier_id", type="integer", example=1),
     *       @OA\Property(property="price_im", type="integer", example=0),
     *       @OA\Property(property="operating_costs", type="integer", example=0),
     *       @OA\Property(property="expected_profit", type="integer", example=0),
     *       @OA\Property(property="quantity", type="integer", example=1),
     *       @OA\Property(property="earning_ratio", type="integer", example=1),
     *       @OA\Property(property="pretax", type="integer", example=0),
     *       @OA\Property(property="vat", type="integer", example=0),
     *       @OA\Property(property="price", type="integer", example=0),
     *       @OA\Property(property="note", type="string", example=""),
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
            if (!$this->isUpdate()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['idx_shared', 'supplier_id', 'price_im', 'operating_costs', 'expected_profit', 'quantity', 'earning_ratio', 'pretax', 'vat', 'price', 'note']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $idx = $this->request->get('idx');
            if ($idx) $idx = to_idx($idx);
            // Sản phẩm nhập && Dịch vụ thuê ngoài
            if ($this->prd_type == PROD_TYPE_OUT_PRODUCT || $this->prd_type == PROD_TYPE_OUT_SERVICE) {
                $idx_im = $this->request->get('idx_im');
                if ($idx_im) $idx_im = to_idx($idx_im);
                if ($idx_im && $idx_im != $model->idx_im) {
                    $temp = $this->model_repository->getModel()->where('idx_im', $idx_im)->first();
                    if ($temp) return $this->respondWithErrorKey('idx.exists');
                    $input['idx_im'] = $idx_im;
                }
            }
            $idx_shared = (boolean)$this->request->get('idx_shared');
            if ($idx_shared && !empty($input['idx_im'])) $idx = $input['idx_im'];
            if ($idx) {
                $temp = $this->product_repository->getModel()->where('id', '<>', $model->product_id)->where('idx', $idx)->first();
                if ($temp) return $this->respondWithErrorKey('idx.exists');
                $input['idx'] = $idx;
            }
            // Update Model
            $model = $this->model_repository->update($model, array_merge($input, ['status' => 0, 'appraiser_id' => $this->auth->id, 'approver_id' => null, 'approved_at' => null]));

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *   path="/backend/bus_out_products/{id}",
     *   summary="Update Import Partial",
     *   operationId="busUpdateImportPartial",
     *   tags={"BackendBusOutProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Import Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example=0),
     *       @OA\Property(property="note", type="string", example=""),
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
            if (!$this->isUpdate()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $product = $model->product;
            if (!$product) return $this->errorNotFound();
            $tz = (int)$this->request->get('tz');
            $input = $this->request->only(['note']);
            $status = $this->request->get('status');
            if (!is_null($status) && intval($status) != $model->status) $input['status'] = (int)$status;
            if (isset($input['status'])) {
                $validatorErrors = $this->getValidator($input, ['status' => 'required|in:0,1,2,3']);
                if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
                if ($input['status'] == 2) { // Hiệu lực phát hành
                    $data = [
                        'supplier_id'  => $model->supplier_id,
                        'price_im'     => $model->price_im,
                        'pretax'       => $model->pretax,
                        'vat'          => $model->vat,
                        'price'        => $model->price,
                        'appraiser_id' => $model->appraiser_id,
                        'approver_id'  => $this->auth->id,
                        'approved_at'  => $this->getDateLocalFromTz($tz),
                    ];
                    // Check idx
                    // Sản phẩm nhập && Dịch vụ thuê ngoài
                    if (($product->prd_type == PROD_TYPE_OUT_PRODUCT || $product->prd_type == PROD_TYPE_OUT_SERVICE) && $model->idx) {
                        $temp = $this->product_repository->getModel()->where('id', '<>', $product->id)->where('idx', $model->idx)->first();
                        if ($temp) return $this->respondWithErrorKey('idx.exists');
                        $data['idx'] = $model->idx;
                    }
                    // Update model
                    $model = $this->model_repository->update($model, array_merge($input, ['approver_id' => $this->auth->id, 'approved_at' => $this->getDateLocalFromTz($tz)]));
                    // Create import history
                    $this->import_history_repository->create($model->toArray());
                    // Update price to products
                    $this->product_repository->update($product, $data);
                } else {
                    $model = $this->model_repository->update($model, array_merge($input, ['appraiser_id' => $this->auth->id, 'approver_id' => null, 'approved_at' => null]));
                }
            } else {
                if (!empty($input)) $model = $this->model_repository->update($model, $input);
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/bus_out_products/{id}",
     *   summary="Delete Import",
     *   operationId="busDeleteImport",
     *   tags={"BackendBusOutProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Import Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            // Check permission
            if (!$this->isDelete()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Delete model
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
