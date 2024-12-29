<?php namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Product\Repositories\FlashsaleRepository;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductSpecialRepository;

/**
 * Class ProductSpecialController
 *
 * @package Modules\Product\Http\Controllers\Api
 */
class ProductSpecialController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;
    /**
     * @var \Modules\Product\Repositories\FlashsaleRepository
     */
    protected $flashsale_repository;

    public function __construct(Request $request,
                                ProductRepository $product_repository,
                                ProductSpecialRepository $product_special_repository,
                                FlashsaleRepository $flashsale_repository) {
        $this->model_repository = $product_special_repository;
        $this->product_repository = $product_repository;
        $this->flashsale_repository = $flashsale_repository;

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
     *   path="/backend/pd_products/{id}/specials",
     *   summary="Get Product Specials",
     *   operationId="getProductSpecials",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Id", example="1"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function indexSpecial($id) {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'view')) return $this->errorForbidden();
            $queries = [
                'and' => [
                    ['product_id', '=', $id],
                ],
            ];
            $results = $this->setUpQueryBuilder($this->model(), $queries, false)->orderBy('priority', 'asc')->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pd_products/{id}/specials",
     *   summary="Create Product Special",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="price", type="string", example=""),
     *       @OA\Property(property="priority", type="integer", example=1),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function storeSpecial($id) {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'create')) return $this->errorForbidden();
            $input = $this->request->only(['price', 'priority', 'quantity', 'is_flashsale']);
            $start_date = $this->request->get('start_date');
            if (!$start_date) $start_date = null;
            $input['start_date'] = $start_date;
            $end_date = $this->request->get('end_date');
            if (!$end_date) $end_date = null;
            $input['end_date'] = $end_date;
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Create model
            $model = $this->model_repository->create(array_merge($input, ['product_id' => $id, 'user_group_id' => 1]));

            // Update flashsale table
            $flashsale_id = $this->request->get('flashsale_id');
            if ($flashsale_id) {
                $flashsale = $this->flashsale_repository->find($flashsale_id);
                if ($flashsale) {
                    $special_ids = $flashsale->special_ids;
                    $special_ids_array = explode(',', $special_ids);
                    if (!in_array($model->id, $special_ids_array)) {
                        $separator = empty($special_ids) ? '' : ',';
                        $special_ids .= $separator . $model->id;
                        $flashsale->special_ids = $special_ids;
                        $flashsale->save();
                    }
                }
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/pd_products/{id}/specials/{special_id}",
     *   summary="Update Product Special",
     *   operationId="updateProductSpecial",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Id", example="1"),
     *   @OA\Parameter(name="special_id", in="path", description="Product Special Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="price", type="string", example=""),
     *       @OA\Property(property="priority", type="integer", example=1),
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
    public function updateSpecial($id, $special_id) {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'edit')) return $this->errorForbidden();
            $model = $this->model_repository->find($special_id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['price', 'priority', 'quantity', 'is_flashsale']);
            $start_date = $this->request->get('start_date');
            if (!$start_date) $start_date = null;
            $input['start_date'] = $start_date;
            $end_date = $this->request->get('end_date');
            if (!$end_date) $end_date = null;
            $input['end_date'] = $end_date;
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Update Model
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/pd_products/{id}/specials/{special_id}",
     *   summary="Delete Product Special",
     *   operationId="deleteProductSpecial",
     *   tags={"BackendPdProducts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Id", example="1"),
     *   @OA\Parameter(name="special_id", in="path", description="Product Special Id", example="1"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroySpecial($id, $special_id) {
        try {
            // Check permission
            if (!$this->isCRUD('products', 'delete')) return $this->errorForbidden();
            $model = $this->model_repository->find($special_id);
            if (!$model) return $this->errorNotFound();
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
