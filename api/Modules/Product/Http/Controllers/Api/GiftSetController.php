<?php namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\GiftSetProductRepository;
use Modules\Product\Repositories\GiftSetRepository;

/**
 * Class GiftSetController
 *
 * @package Modules\Product\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-06-12
 */
class GiftSetController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\GiftSetProductRepository
     */
    protected $gift_set_product_repository;

    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    public function __construct(Request $request, GiftSetRepository $gift_set_repository, GiftSetProductRepository $gift_set_product_repository, ProductRepository $product_repository) {
        $this->model_repository = $gift_set_repository;
        $this->gift_set_product_repository = $gift_set_product_repository;
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
        return [];
    }

    /**
     * Get the validation rules for update.
     *
     * @param int $id
     * @return array
     */
    protected function rulesForUpdate($id) {
        return [];
    }

    /**
     * @OA\Get(
     *   path="/backend/gift_sets_all",
     *   summary="Get Gift Sets All",
     *   operationId="pdGetGiftSetsAll",
     *   tags={"BackendPdGiftSets"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function all() {
        try {
            $results = $this->model_repository->getModel()->orderBy('name', 'asc')->get();
            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/gift_sets",
     *   summary="Get Gift Sets",
     *   operationId="pdGetGiftSets",
     *   tags={"BackendPdGiftSets"},
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
            // Check permission
            if (!$this->isCRUD('products', 'view')) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'id' : strtolower($sort);
            $order = (string)$this->request->get('order');
            $order = !$order ? 'desc' : strtoupper($order);
            $queries = ['and' => [], 'in' => [], 'whereRaw' => [], 'orWhereRaw' => []];
            $data = $this->getRequestData();
            $start_date = (isset($data->{'start_date'}) && !is_null($data->{'start_date'}) && $data->{'start_date'} !== '') ? (string)$data->{'start_date'} : false;
            $end_date = (isset($data->{'end_date'}) && !is_null($data->{'end_date'}) && $data->{'end_date'} !== '') ? (string)$data->{'end_date'} : false;
            if ($start_date && $end_date) $queries['whereRaw'][] = ["(? <= DATE(`end_date`) and DATE(`end_date`) <= ?)", [$start_date, $end_date]];
            // Query by keyword
            /*$q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(utf8_strtolower($q));
                $keys = ['`name`'];
                foreach ($keys as $key) {
                    $iQ = [];
                    $iB = [];
                    foreach ($arrQ as $i) {
                        $iQ[] = "lower($key) like ?";
                        $iB[] = "%$i%";
                    }
                    $queries['orWhereRaw'][] = ['(' . implode(' and ', $iQ) . ')', $iB];
                }
            }*/
            $q = trim(utf8_strtolower((isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : ''));
            if ($q) $queries['whereRaw'][] = ["lower(`name`) like ?", "%$q%"];
            $fields = ['*'];
            $results = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false, $fields)->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'true' ? true : ($paging == 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($results);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, true)->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/gift_sets/{id}",
     *   summary="Get a GiftSet",
     *   operationId="pdGetGiftSet",
     *   tags={"BackendPdGiftSets"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Gift Set Id", example=1),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
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

    protected function getRequestProducts() {
        $products = [];
        $tmpProducts = $this->request->get('products');
        if (is_string($tmpProducts)) $tmpProducts = json_decode($tmpProducts, true);
        if (!empty($tmpProducts)) foreach ($tmpProducts as $item) {
            $validatorErrors = $this->getValidator($item, [
                'id'       => 'required|integer',
                'name'     => 'required',
                'price'    => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:1',
            ]);
            if (empty($validatorErrors)) {
                $product = intval($item['id']) ? $this->product_repository->findByAttributes(['id' => $item['id']]) : null;
                if (!$product) {
                    $product = new \stdClass();
                    $product->id = null;
                }
                foreach (['name', 'price', 'quantity'] as $fieldName) {
                    $product->{$fieldName} = !empty($item[$fieldName]) ? $item[$fieldName] : '';
                }
                $products[] = $product;
            }
        }
        return $products;
    }

    protected function createProducts($products, $model) {
        $total = 0;
        foreach ($products as $product) {
            $sub = (float)$product->price * (int)$product->quantity;
            $this->gift_set_product_repository->create([
                'gift_set_id' => $model->id,
                'product_id'  => $product->id,
                'name'        => $product->name,
                'price'       => $product->price,
                'quantity'    => $product->quantity,
                'total'       => $sub,
            ]);
            $total += $sub;
        }
        return [$total];
    }

    /**
     * @OA\Post(
     *   path="/backend/gift_sets",
     *   summary="Create Gift Set",
     *   operationId="pdCreateGiftSet",
     *   tags={"BackendPdGiftSets"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
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
    public function store() {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'create')) return $this->errorForbidden();
            $input = $this->request->only(['name', 'start_date', 'end_date', 'total', 'description', 'status']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Products
            $products = $this->getRequestProducts();
            if (!$products) return $this->respondWithErrorKey('product_id.required');
            // Create model
            $model = $this->model_repository->create($input);
            // Create products
            list($total) = $this->createProducts($products, $model);
            $input = ['total' => $total];
            // Update model
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/gift_sets/{id}",
     *   summary="Update Gift Set",
     *   operationId="pdUpdateGiftSet",
     *   tags={"BackendPdGiftSets"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Gift Set Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
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
            $input = $this->request->only(['name', 'start_date', 'end_date', 'total', 'description', 'status']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            if (empty($input['start_date'])) $input['start_date'] = null;
            if (empty($input['end_date'])) $input['end_date'] = null;
            // Products
            $products = $this->getRequestProducts();
            if (!$products) return $this->respondWithErrorKey('product_id.required');
            // Create products
            $this->gift_set_product_repository->getModel()->where('gift_set_id', $model->id)->delete();
            list($total) = $this->createProducts($products, $model);
            // Update model
            $model = $this->model_repository->update($model, array_merge($input, ['total' => $total]));
            // Show detail
            $model->products;
            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *   path="/backend/gift_sets/{id}",
     *   summary="Update Gift Set Partial",
     *   operationId="pdUpdateGiftSetPartial",
     *   tags={"BackendPdGiftSets"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Gift Set Id", example=1),
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
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['status']);
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/gift_sets/{id}",
     *   summary="Delete Gift Set",
     *   operationId="pdDeleteGiftSet",
     *   tags={"BackendPdGiftSets"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Gift Set Id", example=1),
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
            // Delete product
            $this->product_repository->getModel()->where('gift_set_id', $model->id)->update(['gift_set_id' => null]);
            // Delete model
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
