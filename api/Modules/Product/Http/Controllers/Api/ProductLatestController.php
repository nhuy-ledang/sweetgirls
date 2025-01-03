<?php namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Product\Repositories\ProductLatestRepository;
use Modules\Product\Repositories\ProductRepository;

/**
 * Class ProductLatestController
 *
 * @package Modules\Product\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2022-06-08
 */
class ProductLatestController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    public function __construct(Request $request, ProductLatestRepository $product_latest_repository, ProductRepository $product_repository) {
        $this->model_repository = $product_latest_repository;
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
            'product_id' => 'required|integer|exists:pd__products,id',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_product_latest",
     *   summary="Get Product Latest",
     *   operationId="pdGetProductLatest",
     *   tags={"BackendPdProductLatest"},
     *   security={{"bearer":{}}},
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
            // Check permission
            if (!$this->isCRUD('products', 'view')) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'id' : strtolower($sort);
            $order = (string)$this->request->get('order');
            $order = !$order ? 'asc' : strtolower($order);
            $queries = ['and' => [], 'orWhereRaw' => []];
            $data = $this->getRequestData();
            // Query by keyword
            /*$q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(utf8_strtolower($q));
                $keys = ['name', 'description'];
                foreach ($keys as $key) {
                    $iQ = [];
                    $iB = [];
                    foreach ($arrQ as $i) {
                        $iQ[] = "lower(`$key`) like ?";
                        $iB[] = "%$i%";
                    }
                    $queries['orWhereRaw'][] = ['(' . implode(' and ', $iQ) . ')', $iB];
                }
            }*/
            $q = trim(utf8_strtolower((isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : ''));
            if ($q) $queries['whereRaw'][] = ["lower(`name`) like ?", "%$q%"];

            $fields = ['pd__products.*', 'lat.id as id', 'lat.product_id as product_id', 'lat.created_at as created_at', 'lat.updated_at as updated_at'];
            $results = $this->setUpQueryBuilder($this->product_repository->getModel(), $queries, false, $fields)
                ->rightJoin('pd__product_latest as lat', 'product_id', '=', 'pd__products.id')
                ->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'false' ? false : ($paging == 'true' ? true : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($results);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->product_repository->getModel(), $queries, true)
                    ->rightJoin('pd__product_latest as lat', 'product_id', '=', 'pd__products.id')->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pd_product_latest",
     *   summary="Create Product Latest",
     *   operationId="pdCreateProductLatest",
     *   tags={"BackendPdProductLatest"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="product_id", type="integer", example=1),
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
            $model = $this->model_repository->findByAttributes(['product_id' => $input['product_id']]);
            if (!$model) $model = $this->model_repository->create($input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/pd_product_latest/{id}",
     *   summary="Delete Product Latest",
     *   operationId="pdDeleteProductLatest",
     *   tags={"BackendPdProductLatest"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Latest Id", example=1),
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
            // Delete model
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
