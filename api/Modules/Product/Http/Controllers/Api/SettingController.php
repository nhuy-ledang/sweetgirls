<?php namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Imagy;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\System\Repositories\SettingRepository;

/**
 * Class SettingController
 *
 * @package Modules\Product\Http\Controllers\Api
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
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
            'key'   => 'required',
            //'value' => 'required',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_settings_all",
     *   summary="Get Product Setting All",
     *   operationId="getProductSettingAll",
     *   tags={"BackendPdSettings"},
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
            // if (info.key === 'pd_description' || info.key === 'pd_note' || info.key === 'pd_short_description') {
            $settings = new \stdClass();
            $results = $this->model_repository->getModel()->where('code', trim($this->module_prefix, '_'))->get();
            foreach ($results as $s) {
                $settings->{$s->key} = $s->value;
                if ($s->key == 'pd_category_bg_image' || $s->key == 'pd_banner' ) {
                    $settings->{$s->key . '_thumb_url'} = $s->value ? media_url_file(Imagy::getThumbnail($s->value, 'thumb')) : '';
                    $settings->{$s->key} = $s->value;
                } else if (in_array($s->key, ['pd_name', 'pd_short_description'])) {
                    $newVal = [];
                    $tmpVal = is_array($s->value) ? $s->value : [];
                    foreach ($tmpVal as $code => $v) {
                        $newVal[$code] = html_entity_decode($v, ENT_QUOTES, 'UTF-8');
                    }
                    $settings->{$s->key} = $newVal;
                } else if (in_array($s->key, ['pd_list_image'])) {
                    $newVal = [];
                    $tmpVal = is_array($s->value) ? $s->value : [];
                    foreach ($tmpVal as $tmp) {
                        $item = [];
                        foreach ($tmp as $code => $v) {
                            if (is_array($v)) {
                                $v2 = [];
                                foreach ($v as $k => $vc) {
                                    $v2[$k] = html_entity_decode($vc, ENT_QUOTES, 'UTF-8');
                                }
                                $item[$code] = $v2;
                            } else {
                                $item[$code] = html_entity_decode($v, ENT_QUOTES, 'UTF-8');
                                if ($code == 'image') {
                                    $item['thumb_url'] = $item[$code] ? media_url_file(Imagy::getThumbnail($item[$code], 'thumb')) : '';
                                }
                            }
                        }
                        $newVal[] = $item;
                    }
                    $settings->{$s->key} = $newVal;
                } else {
                    $settings->{$s->key} = $s->value;
                }
            }
            return $this->respondWithSuccess($settings);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_settings/{key}",
     *   summary="Get Product Setting",
     *   operationId="getProductSetting",
     *   tags={"BackendPdSettings"},
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
     *   path="/backend/pd_settings",
     *   summary="Create Product Setting",
     *   operationId="createProductSetting",
     *   tags={"BackendPdSettings"},
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
            if ($key == 'pd_category_bg_image' || $key == 'pd_banner') {
                $value = '';
                $file_path = $this->request->get('file_path');
                if ($file_path) {
                    $value = $file_path;
                } else {
                list($file, $errorKey) = $this->getRequestFile();
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => $this->module_prefix . 'setting']);
                    if (!is_string($savedFile)) $value = $savedFile->path;
                }
                }
            } else if ($key == 'pd_list_image') {
                $table_images = json_decode($value, true);
                if (empty($table_images)) return $this->errorWrongArgs();
                $newVal = [];
                foreach ($table_images as $k => $v) {
                    $file_path = $this->request->get("filepath_$k");
                    if ($file_path) {
                        $v['image'] = $file_path;
                    } else {
                    list($file, $errorKey) = $this->getRequestFile("file_$k");
                    if ($file) {
                        $savedFile = $this->fileService->store($file, ['sub' => trim($this->module_prefix, '_')]);
                        if (!is_string($savedFile)) $v['image'] = $savedFile->path;
                    }
                    }
                    $newVal[] = $v;
                }
                $value = $newVal;
            }

            // Create Model
            $model = $this->model_repository->createOrUpdate($key, $value);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
