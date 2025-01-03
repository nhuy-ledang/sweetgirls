<?php namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\Request;
use Imagy;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\System\Repositories\SettingRepository;

/**
 * Class SettingController
 *
 * @package Modules\User\Http\Controllers\Api

 
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

    public function __construct(Request $request, SettingRepository $setting_repository, FileRepository $file_repository, FileService $fileService) {
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
            'key' => 'required',
            //'value' => 'required',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/user_settings_all",
     *   summary="Get User Setting All",
     *   operationId="getUserSettingAll",
     *   tags={"BackendUserSettings"},
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
            // Check permission
            if (!$this->isCRUD('user_settings', 'view')) return $this->errorForbidden();
            $settings = new \stdClass();
            $results = $this->model_repository->getModel()->where('code', 'config')->whereRaw('`key` like ?', ["config_user_%"])->get();
            foreach ($results as $s) {
                $settings->{$s->key} = $s->value;
            }
            return $this->respondWithSuccess($settings);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/user_settings/{key}",
     *   summary="Get User Setting",
     *   operationId="getUserSetting",
     *   tags={"BackendUserSettings"},
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
     *   path="/backend/user_settings",
     *   summary="Create User Setting",
     *   operationId="createUserSetting",
     *   tags={"BackendUserSettings"},
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
            // Check admin
            //if (!$this->isAccessAdmin()) return $this->errorForbidden();
            // Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $key = str_replace('-', '_', to_alias((string)$this->request->get('key')));
            $value = $this->request->get('value');
            // Create Model
            $model = $this->model_repository->createOrUpdate($key, $value);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
