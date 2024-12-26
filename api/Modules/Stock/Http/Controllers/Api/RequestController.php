<?php namespace Modules\Stock\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Product\Repositories\ProductRepository;
use Modules\Stock\Repositories\RequestProductRepository;
use Modules\Stock\Repositories\RequestRepository;
use Modules\Stock\Repositories\StockProductRepository;
use Modules\Stock\Repositories\StockRepository;
use Modules\Stock\Repositories\StoProductRepository;
use Modules\Stock\Repositories\TicketRepository;

/**
 * Class RequestController
 *
 * @package Modules\Stock\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-04-18
 */
class RequestController extends ApiBaseModuleController {
    /**
     * @var \Modules\Stock\Repositories\RequestProductRepository
     */
    protected $request_product_repository;

    /**
     * @var \Modules\Stock\Repositories\StoProductRepository;
     */
    protected $sto_product_repository;

    /**
     * @var \Modules\Stock\Repositories\TicketRepository
     */
    protected $ticket_repository;

    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    /**
     * @var \Modules\Stock\Repositories\StockProductRepository
     */
    protected $stock_product_repository;



    public function __construct(Request $request,
                                RequestRepository $request_repository,
                                RequestProductRepository $request_product_repository,
                                TicketRepository $ticket_repository,
                                StockRepository $stock_repository,
                                StoProductRepository $sto_product_repository,
                                StockProductRepository $stock_product_repository,
                                ProductRepository $product_repository) {
        $this->model_repository = $request_repository;
        $this->request_product_repository = $request_product_repository;
        $this->product_repository = $product_repository;
        $this->ticket_repository = $ticket_repository;
        $this->stock_product_repository = $stock_product_repository;
        $this->sto_product_repository = $sto_product_repository;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    public function getStoProducts($tmpProducts) {
        $products = [];
        if (!empty($tmpProducts)) foreach ($tmpProducts as $item) {
            $validatorErrors = $this->getValidator($item, ['product_id' => 'required|integer', 'quantity' => 'required|integer|min:1']);
            if (empty($validatorErrors)) {
                $product = $item['product_id'] ? $this->product_repository->find($item['product_id']) : null;
                if ($product) {
                    foreach (['shipment', 'due_date', 'code'] as $fieldName) $product->{$fieldName} = !empty($item[$fieldName]) ? $item[$fieldName] : '';
                    $product->quantity = (int)$item['quantity'];
                    $products[] = $product;
                }
            }
        }
        return [$products];
    }

    public function createProducts($products, $model) {
        $total = 0;
        foreach ($products as $p) {
//            $price = $p->price_im;
            $price = $p->price;
            $sub = $price * $p->quantity;
            $this->request_product_repository->create([
                'request_id'   => $model->id,
                'stock_id'     => $model->stock_id,
                'product_id'   => $p->id,
                'quantity'     => $p->quantity,
                'price'        => $price,
                'total'        => $sub,
                'shipment'     => $p->shipment,
                'due_date'     => $p->due_date,
                'code'         => $p->code,
                'type'         => $model->type,
            ]);
            $total += $sub;
        }
        return [$total];
    }

    /**
     * @OA\Get(
     *   path="/backend/sto_requests",
     *   summary="Get Requests",
     *   operationId="stoGetRequests",
     *   tags={"BackendStoRequests"},
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
            $queries = ['and' => [], 'whereRaw' => [], 'orWhereRaw' => []];
            $data = $this->getRequestData();
            /*// Query by keyword
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
            }*/
            $results = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false)->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
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
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/sto_requests/{id}",
     *   summary="Get a Request",
     *   operationId="stoGetRequest",
     *   tags={"BackendStoRequests"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Request Id", example=1),
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
     *   path="/backend/sto_requests",
     *   summary="Create Request",
     *   operationId="createRequest",
     *   tags={"BackendStoRequests"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Request Id", example=1),
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
            $input['usr_id'] = $this->auth->id;

            if ('in') {
                $res = app('\Modules\Stock\Http\Controllers\Api\InTicketController')->store()->getData(true);
            } else if ('out') {
                $res = app('\Modules\Stock\Http\Controllers\Api\InTicketController')->store()->getData(true);
            }
            $resId = $res['data']['data']['id'];
            if (!$resId) return $this->respondWithError($res);
            $input['ticket_id'] = $resId;
            $model = $this->model_repository->create($input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/sto_requests/{id}",
     *   summary="Update Request",
     *   operationId="createRequest",
     *   tags={"BackendStoRequests"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Request Id", example=1),
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
                    $this->stock_role_repository->create(['stock_id' => $model->id, 'usr_id' => $item, 'role' => 'keeper']);
                }
            }
            if ($input['seller_ids']) {
                $seller_ids = explode(',', $input['seller_ids']);
                foreach ($seller_ids as $item) {
                    $this->stock_role_repository->create(['stock_id' => $model->id, 'usr_id' => $item, 'role' => 'seller']);
                }
            }


            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/sto_requests/{id}",
     *   summary="Delete Request",
     *   operationId="stoDeleteRequest",
     *   tags={"BackendStoRequests"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Request Id", example=1),
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
            if ($model->status == 'approved') return $this->respondWithErrorKey("status.{$model->status}");
            // Delete model
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
