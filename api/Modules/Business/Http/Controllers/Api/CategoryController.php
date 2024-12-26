<?php namespace Modules\Business\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Business\Repositories\CategoryRepository;
use Modules\Business\Repositories\ProductRepository;

/**
 * Class CategoryController
 *
 * @package Modules\Business\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2021-08-16
 */
class CategoryController extends ApiBaseModuleController {
    protected $maximumLimit = 100;

    /**
     * @var \Modules\Business\Repositories\ProductRepository
     */
    protected $product_repository;

    public function __construct(Request $request,
                                CategoryRepository $category_repository, ProductRepository $product_repository) {
        $this->model_repository = $category_repository;
        $this->product_repository = $product_repository;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreate() {
        return [
            'name' => 'required',
        ];
    }

    /**
     * Get the validation rules for update.
     *
     * @param int $id
     * @return array
     */
    protected function rulesForUpdate($id) {
        return [
            'name' => 'required',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/bus_categories_all",
     *   summary="Get Category All",
     *   operationId="getCategoryAll",
     *   tags={"BackendBusCategories"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function all() {
        try {
            $results = $this->model_repository->all();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/bus_categories",
     *   summary="Get Categories",
     *   operationId="getCategories",
     *   tags={"BackendBusCategories"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="name"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="asc"),
     *   @OA\Parameter(name="paging", description="With Paging", in="query", example="0"),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields, extend_fields: avgReview} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function index() {
        try {
            // Check permission
            //if (!($this->isView() || $this->isViewOwn())) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            $pageSize = (int)$this->request->get('pageSize');
            if (!$page) $page = 1;
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $order = (string)$this->request->get('order');
            $sort = !$sort ? 'name' : strtolower($sort);
            $order = !$order ? 'asc' : strtolower($order);
            $queries = ['and' => [], 'orWhereRaw' => []];
            $data = $this->getRequestData();
            // Query by keyword
            $q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(utf8_strtolower($q));
                $keys = ['`name`'];
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
            $fields = ['*'];
            $extend_fields = $this->getRequestFields('extend_fields');
            if (in_array('product_stats', $extend_fields)) {
                $fields[] = \DB::raw('(select count(*) from `bus__products` where `category_id` = `bus__categories`.`id` and `status` = 1 and `deleted_at` is null) as turn_on');
                $fields[] = \DB::raw('(select count(*) from `bus__products` where `category_id` = `bus__categories`.`id` and `status` = 0 and `deleted_at` is null) as turn_off');
            }
            $results = $this->setUpQueryBuilder($this->model(), $queries, false, $fields)->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
            $output = $results;
            if (in_array('product_stats', $extend_fields)) {
                $output = [];
                foreach ($results as $result) {
                    $result->product_total = $result->turn_on + $result->turn_off;
                    $output[] = $result;
                }
            }
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'false' ? false : ($paging == 'true' ? true : (boolean)$paging);
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

    /**
     * @OA\Post(
     *   path="/backend/bus_categories",
     *   summary="Create Category",
     *   operationId="createCategory",
     *   tags={"BackendBusCategories"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Category Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
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
            //if (!$this->isCreate()) return $this->errorForbidden();
            $input = $this->request->all();

            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);

            $input['idx'] = !empty($input['idx']) ? to_idx($input['idx']) : to_idx($input['name']);
            $model = $this->model_repository->create($input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/bus_categories/{id}",
     *   summary="Update Category",
     *   operationId="updateCategory",
     *   tags={"BackendBusCategories"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Category Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function update($id) {
        try {
            // Check permission
            //if (!$this->isUpdate()) return $this->errorForbidden();
            $input = $this->request->all();

            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);

            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();

            // Update Model
            $input['idx'] = !empty($input['idx']) ? to_idx($input['idx']) : to_idx($input['name']);
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/bus_categories/{id}",
     *   summary="Delete Category",
     *   operationId="deleteCategory",
     *   tags={"BackendBusCategories"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Category Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            // Check permission
            //if (!$this->isDelete()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Check product exists
            $product = $this->product_repository->getModel()->where('category_id', $id)->first();
            if ($product) return $this->errorWrongArgs('category_id.product_exists');
            // Delete model
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/bus_categories_sort_order",
     *   summary="Update Category Sort Order",
     *   operationId="updateCategorySortOrder",
     *   tags={"BackendBusCategories"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function sortOrder() {
        try {
            // Check permission
            //if (!$this->isUpdate($this->module_id)) return $this->errorForbidden();
            $order = (array)$this->request->get('order');
            $data = [];
            if ($order) foreach ($order as $id => $sort_order) {
                $item = ['id' => (int)$id, 'sort_order' => (int)$sort_order];
                $this->model_repository->getModel()->where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
                $data[] = $item;
            }

            return $this->respondWithSuccess($order);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/bus_categories_stats",
     *   summary="Get Category Stats",
     *   operationId="busGetCategoryStats",
     *   tags={"BackendBusCategories"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function stats() {
        try {
            $turn_on = $this->product_repository->getModel()->where('status', 1)->count();
            $turn_off = $this->product_repository->getModel()->where('status', 0)->count();
            $total = $turn_on + $turn_off;
            $output = ['turn_on' => $turn_on, 'turn_off' => $turn_off, 'total' => $total];

            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
