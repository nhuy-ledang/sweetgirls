<?php namespace Modules\Media\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Repositories\FolderRepository;

/**
 * Class FolderController
 * @package Modules\Media\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 9/10/2018 4:37 PM
 */
class FolderController extends ApiBaseModuleController {
    protected $maximumLimit = 20;

    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    protected $file_repository;

    public function __construct(Request $request,
                                FolderRepository $folder_repository,
                                FileRepository $file_repository) {
        $this->model_repository = $folder_repository;
        $this->file_repository = $file_repository;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     * @return array
     */
    protected function rulesForCreate() {
        return [
            'name' => 'required',
        ];
    }

    /**
     * Get the validation rules for update.
     * @param int $id
     * @return array
     */
    protected function rulesForUpdate($id) {
        return [
            'name' => 'required',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/media_folders_all",
     *   summary="getFoldersAll",
     *   operationId="getFoldersAll",
     *   tags={"BackendMediaFolders"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */

    /**
     * @OA\Get(
     *   path="/backend/media_folders",
     *   summary="getFolders",
     *   operationId="getFolders",
     *   tags={"BackendMediaFolders"},
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
            $queries = [
                'or'         => [],
                'orWhereRaw' => []
            ];
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
            $results = $this->setUpQueryBuilder($this->model(), $queries, false)
                ->orderBy('name', 'asc')->take($pageSize)
                ->skip($pageSize * ($page - 1))
                ->get();

            $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();

            return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/media_folders/{id}",
     *   summary="getFolder",
     *   operationId="getFolder",
     *   tags={"BackendMediaFolders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Folder Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */

    /**
     * @OA\Post(
     *   path="/backend/media_folders",
     *   summary="Create Folder",
     *   operationId="createFolder",
     *   tags={"BackendMediaFolders"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="parent_id", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function store() {
        try {
            // Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), $this->rulesForCreate());
            if (!empty($validatorErrors)) {
                return $this->respondWithError($validatorErrors);
            }
            $name = trim((string)$this->request->get('name'));
            $parent_id = $this->request->get('parent_id');
            $parent_id = is_null($parent_id) ? 0 : (int)$parent_id;
            if ($parent_id) {
                $parent = $this->model_repository->find($parent_id);
                if (!$parent) {
                    return $this->errorWrongArgs('folder_id.invalid');
                }
            }
            // Check exists
            $model = $this->model_repository->getModel()->where('parent_id', $parent_id)->whereRaw('LOWER(`name`) LIKE ?', [utf8_strtolower($name)])->first();
            if ($model) {
                return $this->errorWrongArgs('folder_id.exists');
            }
            $model = $this->model_repository->create(['name' => $name, 'parent_id' => $parent_id]);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/media_folders/{id}",
     *   summary="Update Folder",
     *   operationId="updateFolder",
     *   tags={"BackendMediaFolders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Folder Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Parameter(name="App-Env", in="query", description="ENV", example="cms"),
     *   @OA\Parameter(name="Device-Platform", in="query", description="ENV", example="web"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function update($id) {
        try {
            // Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $name = trim((string)$this->request->get('name'));
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Check exists
            $m = $this->model_repository->getModel()->whereRaw('LOWER(`name`) LIKE ?', [utf8_strtolower($name)])->first();
            if ($m && $m->id != $model->id) {
                return $this->errorWrongArgs('folder_id.exists');
            }
            // Update Model
            $model = $this->model_repository->update($model, ['name' => $name]);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/media_folders/{id}",
     *   summary="Delete Folder",
     *   operationId="deleteFolder",
     *   tags={"BackendMediaFolders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Folder Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Update file
            $this->file_repository->getModel()->where('folder_id', $id)->update(['folder_id' => 0]);
            // Destroy
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
