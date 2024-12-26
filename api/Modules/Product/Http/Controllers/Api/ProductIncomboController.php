<?php namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductIncomboRepository;

/**
 * Class ProductIncomboController
 *
 * @package Modules\Product\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2022-06-03
 */
class ProductIncomboController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    public function __construct(Request $request,
                                ProductRepository $product_repository,
                                ProductIncomboRepository $product_incombo_repository) {
        $this->model_repository = $product_incombo_repository;
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
            'product_id' => 'required',
            'sort_order' => 'integer',
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
            'sort_order'  => 'integer',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_product_incombo",
     *   summary="Get Product Incombo",
     *   operationId="getProductIncombo",
     *   tags={"BackendPdIncombo"},
     *   security={{"bearer":{}}},
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
            $data = $this->getRequestData();
            $product_id = isset($data->{'product_id'}) ? (int)$data->{'product_id'} : 0;
            $queries = [
                'and' => [
                    ['product_id', '=', $product_id],
                ],
            ];
            $fields = ['pd__product_incombo.*', 'pr.name as name'];
            $results = $this->setUpQueryBuilder($this->model(), $queries, false, $fields)
                ->leftJoin('pd__products as pr', 'pr.id', '=', 'incombo_id')
                ->orderBy('pd__product_incombo.sort_order', 'asc')
                ->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pd_product_incombo",
     *   summary="Create Product Incombo",
     *   tags={"BackendPdIncombo"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="product_id", type="integer", example=1),
     *       @OA\Property(property="incombo_id", type="integer", example=1),
     *       @OA\Property(property="value", type="string", example=""),
     *       @OA\Property(property="sort_order", type="integer", example=1),
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
            if (is_array($input['products']) && !empty($input['products'])) {
                $this->model_repository->getModel()->where('product_id', $input['product_id'])->delete();
                foreach ($input['products'] as $value) {
                    $value['incombo_id'] = $value['id'];
                    $model = $this->model_repository->create(array_merge($value, ['product_id' => $input['product_id']]));
                }
            } else if (empty($input['products'])) {
                $model = $this->model_repository->getModel()->where('product_id', $input['product_id'])->delete();
            } else {
                $model = $this->model_repository->create($input);
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/pd_product_incombo/{id}",
     *   summary="Update Product Incombo",
     *   operationId="updateProductIncombo",
     *   tags={"BackendPdIncombo"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Incombo Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="product_id", type="integer", example=1),
     *       @OA\Property(property="incombo_id", type="integer", example=1),
     *       @OA\Property(property="value", type="string", example=""),
     *       @OA\Property(property="sort_order", type="integer", example=1),
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
            $input = $this->request->only(['incombo_id', 'value', 'image', 'sort_order']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Check exist
            if ($input['incombo_id'] != $model->incombo_id) {
                $check = $this->model_repository->findByAttributes(['product_id' => $model->product_id, 'incombo_id' => $input['incombo_id']]);
                if ($check) return $this->errorWrongArgs('incombo_id.exists');
            }
            // Update Model
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/pd_product_incombo/{id}",
     *   summary="Delete Product Incombo",
     *   operationId="deleteProductIncombo",
     *   tags={"BackendPdIncombo"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Incombo Id", example="1"),
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
}
