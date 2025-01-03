<?php namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Order\Networks\Tiktok;
use Modules\Product\Repositories\ProductRepository;

/**
 * Class OptionController
 *
 * @package Modules\Product\Http\Controllers\Api
 * @author Nhat Truong <nhattruong.tedfast@gmail.com>
 * Date: 2023-12-20
 */
class NetworkController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    public function __construct(Request $request, ProductRepository $product_repository) {
        $this->model_repository = $product_repository;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_product_networks/{model}/product_tiktok",
     *   summary="Get Product Tiktok",
     *   operationId="pdGetProductTiktok",
     *   tags={"BackendPdNetWork"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="model", in="path", description="SKU", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function getProductTiktok($model) {
        try {
            $result = $this->model_repository->findByAttributes(['model' => $model]);
            if (!$result) return $this->errorNotFound();
            $tiktok = new Tiktok();
            $query = [
                'page_size'   => 100,
                'seller_skus' => [$model],
            ];
            $response = $tiktok->connect()->Product->searchProducts($query);

            return $this->respondWithSuccess($response);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
