<?php namespace Modules\System\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\Media\Helpers\FileHelper;
use Modules\Media\Services\FileService;
use Modules\System\Repositories\FeedbackRepository;

/**
 * Class FeedbackController
 * @package Modules\System\Http\Controllers\ApiPublic
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-05-30
 */
class FeedbackController extends ApiBaseModuleController {

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    public function __construct(Request $request,
                                FeedbackRepository $product_review_repository,
                                FileService $fileService) {
        $this->model_repository = $product_review_repository;
        $this->fileService = $fileService;

        $this->middleware('auth.user');

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     * @return array
     */
    protected function rulesForCreate() {
        return [
            'type'       => 'required',
        ];
    }

    /**
     * @OA\Post(
     *   path="/sys_feedbacks",
     *   summary="Create System Feedback",
     *   operationId="createSystemFeedback",
     *   tags={"SysFeedbacks"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="order_id", type="string", example=""),
     *       @OA\Property(property="type", type="string", example=""),
     *       @OA\Property(property="message", type="string", example=""),
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
            $input = $this->request->all();

            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            list($file, $errorKey) = $this->getRequestFile();
            if ($errorKey) return $this->errorWrongArgs($errorKey);

            if ($file) {
                $original_name = FileHelper::limit($file->getClientOriginalName(), 59);
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $file_mime = finfo_file($finfo, $file);
                finfo_close($finfo);
                $path = '/feedbacks/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . time() . '-' . $original_name;
                $ok = \Storage::disk(config('filesystems.default'))
                    ->getDriver()->put($path, fopen($file, 'r+'), [
                            'visibility'  => 'public',
                            'ContentType' => $file_mime,
                        ]
                    );
                if (!$ok) {
                    return $this->respondWithErrorKey('file.upload_error');
                }
                $savedFile = [
                    'path'      => $path,
                    'extension' => $file->guessClientExtension(),
                    'mimetype'  => $file->getClientMimeType(),
                    'filesize'  => $file->getFileInfo()->getSize(),
                ];

                $input['file'] = $savedFile['path'];
            }

            // Create Model
            $model = $this->model_repository->create(array_merge($input, ['user_id' => $this->auth->id]));

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
