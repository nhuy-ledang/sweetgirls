<?php namespace Modules\Stock\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Product\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Modules\Stock\Repositories\RequestProductRepository;
use Modules\Stock\Repositories\StockProductRepository;
use Modules\Stock\Repositories\StockRepository;
use Modules\Stock\Repositories\RequestRepository;
use Modules\Stock\Repositories\StoProductRepository;
use Modules\Stock\Repositories\TicketRepository;
use Modules\Usr\Repositories\UserRepository;

/**
 * Class OutRequestController
 *
 * @package Modules\Stock\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-05-03
 */
class OutRequestController extends RequestController {
    public function __construct(Request $request,
                                RequestRepository $request_repository,
                                RequestProductRepository $request_product_repository,
                                TicketRepository $ticket_repository,
                                StockRepository $stock_repository,
                                StoProductRepository $sto_product_repository,
                                StockProductRepository $stock_product_repository,
                                ProductRepository $product_repository,
                                UserRepository $staff_repository) {

        parent::__construct($request, $request_repository, $request_product_repository, $ticket_repository, $stock_repository, $sto_product_repository, $stock_product_repository, $product_repository, $staff_repository);
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreate() {
        return [
            //'stock_id' => 'required|integer|exists:sto__stocks,id',
            //'date'     => 'required|date_format:"Y-m-d"',
            'out_type' => 'required|in:sale,destroy,return,transfer',
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
            'out_type' => 'required|in:sale,destroy,return,transfer',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/sto_out_requests",
     *   summary="Get Out Requests",
     *   operationId="stoGetOutRequests",
     *   tags={"BackendStoOutRequests"},
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
            $queries = ['and' => [['type', '=', 'out']], 'whereRaw' => [], 'orWhereRaw' => []];
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
                $totalCount = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, true)->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/sto_out_requests",
     *   summary="Create In Request",
     *   operationId="stoCreateOutRequest",
     *   tags={"BackendStoOutRequests"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="stock_id", type="integer", example=0),
     *       @OA\Property(property="type", type="string", example="in"),
     *       @OA\Property(property="date", type="string", example="2021-02-01"),
     *       @OA\Property(property="reason", type="string", example=""),
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
    public function store() {
        try {
            // Check permission
            //if (!$this->isCreate()) return $this->errorForbidden();
            $input = $this->request->all();
            // Check valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            /*$input['out_supplier_id'] = null;
            $input['out_stock_id'] = null;
            if ($input['out_type'] == 'purchase') {
                $input['out_supplier_id'] = $this->request->get('out_supplier_id');
            } else if ($input['out_type'] == 'transfer') {
                $input['out_stock_id'] = $this->request->get('out_stock_id');
            }
            // Check stock different
            if ($input['out_type'] == 'transfer') {
                $stock_id = $this->request->get('stock_id');
                $out_stock_id = $this->request->get('out_stock_id');
                if ($stock_id && $stock_id == $out_stock_id) return $this->respondWithErrorKey('stock.out_stock.same');
            }*/
            $stock_idx = $this->request->get('stock_idx');
            $staff = $this->auth->staff ? $this->auth->staff : $this->auth;
            $department_idx = $staff->department ? $staff->department->idx : $staff->id;
            $max_idx = $this->model_repository->getModel()->select('idx','created_at')->orderBy('id', 'desc')->first();
            // $max_idx_compare = $max_idx ? substr($max_idx->idx, 0, 11) : null;
            $max_idx_compare = $max_idx && date('ymd', strtotime($max_idx->created_at)) == date('ymd');
            $exp_idx = $max_idx ? explode('-', $max_idx->idx) : [];
            $idx = ($max_idx_compare && !empty($exp_idx)) ? ((int)end($exp_idx) + 1) : 1;
            $input['idx'] = "RWR-$department_idx-$stock_idx-" . date('ymd') . "-$idx";

            // Products
            $tmpProducts = (array)$this->request->get('products');
            list($products) = $this->getStoProducts($tmpProducts);
            if (!$products) return $this->respondWithErrorKey('product_id.required');
            $input['type'] = 'out';
            $input['owner_id'] = $staff->id;
            // Create model
            $model = $this->model_repository->create($input);
            // Create ticket products
            list($total) = $this->createProducts($products, $model);
            $model = $this->model_repository->update($model, ['total' => $total]);
            $model->products;

            // Add request_id to orders
            if ($model->platform == 'website' && $model->invoice_id) DB::table('orders')->where('id', $model->invoice_id)->update(['sto_request_id' => $model->id, 'order_status' => ORDER_SS_PROCESSING]);

            ///// Auto approve ticket
            $tz = (int)$this->request->get('tz');
            $ticket = $this->ticket_repository->filterByAttribute(['request_id' => $model->id]);
            if (!empty($ticket)) $ticket_model = $this->ticket_repository->create([
                'type'        => 'out',
                'idx'         => 'T-' . $model->idx,
                'request_id'  => $model->id,
                'stock_id'    => $model->stock_id,
                'owner_id'    => $this->auth->id,
                'note'        => (isset($input['note']) ? $input['note'] : ''),
                'status'      => STO_TICKET_SS_COMPLETED,
                'reviewer_id' => $this->auth->id,
                'approved_at' => $this->getDateLocalFromTz($tz),
            ]);
            $this->request_product_repository->getModel()->where('stock_id', $model->stock_id)->where('request_id', $model->id)->update(['status' => 1]);
            foreach ($model->products as $product) {
                $this->sto_product_repository->create(['stock_id' => $model->stock_id, 'ticket_id' => $ticket_model->id, 'product_id' => $product->product_id, 'quantity' => $product->quantity, 'price' => $product->price, 'total' => $product->total, 'shipment' => $product->shipment, 'due_date' => $product->due_date, 'code' => $product->code, 'type' => $product->type, 'status' => 1]);
            }
            $model->approved_at = $this->getDateLocalFromTz($tz);
            $model->reviewer_id = $this->auth->id;
            $model->status = STO_REQUEST_SS_COMPLETED;
            $model->out_type = STO_OUT_TYPE_SALE;
            $model->type = 'out';
            foreach ($model->products as $p) {
                $stock_product = $this->stock_product_repository->getModel()->where('stock_id', $model->stock_id)->where('product_id', $p->product_id)->first();
                if ($stock_product) {
                    $this->stock_product_repository->update($stock_product, ['quantity' => $stock_product->quantity - $p->quantity]);
                }
                // Reduce quantity to pd__products // Đã trừ khi đặt hàng
                // $this->product_repository->getModel()->where('id', $p->product_id)->decrement('quantity', $p->quantity);
            }

            // Handle shipping
            if ($model->platform == 'website' && $model->invoice_id) {
                app('\Modules\Order\Http\Controllers\Api\OrderController')->createShipping($model->invoice_id);
            }
            $model->shipping_status = STO_SHIPPING_SS_WAIT_OUT;
            $model->save();

            /////////////////////////

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/sto_out_requests/{id}",
     *   summary="Update In Request",
     *   operationId="stoUpdateOutRequest",
     *   tags={"BackendStoOutRequests"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Request Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="type", type="string", example="in"),
     *       @OA\Property(property="date", type="string", example="2021-02-01"),
     *       @OA\Property(property="reason", type="string", example=""),
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
            $model = $this->model_repository->getModel()->where('id', $id)->where('type', 'out')->first();
            if (!$model) return $this->errorNotFound();
            if ($model->status != 'new') return $this->respondWithErrorKey("status.{$model->status}");
            $input = $this->request->only(['stock_id', 'in_type', 'supplier', 'location', 'in_supplier_id', 'in_stock_id', 'date', 'staff_id', 'storekeeper_id', 'accountant_id', 'reason', 'note']);
            // Check valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $input['in_supplier_id'] = null;
            $input['in_stock_id'] = null;
            if ($input['in_type'] == 'purchase') {
                $input['in_supplier_id'] = $this->request->get('in_supplier_id');
            } else if ($input['in_type'] == 'transfer') {
                $input['in_stock_id'] = $this->request->get('in_stock_id');
            }
            // Check stock different
            if ($input['in_type'] == 'transfer') {
                $stock_id = $this->request->get('stock_id');
                $in_stock_id = $this->request->get('in_stock_id');
                if ($stock_id && $stock_id == $in_stock_id) return $this->respondWithErrorKey('stock.in_stock.same');
            }
            $idx = $this->request->get('idx');
            if ($idx) $idx = to_idx($idx);
            if ($idx && $idx != $model->idx) {
                $temp = $this->model_repository->getModel()->where('idx', $idx)->first();
                if ($temp) return $this->respondWithErrorKey('idx.exists');
                $input['idx'] = $idx;
            }
            // Products
            $tmpProducts = (array)$this->request->get('products');
            list($products) = $this->getStoProducts($tmpProducts);
            if (!$products) return $this->respondWithErrorKey('product_id.required');
            // Update model
            $model = $this->model_repository->update($model, $input);
            // Request products
            $this->ticket_product_repository->getModel()->where('ticket_id', $model->id)->delete();
            // Create ticket products
            list($total) = $this->createProducts($products, $model);
            $model = $this->model_repository->update($model, ['total' => $total]);
            $model->products;

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/sto_out_requests/{id}",
     *   summary="Delete In Request",
     *   operationId="stoDeleteOutRequest",
     *   tags={"BackendStoOutRequests"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Request Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        return parent::destroy($id);
    }

    /**
     * @OA\Post(
     *   path="/backend/sto_out_requests/{id}/status",
     *   summary="Update In Request Status",
     *   operationId="stoUpdateOutRequestStatus",
     *   tags={"BackendStoOutRequests"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Request Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="completed"),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function status($id) {
        try {
            $tz = (int)$this->request->get('tz');
            $input = $this->request->only(['status', 'note']);
            // Check valid
            $validatorErrors = $this->getValidator($input, ['status' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Create ticket
            if ($input['status'] == STO_REQUEST_SS_PROCESSING) {
                $ticket = $this->ticket_repository->filterByAttribute(['request_id' => $model->id]);
                if (!$ticket) $ticket_model = $this->ticket_repository->create(['type' => 'out', 'idx' => 'T-' . $model->idx, 'request_id' => $model->id, 'stock_id' => $model->stock_id, 'owner_id' => $this->auth->id, 'note' => (isset($input['note']) ? $input['note'] : '')]);
                foreach ($model->products as $product) {
                    $this->sto_product_repository->create(['stock_id' => $model->stock_id, 'ticket_id' => $ticket_model->id, 'product_id' => $product->product_id, 'quantity' => $product->quantity, 'price' => $product->price, 'total' => $product->total, 'shipment' => $product->shipment, 'due_date' => $product->due_date, 'code' => $product->code, 'type' => $product->type, 'status' => $product->status]);
                }
                $data['approved_at'] = $this->getDateLocalFromTz($tz);
            } else if ($input['status'] == STO_REQUEST_SS_REJECTED) {
                $data['rejected_at'] = $this->getDateLocalFromTz($tz);
            }
            $data['reviewer_id'] = $this->auth->id;
            // Update model
            $model = $this->model_repository->update($model, array_merge($input, $data));

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/sto_out_requests_products",
     *   summary="Get In Requests Products",
     *   operationId="stoGetOutRequestsProducts",
     *   tags={"BackendStoOutRequests"},
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
    public function products() {
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
            $queries = ['and' => [['sto__tickets.type', '=', 'out'], ['sto__tickets.status', '=', 'completed']], 'whereRaw' => [], 'orWhereRaw' => []];
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
            // Query by ticket idx
            $idx_q = trim(utf8_strtolower((isset($data->{'idx'}) && !is_null($data->{'idx'}) && $data->{'idx'} !== '') ? trim((string)$data->{'idx'}) : ''));
            if ($idx_q) $queries['whereRaw'][] = ["lower(`sto__tickets`.`idx`) like ?", $idx_q];
            // Query by product
            $product_q = trim(utf8_strtolower((isset($data->{'product'}) && !is_null($data->{'product'}) && $data->{'product'} !== '') ? trim((string)$data->{'product'}) : ''));
            if ($product_q) $queries['whereRaw'][] = ["lower(`p`.`name`) like ?", ["%$product_q%"]];
            // Query by product idx
            $product_idx_q = trim(utf8_strtolower((isset($data->{'product_idx'}) && !is_null($data->{'product_idx'}) && $data->{'product_idx'} !== '') ? trim((string)$data->{'product_idx'}) : ''));
            if ($product_idx_q) $queries['whereRaw'][] = ["lower(`p`.`idx`) like ?", $product_idx_q];
            $fields = [
                'sto__tickets.*',
                'p.id as prd__id',
                'p.idx as prd__idx',
                'p.name as prd__name',
                'p.unit as prd__unit',
                'p.image as prd__image',
                'p.short_description as prd__short_description',
                'p.price_im as prd__price_im',
                'p.price as prd__price',
                'c.id as cat__id',
                'c.name as cat__name',
                'tp.quantity as tp__quantity',
                'tp.price as tp__price',
                'tp.total as tp__total',
                'tp.content as tp__content',
            ];
            $results = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false, $fields)
                ->rightJoin('sto__ticket_products as tp', 'tp.ticket_id', 'sto__tickets.id')
                ->leftJoin('pd__products as p', 'product_id', 'p.id')
                ->leftJoin('bus__categories as c', 'c.id', 'p.category_id')
                ->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
            $output = [];
            foreach ($results as $item) {
                $newItem = $this->parseToRespond($item->toArray());
                $product = new \Modules\Product\Entities\Product($newItem['prd']);
                $product->id = $item->prd__id;
                $newItem['product'] = $product;
                unset($newItem['prd']);
                $category = new \Modules\Product\Entities\Category($newItem['cat']);
                $category->id = $item->cat__id;
                $newItem['category'] = $category;
                unset($newItem['cat']);
                $tp = new \Modules\Stock\Entities\StoProduct($newItem['tp']);
                $newItem['tp'] = $tp;
                $output[] = $newItem;
            }
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'true' ? true : ($paging == 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($output);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, true)
                    ->rightJoin('sto__ticket_products as tp', 'tp.ticket_id', 'sto__tickets.id')
                    ->leftJoin('pd__products as p', 'product_id', 'p.id')
                    ->leftJoin('bus__categories as c', 'c.id', 'p.category_id')
                    ->count();
                return $this->respondWithPaging($output, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
