<?php namespace Modules\Stock\Http\Controllers\Api;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Notify\Repositories\NotificationRepository;
use Modules\Stock\Repositories\InventoryProductRepository;
use Modules\Stock\Repositories\StockProductRepository;
use Modules\Stock\Repositories\StockRepository;
use Modules\Product\Repositories\ProductRepository;
use Modules\Stock\Repositories\InventoryRepository;

/**
 * Class InventoryProductController
 *
 * @package Modules\Stock\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2022-04-13
 */
class InventoryProductController extends ApiBaseModuleController {
    /**
     * @var \Modules\Stock\Repositories\InventoryRepository
     */
    protected $inventory_repository;

    /**
     * @var \Modules\Stock\Repositories\StockRepository
     */
    protected $stock_repository;

    /**
     * @var \Modules\Stock\Repositories\StockProductRepository
     */
    protected $stock_product_repository;

    /**
     * @var \Modules\Stock\Repositories\InventoryProductRepository
     */
    protected $stock_inventory_product_repository;

    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    /**
     * @var \Modules\Notify\Repositories\NotificationRepository
     */
    protected $notification_repository;

    public function __construct(Request $request,
                                StockRepository $stock_repository,
                                StockProductRepository $stock_product_repository,
                                InventoryRepository $inventory_repository,
                                InventoryProductRepository $stock_inventory_product_repository,
                                ProductRepository $product_repository,
                                NotificationRepository $notification_repository) {
        $this->model_repository = $stock_inventory_product_repository;
        $this->inventory_repository = $inventory_repository;
        $this->stock_repository = $stock_repository;
        $this->stock_product_repository = $stock_product_repository;
        $this->product_repository = $product_repository;
        $this->notification_repository = $notification_repository;

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
            'stock_id' => 'required|integer|exists:sto__stocks,id',
            'name'     => 'required',
            'date'     => 'required|date_format:"Y-m-d"',
            'type'     => 'required|in:in,out',
        ];
    }

    /**
     * Get the validation rules for update.
     *
     * @param int $id
     * @return array
     */
    protected function rulesForUpdate($id) {
        return [];
    }

    /**
     * @OA\Get(
     *   path="/backend/sto_inventory_product",
     *   summary="Get Stock Inventories",
     *   operationId="getInventories",
     *   tags={"BackendStoInventoryProduct"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", description="Stock", example="desc"),
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
            //if (!($this->isView() || $this->isViewOwn())) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'id' : strtolower($sort);
            $order = (string)$this->request->get('order');
            $order = !$order ? 'desc' : strtoupper($order);
            $queries = [
                'and'        => [],
                'in'         => [],
                'whereRaw'   => [],
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
                        $iQ[] = "lower($key) like ?";
                        $iB[] = "%$i%";
                    }
                    $queries['orWhereRaw'][] = ['(' . implode(' and ', $iQ) . ')', $iB];
                }
            }
            $results = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false)
                ->orderBy($sort, $order)
                ->take($pageSize)
                ->skip($pageSize * ($page - 1))
                ->get();
            /*$output = [];
            foreach ($results as $item) {
                $newItem = $this->parseToRespond($item->toArray());
                $output[] = $newItem;
            }*/
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'true' ? true : ($paging == 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($results);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, true)->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/sto_inventory_product/{id}",
     *   summary="Get a Inventory Product",
     *   operationId="getInventory Product",
     *   tags={"BackendStoInventoryProduct"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Inventory Id", example=1),
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
            //if (!($this->isView() || $this->isViewOwn())) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/sto_inventory_product",
     *   summary="Create Inventory Product",
     *   operationId="createInventory Product",
     *   tags={"BackendStoInventoryProduct"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="stock_id", type="integer", example=0),
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="date", type="string", example="2021-02-01"),
     *       @OA\Property(property="type", type="string", example="in"),
     *     ),
     *   ),
     *   @OA\Parameter(name="tz", in="query", description="TimezoneOffset", example="-420"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function store() {
        try {
            // Check permission
            //if (!$this->isCreate()) return $this->errorForbidden();
            $input = $this->request->only(['stock_id', 'name', 'date', 'type', 'note']);
            $input['type'] = 'in';
            // Check valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Products
            $tmpProducts = (array)$this->request->get('products');
            $products = [];
            if (!empty($tmpProducts)) {
                foreach ($tmpProducts as $item) {
                    $validatorErrors = $this->getValidator($item, ['id' => 'required|integer', 'quantity' => 'required|integer|min:1']);
                    if (empty($validatorErrors)) {
                        if (intval($item['id'])) {
                            $product = $this->product_repository->find($item['id']);
                            if ($product) {
                                $item['weight'] = (int)$item['quantity'] * (float)$product->weight;
                                $products[] = $item;
                            }
                        }
                    }
                }
            }
            if (!$products) return $this->respondWithErrorKey('product_id.required');
            // Create model
            $model = $this->model_repository->create($input);
            // Inventory products
            foreach ($products as $product) {
                $this->stock_inventory_product_repository->create([
                    'inventory_id'  => $model->id,
                    'stock_id'   => $model->stock_id,
                    'product_id' => $product['id'],
                    'date'       => $model->date,
                    'type'       => $model->type,
                    'quantity'   => $product['quantity'],
                    'weight'     => $product['weight'],
                ]);
                $this->product_repository->getModel()->where('id', $product['id'])->update(['quantity' => \DB::raw("`quantity` + " . $product['quantity']), 'quantity_tmp' => \DB::raw("`quantity_tmp` + " . $product['quantity'])]);
            }
            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/sto_inventory_product/{id}",
     *   summary="Update Inventory Product",
     *   operationId="updateInventory Product",
     *   tags={"BackendStoInventoryProduct"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Inventory Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="date", type="string", example="2021-02-01"),
     *       @OA\Property(property="type", type="string", example="in"),
     *     ),
     *   ),
     *   @OA\Parameter(name="tz", in="query", description="TimezoneOffset", example="-420"),
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
            //if (!$this->isUpdate()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['name', 'date', 'note']);
            // Check valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Products
            $tmpProducts = (array)$this->request->get('products');
            $products = [];
            if (!empty($tmpProducts)) {
                foreach ($tmpProducts as $item) {
                    $validatorErrors = $this->getValidator($item, ['id' => 'required|integer', 'quantity' => 'required|integer|min:1']);
                    if (empty($validatorErrors)) {
                        if (intval($item['id'])) {
                            $product = $this->product_repository->find($item['id']);
                            if ($product) {
                                $item['weight'] = (int)$item['quantity'] * (float)$product->weight;
                                $products[] = $item;
                            }
                        }
                    }
                }
            }
            if (!$products) return $this->respondWithErrorKey('product_id.required');
            // Update model
            $model = $this->model_repository->update($model, $input);
            // Inventory products
            foreach ($model->products as $inventory_product) {
                $this->product_repository->getModel()->where('id', $inventory_product->product_id)->update(['quantity' => \DB::raw("`quantity` - " . $inventory_product->quantity), 'quantity_tmp' => \DB::raw("`quantity_tmp` - " . $inventory_product->quantity)]);
            }
            $this->stock_inventory_product_repository->getModel()->where('inventory_id', $model->id)->delete();
            foreach ($products as $product) {
                $this->stock_inventory_product_repository->create([
                    'inventory_id'  => $model->id,
                    'stock_id'   => $model->stock_id,
                    'product_id' => $product['id'],
                    'date'       => $model->date,
                    'type'       => $model->type,
                    'quantity'   => $product['quantity'],
                    'weight'     => $product['weight'],
                ]);
                $this->product_repository->getModel()->where('id', $product['id'])->update(['quantity' => \DB::raw("`quantity` + " . $product['quantity']), 'quantity_tmp' => \DB::raw("`quantity_tmp` + " . $product['quantity'])]);
            }
            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/sto_inventory_product/{id}",
     *   summary="Delete Inventory Product",
     *   operationId="deleteInventory Product",
     *   tags={"BackendStoInventoryProduct"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Inventory Id", example=1),
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
     *   path="/backend/sto_inventory_product_import_check",
     *   summary="Get Product Stats",
     *   operationId="stoGetProductStats",
     *   tags={"BackendStoInventoryProduct"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */

    private function getImportData() {
        $file = $this->request->file('file');
        $rows = [];
        $reader = Excel::toArray(null, $file->getRealPath(), null, \Maatwebsite\Excel\Excel::XLSX);
        $bulkInsertionFields = ['idx', 'quantity', 'created_at', 'name', 'unit'];
        if (!empty($reader[0])) {
            foreach ($reader[0] as $k => $row) {
                if ($k == 0) continue;
                $item = [];
                $numCol = 0;
                foreach ($bulkInsertionFields as $fieldName) {
                    $v = isset($row[$numCol]) ? $row[$numCol] : null;
                    $v = (!is_null($v) && trim((string)$v) != '') ? trim((string)$v) : null;

                    // Convert 'created_at' column to DateTime object
                    if ($fieldName == 'created_at' && !is_null($v)) {
                        try {
                            $v = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($v)->format('Y-m-d');
                        } catch (\Exception $e) {
                            // Handle exception if conversion fails
                            $v = null;
                        }
                    }
                    $item[$fieldName] = $v;
                    $numCol++;
                }
                $rows[] = $item;
            }
        }
        $output = ['invalid' => [], 'valid' => []];
        $rules = ['idx' => 'required', 'quantity' => 'required', 'created_at' => 'required'];
        foreach ($rows as $input) {
            $newInput = $input;
            // Check Valid
            $validatorErrors = $this->getValidator($newInput, $rules);
            if (!empty($validatorErrors)) {
                $input['errors'] = $validatorErrors;
                $output['invalid'][] = $input;
            } else {
                // Check exist email
                $exist_pd = $this->product_repository->findByAttributes(['idx' => $newInput['idx']]);
                if (!$exist_pd) {
                    $input['errors'][0]['errorMessage'] = 'Mã hàng không tồn tại trong hệ thống';
                    $output['invalid'][] = $input;
                } else {
                    $input['insertData'] = $newInput;
                    $output['valid'][] = $input;
                }
            }
        }

        return $output;
    }

    /**
     * @OA\Post(
     *   path="/backend/sto_inventory_product_import_check",
     *   summary="Import Product Check Valid",
     *   operationId="importProductCheck",
     *   tags={"BackendStoInventoryProduct"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object",
     *         @OA\Property(property="file", type="string", format="binary"),
     *         @OA\Property(property="files[]", type="array", @OA\Items(type="string", format="binary")),
     *       ),
     *     )
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function importCheck() {
        try {
            // Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), ['file' => 'required|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);

            $output = $this->getImportData();

            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/sto_inventory_product_import",
     *   summary="Import Product",
     *   operationId="importProduct",
     *   tags={"BackendStoInventoryProduct"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object",
     *         @OA\Property(property="file", type="string", format="binary"),
     *         @OA\Property(property="files[]", type="array", @OA\Items(type="string", format="binary")),
     *       ),
     *     )
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function import() {
        try {
            // Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), ['file' => 'required|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $data = $this->getImportData();
            $data['newData'] = [];
            if (!empty($data['valid'])) {
                foreach ($data['valid'] as $input) {
                    $product = $this->product_repository->getModel()->where('idx', $input['idx'])->first();
                    // Create model
                    $model = $this->model_repository->create(array_merge($input, ['product_id' => $product->id, 'unit' => $product->unit, 'reality' => $input['quantity'], 'quantity' => $input['quantity']]));
                    $model->created_at = $input['created_at'];
                    $model->save();

                    $data['newData'][] = $model;
                }
            }
            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
