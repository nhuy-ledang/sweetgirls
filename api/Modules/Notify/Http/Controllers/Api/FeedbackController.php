<?php namespace Modules\Notify\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Notify\Repositories\FeedbackRepository;

/**
 * Class FeedbackController
 *
 * @package Modules\Notify\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 9/10/2018 4:37 PM
 *
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   resourcePath="/NotifyFeedbacks",
 *   description="Notify Feedbacks Api"
 * )
 */
class FeedbackController extends ApiBaseModuleController {
    protected $maximumLimit = 20;

    public function __construct(Request $request, FeedbackRepository $feedback_repository) {
        $this->model_repository = $feedback_repository;

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreate() {
        return [
            'title'   => 'required',
            'content' => 'required',
            'email'   => 'required|email',
        ];
    }

    /**
     * @SWG\Model(id="FeedbackModel")
     * @SWG\Property(name="title", type="string", required=true, defaultValue=""),
     * @SWG\Property(name="content", type="string", required=true, defaultValue=""),
     * @SWG\Property(name="email", type="string", required=true, defaultValue=""),
     * @SWG\Property(name="category", type="string", required=true, defaultValue=""),
     * @SWG\Property(name="know_from", type="string", required=true, defaultValue=""),
     * @SWG\Api(
     *   path="/backend/notify/feedbacks",
     *   @SWG\Operation(
     *      method="POST",
     *      summary="Create feedback",
     *      nickname="createFeedback",
     *      @SWG\Parameter(name="body", description="Request body", required=true, type="FeedbackModel", paramType="body", allowMultiple=false),
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     */
    public function store() {
        try {
            $input = $this->request->all();

            // Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), $this->rulesForCreate());
            if (!empty($validatorErrors)) {
                return $this->respondWithError($validatorErrors);
            }

            if ($auth = $this->isLogged()) {
                $input['user_id'] = $auth->id;
            }

            // Create Model
            $model = $this->model_repository->create(array_merge($input, []));

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
