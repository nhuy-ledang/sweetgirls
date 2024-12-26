<?php namespace Modules\Page\Http\Controllers\Api;

use Imagy;
use Illuminate\Http\Request;
use Modules\Media\Helpers\FileHelper;
use Modules\Page\Repositories\LayoutPatternDescRepository;
use Modules\Page\Repositories\LayoutPatternRepository;
use Modules\Page\Repositories\LayoutRepository;
use Modules\Page\Repositories\LayoutModuleDescRepository;
use Modules\Page\Repositories\LayoutModuleRepository;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\Page\Repositories\ModuleRepository;

/**
 * Class LayoutModuleController
 *
 * @package Modules\Page\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2022-07-02
 */
class LayoutModuleController extends ApiBaseModuleController {
    /**
     * @var \Modules\Page\Repositories\LayoutModuleDescRepository
     */
    protected $layout_module_desc_repository;

    /**
     * @var \Modules\Page\Repositories\LayoutRepository
     */
    protected $layout_repository;

    /**
     * @var \Modules\Page\Repositories\LayoutPatternRepository
     */
    protected $layout_pattern_repository;

    /**
     * @var \Modules\Page\Repositories\LayoutPatternDescRepository
     */
    protected $layout_pattern_desc_repository;

    /**
     * @var \Modules\Page\Repositories\ModuleRepository
     */
    protected $module_repository;

    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    protected $file_repository;

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    public function __construct(Request $request,
                                LayoutModuleRepository $layout_module_repository,
                                LayoutModuleDescRepository $layout_module_desc_repository,
                                LayoutRepository $layout_repository,
                                LayoutPatternRepository $layout_pattern_repository,
                                LayoutPatternDescRepository $layout_pattern_desc_repository,
                                ModuleRepository $module_repository,
                                FileRepository $file_repository,
                                FileService $fileService) {
        $this->model_repository = $layout_module_repository;
        $this->layout_module_desc_repository = $layout_module_desc_repository;
        $this->layout_repository = $layout_repository;
        $this->layout_pattern_repository = $layout_pattern_repository;
        $this->layout_pattern_desc_repository = $layout_pattern_desc_repository;
        $this->module_repository = $module_repository;
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
        $model->preview_url = config('app.url') . "/module/preview?lmid={$model->id}&ly={$model->layout}";

        return $model;
    }

