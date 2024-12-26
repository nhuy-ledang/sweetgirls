<?php namespace Modules\System\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\System\Repositories\SupportRepository;

/**
 * Class SupportController
 * @package Modules\System\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 9/10/2018 4:37 PM
 *
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   resourcePath="/SystemSupports",
 *   description="System Supports Api"
 * )
 */
class SupportController extends ApiBaseModuleController {
    public function __construct(Request $request, SupportRepository $message_repository) {
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
            'parent_id'  => 'integer',
            'service_id' => 'integer',
            'title'      => 'required',
            'message'    => 'required',
            'to'         => 'integer|exists:users,id',
        ];
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForReply() {
        return [
            'message' => 'required',
            'to'      => 'integer|exists:users,id',
        ];
    }

    /**
     * @SWG\Api(
     *   path="/backend/system/supports",
     *   @SWG\Operation(
     *      method="GET",
     *      summary="Get Supports",
     *      nickname="getSupports",
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
                'and' => [
                    ['parent_id', '=', 0]
                ],
                'or'  => [
                    ['from', '=', $user_id],
                    ['to', '=', $user_id],
                ]
            ];

            $results = $this->setUpQueryBuilder($this->model(), $queries, false)
                ->orderBy('id', 'desc')
                ->take($pageSize)
                ->skip($pageSize * ($page - 1))
                ->get();

            $ids = [];

            $output = [];
            //for ($i = count($results) - 1; 0 <= $i; $i--) {
            //    $item = $results[$i];
            foreach ($results as $item) {
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
     * @SWG\Api(
     *   path="/backend/system/supports/{id}",
     *   @SWG\Operation(
     *      method="GET",
     *      summary="Get a Support",
     *      nickname="getSupport",
     *      @SWG\Parameter(name="id", description="Support Id", required=true, type="integer", paramType="path", allowMultiple=false),
     *      @SWG\Parameter(name="data", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", required=false, type="string", paramType="query", allowMultiple=false, defaultValue="%7B%22embed%22%3A%22fromUser%2CtoUser%22%7D"),
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     */
    public function show($id) {
        try {
            $model = $this->setUpQueryBuilder($this->model(), [], false)->where('id', $id)->first();
            if (!$model) {
                return $this->errorNotFound();
            }
            $queries = ['and' => [['parent_id', '=', $id]]];
            $results = $this->setUpQueryBuilder($this->model(), $queries, false)->orderBy('id', 'desc')->get();
            $items = [];
            for ($i = count($results) - 1; 0 <= $i; $i--) {
                $item = $results[$i];
            //foreach ($results as $item) {
                $item->owner = ($item->from == $this->auth->id);
                $items[] = $item;
            }
            $model->owner = ($model->from == $this->auth->id);
            $model->items = $items;
            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @SWG\Model(id="SupportModel")
     * @SWG\Property(name="service_id", type="integer", required=false, defaultValue=0),
     * @SWG\Property(name="to", type="integer", required=false, defaultValue=0),
     * @SWG\Property(name="title", type="string", required=true, defaultValue=""),
     * @SWG\Property(name="message", type="string", required=true, defaultValue=""),
     * @SWG\Property(name="files", type="string", required=true, defaultValue=""),
     * @SWG\Api(
     *   path="/backend/system/supports",
     *   @SWG\Operation(
     *      method="POST",
     *      summary="Create Support",
     *      nickname="createSupport",
     *      @SWG\Parameter(name="service_id", description="Service Id", required=false, type="integer", paramType="form", defaultValue=1),
     *      @SWG\Parameter(name="to", description="To", required=false, type="integer", paramType="form", defaultValue=1),
     *      @SWG\Parameter(name="title", description="title", required=false, type="string", paramType="form", defaultValue="Title"),
     *      @SWG\Parameter(name="message", description="Message", required=false, type="string", paramType="form", defaultValue="Message"),
     *      @SWG\Parameter(name="file", description="File image", required=false, type="file", paramType="form", allowMultiple=false),
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     */
    public function store() {
        try {
            $input = $this->request->only(['service_id', 'to', 'title', 'message']);

            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) {
                return $this->respondWithError($validatorErrors);
            }
            list($files, $errorKeys) = $this->getRequestFiles(false, '*');
            if ($files) {
                //=== Check extension
                $newFiles = [];
                $mines = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'application/zip', 'application/x-7z-compressed', 'application/x-tar', 'application/gzip', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/pdf'];
                foreach ($files as $file) {
                    if (!in_array($file->getMimeType(), $mines)) {
                        $errorKeys[] = 'file.mime';
                    } else {
                        $newFiles[] = $file;
                    }
                }
                $files = $newFiles;
            }
            $attaches = [];
            if ($files) {
                if ($errorKeys) {
                    return $this->errorWrongArgs($errorKeys[0]);
                }
                foreach ($files as $file) {
                    $path = "/attaches/{$this->auth->id}/" . date('Ymd') . "/" . date('His') . \Modules\Media\Helpers\FileHelper::slug($file->getClientOriginalName());
                    $ok = \Storage::disk(config('filesystems.default'))
                        ->getDriver()->put($path, fopen($file, 'r+'), [
                            'visibility'  => 'public',
                            'ContentType' => $file->getMimeType()
                        ]);
                    if ($ok) {
                        $attaches[] = $path;
                    }
                }
            }
            if ($attaches) $input['attaches'] = $attaches;

            // Create Model
            $model = $this->model_repository->create(array_merge($input, ['from' => $this->auth->id]));

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @SWG\Api(
     *   path="/backend/system/supports/{id}",
     *   @SWG\Operation(
     *      method="POST",
     *      summary="Create a Support Reply",
     *      nickname="createSupportreply",
     *      @SWG\Parameter(name="id", description="Support Id", required=true, type="integer", paramType="path", allowMultiple=false),
     *      @SWG\Parameter(name="service_id", description="Service Id", required=false, type="integer", paramType="form", defaultValue=1),
     *      @SWG\Parameter(name="to", description="To", required=false, type="integer", paramType="form", defaultValue=1),
     *      @SWG\Parameter(name="title", description="title", required=false, type="string", paramType="form", defaultValue="Title"),
     *      @SWG\Parameter(name="message", description="Message", required=false, type="string", paramType="form", defaultValue="Message"),
     *      @SWG\Parameter(name="file", description="File image", required=false, type="file", paramType="form", allowMultiple=false),
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     */
    public function reply($id) {
        try {
            $support = $this->model_repository->findByAttributes(['id' => $id, 'parent_id' => 0]);
            if (!$support) {
                return $this->errorNotFound();
            }
            $input = $this->request->only(['message']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForReply());
            if (!empty($validatorErrors)) {
                return $this->respondWithError($validatorErrors);
            }
            $input['parent_id'] = $support->id;
            $input['to'] = $support->from;
            $input['service_id'] = $support->service_id;
            $input['title'] = $support->title;

            list($files, $errorKeys) = $this->getRequestFiles(false, '*');
            if ($files) {
                //=== Check extension
                $newFiles = [];
                $mines = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'application/zip', 'application/x-7z-compressed', 'application/x-tar', 'application/gzip', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/pdf'];
                foreach ($files as $file) {
                    if (!in_array($file->getMimeType(), $mines)) {
                        $errorKeys[] = 'file.mime';
                    } else {
                        $newFiles[] = $file;
                    }
                }
                $files = $newFiles;
            }
            $attaches = [];
            if ($files) {
                if ($errorKeys) {
                    return $this->errorWrongArgs($errorKeys[0]);
                }
                foreach ($files as $file) {
                    $path = "/attaches/{$this->auth->id}/" . date('Ymd') . "/" . date('His') . \Modules\Media\Helpers\FileHelper::slug($file->getClientOriginalName());
                    $ok = \Storage::disk(config('filesystems.default'))
                        ->getDriver()->put($path, fopen($file, 'r+'), [
                            'visibility'  => 'public',
                            'ContentType' => $file->getMimeType()
                        ]);
                    if ($ok) {
                        $attaches[] = $path;
                    }
                }
            }
            if ($attaches) $input['attaches'] = $attaches;

            // Create Model
            $model = $this->model_repository->create(array_merge($input, ['from' => $this->auth->id]));

            // Update replied
            $support->readed = 1;
            $support->replied = 1;
            $support->save();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @SWG\Api(
     *   path="/backend/system/supports_unread",
     *   @SWG\Operation(
     *      method="GET",
     *      summary="Get Supports Unread",
     *      nickname="getSupportsUnread",
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
