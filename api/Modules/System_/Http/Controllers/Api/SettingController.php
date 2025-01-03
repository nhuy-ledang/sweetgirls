<?php namespace Modules\System\Http\Controllers\Api;

use Illuminate\Http\Request;
use Imagy;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\System\Repositories\SettingRepository;

/**
 * Class SettingController
 *
 * @package Modules\System\Http\Controllers\Api

 
 */
class SettingController extends ApiBaseModuleController {
    protected $hidden = [];

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
                                FileRepository $file_repository,
                                FileService $fileService) {
        $this->request = $request;
        $this->model_repository = $setting_repository;
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
        return [
            'key'   => 'required',
            //'value' => 'required',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/sys_settings_all",
     *   summary="Get Setting All",
     *   operationId="getSettingAll",
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
            //$settings = $this->model_repository->all();
            $settings = new \stdClass();
            $results = $this->model_repository->getModel()->where('code', 'config')->get();
            foreach ($results as $s) {
                $settings->{$s->key} = $s->value;
                if (in_array($s->key, ['config_icon', 'config_logo', 'config_image', 'config_bg_login', 'config_icon_marker', 'config_icon_vendor', 'config_watermark_lg', 'config_watermark_md', 'config_watermark_sm', 'config_wheel', 'config_wheel_bg'])) {
                    $settings->{$s->key . '_thumb_url'} = $s->value ? media_url_file(Imagy::getThumbnail($s->value, 'thumb')) : '';
                }/* else if (in_array($s->key, ['config_address'])) {
                    $newVal = [];
                    $tmpVal = is_array($s->value) ? $s->value : [];
                    foreach ($tmpVal as $code => $v) {
                        $newVal[$code] = html_entity_decode($v, ENT_QUOTES, 'UTF-8');
                    }
                    $settings->{$s->key} = $newVal;
                }*/
            }

            return $this->respondWithSuccess($settings);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/sys_settings/{key}",
     *   summary="Get a Setting",
     *   operationId="getSetting",
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
            // Check permission
            if (!$this->isCRUD('system', 'view')) return $this->errorForbidden();
            $value = $this->model_repository->findByKey($key);

            return $this->respondWithSuccess($value);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/sys_settings",
     *   summary="Create Setting For Admin",
     *   operationId="createSetting",
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
            // Check permission
            if (!$this->isCRUD('system', 'create')) return $this->errorForbidden();
            // Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $key = str_replace('-', '_', to_alias((string)$this->request->get('key')));
            $value = $this->request->get('value');
            if (is_null($value)) $value = '';
            if (in_array($key, ['config_icon', 'config_logo', 'config_image', 'config_bg_login', 'config_icon_marker', 'config_icon_vendor', 'config_wheel', 'config_wheel_bg',]) || in_array($key, ['config_watermark_lg', 'config_watermark_md', 'config_watermark_sm'])) {
                $file_path = $this->request->get('file_path');
                if ($file_path) {
                    $value = $file_path;
                } else {
                    list($file, $errorKey) = $this->getRequestFile();
                    if ($file) {
                        $savedFile = $this->fileService->store($file, ['sub' => 'st']);
                        if (!is_string($savedFile)) $value = $savedFile->path;
                    }
                }
                if (in_array($key, ['config_watermark_lg', 'config_watermark_md', 'config_watermark_sm'])) {
                    if ($value && \Storage::exists($value)) {
                        if ($key == 'config_watermark_lg') {
                            $path = '/watermark/watermark_lg_cus.png';
                        } else if ($key == 'config_watermark_md') {
                            $path = '/watermark/watermark_md_cus.png';
                        } else {
                            $path = '/watermark/watermark_sm_cus.png';
                        }
                        $file = \Storage::get($value);
                        \Storage::disk('public')->put($path, $file, ['visibility' => 'public']);
                    }
                }
            }
            // Create Model
            $model = $this->model_repository->createOrUpdate($key, $value);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
