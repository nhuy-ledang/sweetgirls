<?php namespace Modules\User\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
//use Modules\Activity\Entities\Order;
use Modules\Order\Repositories\OrderRepository as CoreOrderRepository;
use Modules\User\Repositories\UserRepository;

/**
 * Class OrderController
 *
 * @package Modules\User\Http\Controllers\ApiPublic
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2021-07-09
 */
class OrderController extends ApiBaseModuleController {
    /**
     * @var \Modules\User\Repositories\UserRepository
     */
    protected $user_repository;

    public function __construct(Request $request,
                                CoreOrderRepository $core_order_repository,
                                UserRepository $user_repository) {
        $this->model_repository = $core_order_repository;
        $this->user_repository = $user_repository;

        $this->middleware('auth.user');

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/user_orders",
     *   summary="Get User Orders",
     *   operationId="getUserOrders",
     *   tags={"UserOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
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
                'and'        => [
                    ['orders.user_id', '=', $this->auth->id]
                ],
                'in'         => [],
                'whereRaw'   => [],
                'orWhereRaw' => [],
            ];

            $data = $this->getRequestData();
            $payment_status = trim(utf8_strtolower((isset($data->{'payment_status'}) && !is_null($data->{'payment_status'}) && $data->{'payment_status'} !== '') ? trim((string)$data->{'payment_status'}) : ''));
            if ($payment_status) $queries['and'][] = ['orders.payment_status', '=', $payment_status];
            $payment_code = trim(utf8_strtolower((isset($data->{'payment_code'}) && !is_null($data->{'payment_code'}) && $data->{'payment_code'} !== '') ? trim((string)$data->{'payment_code'}) : ''));
            if ($payment_code) $queries['in'][] = ['orders.payment_code', explode(',', $payment_code)];
            $shipping_status = trim(utf8_strtolower((isset($data->{'shipping_status'}) && !is_null($data->{'shipping_status'}) && $data->{'shipping_status'} !== '') ? trim((string)$data->{'shipping_status'}) : ''));
            if ($shipping_status) $queries['in'][] = ['orders.shipping_status', explode(',', $shipping_status)];
            $order_status = trim(utf8_strtolower((isset($data->{'order_status'}) && !is_null($data->{'order_status'}) && $data->{'order_status'} !== '') ? trim((string)$data->{'order_status'}) : ''));
            if ($order_status) $queries['and'][] = ['orders.order_status', '=', $order_status];

            $fields = [
                'orders.*',

//                'oh.comment as oh__comment',
            ];

            $results = $this->setUpQueryBuilder($this->model(), $queries, false, $fields)
//                ->leftJoin('act__orders as act', function($join) {
//                    $join->on('act.master_id', '=', 'orders.id')->where('orders.type', 'activity');
//                })
//                ->leftJoin('orders as pd', function($join) {
//                    $join->on('pd.master_id', '=', 'orders.id');
//                })
//                ->leftJoin('order__histories as oh', function($join) {
//                    $join->on('oh.order_id', '=', 'orders.id')->on('oh.order_status' , '=', 'orders.order_status');
//                })
                ->orderBy($sort, $order)
                ->take($pageSize)
                ->skip($pageSize * ($page - 1))
                ->get();
            $output = [];
            foreach ($results as $item) {
                $newItem = $this->parseToRespond($item->toArray());
                $order = new \Modules\Order\Entities\Order($newItem);
                $order->id = $item->id;
                $newOrder = $order->toArray();
                $newOrder['products'] = [];
                foreach ($order->products as $product) {
                    $product['options'] = \DB::table('order__options')
                        ->where('order__options.order_id',$product['pivot']->order_id)
//                            ->where('order__options.order_product_id',$product['id'])
                        ->get();
                    $product['quantity_op'] = \DB::table('order__products')
                        ->where('order__products.product_id',$product->id)
                        ->where('order__products.order_id', $order->id)
                        ->value('order__products.quantity');
                    $newOrder['products'][] = $product;
                }
                $newOrder['created_at'] = $item->created_at;
                $newOrder['order_status_name'] = $item->order_status_name;
                $newOrder['comment'] = $item->pd__comment;
                $output[] = $newOrder;
            }

            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging === 'true' ? true : ($paging === 'false' ? false : (boolean)$paging);

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
}
