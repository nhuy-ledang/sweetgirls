<?php namespace Modules\Usr\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Usr\Repositories\NotifyRepository;
use Modules\Usr\Repositories\UserRepository;

/**
 * Class NotifyController
 *
 * @package Modules\Notify\Http\Controllers\Api
 */
class NotifyController extends ApiBaseModuleController {
    protected $maximumLimit = 20;

    /**
     * @var \Modules\Usr\Repositories\UserRepository
     */
    protected $user_repository;

    public function __construct(Request $request, NotifyRepository $notify_repository, UserRepository $user_repository) {
        $this->model_repository = $notify_repository;
        $this->user_repository = $user_repository;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/backend/usr_notifies",
     *   summary="Get Usr Notifies",
     *   operationId="getUsrNotifies",
     *   tags={"BackendUsrNotifies"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function index() {
        try {
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'id' : strtolower($sort);
            $order = (string)$this->request->get('order');
            $order = !$order ? 'desc' : strtoupper($order);
            $data = $this->getRequestData();
            $queries = ['and' => [['usr_id', '=', $this->auth->id]]];
            $results = $this->setUpQueryBuilder($this->model(), $queries)
                ->orderBy($sort, $order)
                ->take($pageSize)
                ->skip($pageSize * ($page - 1))
                ->get();
            $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();
            /*$output = [];
            foreach ($results as $result) {
                $item = $result->toArray();
                $output[] = $item;
            }*/
            return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/usr_notifies/{id}",
     *   summary="Get a Usr Notify",
     *   operationId="getUsrNotify",
     *   tags={"BackendUsrNotifies"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Usr Notify Id", example="1"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function show($id) {
        try {
            $queries = ['and' => [['id', '=', $id]]];
            $model = $this->setUpQueryBuilder($this->model(), $queries)->first();
            if (!$model) return $this->errorNotFound();
            $model->is_read = true;
            $model->save();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/usr_notifies/{id}",
     *   summary="Delete a Usr Notify",
     *   operationId="deleteUsrNotify",
     *   tags={"BackendUsrNotifies"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Usr Notify Id", example="1"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function destroy($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Delete model
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_notifies_mark_read",
     *   summary="Usr Notify Mark Read",
     *   operationId="usrNotifyMarkRead",
     *   tags={"BackendUsrNotifies"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function markRead() {
        try {
            $this->model_repository->getModel()->where('usr_id', $this->auth->id)->update(['is_read' => true]);

            return $this->respondWithSuccess(true);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/usr_notifies_alert",
     *   summary="Get Usr Notifies For Popup",
     *   operationId="getUsrNotifiesForPopup",
     *   tags={"BackendUsrNotifies"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function getAlerts() {
        try {
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'id' : strtolower($sort);
            $order = (string)$this->request->get('order');
            $order = !$order ? 'desc' : strtoupper($order);
            $data = $this->getRequestData();
            $queries = ['and' => [['usr_id', '=', $this->auth->id]]];
            $results = $this->setUpQueryBuilder($this->model(), $queries)
                ->orderBy($sort, $order)
                ->take($pageSize)
                ->skip($pageSize * ($page - 1))
                ->get();
            $unreadCount = $this->model_repository->getModel()->where('usr_id', $this->auth->id)->where('is_read', false)->count();
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging === 'true' ? true : ($paging === 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($results, ['alert' => $unreadCount]);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page, ['alert' => $unreadCount]);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_notifies_alert",
     *   summary="Mark Read Usr Notifies",
     *   operationId="markReadUsrNotifies",
     *   tags={"BackendUsrNotifies"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="ids", type="string", example="1,2,3"),
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
    public function markAlerts() {
        try {
            $tmpIds = (string)$this->request->get('ids');
            $ids = [];
            if (!empty($tmpIds)) {
                foreach (explode(',', $tmpIds) as $id) {
                    if (intval($id)) $ids[] = intval($id);
                }
                $ids = array_unique($ids);
            }
            $results = $this->model_repository->getModel()->where('usr_id', $this->auth->id);
            if ($ids) $results = $results->whereIn('id', $ids);
            $results = $results->update(['is_read' => true]);
            $unreadCount = $this->model_repository->getModel()->where('usr_id', $this->auth->id)->where('is_read', false)->count();

            return $this->respondWithSuccess($results, ['alert' => $unreadCount]);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/usr_notifies_unread_total",
     *   summary="Get Usr Notifies Unread Total",
     *   operationId="getUsrNotifiesUnreadTotal",
     *   tags={"BackendUsrNotifies"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function getUnreadTotal() {
        try {
            $unreadCount = $this->model_repository->getModel()->where('usr_id', $this->auth->id)->where('is_read', false)->count();
            return $this->respondWithSuccess($unreadCount);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usr_notifies_destroys",
     *   summary="Delete All Usr Notify",
     *   operationId="deleteAllUsrNotify",
     *   tags={"BackendUsrNotifies"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function destroys() {
        try {
            $this->model_repository->getModel()->where('usr_id', $this->auth->id)->delete();

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
