<?php namespace Modules\Product\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\Product\Repositories\ProductReviewLikeRepository;
use Modules\Product\Repositories\ProductReviewRepository;

/**
 * Class ProductReviewLikeController
 * @package Modules\Product\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-05-30
 */
class ProductReviewLikeController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\ProductReviewRepository
     */
    protected $product_review_repository;

    public function __construct(Request $request,
                                ProductReviewRepository $product_review_repository,
                                ProductReviewLikeRepository $product_review_interact_repository) {
        $this->product_review_repository = $product_review_repository;
        $this->model_repository = $product_review_interact_repository;

        $this->middleware('auth.user');

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     * @return array
     */
    protected function rulesForCreate() {
        return [];
    }

    /**
     * @OA\Get(
     *   path="/reviews/{id}/likes",
     *   summary="Get Product Review Likes",
     *   operationId="getProductReviewLikes",
     *   tags={"Products"},
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
    public function getLikes($id) {
        try {
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            /*$pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;*/
            $pageSize = 100;
            $sort = 'id';
            $order = 'desc';
            $queries = [
                'and'      => [
                    ['review_id', '=', $id],
                ],
                'whereRaw' => [],
            ];
            $results = $this->setUpQueryBuilder($this->model(), $queries)->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))
                ->with('user')
                ->get();
            return $this->respondWithSuccess($results);
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

    /**
     * @OA\Post(
     *   path="/products/{id}/reviews",
     *   summary="Add Product Review Like",
     *   operationId="addProductReviewLikes",
     *   tags={"Products"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="product_id", type="integer", example=1),
     *       @OA\Property(property="rating", type="integer", example=1),
     *       @OA\Property(property="review", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function createLike($id) {
        try {
            $input = $this->request->only('like');

            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);


            // Check review exist
            $review = $this->product_review_repository->find($id);
            if (!$review) return $this->errorNotFound();

            $model = $this->model_repository->getModel()->where(['review_id' => $id, 'user_id' => $this->auth->id])->first();
            if ($model) {
                $model = $this->model_repository->update($model, $input);
            } else {
                // Create model
                $model = $this->model_repository->create(array_merge($input, ['review_id' => $id, 'user_id' => $this->auth->id]));
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
