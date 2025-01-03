<?php namespace Modules\Stock\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Product\Repositories\ProductRepository;
use Modules\Stock\Repositories\StockProductRepository;
use Modules\Stock\Repositories\StockRepository;
use Modules\Stock\Repositories\TicketProductRepository;
use Modules\Stock\Repositories\TicketRepository;
use Modules\Usr\Repositories\UserRepository;

/**
 * Class ReqTicketController
 *
 * @package Modules\Stock\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-05-03
 */
class ReqTicketController extends OutTicketController {
    public function __construct(Request $request,
                                TicketRepository $ticket_repository,
                                TicketProductRepository $ticket_product_repository,
                                StockRepository $stock_repository,
                                StockProductRepository $stock_product_repository,
                                ProductRepository $product_repository,
                                UserRepository $staff_repository) {

        parent::__construct($request, $ticket_repository, $ticket_product_repository, $stock_repository, $stock_product_repository, $product_repository, $staff_repository);
    }

    /**
     * Get the validation rules for update.
     *
     * @param int $id
     * @return array
     */
    protected function rulesForUpdate($id) {
        return [
            'stock_id' => 'required|integer|exists:sto__stocks,id',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/sto_req_tickets",
     *   summary="Get Req Tickets",
     *   operationId="stoGetReqTickets",
     *   tags={"BackendStoReqTickets"},
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
            $queries = ['and' => [['type', '=', 'out']], 'whereRaw' => [['invoice_id is not null']], 'orWhereRaw' => []];
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
     * @OA\Put(
     *   path="/backend/sto_req_tickets/{id}",
     *   summary="Update Req Ticket",
     *   operationId="stoUpdateReqTicket",
     *   tags={"BackendStoReqTickets"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Ticket Id", example=1),
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
            $model = $this->model_repository->getModel()->where('id', $id)->where('type', 'out')->whereNotNull('invoice_id')->first();
            if (!$model) return $this->errorNotFound();
            if ($model->status != 'new') return $this->respondWithErrorKey("status.{$model->status}");
            $input = $this->request->only(['stock_id', 'date', 'storekeeper_id', 'accountant_id', 'reason', 'note']);
            // Check valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $idx = $this->request->get('idx');
            if ($idx) $idx = to_idx($idx);
            if ($idx && $idx != $model->idx) {
                $temp = $this->model_repository->getModel()->where('idx', $idx)->first();
                if ($temp) return $this->respondWithErrorKey('idx.exists');
                $input['idx'] = $idx;
            }
            // Products
            list($products) = $this->getProducts();
            if (!$products) return $this->respondWithErrorKey('product_id.required');
            if (!$model->out_type) { // Will remove
                $invoice = $model->invoice;
                $input['out_type'] = 'sale';
                if ($invoice) $input['out_customer_id'] = $invoice->customer_id;
            }
            // Update model
            $model = $this->model_repository->update($model, $input);
            // From order invoice
            /*// Ticket products
            $this->ticket_product_repository->getModel()->where('ticket_id', $model->id)->delete();
            // Create ticket products
            list($total) = $this->createProducts($products, $model);
            $model = $this->model_repository->update($model, ['total' => $total]);*/
            $model->products;

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/sto_req_tickets/{id}",
     *   summary="Delete Req Ticket",
     *   operationId="stoDeleteReqTicket",
     *   tags={"BackendStoReqTickets"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Ticket Id", example=1),
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
     *   path="/backend/sto_req_tickets/{id}/status",
     *   summary="Update Req Ticket Status",
     *   operationId="stoUpdateReqTicketStatus",
     *   tags={"BackendStoReqTickets"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Ticket Id", example=1),
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
            $validatorErrors = $this->getValidator($input, ['status' => 'required|in:completed,rejected']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            if ($model->status != 'new') return $this->respondWithErrorKey("status.{$model->status}");
            // Update quantity
            if ($input['status'] == 'completed') {
                $ids = [];
                foreach ($model->products as $p) $ids[] = $p->product_id;
                $stock_products = $this->stock_product_repository->getModel()->where('stock_id', $model->stock_id)->whereIn('product_id', $ids)->get();
                $obj = [];
                foreach ($stock_products as $p) $obj[$p->product_id] = $p->quantity;
                // Check product not enough
                $not_enough = false;
                foreach ($model->products as $p) {
                    $inQuantity = isset($obj[$p->product_id]) ? $obj[$p->product_id] : 0;
                    if ($p->quantity > $inQuantity) {
                        $not_enough = true;
                        break;
                    }
                }
                if ($not_enough) return $this->respondWithErrorKey('stock.product.not_enough');
                // Update quantity
                foreach ($model->products as $p) {
                    $stock_product = $this->stock_product_repository->getModel()->where('stock_id', $model->stock_id)->where('product_id', $p->product_id)->first();
                    if ($stock_product) {
                        $this->stock_product_repository->update($stock_product, ['quantity' => $stock_product->quantity - $p->quantity]);
                    }
                }
                $data = array_merge($input, ['approver_id' => $this->auth->id, 'approved_at' => $this->getDateLocalFromTz($tz)]);
            } else {
                $data = array_merge($input, ['rejected_at' => $this->getDateLocalFromTz($tz)]);
            }
            // Update model
            $model = $this->model_repository->update($model, $data);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
