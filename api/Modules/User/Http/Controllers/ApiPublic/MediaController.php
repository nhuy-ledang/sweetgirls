<?php namespace Modules\User\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\Media\Events\FileWasUploaded;
use Modules\Media\Http\Requests\UploadMediaRequest;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\User\Entities\History;
use Modules\User\Repositories\UserRepository;

/**
 * Class MediaController
 * @package Modules\User\Http\Controllers\ApiPublic

 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   resourcePath="/Auth",
 *   swaggerVersion="1.2",
 *   description="Auth Api"
 * )
 */
class MediaController extends ApiBaseModuleController {
    /**
     * @var FileService
     */
    private $fileService;

    /**
     * @var \Modules\User\Repositories\UserRepository
     */
    private $user_repository;

    public function __construct(Request $request, FileRepository $file_repository, FileService $fileService, UserRepository $user_repository) {
        $this->model_repository = $file_repository;
        $this->fileService = $fileService;
        $this->user_repository = $user_repository;

        $this->middleware('auth.user');

        parent::__construct($request);
    }

    /**
     * @SWG\Api(
     *   path="/auth/avatar",
     *   @SWG\Operation(
     *      method="POST",
     *      summary="Upload avatar",
     *      nickname="uploadAvatar",
     *      @SWG\Parameter(name="Authorization", description="Authorization", required=false, type="body", paramType="form", allowMultiple=false),
     *      @SWG\Parameter(name="file", description="File image", required=true, type="file", paramType="form", allowMultiple=false),
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     * @param UploadMediaRequest $request
     * @return \Illuminate\Support\Facades\Response|mixed
     */
    public function avatar(UploadMediaRequest $request) {
        try {
            $file = $request->file('file');

            //=== Check file size
            if ($file->getSize() > config('media.config.max-total-size')) {
                return $this->respondWithErrorKey('file.max');
            }

            //=== Check extension
            $mines = array();
            foreach (explode(',', config('media.config.allowed-types')) as $mine) {
                $mines[] = str_replace('.', 'image/', $mine);
            }

            if (!in_array($file->getMimeType(), $mines)) {
                return $this->respondWithErrorKey('file.mime');
            }

            $savedFile = $this->fileService->store($file, [
                'sub'     => MEDIA_SUB_AVATAR,
                'user_id' => $this->auth->id
            ]);

            if (is_string($savedFile)) {
                return $this->errorWrongArgs($savedFile, 409);
            }

            event(new FileWasUploaded($savedFile));

            // Unlink old avatar
            if ($this->auth->avatar) {
                $model = $this->model_repository->findByAttributes([
                    'user_id' => $this->auth->id,
                    'path'    => $this->auth->avatar,
                    'object'  => MEDIA_SUB_AVATAR
                ]);
                if ($model) {
                    /*$model->deleted_at = date('Y-m-d H:i:s');
                    $model->save();*/
                    $this->model_repository->destroy($model);
                }
            }

            // Update Model
            $model = $this->model_repository->update($this->auth, array('avatar' => $savedFile->path));

            // Logger
            History::logger($this->auth->id, LOGGER_USER_AVATAR_CHANGED, $model->avatar_url);

            return $this->respondWithSuccess($this->responseUserForNotify($model));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
