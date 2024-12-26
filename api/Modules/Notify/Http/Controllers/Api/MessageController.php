<?php namespace Modules\Notify\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Notify\Repositories\MessageRepository;

/**
 * Class MessageController
 * @package Modules\Notify\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 9/10/2018 4:37 PM
 *
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   resourcePath="/NotifyMessages",
 *   description="Notify Messages Api"
 * )
 */
class MessageController extends ApiBaseModuleController {
    public function __construct(Request $request, MessageRepository $message_repository) {
        $this->model_repository = $message_repository;

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
            'message' => 'required',
            'to'      => 'integer|exists:users,id'
        ];
    }

    /**
     * @SWG\Api(
     *   path="/backend/notify/messages",
     *   @SWG\Operation(
     *      method="GET",
     *      summary="Get Messages",
     *      nickname="getMessages",
     *      @SWG\Parameter(name="page", description="Current Page", required=true, type="integer", paramType="query", allowMultiple=false, defaultValue="1"),
     *      @SWG\Parameter(name="pageSize", description="Item total on page", required=true, type="integer", paramType="query", allowMultiple=false, defaultValue="20"),
     *      @SWG\Parameter(name="sort", description="Sort by", required=false, type="string", paramType="query", allowMultiple=false, defaultValue="id"),
     *      @SWG\Parameter(name="order", description="Order", required=false, type="string", paramType="query", allowMultiple=false, defaultValue="desc"),
     *      @SWG\Parameter(name="data", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", required=false, type="string", paramType="query", allowMultiple=false, defaultValue="%7B%22embed%22%3A%22fromUser%2CtoUser%22%7D"),
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
                $order = 'desc';
            } else {
                $order = strtoupper($order);
            }
            $sort = 'id';
            $order = 'desc';

            $data = $this->getRequestData();
            $user_id = isset($data->{'user_id'}) ? (int)$data->{'user_id'} : $this->auth->id;

            $queries = [
                'or' => [
                    ['from', '=', $user_id],
                    ['to', '=', $user_id],
                ]
            ];

            $results = $this->setUpQueryBuilder($this->model(), $queries, false)
                ->orderBy($sort, $order)
                ->take($pageSize)
                ->skip($pageSize * ($page - 1))
                ->get();

            $ids = [];

            $output = [];
            for ($i = count($results) - 1; 0 <= $i; $i--) {
                $item = $results[$i];
                $item->owner = ($item->from == $this->auth->id);
                if (!$item->owner && !$item->readed) {
                    $ids[] = $item->id;
                }
                $output[] = $item;
            }

            if (!isset($data->{'user_id'})) {
                if (!empty($ids)) $this->model_repository->getModel()->whereIn('id', $ids)->update(['readed' => 1]);
            }

            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @SWG\Model(id="MessageModel")
     * @SWG\Property(name="to", type="integer", required=false, defaultValue=0),
     * @SWG\Property(name="message", type="string", required=true, defaultValue=""),
     * @SWG\Api(
     *   path="/backend/notify/messages",
     *   @SWG\Operation(
     *      method="POST",
     *      summary="Create Message",
     *      nickname="createMessage",
     *      @SWG\Parameter(name="body", description="Request body", required=true, type="MessageModel", paramType="body", allowMultiple=false),
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

            // Create Model
            $model = $this->model_repository->create(array_merge($input, ['from' => $this->auth->id]));

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @SWG\Api(
     *   path="/backend/notify/messages_unread",
     *   @SWG\Operation(
     *      method="GET",
     *      summary="Get Messages Unread",
     *      nickname="getMessagesUnread",
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     */
    public function getUnread() {
        try {
            $queries = [
                'and' => [
                    ['to', '=', $this->auth->id],
                    ['readed', '=', 0],
                ]
            ];

            $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();

            return $this->respondWithSuccess($totalCount);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
