<?php namespace Modules\Product\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductReviewImageRepository;
use Modules\Product\Repositories\ProductReviewRepository;
use Modules\User\Repositories\UserCoinRepository;
use Modules\User\Repositories\UserRepository;

/**
 * Class ProductReviewController
 * @package Modules\Product\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-05-30
 */
class ProductReviewController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    protected $file_repository;

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    /**
     * @var \Modules\Product\Repositories\ProductReviewImageRepository
     */
    protected $image_repository;

    /**
     * @var \Modules\User\Repositories\UserRepository
     */
    protected $user_repository;

    /**
     * @var \Modules\User\Repositories\UserCoinRepository
     */
    protected $user_coin_repository;


    public function __construct(Request $request,
                                FileRepository $file_repository,
                                FileService $fileService,
                                ProductReviewRepository $product_review_repository,
                                ProductReviewImageRepository $product_review_image_repository,
                                ProductRepository $product_repository,
                                UserRepository $user_repository,
                                UserCoinRepository $user_coin_repository) {
        $this->model_repository = $product_review_repository;
        $this->image_repository = $product_review_image_repository;
        $this->product_repository = $product_repository;
        $this->file_repository = $file_repository;
        $this->fileService = $fileService;
        $this->user_repository = $user_repository;
        $this->user_coin_repository = $user_coin_repository;

        $this->middleware('auth.user')->except('all');

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     * @return array
     */
    protected function rulesForCreate() {
        return [
            'rating'     => 'required|in:1,2,3,4,5',
            'review'     => 'required',
        ];
    }

    /**
     * Get the validation rules for create.
     * @return array
     */
    protected function rulesForSearch() {
        return [
            'type'     => 'in:1,2',
        ];
    }

    /**
     * @OA\Get(
     *   path="/pd_review_all",
     *   summary="Get Reviews All",
     *   operationId="pdGetReviewsAll",
     *   tags={"Products"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="filter", in="query", description="Filter", example="1"),
     *   @OA\Parameter(name="type", in="query", description="Type", example="1"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function all() {
        try {
            $user_id = (int)$this->request->get('user_id');
            $filter = (int)$this->request->get('filter');
            $type = (int)$this->request->get('type');
            // Check Valid
            if ($type) {
                $validatorErrors = $this->getValidator([$type], $this->rulesForSearch());
                if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            }

            $user = $this->isLogged();
            $results = $this->model_repository->getModel()
                ->with(['user', 'product', 'images']);

            if ($user) {
                $results = $results->withCount(['likes as liked' => function($query) use ($user) {
                    $query->where('user_id', $user->id);
                }]);
            }
            $results = $results
                ->withCount('likes as like_total')
                ->withCount('comments as comment_total');

            // Filter: comment
            if ($filter == 1) $results = $results->orderBy('comment_total', 'desc');
            // Filter: like
            if ($filter == 3) $results = $results->orderBy('like_total', 'desc');

            // Type: unbox
            if ($type == 1) $results = $results->whereNotNull('link');
            // Type: from us
            if ($type == 2) $results = $results->whereNull(['user_id', 'product_id']);

            if ($user_id) $results = $results->where('user_id', $user_id);

            $results = $results->where('status', 1)->orderBy('updated_at', 'desc')->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/products/{id}/reviews",
     *   summary="Get Product Reviews",
     *   operationId="getProductReviews",
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
    public function getReviews($id) {
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
                    ['product_id', '=', $id],
                ],
                'whereRaw' => [],
            ];
            $results = $this->setUpQueryBuilder($this->model(), $queries)->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
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
     * @OA\Put(
     *   path="/products/{id}/reviews",
     *   summary="Add Product Review",
     *   operationId="addProductReview",
     *   tags={"Products"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
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
    public function createReview($id) {
        try {
            $input = $this->request->only('rating', 'review');

            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            list($files, $errorKeys) = $this->getRequestFiles();
            if (count($files) > 10) return $this->errorWrongArgs('attach.file_max');
            if ($errorKeys) return $this->errorWrongArgs($errorKeys[0]);
            // Upload file
            $savedFiles = [];
            foreach ($files as $file) {
                $savedFile = $this->fileService->store($file, ['sub' => 'at', 'user_id' => $this->auth->id]);
                if (!is_string($savedFile)) $savedFiles[] = $savedFile;
            }
            // Check product exist
            $product = $this->product_repository->find($id);
            if (!$product) return $this->errorNotFound();
            // Create model
            $model = $this->model_repository->create(array_merge($input, ['product_id' => $id, 'user_id' => $this->auth->id]));
            // Update File
            foreach ($savedFiles as $key => $savedFile) {
                $this->image_repository->create(['image' => $savedFile->path, 'review_id' => $model->id]);
            }
            // Update File
            foreach ($savedFiles as $savedFile) {
                $savedFile->object_id = $model->id;
                $savedFile->save();
            }
            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/pd_review_link",
     *   summary="Add Review Link",
     *   operationId="addReviewLink",
     *   tags={"Products"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="link", type="string", example=""),
     *       @OA\Property(property="status", type="integer", example=1),
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
    public function createLink() {
        try {
            $input = $this->request->only('link', 'status');

            // Create model
            $model = $this->model_repository->create(array_merge($input, ['user_id' => $this->auth->id]));
            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/products/{id}/share",
     *   summary="Add Product Share",
     *   operationId="addProductShare",
     *   tags={"Products"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function createShareAction($id) {
        try {
            $product = $this->product_repository->find($id);
            if (!$product) return $this->errorNotFound();

            $user = $this->auth();
            $this->user_repository->update($user, ['coins' => $user->coins + 100, 'points' => $user->points + 100]);
            $model = $this->user_coin_repository->create(['user_id' => $user->id, 'type' => 'share', 'obj_id' => $id, 'coins' => 100, 'total' => $user->coins]);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
