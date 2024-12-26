<?php namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductOptionRepository;
use Modules\Product\Repositories\OptionRepository;
use Modules\Product\Repositories\ProductVariantRepository;

/**
 * Class ProductOptionController
 *
 * @package Modules\Product\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-07-03
 */
class ProductOptionController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    /**
     * @var \Modules\Product\Repositories\ProductVariantRepository
     */
    protected $product_variant_repository;

    /**
     * @var \Modules\Product\Repositories\OptionRepository
     */
    protected $option_repository;

    public function __construct(Request $request,
                                ProductOptionRepository $product_option_repository,
                                ProductRepository $product_repository,
                                ProductVariantRepository $product_variant_repository,
                                OptionRepository $option_repository) {
        $this->model_repository = $product_option_repository;
        $this->product_repository = $product_repository;
        $this->product_variant_repository = $product_variant_repository;
        $this->option_repository = $option_repository;

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
            'option_id'  => 'required|integer|exists:pd__options,id',
        ];
    }

    /**
     * Get the validation rules for update.
     *
     * @param int $id
     *
     * @return array
     */
    protected function rulesForUpdate($id) {
        return [
            'product_id' => 'required|integer|exists:pd__products,id',
            'option_id'  => 'required|integer|exists:pd__options,id',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_product_options",
     *   summary="Get Product Options",
     *   operationId="pdGetProductOptions",
     *   tags={"BackendPdProductOptions"},
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
            $data = $this->getRequestData();
            $product_id = isset($data->{'product_id'}) ? (int)$data->{'product_id'} : 0;
            $queries = ['and' => [['product_id', '=', $product_id]]];
            $fields = ['pd__product_options.*', 'opt.name as name'];
            $results = $this->setUpQueryBuilder($this->model(), $queries, false, $fields)
                ->leftJoin('pd__options as opt', 'opt.id', '=', 'option_id')
                ->orderBy('opt.sort_order', 'asc')->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pd_product_options",
     *   summary="Create Product Option",
     *   operationId="pdCreatetProductOption",
     *   tags={"BackendPdProductOptions"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="product_id", type="integer", example=1),
     *       @OA\Property(property="option_id", type="integer", example=1),
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
            $input = $this->request->only(['product_id', 'option_id']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Check exist
            $check = $this->model_repository->findByAttributes(['product_id' => $input['product_id'], 'option_id' => $input['option_id']]);
            if ($check) return $this->errorWrongArgs('option_id.exists');
            // Create model
            $model = $this->model_repository->create($input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/pd_product_options/{id}",
     *   summary="Update Product Option",
     *   operationId="pdUpdateProductOption",
     *   tags={"BackendPdProductOptions"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Option Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="product_id", type="integer", example=1),
     *       @OA\Property(property="option_id", type="integer", example=1),
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
            $input = $this->request->only(['product_id', 'option_id']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Check exist
            $check = $this->model_repository->findByAttributes(['product_id' => $input['product_id'], 'option_id' => $input['option_id']]);
            if ($check) return $this->errorWrongArgs('option_id.exists');
            // Update Model
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/pd_product_options/{id}",
     *   summary="Delete Product Option",
     *   operationId="pdDeleteProductOption",
     *   tags={"BackendPdProductOptions"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Option Id", example=1),
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
            // Check using
            $find = $this->product_variant_repository->findByAttributes(['product_id' => $model->product_id, 'option_id' => $model->option_id]);
            if ($find) return $this->respondWithErrorKey('system.forbidden', 400, '', [], $find);
            // Delete model
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
