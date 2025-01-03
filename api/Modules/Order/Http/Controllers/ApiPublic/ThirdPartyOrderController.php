<?php namespace Modules\Order\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Traits\MomoTrait;
use Modules\Core\Traits\OnepayTrait;
use Modules\Notify\Repositories\NotificationRepository;
use Modules\Order\Repositories\CartRepository;
use Modules\Order\Repositories\OrderRepository;
use Modules\Order\Repositories\OrderHistoryRepository;
use Modules\Order\Repositories\OrderProductRepository;
use Modules\Order\Repositories\OrderTotalRepository;
use Modules\Product\Repositories\ProductQuantityRepository;
use Modules\Product\Repositories\ProductRepository;
use Modules\System\Repositories\SettingRepository;
use Modules\User\Repositories\AddressRepository as UserAddressRepository;
use Modules\User\Repositories\NotifyRepository as UserNotifyRepository;
use Modules\User\Repositories\UserCoinRepository;
use Modules\User\Repositories\UserRepository;

/**
 * Class ThirdPartyOrderController
 *
 * @package Modules\Order\Http\Controllers\ApiPublic
 */
class ThirdPartyOrderController extends ApiBaseModuleController {

    public function __construct(Request $request, OrderRepository $order_repository) {
        $this->model_repository = $order_repository;

        $this->middleware('order.api_public');

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/third_party/orders",
     *   summary="Get Third Party Order",
     *   operationId="ordGetThirdPartyOrders",
     *   tags={"ThirdPartyOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="secret_key", in="query", description="Secret Key", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found")
     * )
     */
    public function index() {
        try {


            return $this->respondWithSuccess([]);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
