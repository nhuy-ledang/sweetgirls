<?php namespace Modules\Notify\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Notify\Repositories\NotificationRepository;

/**
 * Class NotificationController
 *
 * @package Modules\Notify\Http\Controllers\Api
 *
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   resourcePath="/NotifyNotifications",
 *   description="Notify Notifications Api"
 * )
 */
class NotificationController extends ApiBaseModuleController {
    protected $maximumLimit = 20;

    public function __construct(Request $request, NotificationRepository $notification_repository) {
        $this->model_repository = $notification_repository;

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
     *
     * @return array
     */
    protected function rulesForUpdate($id) {
        return [];
    }

    /**
     * @SWG\Api(
     *   path="/backend/notify/notifications",
     *   @SWG\Operation(
     *      method="GET",
     *      summary="GetNotification",
     *      nickname="getNotification",
     *      @SWG\Parameter(name="page", description="Current Page", required=true, type="integer", paramType="query", allowMultiple=false, defaultValue="1"),
     *      @SWG\Parameter(name="pageSize", description="Item total on page", required=true, type="integer", paramType="query", allowMultiple=false, defaultValue="20"),
     *      @SWG\Parameter(name="sort", description="Sort by", required=false, type="string", paramType="query", allowMultiple=false, defaultValue="id"),
     *      @SWG\Parameter(name="order", description="Order", required=false, type="string", paramType="query", allowMultiple=false, defaultValue="desc"),
     *      @SWG\Parameter(name="data", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", required=false, type="string", paramType="query", allowMultiple=false, defaultValue=""),
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     */
    public function index() {
        try {
            $page = (int)$this->request->get('page');
            $pageSize = (int)$this->request->get('pageSize');
            $sort = (string)$this->request->get('sort');
            $order = (string)$this->request->get('order');

            $data = $this->getRequestData();

            if (!$page) {
                $page = 1;
            }
            if (!$pageSize) {
                $pageSize = $this->pageSize;
            }
            if (!$sort) {
                $sort = 'id';
            } else {
                $sort = strtolower($sort);
            }
            if (!$order) {
                $order = 'DESC';
            } else {
                $order = strtoupper($order);
            }

            $queries = $this->defaultQueries;
            $queries[] = ['user_id', '=', $this->auth->id];

            $results = $this->setUpQueryBuilder($this->model(), $queries, false)
                ->orderBy($sort, $order)
                ->take($pageSize)
                ->skip($pageSize * ($page - 1))
                ->get();

            $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();

            $output = [];
            foreach ($results as $result) {
                $item = $result->toArray();
                
                $output[] = $item;
            }

            return $this->respondWithPaging($output, $totalCount, $pageSize, $page);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @SWG\Api(
     *   path="/backend/notify/notifications/{id}",
     *   @SWG\Operation(
     *      method="GET",
     *      summary="Get a Notification",
     *      nickname="getNotification",
     *      @SWG\Parameter(name="id", description="Notify Notification Id", required=true, type="integer", paramType="path", allowMultiple=false),
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     *
     * Show detail resource
     *
     * @param int $id
     * @return mixed|Response
     */
    public function show($id) {
        try {
            $model = $this->setUpQueryBuilder($this->model(), [], false)->where('id', $id)->first();

            if (!$model) {
                return $this->errorNotFound();
            }

            $model->is_read = true;
            $model->save();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @SWG\Model(id="NotifyNotificationModel")
     * @SWG\Property(name="place_id", type="integer", required=true, defaultValue=1),
     * @SWG\Property(name="title", type="string", required=false, defaultValue=1),
     * @SWG\Property(name="summary", type="string", required=false, defaultValue=""),
     * @SWG\Api(
     *   path="/backend/notify/notifications",
     *   @SWG\Operation(
     *      method="POST",
     *      summary="Create Notify Notification",
     *      nickname="createNotifyNotification",
     *      @SWG\Parameter(name="body", description="Request body", required=true, type="NotifyNotificationModel", paramType="body", allowMultiple=false),
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

            $place = $this->place_repository->find($input['place_id']);

            if ($place->user_id != $this->auth->id) {
                return $this->errorForbidden();
            }

            $model = $this->model_repository->create($input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @SWG\Model(id="NotifyNotificationUpdateModel")
     * @SWG\Property(name="title", type="string", required=false, defaultValue=1),
     * @SWG\Property(name="summary", type="string", required=false, defaultValue=""),
     * @SWG\Api(
     *   path="/backend/notify/notifications/{id}",
     *   @SWG\Operation(
     *      method="POST",
     *      summary="Update Notify Notification",
     *      nickname="updateNotifyNotification",
     *      @SWG\Parameter(name="id", description="Notify Notification Id", required=true, type="integer", paramType="path", allowMultiple=false),
     *      @SWG\Parameter(name="body", description="Request body", required=true, type="NotifyNotificationUpdateModel", paramType="body", allowMultiple=false),
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     *
     * Update resource
     *
     * @param int $id
     * @return mixed|Response
     */
    public function update($id) {
        try {
            $input = $this->request->only([
                'title',
                'summary'
            ]);

            $model = $this->model_repository->find($id);
            if (!$model) {
                return $this->errorNotFound();
            }

            $place = $model->place;

            if (!$place || ($place && $place->user_id != $this->auth->id)) {
                return $this->errorForbidden();
            }

            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) {
                return $this->respondWithError($validatorErrors);
            }

            // Update Model
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @SWG\Api(
     *   path="/backend/notify/notifications/{id}",
     *   @SWG\Operation(
     *      method="DELETE",
     *      summary="Delete a Notification",
     *      nickname="deleteNotification",
     *      @SWG\Parameter(name="id", description="Notify Notification Id", required=true, type="integer", paramType="path", allowMultiple=false),
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     *
     * Delete resource
     *
     * @param int $id
     * @return mixed|Response
     */
    public function destroy($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) {
                return $this->errorNotFound();
            }

            $place = $model->place;

            if ($place && $place->user_id != $this->auth->id) {
                return $this->errorForbidden();
            }

            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
