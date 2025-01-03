<?php namespace Modules\System\Http\Controllers\Api;

use Illuminate\Http\Request;
use Imagy;
use Modules\Activity\Repositories\ActivityRepository;
use Modules\Exhibit\Repositories\ExhibitRepository;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\System\Repositories\SettingRepository;

/**
 * Class SettingBannerController
 *
 * @package Modules\System\Http\Controllers\Api

 
 */
class SettingBannerController extends ApiBaseModuleController {
    protected $hidden = [];

    /**
     * @var \Modules\Activity\Repositories\ActivityRepository
     */
    protected $activity_repository;

    /**
     * @var \Modules\Exhibit\Repositories\ExhibitRepository
     */
    protected $exhibit_repository;

    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    protected $file_repository;

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    public function __construct(Request $request,
                                SettingRepository $setting_repository,
                                ActivityRepository $activity_repository,
                                ExhibitRepository $exhibit_repository,
                                FileRepository $file_repository,
                                FileService $fileService) {
        $this->request = $request;
        $this->model_repository = $setting_repository;
        $this->activity_repository = $activity_repository;
        $this->exhibit_repository = $exhibit_repository;
        $this->file_repository = $file_repository;
        $this->fileService = $fileService;

        $this->middleware('auth.usr')->only(['store']);

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreate() {
        return [
            'key' => 'required',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/sys_banner_all",
     *   summary="Get Banner Setting All",
     *   operationId="getBannerSettingAll",
     *   tags={"BackendSystems"},
     *   security={{"bearer":{}}},
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
            $setting = new \stdClass();
            $results = $this->model_repository->getModel()->where('code', 'banner')->get();
            foreach ($results as $s) {
                $setting->{$s->key} = $s->value;
                if (in_array($s->key, ['banner_bg', 'banner_sub'])) {
                    $setting->{$s->key . '_thumb_url'} = $s->value ? media_url_file(Imagy::getThumbnail($s->value, 'thumb')) : '';
                    $setting->{$s->key} = $s->value;
                } else {
                    $setting->{$s->key} = $s->value;
                }
            }
            if (isset($setting->banner_type) && $setting->banner_type === 'activity') {
                if (isset($setting->banner_value) && $setting->banner_value) {
                    $activity = $this->activity_repository->find($setting->banner_value);
                    if ($activity) $setting->banner_value_name = $activity->name;
                }
            } else if (isset($setting->banner_type) && $setting->banner_type === 'exhibit') {
                if (isset($setting->banner_value) && $setting->banner_value) {
                    $exhibit = $this->exhibit_repository->find($setting->banner_value);
                    if ($exhibit) $setting->banner_value_name = $exhibit->name;
                }
            }
            return $this->respondWithSuccess($setting);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/sys_banner/{key}",
     *   summary="Get Banner Setting",
     *   operationId="getBannerSetting",
     *   tags={"BackendSystems"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="key", in="path", description="Key", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function show($key) {
        try {
            $value = $this->model_repository->findByKey($key);

            return $this->respondWithSuccess($value);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/sys_banner",
     *   summary="Create Banner Setting",
     *   operationId="createBannerSetting",
     *   tags={"BackendSystems"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="key", type="string", example=""),
     *       @OA\Property(property="value", type="string", example=""),
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
            // Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);

            // Check admin
            //if (!$this->isAccessAdmin()) return $this->errorForbidden();

            $key = str_replace('-', '_', to_alias((string)$this->request->get('key')));
            $value = $this->request->get('value');
            if (is_null($value)) $value = '';
            if (in_array($key, ['banner_bg', 'banner_sub'])) {
                $value = '';
                $file_path = $this->request->get('file_path');
                if ($file_path) {
                    $value = $file_path;
                } else {
                list($file, $errorKey) = $this->getRequestFile();
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => MEDIA_SUB_BANNER]);
                    if (!is_string($savedFile)) $value = $savedFile->path;
                }
                }
            } else if(is_null($value)) {
                $value = '';
            }

            // Create Model
            $model = $this->model_repository->createOrUpdate($key, $value);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/sys_banner/type",
     *   summary="Update Type Banner Setting",
     *   operationId="updateTypeBannerSetting",
     *   tags={"BackendSystems"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="banner_type", type="string", example=""),
     *       @OA\Property(property="banner_value", type="string", example=""),
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
    public function updateType() {
        try {
            $input = []; //$this->request->only(['banner_type', 'banner_value']);
            $banner_type = $this->request->get('banner_type');
            $input['banner_type'] = is_null($banner_type) ? '' : $banner_type;
            $banner_value = $this->request->get('banner_value');
            $input['banner_value'] = is_null($banner_value) ? '' : $banner_value;
            $models = [];
            // Update Model
            if (!empty($input)) {
                foreach ($input as $key => $value) {
                    $models[] = $this->model_repository->createOrUpdate($key, $value);
                }
            }

            return $this->respondWithSuccess($models);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
