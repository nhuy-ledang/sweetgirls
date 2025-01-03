<?php namespace Modules\Stock\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Product\Repositories\ProductRepository;
use Modules\Stock\Repositories\StoProductRepository;
use Modules\Stock\Repositories\RequestRepository;
use Modules\Stock\Repositories\StockProductRepository;
use Modules\Stock\Repositories\StockRepository;
use Modules\Stock\Repositories\TicketRepository;
use Modules\Usr\Repositories\UserRepository;

/**
 * Class TicketController
 *
 * @package Modules\Stock\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-04-18
 */
class TicketController extends ApiBaseModuleController {

    /**
     * @var \Modules\Stock\Repositories\StoProductRepository;
     */
    protected $sto_product_repository;

    /**
     * @var \Modules\Stock\Repositories\RequestRepository;
     */
    protected $request_repository;

    /**
     * @var \Modules\Stock\Repositories\StockRepository
     */
    protected $stock_repository;

    /**
     * @var \Modules\Stock\Repositories\StockProductRepository
     */
    protected $stock_product_repository;

    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    /**
     * @var \Modules\Usr\Repositories\UserRepository;
     */
    protected $staff_repository;

    public function __construct(Request $request,
                                TicketRepository $ticket_repository,
                                RequestRepository $request_repository,
                                StoProductRepository $sto_product_repository,
                                StockRepository $stock_repository,
                                StockProductRepository $stock_product_repository,
                                ProductRepository $product_repository,
                                UserRepository $staff_repository) {
        $this->model_repository = $ticket_repository;
        $this->request_repository = $request_repository;
        $this->sto_product_repository = $sto_product_repository;
        $this->stock_repository = $stock_repository;
        $this->stock_product_repository = $stock_product_repository;
        $this->product_repository = $product_repository;
        $this->staff_repository = $staff_repository;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/backend/sto_tickets",
     *   summary="Get Tickets",
     *   operationId="stoGetTickets",
     *   tags={"BackendStoTickets"},
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
                $totalCount = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, true)->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/sto_tickets/{id}",
     *   summary="Get a Ticket",
     *   operationId="stoGetTicket",
     *   tags={"BackendStoTickets"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Ticket Id", example=1),
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

    protected function getStoProducts() {
        $products = [];
        $tmpProducts = (array)$this->request->get('products');
        if (!empty($tmpProducts)) foreach ($tmpProducts as $item) {
            $validatorErrors = $this->getValidator($item, ['product_id' => 'required|integer', 'quantity' => 'required|integer|min:1']);
            if (empty($validatorErrors)) {
                $product = $item['product_id'] ? $this->product_repository->find($item['product_id']) : null;
                if ($product) {
                    foreach (['ord_quantity', 'shipment', 'due_date', 'code'] as $fieldName) $product->{$fieldName} = !empty($item[$fieldName]) ? $item[$fieldName] : '';
                    $product->quantity = (int)$item['quantity'];
                    $products[] = $product;
                }
            }
        }
        return [$products];
    }

    protected function createProducts($products, $model) {
        $total = 0;
        foreach ($products as $p) {
//            $price = $p->price_im;
            $price = $p->price;
            $sub = $price * $p->quantity;
            $this->ticket_product_repository->create([
                'ticket_id'    => $model->id,
                'product_id'   => $p->id,
                'quantity'     => $p->quantity,
                'price'        => $price,
                'total'        => $sub,
                'ord_quantity' => $p->ord_quantity,
                'shipment'     => $p->shipment,
                'due_date'     => $p->due_date,
                'code'         => $p->code,
            ]);
            $total += $sub;
        }
        return [$total];
    }

    /**
     * @OA\Delete(
     *   path="/backend/sto_tickets/{id}",
     *   summary="Delete Ticket",
     *   operationId="stoDeleteTicket",
     *   tags={"BackendStoTickets"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Ticket Id", example=1),
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
