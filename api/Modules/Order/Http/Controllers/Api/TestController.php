<?php namespace Modules\Order\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Order\Repositories\OrderRepository;
use Modules\Order\Transport\Facade\Transport;

class TestController extends ApiBaseModuleController {
    public function __construct(Request $request, OrderRepository $order_repository) {
        $this->model_repository = $order_repository;

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_test",
     *   summary="Get Test",
     *   operationId="ordTest",
     *   tags={"BackendOrdTest"},
     *   security={{"bearer":{}}},
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
//            $res = Transport::login();
//            $res = Transport::getProvinces();
            $res = Transport::getPrice();
            return $this->respondWithSuccess($res);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
