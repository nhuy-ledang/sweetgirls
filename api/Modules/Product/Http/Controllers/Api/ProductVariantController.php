<?php namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Product\Repositories\OptionRepository;
use Modules\Product\Repositories\ProductOptionValueRepository;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductOptionRepository;
use Modules\Product\Repositories\ProductVariantRepository;

/**
 * Class ProductVariantController
 *
 * @package Modules\Product\Http\Controllers\Api
 */
class ProductVariantController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    /**
     * @var \Modules\Product\Repositories\ProductOptionRepository
     */
    protected $product_option_repository;

    /**
     * @var \Modules\Product\Repositories\ProductOptionValueRepository
     */
    protected $product_option_value_repository;

    /**
     * @var \Modules\Product\Repositories\OptionRepository
     */
    protected $option_repository;

    public function __construct(Request $request,
                                ProductVariantRepository $product_variant_repository,
                                ProductRepository $product_repository,
                                ProductOptionRepository $product_option_repository,
                                ProductOptionValueRepository $product_option_value_repository,
                                OptionRepository $option_repository) {
        $this->model_repository = $product_variant_repository;
        $this->product_repository = $product_repository;
        $this->product_option_repository = $product_option_repository;
        $this->product_option_value_repository = $product_option_value_repository;
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
            'product_id'      => 'required',
            'option_id'       => 'required',
            'option_value_id' => 'required',
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
            'product_id'      => 'required',
            'option_id'       => 'required',
            'option_value_id' => 'required',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_product_variants",
     *   summary="Get Product Variants",
     *   operationId="pdGetProductVariants",
     *   tags={"BackendPdProductVariants"},
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
        /*try {
            $data = $this->getRequestData();
            $product_id = isset($data->{'product_id'}) ? (int)$data->{'product_id'} : 0;
            $queries = [
                'and' => [
                    ['product_id', '=', $product_id],
                ],
            ];
            $fields = ['pd__product_options.*', 'pr.name as option_name'];
            $results = $this->setUpQueryBuilder($this->model(), $queries, false, $fields)
                ->leftJoin('pd__options as pr', 'pr.id', '=', 'option_id')
                ->orderBy('pd__product_options.created_at', 'asc')
                ->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }*/
    }

    /**
     * @OA\Post(
     *   path="/backend/pd_product_variants",
     *   summary="Create Product Variant",
     *   operationId="pdCreateProductVariant",
     *   tags={"BackendPdProductVariants"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="product_id", type="integer", example=1),
     *       @OA\Property(property="option_id", type="integer", example=1),
     *       @OA\Property(property="option_value_id", type="integer", example=1),
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
            $input = $this->request->only(['product_id', 'option_id', 'option_value_id']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Check product
            $product = $this->product_repository->find($input['product_id']);
            if (!$product) return $this->respondWithErrorKey('product_id.required');
            //Check

            return $this->respondWithSuccess([]);

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
     *   path="/backend/pd_product_variants/{id}",
     *   summary="Update Product Variant",
     *   operationId="pdUpdateProductVariant",
     *   tags={"BackendPdProductVariants"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Variant Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="product_id", type="integer", example=1),
     *       @OA\Property(property="option_id", type="integer", example=1),
     *       @OA\Property(property="option_value_id", type="integer", example=1),
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
        /*try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['option_id', 'value', 'image', 'sort_order']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Check exist
            if ($input['option_id'] != $model->option_id) {
                $check = $this->model_repository->findByAttributes(['product_id' => $model->product_id, 'option_id' => $input['option_id']]);
                if ($check) return $this->errorWrongArgs('option_id.exists');
            }
            // Update Model
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }*/
    }

    /**
     * @OA\Delete(
     *   path="/backend/pd_product_variants/{id}",
     *   summary="Delete Product Variant",
     *   operationId="pdDeleteProductVariant",
     *   tags={"BackendPdProductVariants"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Variant Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        /*try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Check using
            // Delete model
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }*/
    }
}
