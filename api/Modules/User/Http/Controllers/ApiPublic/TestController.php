<?php namespace Modules\User\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\User\Repositories\UserRepository;

/**
 * Class TestController
 * @package Modules\User\Http\Controllers\ApiPublic
 * @author Huy D <huydang1920@gmail.com>
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   resourcePath="/Tests",
 *   description="Tests Api"
 * )
 */
class TestController extends ApiBaseModuleController {
    public function __construct(Request $request, UserRepository $user_repository) {
        $this->model_repository = $user_repository;

        //$this->middleware('auth.user')->except(['store']);

        parent::__construct($request);
    }

    /**
     * @SWG\Api(
     *   path="/tests/push",
     *   @SWG\Operation(
     *      method="GET",
     *      summary="push",
     *      nickname="push",
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     */
    public function push() {
        try {
            // Send Notification - Push notification on the user's profile
            $pushData['place_id'] = 1;
            $pushType = 1;
            $pushMessage = 'Test';

            $user = $this->model_repository->find(1);
            $this->pushNotifications($user, $pushMessage, $pushType, $pushData);
            // End Send Notification
            return $this->respondWithSuccess($user);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @SWG\Api(
     *   path="/tests/all",
     *   @SWG\Operation(
     *      method="GET",
     *      summary="all",
     *      nickname="all",
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     */
    public function all() {
        try {
            return $this->respondWithSuccess(str_random(4));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
