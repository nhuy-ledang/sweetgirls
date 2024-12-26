<?php namespace Modules\Order\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\Order\Repositories\OrderShippingHistoryRepository;

/**
 * Class OrderShippingHistoryController
 *
 * @package Modules\Order\Http\Controllers\ApiPublic
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2022-01-28
 */
class OrderShippingHistoryController extends ApiBaseModuleController {
    public function __construct(Request $request, OrderShippingHistoryRepository $order_shipping_history_repository) {
        $this->model_repository = $order_shipping_history_repository;

        $this->middleware('auth.user');

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/ord_order_shipping_histories",
     *   summary="Get Order Shipping Histories",
     *   operationId="getOrderShippingHistories",
     *   tags={"OrderShippingHistories"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example="%7B%22order_id%22%3A0%7D"),
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
            $data = $this->getRequestData();
            $order_id = !empty($data->{'order_id'}) ? (int)$data->{'order_id'} : 0;
            $queries = [
                'and' => [
                    ['order_id', '=', $order_id],
                ],
            ];
            $results = $this->setUpQueryBuilder($this->model(), $queries, false)->orderBy('id', 'desc')->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
