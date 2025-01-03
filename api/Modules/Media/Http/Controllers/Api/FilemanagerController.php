<?php namespace Modules\Media\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Repositories\FolderRepository;
use Modules\Media\Services\FileService;

/**
 * Class FilemanagerController
 *
 * @package Modules\Media\Http\Controllers\Api
 */
class FilemanagerController extends ApiBaseModuleController {
    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * @var \Modules\Media\Repositories\FolderRepository
     */
    protected $folder_repository;

    public function __construct(Request $request,
                                FileRepository $file_repository,
                                FolderRepository $folder_repository,
                                FileService $fileService) {
        $this->model_repository = $file_repository;
        $this->folder_repository = $folder_repository;
        $this->fileService = $fileService;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/backend/filemanager",
     *   summary="getFilemanager",
     *   operationId="getFilemanager",
     *   tags={"BackendMediaFilemanager"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="name"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="asc"),
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
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $data = $this->getRequestData();
            $folder_id = (isset($data->{'folder_id'}) && !is_null($data->{'folder_id'}) && $data->{'folder_id'} !== '') ? (int)$data->{'folder_id'} : 0;
            $output = [];
            $output['breadcrumbs'] = [];
            $output['tree'] = [['id' => 0, 'name' => 'Thư mục gốc']];
            $level = '-';
            if ($folder_id) {
                $output['breadcrumbs'][] = [
                    'text' => '/Root',
                    //'href' => $this->url->link('media/filemanager', 'user_token=' . $this->session->data['user_token'] . $url)
                ];
                $path = '';
                $parts = explode('_', $this->folder_repository->getPath($folder_id));
                foreach ($parts as $path_id) {
                    if (!$path) {
                        $path = (int)$path_id;
                    } else {
                        $path .= '_' . (int)$path_id;
                    }
                    $folder = $this->folder_repository->find($path_id);
                    if ($folder) {
                        $output['breadcrumbs'][] = [
                            'folder_id' => $folder->id,
                            'text'      => $folder->name,
                            //'href' => $this->url->link('media/filemanager', 'user_token=' . $this->session->data['user_token'] . '&folder_id=' . $path . $url)
                        ];
                        $output['tree'][] = array_merge($folder->toArray(), ['name' => $level . '&nbsp;' . $folder->name]);
                        $level .= '-';
                    }
                }
            }
            $children = $this->folder_repository->getModel()->where('parent_id', $folder_id)->select(['id', 'name'])->get();
            foreach ($children as $folder) {
                $output['tree'][] = array_merge($folder->toArray(), ['name' => $level . '&nbsp;' . $folder->name]);
            }
            $output['folders'] = [];
            $output['files'] = [];
            $queries = ['and' => [], 'orWhereRaw' => []];
            $queries['and'][] = ['parent_id', '=', $folder_id];
            // Query by keyword
            $q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(utf8_strtolower($q));
                $keys = ['name'];
                foreach ($keys as $key) {
                    $iQ = [];
                    $iB = [];
                    foreach ($arrQ as $i) {
                        $iQ[] = "LOWER(`$key`) LIKE ?";
                        $iB[] = "%$i%";
                    }
                    $queries['orWhereRaw'][] = ['(' . implode(' and ', $iQ) . ')', $iB];
                }
            }
            $offset = $pageSize;
            $folder_total = $this->setUpQueryBuilder($this->folder_repository->getModel(), $queries, true)->count();
            if ($folder_total > 0) {
                $results = $this->setUpQueryBuilder($this->folder_repository->getModel(), $queries, false)->orderBy('name', 'asc')->take($pageSize)->skip($pageSize * ($page - 1))->get();
                foreach ($results as $result) {
                    $output['folders'][] = array_merge($result->toArray(), [
                        'type' => 'directory',
                        //'href' => $this->url->link('media/filemanager', 'user_token=' . $this->session->data['user_token'] . '&folder_id=' . $result['id'] . $url)
                    ]);
                }
                $offset = $pageSize - count($output['folders']);
                $totalPage = ceil($folder_total / $pageSize);
                $totalPageStart = floor($folder_total / $pageSize);
                $remaining = $totalPage * $pageSize - $folder_total;
                $page2 = $page - $totalPageStart;
                if ($page2 < 1) $page2 = 1;
                $start = $pageSize * ($page2 - 1) - ($pageSize - $remaining);
                if ($start < 0) $start = 0;
            } else {
                $start = $pageSize * ($page - 1);
            }
            $queries = ['and' => [], 'orWhereRaw' => []];
            $queries['and'][] = ['folder_id', '=', $folder_id];
            // Query by keyword
            $q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(utf8_strtolower($q));
                $keys = ['filename'];
                foreach ($keys as $key) {
                    $iQ = [];
                    $iB = [];
                    foreach ($arrQ as $i) {
                        $iQ[] = "LOWER(`$key`) LIKE ?";
                        $iB[] = "%$i%";
                    }
                    $queries['orWhereRaw'][] = ['(' . implode(' and ', $iQ) . ')', $iB];
                }
            }
            $file_total = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, true)->count();
            if ($offset > 0) {
                $results = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false)->orderBy('id', 'desc')->take($offset)->skip($start)->get();
                foreach ($results as $result) {
                    $output['files'][] = array_merge($result->toArray(), [
                        'name' => $result->filename,
                        //'thumb' => media_url_file(html_entity_decode($result['path'], ENT_QUOTES, 'UTF-8'), 'small'),
                        //'href'  => media_url_file(html_entity_decode($result['path'], ENT_QUOTES, 'UTF-8'))
                    ]);
                }
            }

            $totalCount = $folder_total + $file_total;

            return $this->respondWithPaging($output, $totalCount, $pageSize, $page);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
