<?php namespace Modules\Usr\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Usr\Repositories\ActivityRepository;

/**
 * Class ActivityController
 *
 * @package Modules\Usr\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2022-10-24
 */
class ActivityController extends ApiBaseModuleController {
    /**
     * @var string
     */
    private $module_id = 'usrs';

    public function __construct(Request $request,
                                ActivityRepository $activity_repository) {
        $this->model_repository = $activity_repository;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/backend/usr_activities",
     *   summary="Get Usr Activities",
     *   operationId="getUsrActivities",
     *   tags={"BackendUsrActivities"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="desc"),
     *   @OA\Parameter(name="paging", description="With Paging", in="query", example="0"),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields, extend_fields: avgReview} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function index() {
        try {
            // Check permission
            //if (!($this->isView($this->module_id) || $this->isViewOwn($this->module_id))) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'id' : strtolower($sort);
            $order = (string)$this->request->get('order');
            $order = !$order ? 'desc' : strtolower($order);
            $queries = ['and' => [
                ['usr_id', '=', $this->auth->id],
            ]];
            /*$data = $this->getRequestData();
            $project_id = (isset($data->{'project_id'}) && intval($data->{'project_id'})) ? intval($data->{'project_id'}) : false;
            if ($project_id) $queries['and'][] = ['project_id', '=', $project_id];
            $task_id = (isset($data->{'task_id'}) && intval($data->{'task_id'})) ? intval($data->{'task_id'}) : false;
            if ($task_id) $queries['and'][] = ['task_id', '=', $task_id];*/
            $results = $this->setUpQueryBuilder($this->model(), $queries)->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'false' ? false : ($paging == 'true' ? true : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($results);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true);
                $totalCount = $totalCount->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
