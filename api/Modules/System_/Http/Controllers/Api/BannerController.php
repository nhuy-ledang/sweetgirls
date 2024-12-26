<?php namespace Modules\System\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Media\Services\FileService;
use Modules\System\Repositories\BannerRepository;

/**
 * Class BannerController
 *
 * @package Modules\System\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 9/10/2018 4:37 PM
 */
class BannerController extends ApiBaseModuleController {
    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    public function __construct(Request $request,
                                BannerRepository $banner_repository,
                                FileService $fileService) {
        $this->model_repository = $banner_repository;
        $this->fileService = $fileService;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/backend/sys_banners_all",
     *   summary="Get All Banners",
     *   operationId="getAllBanners",
     *   tags={"BackendSysBanners"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields, extend_fields:total_courses} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function all() {
        try {
            //$results = $this->model_repository->getAll();
            $results = $this->setUpQueryBuilder($this->model())->where('status', 1)->get();
            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/sys_banners",
     *   summary="Get System Banners",
     *   operationId="getSystemBanners",
     *   tags={"BackendSysBanners"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="App-Env", in="query", description="ENV", example="cms"),
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields, extend_fields:total_courses} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
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
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $queries = [
                'and'        => [],
                'orWhereRaw' => []
            ];
            $results = $this->setUpQueryBuilder($this->model(), $queries)
                ->orderBy('name', 'asc')
                ->take($pageSize)
                ->skip($pageSize * ($page - 1))
                ->get();
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging === 'true' ? true : ($paging === 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($results);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/sys_banners/{id}",
     *   summary="Get System Banners",
     *   operationId="getSystemBanners",
     *   tags={"BackendSysBanners"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Banner Id - Home Top: 1", example="1"),
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
     *   path="/backend/sys_banners/{id}",
     *   summary="Create System Banner",
     *   operationId="createSystemBanner",
     *   tags={"BackendSysBanners"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Banner Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example="cms"),
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
            $input = $this->request->only(['name', 'status']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, ['name' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Update Model
            $model = $this->model_repository->create($input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/sys_banners/{id}",
     *   summary="Update System Banner",
     *   operationId="updateSystemBanner",
     *   tags={"BackendSysBanners"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Banner Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example="cms"),
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
            $input = $this->request->only(['name', 'status']);
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Check Valid
            $validatorErrors = $this->getValidator($input, ['name' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Update Model
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *   path="/backend/sys_banners/{id}",
     *   summary="Update System Banner Partial",
     *   operationId="updateSystemBannerPartial",
     *   tags={"BackendSysBanners"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Banner Id", example="1"),
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
     *   path="/backend/sys_banners/{id}",
     *   summary="Delete System Banner",
     *   operationId="deteleSystemBanner",
     *   tags={"BackendSysBanners"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Banner Id - Home Top: 1", example="1"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function destroy($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();

            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
