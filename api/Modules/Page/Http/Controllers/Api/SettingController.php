<?php namespace Modules\Page\Http\Controllers\Api;

use Illuminate\Http\Request;
use Imagy;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\System\Repositories\SettingRepository;

/**
 * Class SettingController
 *
 * @package Modules\Page\Http\Controllers\Api

 
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
     *   path="/backend/pg_settings_all",
     *   summary="Get Page Page Setting All",
     *   operationId="getPagePageSettingAll",
     *   tags={"BackendPgSettings"},
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
            // if (info.key === 'pg_description' || info.key === 'pg_note' || info.key === 'pg_short_description') {
            $settings = new \stdClass();
            $results = $this->model_repository->getModel()->where('code', trim($this->module_prefix, '_'))->get();
            foreach ($results as $s) {
                $settings->{$s->key} = $s->value;
                if (in_array($s->key, ['pg_category_bg', 'pg_footer_bg', 'pg_title_icon'])) {
                    $settings->{$s->key . '_thumb_url'} = $s->value ? media_url_file(Imagy::getThumbnail($s->value, 'thumb')) : '';
                    $settings->{$s->key} = $s->value;
                } else if (in_array($s->key, ['pg_description', 'pg_note', 'pg_short_description'])) {
                    $newVal = [];
                    $tmpVal = is_array($s->value) ? $s->value : [];
                    foreach ($tmpVal as $code => $v) {
                        $newVal[$code] = html_entity_decode($v, ENT_QUOTES, 'UTF-8');
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
     *   path="/backend/pg_settings/{key}",
     *   summary="Get Page Page Setting",
     *   operationId="getPagePageSetting",
     *   tags={"BackendPgSettings"},
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
     *   path="/backend/pg_settings",
     *   summary="Create Page Page Setting",
     *   operationId="createPagePageSetting",
     *   tags={"BackendPgSettings"},
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
            if ($key == 'pg_category_bg' || $key == 'pg_footer_bg' || $key == 'pg_title_icon') {
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
            }

            // Create Model
            $model = $this->model_repository->createOrUpdate($key, $value);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
