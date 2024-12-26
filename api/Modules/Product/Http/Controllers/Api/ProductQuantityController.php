<?php namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductQuantityRepository;

/**
 * Class ProductQuantityController
 *
 * @package Modules\Product\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2022-01-28
 */
class ProductQuantityController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    public function __construct(Request $request,
                                ProductRepository $product_repository,
                                ProductQuantityRepository $product_quantity_repository) {
        $this->model_repository = $product_quantity_repository;
        $this->product_repository = $product_repository;

        $this->middleware('auth.usr')->except(['index', 'show']);

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreate() {
        return [
            'quantity' => 'required|min:1',
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
            'quantity' => 'required|min:1',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_product_quantities",
     *   summary="Get Product Quantities",
     *   operationId="getProductQuantities",
     *   tags={"BackendPdQuantities"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example="%7B%22product_id%22%3A0%7D"),
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
            $data = $this->getRequestData();
            $product_id = !empty($data->{'product_id'}) ? (int)$data->{'product_id'} : 0;
            $queries = [
                'and' => [
                    ['product_id', '=', $product_id],
                ],
            ];
            $type = !empty($data->{'type'}) ? (string)$data->{'type'} : '';
            if ($type && in_array($type, ['in', 'out'])) {
                $queries['and'][] = ['type', '=', $type];
            }
            $results = $this->setUpQueryBuilder($this->model(), $queries, false)->orderBy('id', 'desc')->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_product_quantities/{id}",
     *   summary="Get Product Quantity",
     *   operationId="getProductQuantities",
     *   tags={"BackendPdQuantities"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Quantity Id", example="1"),
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

    /**
     * @OA\Post(
     *   path="/backend/pd_product_quantities",
     *   summary="Create Product Quantity",
     *   operationId="createProductQuantities",
     *   tags={"BackendPdQuantities"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="product_id", type="integer", example=1),
     *       @OA\Property(property="quantity", type="integer", example=0),
     *       @OA\Property(property="note", type="string", example=""),
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
            $input = $this->request->only(['product_id', 'quantity', 'note']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $input['type'] = 'in';
            // Create model
            $model = $this->model_repository->create($input);
            // Update quantity
            $product = $model->product;
            $product->quantity = $product->quantity + $model->quantity;
            $product->save();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/pd_product_quantities/{id}",
     *   summary="Update Product Quantity",
     *   operationId="updateProductQuantity",
     *   tags={"BackendPdQuantities"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Quantity Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="quantity", type="integer", example=0),
     *       @OA\Property(property="note", type="string", example=""),
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
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['quantity', 'note']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $quantity = (int)$this->request->get('quantity');
            // Update Model
            if ($model->type == 'in') {
                if ($quantity != $model->quantity) {
                    $product = $model->product;
                    $product->quantity = $product->quantity - $model->quantity + $quantity;
                    $product->save();
                }
                $model = $this->model_repository->update($model, $input);
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/pd_product_quantities/{id}",
     *   summary="Delete Product Quantity",
     *   operationId="deleteProductQuantity",
     *   tags={"BackendPdQuantities"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Quantity Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            if ($model->type == 'in') {
                $product = $model->product;
                $product->quantity = $product->quantity - $model->quantity;
                $product->save();
                $this->model_repository->destroy($model);
            }

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
