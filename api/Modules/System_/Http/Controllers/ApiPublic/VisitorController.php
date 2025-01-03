<?php namespace Modules\System\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\System\Repositories\VisitorRepository;

/**
 * Class VisitorController
 * @package Modules\System\Http\Controllers\ApiPublic
 * @author Nhat Truong <nhattruongqn96@gmail.com>
 * Date: 2023-10-04
 */
class VisitorController extends ApiBaseModuleController {

    public function __construct(Request $request,
                                VisitorRepository $visitor_repository) {
        $this->model_repository = $visitor_repository;

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     * @return array
     */
    protected function rulesForCreate() {
        return [
        ];
    }

    /**
     * @OA\Post(
     *   path="/sys_visitors",
     *   summary="Create System Visitor",
     *   operationId="createSystemVisitor",
     *   tags={"SysVisitors"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
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
            $user_id = $this->request->get('user_id');
            $ip = $this->request->server->get('REMOTE_ADDR');
            $session_id = $this->getSessionId();
            $dateNow = date('Y-m-d');
            if (!$ip || !$session_id) return;
            $model = $this->model_repository->getModel()->where(['ip' => $ip, 'session_id' => $session_id])->whereRaw('DATE(created_at) = ?', [$dateNow])->first();
            if ($model) {
                $model->clicks += 1;
                $model->user_id = $user_id;
                $model->save();
            } else {
                $data = [
                    'ip'         => $ip,
                    'user_id'    => $user_id ? $user_id : null,
                    'session_id' => $session_id,
                    'clicks'     => 1,
                ];
                $model = $this->model_repository->create($data);
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
