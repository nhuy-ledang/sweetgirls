<?php namespace Modules\Page\Http\Controllers\Api;

use Imagy;
use Illuminate\Http\Request;
use Modules\Media\Helpers\FileHelper;
use Modules\Page\Repositories\LayoutRepository;
use Modules\Page\Repositories\LayoutPatternDescRepository;
use Modules\Page\Repositories\LayoutPatternRepository;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;

/**
 * Class LayoutPatternController
 *
 * @package Modules\Page\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2022-10-05
 */
class LayoutPatternController extends ApiBaseModuleController {
    /**
     * @var \Modules\Page\Repositories\LayoutPatternDescRepository
     */
    protected $layout_pattern_desc_repository;

    /**
     * @var \Modules\Page\Repositories\LayoutRepository
     */
    protected $layout_repository;

    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    protected $file_repository;

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    public function __construct(Request $request,
                                LayoutPatternRepository $layout_pattern_repository,
                                LayoutPatternDescRepository $layout_pattern_desc_repository,
                                LayoutRepository $layout_repository,
                                FileRepository $file_repository,
                                FileService $fileService) {
        $this->model_repository = $layout_pattern_repository;
        $this->layout_pattern_desc_repository = $layout_pattern_desc_repository;
        $this->layout_repository = $layout_repository;
        $this->file_repository = $file_repository;
        $this->fileService = $fileService;

        $this->middleware('auth.usr')->except(['index', 'show']);

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreate() {
        return [
            'name' => 'required',
            'code' => 'required|unique:pg__modules,code',
        ];
    }

    /**
     * Get the validation rules for update.
     *
     * @param int $id
     * @return array
     */
    protected function rulesForUpdate($id) {
        return [
            'name' => 'required',
        ];
    }

    protected function getResultModel($model) {
        $model = $this->getModuleResultModel($model);
        // Preview
        $model->preview_url = config('app.url') . "/module/preview?lpid={$model->id}&ly={$model->layout}";

        return $model;
    }

    /**
     * @OA\Get(
     *   path="/backend/pg_layout_patterns",
     *   summary="Get Page Layout Patterns",
     *   operationId="getPageLayoutPatterns",
     *   tags={"BackendPgLayoutPatterns"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", required=false, description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", required=false, description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", required=false, description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", required=false, description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", required=false, description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", required=false, description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example="%7B%22layout_id%22%3A0%7D"),
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
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $order = (string)$this->request->get('order');
            $sort = !$sort ? 'name' : strtolower($sort);
            $order = !$order ? 'asc' : strtolower($order);
            $queries = [
                'and'        => [],
                'whereRaw'   => [],
                'orWhereRaw' => [],
            ];
            $data = $this->getRequestData();
            // Query by keyword
            $q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(utf8_strtolower($q));
                $keys = ['name'];
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
            $fields = ['*', \DB::raw("(select `cf_data` from `pg__modules` where `code` = `pg__layout_patterns`.`code`) as cf_data")];
            $results = $this->setUpQueryBuilder($this->model(), $queries, false, $fields)->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
            $output = [];
            foreach ($results as $result) {
                $output[] = $this->getResultModel($result);
            }
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging === 'true' ? true : ($paging === 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($output);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();
                return $this->respondWithPaging($output, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pg_layout_patterns/{id}",
     *   summary="Get Page Layout Pattern",
     *   operationId="getPageLayoutPattern",
     *   tags={"BackendPgLayoutPatterns"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, description="Layout Pattern Id", example="1"),
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
            $model = $this->getResultModel($model);
            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_layout_patterns",
     *   summary="Create Page Layout Pattern",
     *   operationId="createPageLayoutPatterns",
     *   tags={"BackendPgLayoutPatterns"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="layout_id", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="code", type="string", example=""),
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
            $input = $this->request->only(['layout_id', 'name', 'code', 'title', 'sub_title', 'short_description', 'description', 'is_overwrite', 'layout', 'tile', 'image', 'attach', 'menu_text', 'btn_text', 'btn_link']);
            $module_id = $this->request->get('module_id');
            $input['module_id'] = intval($module_id) ? (int)$module_id : null;
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $input['properties'] = $this->getRequestProperties();
            $input['table_contents'] = $this->getRequestTableContents();
            $input['table_images'] = $this->getRequestTableImages();
            // Upload attach file
            $attach_file = $this->request->file('attach_file');
            if ($attach_file) {
                $attachData = $this->uploadAttachFile($attach_file);
                if ($attachData) $input['attach'] = $attachData['path'];
            }
            // Create model
            $model = $this->model_repository->create($input);
            // Upload image
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $model = $this->model_repository->update($model, ['image' => $file_path]);
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                if ($errorKey) return $this->errorWrongArgs($errorKey);
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => 'pgc', 'object_id' => $model->id]);
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
     *   path="/backend/pg_layout_patterns/{id}",
     *   summary="Update Page Layout Pattern",
     *   operationId="updatePageLayoutPattern",
     *   tags={"BackendPgLayoutPatterns"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Layout Pattern Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="code", type="string", example=""),
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
    public function update($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['name', 'title', 'sub_title', 'short_description', 'description', 'is_overwrite', 'layout', 'tile', 'image', 'attach', 'menu_text', 'btn_text', 'btn_link']);
            $module_id = $this->request->get('module_id');
            $input['module_id'] = intval($module_id) ? (int)$module_id : null;
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $input['properties'] = $this->getRequestProperties();
            $input['table_contents'] = $this->getRequestTableContents();
            $input['table_images'] = $this->getRequestTableImages();
            // Upload attach file
            $attach_file = $this->request->file('attach_file');
            if ($attach_file) {
                $attachData = $this->uploadAttachFile($attach_file);
                if ($attachData) $input['attach'] = $attachData['path'];
            }
            // Upload image
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $input['image'] = $file_path;
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                if ($errorKey) return $this->errorWrongArgs($errorKey);
                if ($file) {
                    // Unlink old image
                    $oldFile = null;
                    //if ($model->image) $oldFile = $this->file_repository->findByAttributes(['object' => 'pgc', 'path' => $model->image]);
                    // New image
                    $savedFile = $this->fileService->store($file, ['sub' => 'pgc', 'object_id' => $model->id]);
                    if (!is_string($savedFile)) {
                        $input['image'] = $savedFile->path;
                        // Unlink old avatar
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
     * @OA\Delete(
     *   path="/backend/pg_layout_patterns/{id}",
     *   summary="Delete Page Layout Pattern",
     *   operationId="deletePageLayoutPattern",
     *   tags={"BackendPgLayoutPatterns"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Layout Pattern Id", example=1),
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
     *   path="/backend/pg_layout_patterns/{id}/description",
     *   summary="Update Layout Pattern Description",
     *   operationId="updatePageLayoutPatternDescription",
     *   tags={"BackendPgLayoutPatterns"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Layout Pattern Description Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="lang", type="string", example="en"),
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="description", type="string", example=""),
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
    public function description($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['lang', 'name', 'title', 'sub_title', 'short_description', 'description', 'menu_text', 'btn_text', 'btn_link']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, ['lang' => 'required', 'name' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $input['table_contents'] = $this->getRequestTableContents();
            $input['table_images'] = $this->getRequestTableImages();
            $lang = $this->request->get('lang');
            if ($lang == 'vi') {
                //$model = $this->model_repository->update($model, $input);
            } else {
                $modelDesc = $this->layout_pattern_desc_repository->findByAttributes(['id' => $id, 'lang' => $lang]);
                if (!$modelDesc) {
                    $modelDesc = $this->layout_pattern_desc_repository->create(array_merge($input, ['id' => $id, 'lang' => $lang]));
                } else {
                    $modelDesc = $this->layout_pattern_desc_repository->update($modelDesc, $input);
                }
                $translates = $model->translates ? $model->translates : [];
                $translates[] = $lang;
                $translates = array_unique($translates);
                $model->translates = $translates;
                $model->save();
            }
            $model->descs;

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_layout_patterns/{id}/images",
     *   summary="Update Page Layout Pattern Images",
     *   operationId="updatePageLayoutPatternImages",
     *   tags={"BackendPgLayoutPatterns"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Layout Pattern Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
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
    public function updateImages($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = [];
            $table_images = (string)$this->request->get('table_images');
            $table_images = json_decode($table_images, true);
            if (!empty($table_images)) {
                $newVal = [];
                foreach ($table_images as $k => $value) {
                    $file_path = $this->request->get("filepath_$k");
                    if ($file_path) {
                        $value['image'] = $file_path;
                    } else {
                        list($file, $errorKey) = $this->getRequestFile("file_$k");
                        if ($file) {
                            $savedFile = $this->fileService->store($file, ['sub' => trim($this->module_prefix, '_')]);
                            if (!is_string($savedFile)) $value['image'] = $savedFile->path;
                        }
                    }
                    $newVal[] = $value;
                }
                if ($newVal) $input['table_images'] = $newVal;
            }
            // Update Model
            if ($input) $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
