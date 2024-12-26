<?php namespace Modules\Product\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\Product\Repositories\ProductLikeRepository;
use Modules\Product\Repositories\ProductRepository;

/**
 * Class ProductLikeController
 *
 * @package Modules\Product\Http\Controllers\ApiPublic
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2022-06-03
 */
class ProductLikeController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    public function __construct(Request $request, ProductLikeRepository $like_repository, ProductRepository $product_repository) {
        $this->model_repository = $like_repository;
        $this->product_repository = $product_repository;

        $this->middleware('auth.user');

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreate() {
        return [
            //'product_id' => 'required|integer|exists:pd__products,id',
            'liked' => 'required|boolean',
        ];
    }

    /**
     * @OA\Post(
     *   path="/products/{id}/like",
     *   summary="Add Product Like Dislike",
     *   description="Add Product Like Dislike",
     *   operationId="addProductLikeDislike",
     *   tags={"Products"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Product Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="liked", type="integer", example=1),
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
    public function like($id) {
        try {
            $input = $this->request->only(['liked']);

            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);

            // Check product exist
            $product = $this->product_repository->find($id);
            if (!$product) return $this->errorNotFound();

            $model = $this->model_repository->findByAttributes(['product_id' => $id, 'user_id' => $this->auth->id]);
            if (!$model) {
                $model = $this->model_repository->create(array_merge($input, ['product_id' => $id, 'user_id' => $this->auth->id]));
            } else {
                $model = $this->model_repository->update($model, $input);
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
