<?php namespace Modules\System\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\System\Repositories\BannerImageRepository;
use Modules\System\Repositories\BannerRepository;

/**
 * Class BannerImageController
 *
 * @package Modules\Blog\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2121-12-04
 */
class BannerImageController extends ApiBaseModuleController {
    /**
     * @var \Modules\System\Repositories\BannerRepository
     */
    protected $banner_repository;

    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    protected $file_repository;

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    public function __construct(Request $request,
                                BannerRepository $banner_repository,
                                BannerImageRepository $banner_image_repository,
                                FileRepository $file_repository,
                                FileService $fileService) {
        $this->model_repository = $banner_image_repository;
        $this->banner_repository = $banner_repository;
        $this->file_repository = $file_repository;
        $this->fileService = $fileService;

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
     *   path="/backend/sys_banners/{id}/images",
     *   summary="Get Banner Images",
     *   operationId="getBannerImages",
     *   tags={"BackendSysBanners"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Banner Id", example="1"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function indexImage($id) {
        try {
            $queries = [
                'and' => [
                    ['banner_id', '=', $id],
                ],
            ];
            $results = $this->setUpQueryBuilder($this->model(), $queries, false)->orderBy('sort_order', 'asc')->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/sys_banners/{id}/images",
     *   summary="Create Banner Image",
     *   tags={"BackendSysBanners"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Banner Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="image_alt", type="string", example=""),
     *       @OA\Property(property="sort_order", type="integer", example=1),
     *     ),
     *   ),
     *   @OA\Parameter(name="App-Env", in="query", description="ENV", example="cms"),
     *   @OA\Parameter(name="Device-Platform", in="query", description="ENV", example="web"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function storeImage($id) {
        try {
            $input = $this->request->only(['title', 'caption', 'link', 'linkname', 'sort_order']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Create model
            $model = $this->model_repository->create(array_merge($input, ['banner_id' => $id]));
            // Upload avatar
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $model = $this->model_repository->update($model, ['image' => $file_path]);
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => MEDIA_SUB_BANNER, 'object_id' => $model->id]);
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
     *   path="/backend/sys_banners/{id}/images/{image_id}",
     *   summary="Update Banner Image",
     *   operationId="updateBannerImage",
     *   tags={"BackendSysBanners"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Banner Id", example="1"),
     *   @OA\Parameter(name="image_id", in="path", description="Banner Image Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="image_alt", type="string", example=""),
     *       @OA\Property(property="sort_order", type="integer", example=1),
     *     ),
     *   ),
     *   @OA\Parameter(name="App-Env", in="query", description="ENV", example="cms"),
     *   @OA\Parameter(name="Device-Platform", in="query", description="ENV", example="web"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function updateImage($id, $image_id) {
        try {
            $model = $this->model_repository->find($image_id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['title', 'caption', 'link', 'linkname', 'sort_order']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Check file
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $input['image'] = $file_path;
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                if ($errorKey) return $this->errorWrongArgs($errorKey);
                // Upload avatar
                if ($file) {
                    $oldFile = null;
                    if ($model->image) $oldFile = $this->file_repository->findByAttributes(['object' => MEDIA_SUB_BANNER, 'path' => $model->image]);
                    // New avatar
                    $savedFile = $this->fileService->store($file, ['sub' => MEDIA_SUB_BANNER, 'object_id' => $model->id]);
                    if (!is_string($savedFile)) {
                        $input['image'] = $savedFile->path;
                        // Unlink old avatar
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
     * @OA\Delete(
     *   path="/backend/sys_banners/{id}/images/{image_id}",
     *   summary="Delete Banner Image",
     *   operationId="deleteBannerImage",
     *   tags={"BackendSysBanners"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Banner Id", example="1"),
     *   @OA\Parameter(name="image_id", in="path", description="Banner Image Id", example="1"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroyImage($id, $image_id) {
        try {
            $model = $this->model_repository->find($image_id);
            if (!$model) return $this->errorNotFound();
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
