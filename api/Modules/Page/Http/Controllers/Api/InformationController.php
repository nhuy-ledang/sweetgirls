<?php namespace Modules\Page\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Core\Repositories\SeoUrlRepository;
use Modules\Page\Repositories\InformationDescRepository;
use Modules\Page\Repositories\InformationRepository;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;

/**
 * Class InformationController
 *
 * @package Modules\Blog\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2021/12/22
 */
class InformationController extends ApiBaseModuleController {
    /**
     * @var \Modules\Page\Repositories\InformationDescRepository
     */
    protected $information_desc_repository;

    /**
     * @var \Modules\Core\Repositories\SeoUrlRepository
     */
    protected $seo_url_repository;

    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    protected $file_repository;

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    public function __construct(Request $request,
                                InformationRepository $information_repository,
                                InformationDescRepository $information_desc_repository,
                                SeoUrlRepository $seo_url_repository,
                                FileRepository $file_repository,
                                FileService $fileService) {
        $this->model_repository = $information_repository;
        $this->information_desc_repository = $information_desc_repository;
        $this->seo_url_repository = $seo_url_repository;
        $this->file_repository = $file_repository;
        $this->fileService = $fileService;

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/backend/pg_informations",
     *   summary="Get Page Informations",
     *   operationId="getPageInformations",
     *   tags={"BackendPgInformations"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="page", in="query", required=false, description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", required=false, description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", required=false, description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", required=false, description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", required=false, description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
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
            $order = !$order ? 'desc' : strtolower($order);
            $queries = [
                'whereRaw'   => [],
                'orWhereRaw' => [],
            ];
            $data = $this->getRequestData();
            // Query by keyword
            $q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(utf8_strtolower($q));
                $keys = ['title', 'description'];
                foreach ($keys as $key) {
                    $iQ = [];
                    $iB = [];
                    foreach ($arrQ as $i) {
                        $iQ[] = "lower(`$key`) like ?";
                        $iB[] = "%$i%";
                    }
                    $queries['orWhereRaw'][] = ['(' . implode(' and ', $iQ) . ')', $iB];
                }
            }
            if (!$this->isCms()) {
                $visible = [];
            } else {
                $visible = [];
            }
            $results = $this->setUpQueryBuilder($this->model(), $queries)->orderBy($sort, $order)
                ->take($pageSize)
                ->skip($pageSize * ($page - 1))
                ->get();//->makeVisible($visible);
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

    /**
     * @OA\Get(
     *   path="/backend/pg_informations/{id}",
     *   summary="Get a Page Information",
     *   operationId="getPageInformation",
     *   tags={"BackendPgInformations"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="App-Env", in="query", required=false, description="ENV", example="cms"),
     *   @OA\Parameter(name="id", in="path", required=true, description="Information Id", example="1"),
     *   @OA\Parameter(name="data", in="query", required=false, description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
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
            $model = $this->setUpQueryBuilder($this->model(), [], false)->where('id', $id)->first();
            if (!$model) return $this->errorNotFound();
            /*if (!$this->isCms()) {
                $visible = [];
            } else {
                $visible = [];
            }
            $model->makeVisible($visible);*/

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * Update Seo Url
     * @param $id
     * @param $lang
     * @param $input
     * @return bool|\Illuminate\Support\Facades\Response|mixed
     */
    private function updateSeoUrl($id, $lang, &$input) {
        $alias = $this->request->get('alias');
        if ($alias) {
            $query = "information_id=$id";
            $seo_urls = $this->seo_url_repository->getSeoUrlsByKeyword($alias);
            $errorKey = '';
            foreach ($seo_urls as $item) {
                if (!($item->query == $query)) {
                    $errorKey = 'keyword.exists';
                    break;
                }
            }
            if ($errorKey) return [$errorKey, false];
            $seo_url = $this->seo_url_repository->getSeoUrlByQuery($query, $lang);
            if ($seo_url) {
                if ($seo_url->keyword != $alias && $seo_url->keyword != seo_url($alias)) {
                    $input['alias'] = seo_url($alias);
                    $this->seo_url_repository->getModel()->where('id', $seo_url->id)->update(['keyword' => $input['alias']]);
                } else {
                    $input['alias'] = $seo_url->keyword;
                }
            } else {
                $input['alias'] = seo_url($alias);
                $seo_url = $this->seo_url_repository->create(['lang' => $lang, 'query' => $query, 'keyword' => $input['alias'], 'push' => "route=page/information&$query"]);
            }
            return [$errorKey, $seo_url];
        }
        return [false, false];
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_informations",
     *   summary="Create Page Information",
     *   operationId="createPageInformation",
     *   tags={"BackendPgInformations"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="title", type="string", example=""),
     *       @OA\Property(property="description", type="string", example=""),
     *       @OA\Property(property="status", type="integer", example="1"),
     *     ),
     *   ),
     *   @OA\Parameter(name="App-Env", in="query", description="ENV", example="cms"),
     *   @OA\Parameter(name="Device-Platform", in="query", description="ENV", example="web"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function store() {
        try {
            $input = $this->request->only(['title', 'description', 'short_description', 'meta_title', 'meta_description', 'meta_keyword', 'top', 'image', 'image_alt', 'sort_order', 'status', 'alias']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Check seo url exist
            $alias = $this->request->get('alias');
            if ($alias) {
                $seo_urls = $this->seo_url_repository->getSeoUrlsByKeyword($alias);
                if ($seo_urls->count() > 0) return $this->errorWrongArgs('keyword.exists');
            }
            // Create model
            $model = $this->model_repository->create($input);
            // Update seo url
            $input = [];
            list($errorKey, $seo_url) = $this->updateSeoUrl($model->id, 'vi', $input);
            if (!$errorKey && !empty($input)) $model = $this->model_repository->update($model, $input);
            // Upload avatar
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $model = $this->model_repository->update($model, ['image' => $file_path]);
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                //if ($errorKey) return $this->errorWrongArgs($errorKey);
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => 'inf', 'object_id' => $model->id]);
                    if (!is_string($savedFile)) $model = $this->model_repository->update($model, ['image' => $savedFile->path]);
                }
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/pg_informations/{id}",
     *   summary="Update Page Information",
     *   operationId="updatePageInformation",
     *   tags={"BackendPgInformations"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Information Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="title", type="string", example=""),
     *       @OA\Property(property="description", type="string", example=""),
     *       @OA\Property(property="status", type="integer", example="1"),
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
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['title', 'description', 'short_description', 'meta_title', 'meta_description', 'meta_keyword', 'top', 'image', 'image_alt', 'sort_order', 'status', 'alias']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Update seo url
            list($errorKey, $seo_url) = $this->updateSeoUrl($id, 'vi', $input);
            if ($errorKey) return $this->errorWrongArgs($errorKey);
            // Upload avatar
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $input['image'] = $file_path;
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                if ($errorKey) return $this->errorWrongArgs($errorKey);
                if ($file) {
                    // Unlink old image
                    $oldFile = null;
                    //if ($model->image) $oldFile = $this->file_repository->findByAttributes(['object' => 'inf', 'path' => $model->image]);
                    // New image
                    $savedFile = $this->fileService->store($file, ['sub' => 'inf', 'object_id' => $model->id]);
                    if (!is_string($savedFile)) {
                        $input['image'] = $savedFile->path;
                        // Unlink old image
                        //if ($oldFile) $this->file_repository->destroy($oldFile);
                    }
                }
            }
            // Update Model
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *   path="/backend/pg_informations/{id}",
     *   summary="Update Page Information Partial",
     *   operationId="UpdatePageInformationPartial",
     *   tags={"BackendPgInformations"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Information Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example=1),
     *       @OA\Property(property="top", type="integer", example=1),
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
    public function patch($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['status']);
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/pg_informations/{id}",
     *   summary="Delete Page Information",
     *   operationId="deletePageInformation",
     *   tags={"BackendPgInformations"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Information Id", example=1),
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
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_informations/{id}/description",
     *   summary="Update Page Information Description",
     *   operationId="updatePageInformationdescription",
     *   tags={"BackendPgInformations"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Page Information Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="lang", type="string", example="en"),
     *       @OA\Property(property="meta_title", type="string", example=""),
     *       @OA\Property(property="meta_description", type="string", example=""),
     *       @OA\Property(property="meta_keyword", type="string", example=""),
     *       @OA\Property(property="alias", type="string", example=""),
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
    public function description($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['lang', 'title', 'meta_title', 'meta_description', 'meta_keyword', 'description', 'short_description']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, ['lang' => 'required', 'title' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $lang = $this->request->get('lang');
            list($errorKey, $seo_url) = $this->updateSeoUrl($id, $lang, $input);
            if ($errorKey) return $this->errorWrongArgs($errorKey);
            if ($lang == 'vi') {
                //$model = $this->model_repository->update($model, $input);
            } else {
                $modelDesc = $this->information_desc_repository->findByAttributes(['id' => $id, 'lang' => $lang]);
                if (!$modelDesc) {
                    $modelDesc = $this->information_desc_repository->create(array_merge($input, ['id' => $id, 'lang' => $lang]));
                } else {
                    $modelDesc = $this->information_desc_repository->update($modelDesc, $input);
                }
                $translates = $model->translates ? $model->translates : [];
                $translates[] = $lang;
                $translates = array_unique($translates);
                $model->translates = $translates;
                $model->save();
            }
            $model->descs;
            $model->makeVisible(['meta_title', 'meta_description', 'meta_keyword', 'description', 'image', 'translates', 'alias', 'status']);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
