<?php namespace Modules\Order\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\Order\Networks\Lazada;
use Modules\Order\Networks\Shopee;
use Modules\Order\Networks\Tiktok;

/**
 * Class StatisticController
 *
 * @package Modules\Order\Http\Controllers\ApiPublic
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-01-17
 */
class NetworkController extends ApiBaseModuleController {
    /**
     * @var \Modules\Order\Repositories\OrderRepository;
     */
    protected $order_repository;
    /**
     * @var \Modules\User\Repositories\UserRepository;
     */
    protected $user_repository;

    public function __construct(Request $request) {
        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/auth/tiktok",
     *   summary="Get Auth Tiktok",
     *   operationId="getAuthTiktok",
     *   tags={"Networks"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function tiktok() {
        try {
            $allInput = $this->request->all();
            if ($allInput['code']) {
                $tiktok = new Tiktok($allInput['code']);
            }
            if ($tiktok) {
                return response('Thành công');
            } else {
                return response('Thất bại');
            }
        } catch (\Exception $e) {
            return response('responsecode=0');
        }
    }

    /**
     * @OA\Get(
     *   path="/auth/shopee",
     *   summary="Get Auth Shopee",
     *   operationId="getAuthShopee",
     *   tags={"Networks"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function shopee() {
        try {
            $allInput = $this->request->all();
            if ($allInput['code']) {
                $shopee = new Shopee($allInput['shop_id'], $allInput['code']);
            }
            return $this->respondWithError($shopee);

        } catch (\Exception $e) {
            return response('responsecode=0');
        }
    }

    /**
     * @OA\Get(
     *   path="/auth/lazada",
     *   summary="Get Auth Lazada",
     *   operationId="getAuthLazada",
     *   tags={"Networks"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function lazada() {
        try {
            $allInput = $this->request->all();
            if ($allInput['code']) {
                $lazada = new Lazada($allInput['code']);
            }
            return $this->respondWithError($lazada);

        } catch (\Exception $e) {
            return response('responsecode=0');
        }
    }
}