    /**
     * @OA\Get(
     *   path="/backend/pg_layout_modules",
     *   summary="Get Page Layout Modules",
     *   operationId="getPageLayoutModules",
     *   tags={"BackendPgLayoutModules"},
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
            $data = $this->getRequestData();
            $layout_id = !empty($data->{'layout_id'}) ? (int)$data->{'layout_id'} : 0;
            $queries = [
                'and' => [
                    ['layout_id', '=', $layout_id],
                ],
            ];
            $fields = ['*', \DB::raw("(select `cf_data` from `pg__modules` where `code` = `pg__layout_modules`.`code`) as cf_data")];
            $results = $this->setUpQueryBuilder($this->model(), $queries, false, $fields)->orderBy('sort_order', 'asc')->get();
            $output = [];
            foreach ($results as $result) {
                $output[] = $this->getResultModel($result);
            }

            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pg_layout_modules/{id}",
     *   summary="Get Page Layout Module",
     *   operationId="getPageLayoutModule",
     *   tags={"BackendPgLayoutModules"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, description="Layout Module Id", example="1"),
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
     *   path="/backend/pg_layout_modules",
     *   summary="Create Page Layout Module",
     *   operationId="createPageLayoutModules",
     *   tags={"BackendPgLayoutModules"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="layout_id", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="code", type="string", example=""),
     *       @OA\Property(property="sort_order", type="integer", example=0),
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
            $input = $this->request->only(['layout_id', 'name', 'code', 'title', 'sub_title', 'short_description', 'description', 'is_overwrite', 'layout', 'tile', 'image', 'attach', 'menu_text', 'btn_text', 'btn_link', 'sort_order']);
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
                //if ($errorKey) return $this->errorWrongArgs($errorKey);
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
     *   path="/backend/pg_layout_modules/{id}",
     *   summary="Update Page Layout Module",
     *   operationId="updatePageLayoutModule",
     *   tags={"BackendPgLayoutModules"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Layout Module Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="code", type="string", example=""),
     *       @OA\Property(property="sort_order", type="integer", example=0),
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
            $input = $this->request->only(['name', 'title', 'sub_title', 'short_description', 'description', 'is_overwrite', 'layout', 'tile', 'image', 'attach', 'menu_text', 'btn_text', 'btn_link', 'sort_order']);
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
     *   path="/backend/pg_layout_modules/{id}",
     *   summary="Delete Page Layout Module",
     *   operationId="deletePageLayoutModule",
     *   tags={"BackendPgLayoutModules"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Layout Module Id", example=1),
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
     *   path="/backend/pg_layout_modules/{id}/description",
     *   summary="Update Layout Module Description",
     *   operationId="updatePageLayoutModuleDescription",
     *   tags={"BackendPgLayoutModules"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Layout Module Description Id", example="1"),
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
                $modelDesc = $this->layout_module_desc_repository->findByAttributes(['id' => $id, 'lang' => $lang]);
                if (!$modelDesc) {
                    $modelDesc = $this->layout_module_desc_repository->create(array_merge($input, ['id' => $id, 'lang' => $lang]));
                } else {
                    $modelDesc = $this->layout_module_desc_repository->update($modelDesc, $input);
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
     *   path="/backend/pg_layout_modules/{id}/images",
     *   summary="Update Page Layout Module Images",
     *   operationId="updatePageLayoutModuleImages",
     *   tags={"BackendPgLayoutModules"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Layout Module Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="sort_order", type="integer", example=0),
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

    /**
     * @OA\Post(
     *   path="/backend/pg_layout_modules/{id}/pattern",
     *   summary="Copy Page Layout Module To Pattern",
     *   operationId="copyPageLayoutModuleToPattern",
     *   tags={"BackendPgLayoutModules"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Layout Module Id", example="1"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function pattern($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Create pattern
            $pattern = $this->layout_pattern_repository->create($model->toArray());
            // Create pattern description
            foreach ($model->descs as $desc) {
                $this->layout_pattern_desc_repository->create(array_merge($desc->toArray(), ['id' => $pattern->id]));
            }
            return $this->respondWithSuccess($pattern);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_layout_modules_sort_order",
     *   summary="Update Layout Module Sort Order",
     *   operationId="updatePageLayoutModuleSortOrder",
     *   tags={"BackendPgLayoutModules"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Layout Module Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function sortOrder() {
        try {
            $order = (array)$this->request->get('order');
            if ($order) foreach ($order as $item_id => $sort_order) {
                $this->model_repository->getModel()->where('id', (int)$item_id)->update(['sort_order' => (int)$sort_order]);
            }

            return $this->respondWithSuccess($order);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_layout_modules_modules",
     *   summary="Clone Layout Module From Modules",
     *   operationId="cloneLayoutModuleFromModules",
     *   tags={"BackendPgLayoutModules"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="ids", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function cloneModules() {
        try {
            $input = $this->request->only(['layout_id']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, ['layout_id' => 'required|integer']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $ids = $this->request->get('ids');
            $module_ids = [];
            if (is_array($ids)) {
                $module_ids = $ids;
            } else if ($ids) {
                foreach (explode(',', $ids) as $id) {
                    if (intval($id)) $module_ids[] = (int)$id;
                }
            }
            $module_ids = array_unique($module_ids);
            if (!$module_ids) return $this->errorWrongArgs();
            $modules = $this->module_repository->getModel()->whereIn('id', $module_ids)->get();
            if (!$modules) return $this->errorWrongArgs();
            $temp = $this->model_repository->getModel()->selectRaw('max(sort_order) as sort_order')->where('layout_id', $input['layout_id'])->first();
            $sort_order = $temp ? ((int)$temp->sort_order) : 0;
            $models = [];
            foreach ($modules as $module) {
                $data = array_merge($module->toArray(), $input, ['module_id' => $module->id, 'sort_order' => $sort_order++, 'status' => false]);
                // Create model
                $model = $this->model_repository->create($data);
                // Create description
                foreach ($module->descs as $desc) {
                    $this->layout_module_desc_repository->create(array_merge($desc->toArray(), ['id' => $model->id]));
                }
                $models[] = $model;
            }

            return $this->respondWithSuccess($models);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_layout_modules_patterns",
     *   summary="Clone Layout Module From Patterns",
     *   operationId="cloneLayoutModuleFromPatterns",
     *   tags={"BackendPgLayoutModules"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="ids", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function clonePatterns() {
        try {
            $input = $this->request->only(['layout_id']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, ['layout_id' => 'required|integer']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $ids = $this->request->get('ids');
            $pattern_ids = [];
            if (is_array($ids)) {
                $pattern_ids = $ids;
            } else if ($ids) {
                foreach (explode(',', $ids) as $id) {
                    if (intval($id)) $pattern_ids[] = (int)$id;
                }
            }
            $pattern_ids = array_unique($pattern_ids);
            if (!$pattern_ids) return $this->errorWrongArgs();
            $patterns = $this->layout_pattern_repository->getModel()->whereIn('id', $pattern_ids)->get();
            if (!$patterns) return $this->errorWrongArgs();
            $temp = $this->model_repository->getModel()->selectRaw('max(sort_order) as sort_order')->where('layout_id', $input['layout_id'])->first();
            $sort_order = $temp ? ((int)$temp->sort_order) : 0;
            $models = [];
            foreach ($patterns as $pattern) {
                $data = array_merge($pattern->toArray(), $input, ['sort_order' => $sort_order++, 'status' => false]);
                // Create model
                $model = $this->model_repository->create($data);
                // Create description
                foreach ($pattern->descs as $desc) {
                    $this->layout_module_desc_repository->create(array_merge($desc->toArray(), ['id' => $model->id]));
                }
                $models[] = $model;
            }

            return $this->respondWithSuccess($models);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
