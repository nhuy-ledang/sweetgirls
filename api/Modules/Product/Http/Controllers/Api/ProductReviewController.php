<?php namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Product\Repositories\ProductReviewRepository;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\User\Repositories\UserCoinRepository;
use Modules\User\Repositories\UserRepository;

/**
 * Class ProductReviewController
 *
 * @package Modules\Product\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2021/06/03
 */
class ProductReviewController extends ApiBaseModuleController {
    /**
     * @var \Modules\User\Repositories\UserCoinRepository
     */
    protected $user_coin_repository;

    /**
     * @var \Modules\User\Repositories\UserRepository
     */
    protected $user_repository;

    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    protected $file_repository;

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    public function __construct(Request $request,
                                ProductReviewRepository $review_repository,
                                UserCoinRepository $user_coin_repository,
                                UserRepository $user_repository,
                                FileRepository $file_repository,
                                FileService $fileService,) {
        $this->model_repository = $review_repository;
        $this->user_coin_repository = $user_coin_repository;
        $this->user_repository = $user_repository;
        $this->file_repository = $file_repository;
        $this->fileService = $fileService;
        $this->voucher_repository = $voucher_repository;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_reviews",
     *   summary="Get Reviews",
     *   operationId="getReviews",
     *   tags={"BackendPdProductReviews"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="page", in="query", required=false, description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", required=false, description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", required=false, description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", required=false, description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", required=false, description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
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
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'id' : strtolower($sort);
            $order = (string)$this->request->get('order');
            $order = !$order ? 'desc' : strtolower($order);
            $queries = [
                'whereRaw'   => [],
                'orWhereRaw' => [],
            ];
            $data = $this->getRequestData();
            // Query by keyword
            $q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(utf8_strtolower($q));
                $keys = ['title', 'description'];
                foreach ($keys as $key) {
                    $iQ = [];
                    $iB = [];
                    foreach ($arrQ as $i) {
                        $iQ[] = "lower(`$key`) like ?";
                        $iB[] = "%$i%";
                    }
                    $queries['orWhereRaw'][] = ['(' . implode(' and ', $iQ) . ')', $iB];
                }
            }
            if (!$this->isCms()) {
                $visible = [];
            } else {
                $visible = [];
            }
            $results = $this->setUpQueryBuilder($this->model(), $queries)->orderBy($sort, $order)
                ->with(['user', 'product'])
                ->take($pageSize)
                ->skip($pageSize * ($page - 1))
                ->get();//->makeVisible($visible);
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'false' ? false : ($paging == 'true' ? true : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($results);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true);
                $totalCount = $totalCount->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_reviews/{id}",
     *   summary="Get a Review",
     *   operationId="getReview",
     *   tags={"BackendPdProductReviews"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, description="Review Id", example="1"),
     *   @OA\Parameter(name="data", in="query", required=false, description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function show($id) {
        try {
            $model = $this->setUpQueryBuilder($this->model(), [], false)->where('id', $id)->first();
            if (!$model) return $this->errorNotFound();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pd_reviews",
     *   summary="Create Review",
     *   tags={"BackendPdProductReviews"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="rating", type="integer", example=""),
     *       @OA\Property(property="review", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function store() {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'create')) return $this->errorForbidden();
            $input = $this->request->all();
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);

            // Create model
            $model = $this->model_repository->create($input);
            // Upload image
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $model = $this->model_repository->update($model, ['image' => $file_path]);
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                //if ($errorKey) return $this->errorWrongArgs($errorKey);
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => $this->module_name, 'object_id' => $model->id]);
                    if (!is_string($savedFile)) $model = $this->model_repository->update($model, ['image' => $savedFile->path]);
                }
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/pd_reviews/{id}",
     *   summary="Update Review",
     *   operationId="updateReview",
     *   tags={"BackendPdProductReviews"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Review Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="rating", type="integer", example=""),
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
    public function update($id) {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'edit')) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->all();
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Upload image
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $input['image'] = $file_path;
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                if ($errorKey) return $this->errorWrongArgs($errorKey);
                if ($file) {
                    // Unlink old image
                    $oldFile = null;
                    if ($model->image) $oldFile = $this->file_repository->findByAttributes(['object' => $this->module_name, 'path' => $model->image]);
                    // New image
                    $savedFile = $this->fileService->store($file, ['sub' => $this->module_name, 'object_id' => $model->id]);
                    if (!is_string($savedFile)) {
                        $input['image'] = $savedFile->path;
                        // Unlink old image
                        //if ($oldFile) $this->file_repository->destroy($oldFile);
                    }
                }
            }
            // Update Model
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *   path="/backend/pd_reviews/{id}",
     *   summary="Update Review Partial",
     *   operationId="UpdateReviewPartial",
     *   tags={"BackendPdProductReviews"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Review Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
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
    public function patch($id) {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'edit')) return $this->errorForbidden();
            $tz = (int)$this->request->get('tz');
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['status']);
            $coins = 0;
            $type = '';
            if (intval($input['status']) == 1) { // approved
                $input['approved_at'] = $this->getDateLocalFromTz($tz);
                if ($model->rating == 0 && $model->link) {
                    $coins = 150;
                    $type = 'review';
                    $code = $this->createVoucherCode($model->user_id);
                    $this->voucher_repository->create(['source' => 'review', 'obj_id' => $id, 'user_id' => $model->user_id, 'code' => $code, 'amount' => 50000, 'quantity' => 1]);
                } else if ($model->rating > 0) {
                    $coins = 100;
                    $type = 'product';
                }
            } else { // revert
                $input['approved_at'] = null;
                if ($model->rating == 0 && $model->link) {
                    $coins = -150;
                    $type = 'review';
                    $this->voucher_repository->getModel()->where('source', 'review')->where('obj_id', $id)->where('user_id', $model->user_id)->delete();
                } else if ($model->rating > 0) {
                    $coins = -100;
                    $type = 'product';
                }
            }
            if ($coins && $type) {
                $user = $this->user_repository->find($model->user_id);
                $this->user_repository->update($user, ['coins' => $user->coins + $coins, 'points' => $user->points + $coins]);
                $this->user_coin_repository->create(['user_id' => $model->user_id, 'type' => $type, 'obj_id' => $model->product_id, 'coins' => $coins, 'total' => $user->coins]);
            }

            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/pd_reviews/{id}",
     *   summary="Delete Review",
     *   operationId="deleteReview",
     *   tags={"BackendPdProductReviews"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Review Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'delete')) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $this->model_repository->destroy($model);
            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * Get code
     *
     * @param $user_id
     * @return string
     */
    private function createVoucherCode($user_id) {
        $code = strtoupper(str_random_alpha_numeric(1, false, false) . str_random_not_cap(9));
        $find = $this->voucher_repository->getModel()->where('user_id', $user_id)->where('code', $code)->first();
        if (!$find) {
            return $code;
        } else {
            return $this->createVoucherCode($user_id);
        }
    }
}
