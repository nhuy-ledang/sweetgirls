<?php namespace Modules\Media\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Media\Events\FileWasUploaded;
use Modules\Media\Helpers\FileHelper;
use Modules\Media\Http\Requests\UploadMediaRequest;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Repositories\FolderRepository;
use Modules\Media\Services\FileService;

/**
 * Class MediaController
 * @package Modules\Media\Http\Controllers\Api
 */
class MediaController extends ApiBaseModuleController {
    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * @var \Modules\Media\Repositories\FolderRepository
     */
    protected $folder_repository;

    public function __construct(Request $request,
                                FileService $fileService,
                                FileRepository $file_repository,
                                FolderRepository $folder_repository) {
        $this->model_repository = $file_repository;

        $this->fileService = $fileService;
        $this->folder_repository = $folder_repository;

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
        return [
            'name' => 'required',
        ];
    }

    /**
     * @OA\Post(
     *   path="/backend/media/upload",
     *   summary="Upload File",
     *   operationId="uploadFile",
     *   tags={"BackendMedia"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     description="Upload File (Subfolder:lo,pi,re,ba,av,pd)",
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object",
     *         @OA\Property(property="sub", type="string", example=""),
     *         @OA\Property(property="folder_id", type="string", example=0),
     *         @OA\Property(property="file", type="string", format="binary"),
     *       ),
     *     )
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     * @param UploadMediaRequest $request
     * @return \Illuminate\Support\Facades\Response|mixed
     */
    public function storeFile(UploadMediaRequest $request) {
        try {
            //if (!$this->isAccessAdmin()) return $this->respondWithErrorKey('system.permission');
            $sub = $this->request->get('sub');
            if (!$sub || $sub == 'undefined') $sub = 'fm';
            //=== Check Valid
            $validatorErrors = $this->getValidator(compact('sub'), $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            //=== Check file size
            $file = $request->file('file');
            if ($file->getSize() > config('media.config.max-total-size')) {
                return $this->respondWithErrorKey('file.max');
            }
            //=== Check extension
            $mines = [];
            foreach (explode(',', config('media.config.allowed-types')) as $mine) {
                $mines[] = str_replace('.', 'image/', $mine);
            }
            if (!in_array($file->getMimeType(), $mines)) return $this->respondWithErrorKey('file.mime');
            $optional = ['sub' => $sub, 'user_id' => $this->auth->id];
            $folder_id = (int)$this->request->get('folder_id');
            if ($folder_id) $optional['folder_id'] = $folder_id;
            $savedFile = $this->fileService->store($file, $optional);
            if (is_string($savedFile)) return $this->errorWrongArgs($savedFile, 409);
            event(new FileWasUploaded($savedFile));
            return $this->respondWithSuccess($savedFile);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/media/unlink",
     *   summary="Unlink File",
     *   operationId="unlinkFile",
     *   tags={"BackendMedia"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="path", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     * @return \Illuminate\Support\Facades\Response|mixed
     */
    public function unlinkFile() {
        try {
            //if (!$this->isAccessAdmin()) return $this->respondWithErrorKey('system.permission');
            $model = $this->model_repository->findByAttributes(['path' => $this->request->get('path')]);
            if (!$model) return $this->errorNotFound();
            $this->model_repository->destroy($model);
            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    // path="/backend/media/upload/document"
    // ??????
    /*public function uploadDocument() {
        try {
            if (!$this->isAccessAdmin()) return $this->respondWithErrorKey('system.permission');
            $file = $this->request->file('file');
            if (!$file) return $this->respondWithErrorKey('file.required');
            $original_name = FileHelper::slug($file->getClientOriginalName());
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_mime = finfo_file($finfo, $file);
            finfo_close($finfo);
            $path = '/files/' . date('Ym') . '/' . $original_name;
            $ok = \Storage::disk(config('filesystems.default'))->getDriver()->put($path, fopen($file, 'r+'), ['visibility' => 'public', 'ContentType' => $file_mime]);
            if (!$ok) return $this->respondWithErrorKey('file.upload_error');
            $savedFile = [
                'path'      => $path,
                'extension' => $file->guessClientExtension(),
                'mimetype'  => $file->getClientMimeType(),
                'filesize'  => $file->getFileInfo()->getSize(),
                'file_url'  => config('filesystems.disks.local.url') . $path
            ];
            return $this->respondWithSuccess($savedFile);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }*/

    /**
     * @OA\Post(
     *   path="/backend/media/uploads",
     *   summary="Upload Files",
     *   tags={"BackendMedia"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         @OA\Property(property="file", type="string", format="binary"),
     *       ),
     *     )
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function storeFiles() {
        try {
            //if (!$this->isAccessAdmin()) return $this->respondWithErrorKey('system.permission');
            $sub = $this->request->get('sub');
            if (!$sub || $sub == 'undefined') $sub = 'fm';
            list($files, $errorKeys) = $this->getRequestFiles();
            if ($errorKeys) return $this->errorWrongArgs($errorKeys[0]);
            $optional = ['sub' => $sub, 'user_id' => $this->auth->id];
            $folder_id = (int)$this->request->get('folder_id');
            if ($folder_id) $optional['folder_id'] = $folder_id;
            $savedFiles = [];
            foreach ($files as $file) {
                $savedFile = $this->fileService->store($file, $optional);
                if (!is_string($savedFile)) $savedFiles[] = $savedFile;
            }

            return $this->respondWithSuccess($savedFiles);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    public function deleteAll() {
        try {
            $folders = $this->request->get('folders');
            if (!is_array($folders)) $folders = [];
            $newFolders = [];
            foreach ($folders as $id) {
                if (intval($id)) $newFolders[] = (int)$id;
            }
            $files = $this->request->get('files');
            if (!is_array($files)) $files = [];
            $newFiles = [];
            foreach ($files as $id) {
                if (intval($id)) $newFiles[] = (int)$id;
            }
            // Check valid
            if (!empty($newFolders)) {
                $exist = $this->folder_repository->getModel()->whereIn('parent_id', $newFolders)->first();
                if (!$exist) $exist = $this->model_repository->getModel()->whereIn('folder_id', $newFolders)->first();
                if ($exist) return $this->respondWithErrorKey('folder_id.empty');
            }
            // Delete folders
            if (!empty($newFolders)) {
                $this->model_repository->getModel()->whereIn('folder_id', $newFolders)->update(['folder_id' => 0]);
                foreach ($newFolders as $id) {
                    $this->folder_repository->getModel()->where('id', $id)->delete();
                }
            }
            // Delete files
            foreach ($newFiles as $id) {
                $model = $this->model_repository->find($id);
                if ($model) $this->model_repository->destroy($model);
            }

            return $this->respondWithSuccess(compact('newFolders', 'newFiles'));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/media_moves",
     *   summary="Move Files",
     *   operationId="moveFiles",
     *   tags={"BackendMedia"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="folder_id", type="integer", example=0),
     *       @OA\Property(property="folders", type="string", example=""),
     *       @OA\Property(property="files", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     * @return \Illuminate\Support\Facades\Response|mixed
     */
    public function moveAll() {
        try {
            $folder_id = $this->request->get('folder_id');
            $folder_id = is_null($folder_id) || $folder_id === '' ? false : (int)$folder_id;
            if ($folder_id === false) return $this->respondWithErrorKey('folder_id.required');
            if ($folder_id) {
                $folder = $this->folder_repository->find($folder_id);
                if (!$folder) return $this->respondWithErrorKey('folder_id.required');
            } else {
                $folder_id = 0;
            }
            $folders = $this->request->get('folders');
            $files = $this->request->get('files');
            if (!is_array($folders)) $folders = [];
            if (!is_array($files)) $files = [];
            $newFolders = [];
            foreach ($folders as $id) {
                if (intval($id) && intval($id) != $folder_id) $newFolders[] = (int)$id;
            }
            $newFiles = [];
            foreach ($files as $id) {
                if (intval($id)) $newFiles[] = (int)$id;
            }
            if (!empty($newFolders)) $this->folder_repository->getModel()->whereIn('id', $newFolders)->update(['parent_id' => $folder_id]);
            if (!empty($newFiles)) $this->model_repository->getModel()->whereIn('id', $newFiles)->update(['folder_id' => $folder_id]);

            return $this->respondWithSuccess(compact('folder_id', 'newFolders', 'newFiles'));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/media_files/{id}",
     *   summary="Update Media File",
     *   operationId="updateMediaFile",
     *   tags={"BackendMedia"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Media File Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
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
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            list($type, $subtype) = explode('/', $model->mimetype);
            $input = [];
            $name = $this->request->get('name');
            if (!is_null($name)) {
                $input['filename'] = trim((string)$name);
            }
            if ($type == 'image' && in_array($subtype, ['png', 'jpeg'])) {
                $watermark = $this->request->get('watermark');
                $position = (string)$this->request->get('position');
                $x = $this->request->get('x');
                $x = is_null($x) ? 0 : (int)$x;
                $y = $this->request->get('y');
                $y = is_null($y) ? 10 : (int)$y;
                if (!in_array($position, ['top-left', 'top-right', 'bottom-left', 'bottom-right'])) $position = 'top-left';
                if (!is_null($watermark)) {
                    $watermark = (boolean)$watermark;
                    $input['watermark'] = $watermark;
                    $input['position'] = $position;
                    $input['x'] = $x;
                    $input['y'] = $y;
                    // Create watermark or not
                    if ($watermark != $model->watermark || $model->x != $x || $model->y != $y) {
                        app('imagy')->generateWatermark($model->path, $watermark, $position, $x, $y);
                    }
                }
            }
            // Update Model
            if (!empty($input)) $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
