<?php namespace Modules\User\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductReviewRepository;

/**
 * Class ReviewController
 * package Modules\User\Http\Controllers\ApiPublic
 */
class ReviewController extends ApiBaseModuleController {
    /**
     * @var \Modules\User\Repositories\UserRepository
     */
    protected $user_repository;

    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    public function __construct(Request $request,
                                ProductReviewRepository $review_repository,
                                ProductRepository $product_repository) {
        $this->model_repository = $review_repository;
        $this->product_repository = $product_repository;

        $this->middleware('auth.user');

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/user_reviews",
     *   summary="Get User Reviews",
     *   operationId="getUserReviews",
     *   tags={"UserReviews"},
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
            /*$pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;*/
            $pageSize = 100;
            $sort = 'orders.id';
            $order = 'desc';

            $queries = [
                'and' => [],
                'whereRaw' => [],
            ];
            $output = [];
            $fields = ['pd__products.*'];
            $results = $this->product_repository->getModel()->rightJoin('order__products as op', 'op.product_id', '=', 'pd__products.id')->rightJoin('orders as orders', 'op.order_id', '=', 'orders.id')
                ->where('orders.user_id', $this->auth->id)
                ->where('orders.order_status', ORDER_SS_COMPLETED)
                ->select($fields)->distinct()
                ->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get()
                ->makeHidden(['manufacturer_id', 'category_id', 'categories', 'meta_title', 'meta_description', 'meta_keyword', 'description', 'image', 'translates', 'alias', 'status']);
            $item['review'] = [];
            foreach ($results as $item) {
                $item['review'] = \DB::table('pd__product_reviews')
                    ->where('pd__product_reviews.product_id', $item->id)
                    ->where('pd__product_reviews.user_id', $this->auth->id)
                    ->first();
                $output[] = $item;
            }

            return $this->respondWithSuccess($output);
            /*$paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'false' ? false : ($paging == 'true' ? true : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($results);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }*/
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
