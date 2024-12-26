<?php namespace Modules\Stock\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\Stock\Repositories\StockProductRepository;
use Modules\Stock\Repositories\StockRepository;
use Modules\Stock\Repositories\StockRoleRepository;

/**
 * Class StockController
 *
 * @package Modules\Stock\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2021-08-16
 */
class StockController extends ApiBaseModuleController {
    /**
     * @var \Modules\Stock\Repositories\StockProductRepository
     */
    protected $stock_product_repository;

    /**
     * @var \Modules\Stock\Repositories\StockRoleRepository
     */
    protected $stock_role_repository;

    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    protected $file_repository;

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    public function __construct(Request $request,
                                StockRepository $stock_repository,
                                StockProductRepository $stock_product_repository,
                                StockRoleRepository $stock_role_repository,
                                FileRepository $file_repository,
                                FileService $fileService) {
        $this->model_repository = $stock_repository;
        $this->stock_product_repository = $stock_product_repository;
        $this->stock_role_repository = $stock_role_repository;
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
     *   path="/backend/sto_stocks_all",
     *   summary="GetStockAll",
     *   operationId="GetStockAll",
     *   tags={"BackendStoStocks"},
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
     *   path="/backend/sto_stocks",
     *   summary="Get Stocks",
     *   operationId="getStocks",
     *   tags={"BackendStoStocks"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="name"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="asc"),
     *   @OA\Parameter(name="paging", description="With Paging", in="query", example="0"),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
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
            //if (!($this->isView() || $this->isViewOwn())) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $order = (string)$this->request->get('order');
            $sort = !$sort ? 'name' : strtolower($sort);
            $order = !$order ? 'asc' : strtolower($order);
            $queries = [
                'and'        => [],
                'orWhereRaw' => [],
            ];
            $data = $this->getRequestData();
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
            }
            $results = $this->setUpQueryBuilder($this->model(), $queries, false)
                ->orderBy($sort, $order)
                ->take($pageSize)->skip($pageSize * ($page - 1))
                ->get();

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
     * @OA\Post(
     *   path="/backend/sto_stocks",
     *   summary="Create Stock",
     *   operationId="createStock",
     *   tags={"BackendStoStocks"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Stock Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="description", type="string", example=""),
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
            //if (!$this->isCreate()) return $this->errorForbidden();
            $input = $this->request->all();
            // Check valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $input['idx'] = !empty($input['idx']) ? to_idx($input['idx']) : to_idx($input['name']);
            $model = $this->model_repository->create($input);
            if ($input['keeper_ids']) {
                $keeper_ids = explode(',', $input['keeper_ids']);
                foreach ($keeper_ids as $item) {
                    $this->stock_role_repository->create(['stock_id' => $model->id, 'staff_id' => $item, 'role' => 'keeper']);
                }
            }
            if ($input['seller_ids']) {
                $seller_ids = explode(',', $input['seller_ids']);
                foreach ($seller_ids as $item) {
                    $this->stock_role_repository->create(['stock_id' => $model->id, 'staff_id' => $item, 'role' => 'seller']);
                }
            }
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
     * @OA\Post(
     *   path="/backend/sto_stocks/{id}",
     *   summary="Update Stock",
     *   operationId="updateStock",
     *   tags={"BackendStoStocks"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Stock Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="description", type="string", example=""),
     *     ),
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
            //if (!$this->isUpdate()) return $this->errorForbidden();
            $input = $this->request->all();
            // Check valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Update Model
            $input['idx'] = !empty($input['idx']) ? to_idx($input['idx']) : to_idx($input['name']);
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
            $model = $this->model_repository->update($model, $input);
            if ($input['keeper_ids'] || $input['seller_ids']) $this->stock_role_repository->getModel()->where('stock_id', $model->id)->delete();
            if ($input['keeper_ids']) {
                $keeper_ids = explode(',', $input['keeper_ids']);
                foreach ($keeper_ids as $item) {
                    $this->stock_role_repository->create(['stock_id' => $model->id, 'staff_id' => $item, 'role' => 'keeper']);
                }
            }
            if ($input['seller_ids']) {
                $seller_ids = explode(',', $input['seller_ids']);
                foreach ($seller_ids as $item) {
                    $this->stock_role_repository->create(['stock_id' => $model->id, 'staff_id' => $item, 'role' => 'seller']);
                }
            }


            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/sto_stocks/{stock_id}",
     *   summary="Delete Stock",
     *   operationId="deleteStock",
     *   tags={"BackendStoStocks"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Stock Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            // Check permission
            //if (!$this->isDelete()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Delete model
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/sto_stocks/{id}/products",
     *   summary="Get Stock Products",
     *   operationId="createStockProducts",
     *   tags={"BackendStoStocks"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Stock Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function products($id) {
        try {
            $results = $this->stock_product_repository->getModel()->where('stock_id', $id)->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
