<?php namespace Modules\User\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\Order\Repositories\OrderRepository;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductReviewRepository;
use Modules\User\Repositories\UserCoinRepository;
use Modules\User\Repositories\UserRepository;

/**
 * Class CoinController
 *
 * @package Modules\User\Http\Controllers\ApiPublic
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2021-07-09
 */
class CoinController extends ApiBaseModuleController {
    /**
     * @var \Modules\Order\Repositories\OrderRepository
     */
    protected $order_repository;

    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    /**
     * @var \Modules\Product\Repositories\ProductReviewRepository
     */
    protected $review_repository;

    /**
     * @var \Modules\User\Repositories\UserRepository
     */
    protected $user_repository;

    public function __construct(Request $request,
                                UserCoinRepository $user_coin_repository,
                                OrderRepository $order_repository,
                                ProductRepository $product_repository,
                                ProductReviewRepository $review_repository,
                                UserRepository $user_repository) {
        $this->model_repository = $user_coin_repository;
        $this->order_repository = $order_repository;
        $this->product_repository = $product_repository;
        $this->review_repository = $review_repository;
        $this->user_repository = $user_repository;

        $this->middleware('auth.user');

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/user_coins",
     *   summary="Get User Coins",
     *   operationId="getUserCoins",
     *   tags={"UserCoins"},
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
                    ['user__coins.user_id', '=', $this->auth->id]
                ],
                'in'         => [],
                'whereRaw'   => [],
                'orWhereRaw' => [],
            ];

            $fields = [
                'user__coins.*',
            ];

            $results = $this->setUpQueryBuilder($this->model(), $queries, false, $fields)
                ->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
            $orderIds = [];
            $productIds = [];
            $reviewIds = [];
            $shareIds = [];
            foreach ($results as $item) {
                if ($item->type == 'order') {
                    $orderIds[] = $item->obj_id;
                } else if ($item->type == 'product') {
                    $productIds[] = $item->obj_id;
                } else if ($item->type == 'review') {
                    $reviewIds[] = $item->obj_id;
                } else if ($item->type == 'share') {
                    $shareIds[] = $item->obj_id;
                }
            }
            // Get orders
            $orderObj = [];
            if($orderIds) {
                $tmp = $this->order_repository->getModel()->whereIn('id', $orderIds)->with('products')->get();
                foreach ($tmp as $t) $orderObj[$t->id] = $t;
            }
            // Get review products
            $productObj = [];
            if($productIds) {
                $tmp = $this->review_repository->getModel()->whereIn('product_id', $productIds)->whereNull('link')->get();
                foreach ($tmp as $t) $productObj[$t->product_id] = $t;
            }
            // Get unbox products
            $reviewObj = [];
            if ($reviewIds) {
                $tmp = $this->review_repository->getModel()->whereIn('product_id', $reviewIds)->where('rating', 0)->whereNull('review')->get();
                foreach ($tmp as $t) $reviewObj[$t->product_id] = $t;
            }
            // Get share
            $shareObj = [];
            if ($shareIds) {
                $tmp = $this->product_repository->getModel()->whereIn('id', $shareIds)->get();
                foreach ($tmp as $t) $shareObj[$t->id] = $t;
            }

            $output = [];
            foreach ($results as $item) {
                if ($item->type == 'order') {
                    if(isset($orderObj[$item->obj_id])) $item->order = $orderObj[$item->obj_id];
                } else if ($item->type == 'product') {
                    if(isset($productObj[$item->obj_id])) $item->review = $productObj[$item->obj_id];
                } else if ($item->type == 'review') {
                    if(isset($reviewObj[$item->obj_id])) $item->review = $reviewObj[$item->obj_id];
                }  else if ($item->type == 'share') {
                    if(isset($shareObj[$item->obj_id])) $item->product = $shareObj[$item->obj_id];
                } else {
                     continue;
                }
                $output[] = $item;
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
