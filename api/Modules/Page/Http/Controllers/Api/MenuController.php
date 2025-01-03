<?php namespace Modules\Page\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Media\Services\FileService;
use Modules\Page\Repositories\MenuDescRepository;
use Modules\Page\Repositories\MenuRepository;

/**
 * Class MenuController
 *
 * @package Modules\Page\Http\Controllers\Api
 */
class MenuController extends ApiBaseModuleController {
    /**
     * @var \Modules\Page\Repositories\PageDescRepository
     */
    protected $menu_desc_repository;

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    public function __construct(Request $request,
                                MenuRepository $menu_repository,
                                MenuDescRepository $menu_desc_repository,
                                FileService $fileService) {
        $this->model_repository = $menu_repository;
        $this->menu_desc_repository = $menu_desc_repository;
        $this->fileService = $fileService;

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
            'name' => 'required',
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

    /**
     * @OA\Get(
     *   path="/backend/pg_menus_all",
     *   summary="GetMenuAll",
     *   operationId="getMenuAll",
     *   tags={"BackendPgMenus"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function all() {
        try {
            $results = $this->model_repository->getModel()->where('parent_id', 0)->with('childs')->orderBy('sort_order', 'asc')->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pg_menus",
     *   summary="Get Menus",
     *   operationId="getMenus",
     *   tags={"BackendPgMenus"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="App-Env", in="query", required=false, description="ENV", example="cms"),
     *   @OA\Parameter(name="paging", in="query", required=false, description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", required=false, description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", required=false, description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", required=false, description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", required=false, description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", required=false, description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example="%7B%22subject_id%22%3A0%2C%22course_id%22%3A0%2C%22q%22%3A%22%22%7D"),
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
            // Check permission
            if (!$this->isCRUD('menus', 'view')) return $this->errorForbidden();
            $results = $this->model_repository->getModel()->where('parent_id', 0)->orderBy('sort_order', 'asc')->get();
            $output = [];
            foreach ($results as $level1) {
                $output[] = $level1;
                $level2s = $this->model_repository->getModel()->where('parent_id', $level1->id)->orderBy('sort_order', 'asc')->get();
                foreach ($level2s as $level2) {
                    $level2->parent = ['name' => $level1->name];
                    $output[] = $level2;
                    $level3s = $this->model_repository->getModel()->where('parent_id', $level2->id)->orderBy('sort_order', 'asc')->get();
                    foreach ($level3s as $level3) {
                        $level3->parent = ['name' => $level1->name . ' > ' . $level2->name];
                        $output[] = $level3;
                    }
                }
            }
            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pg_menus/{id}",
     *   summary="Get Menu",
     *   operationId="getMenu",
     *   tags={"BackendPgMenus"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="App-Env", in="query", required=false, description="ENV", example="cms"),
     *   @OA\Parameter(name="id", in="path", required=true, description="Menu Id", example="1"),
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
            $fields = ['*'];
            $model = $this->setUpQueryBuilder($this->model(), [], false, $fields)->where('id', $id)->first();
            if (!$model) return $this->errorNotFound();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_menus",
     *   summary="Create Menu",
     *   operationId="createMenu",
     *   tags={"BackendPgMenus"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="category_id", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="description", type="string", example=""),
     *       @OA\Property(property="sort_order", type="integer", example=0),
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
            // Check permission
            if (!$this->isCRUD('menus', 'create')) return $this->errorForbidden();
            $input = $this->request->only(['parent_id', 'page_id', 'name', 'icon', 'image', 'source', 'link', 'sort_order', 'is_redirect', 'is_sidebar', 'is_header', 'is_footer', 'status']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);

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
                    $savedFile = $this->fileService->store($file, ['sub' => 'menus', 'object_id' => $model->id]);
                    if (!is_string($savedFile)) $model = $this->model_repository->update($model, ['image' => $savedFile->path]);
                }
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_menus/{id}",
     *   summary="Update Menu",
     *   operationId="updateMenu",
     *   tags={"BackendPgMenus"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Menu Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="category_id", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="description", type="string", example=""),
     *       @OA\Property(property="sort_order", type="integer", example=0),
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
            // Check permission
            if (!$this->isCRUD('menus', 'edit')) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['parent_id', 'page_id', 'name', 'icon', 'image', 'source', 'link', 'sort_order', 'is_redirect', 'is_sidebar', 'is_header', 'is_footer', 'status']);
            $parent_id = (int)$this->request->get('parent_id');
            if (!$parent_id) $parent_id = 0;
            if ($parent_id == $id) return $this->errorWrongArgs();
            if ($parent_id) {
                $parent = $this->model_repository->find($parent_id);
                if (!$parent) {
                    $parent_id = 0;
                } else {
                    if($parent->parent_id == $id) return $this->errorWrongArgs();
                }
            }
            $input['parent_id'] = $parent_id;
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);

            // Upload image
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $input['image'] = $file_path;
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                if ($errorKey) return $this->errorWrongArgs($errorKey);
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => 'menus', 'object_id' => $model->id]);
                    if (!is_string($savedFile)) {
                        $input['image'] = $savedFile->path;
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
     *   path="/backend/pg_menus/{id}",
     *   summary="Update Menu Partial",
     *   operationId="UpdateMenuPartial",
     *   tags={"BackendPgMenus"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Menu Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example=1),
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
            // Check permission
            if (!$this->isCRUD('menus', 'edit')) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['status', 'is_sub', 'is_sidebar', 'is_header', 'is_footer']);
            if (isset($input['is_sub']) && !is_null($input['is_sub'])) {
                if (!$model->parent_id) {
                    $input['is_sub'] = (boolean)$input['is_sub'];
                    if ($input['is_sub'] === true) {
                        $this->model_repository->getModel()->where('id', '<>', $model->id)->update(['is_sub' => false]);
                    }
                } else {
                    unset($input['is_sub']);
                }
            }
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/pg_menus/{id}",
     *   summary="Delete Menu",
     *   operationId="deleteMenu",
     *   tags={"BackendPgMenus"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Menu Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            // Check permission
            if (!$this->isCRUD('menus', 'delete')) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            if (!in_array(intval($id), [1])) {
                $this->model_repository->destroy($model);
            }

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/pg_menus/{id}/description",
     *   summary="Update Menu Description",
     *   operationId="updateMenuDescription",
     *   tags={"BackendPgMenus"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Menu Id", example="1"),
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
            // Check permission
            if (!$this->isCRUD('menus', 'edit')) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['lang', 'name', 'link']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, ['lang' => 'required', 'name' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $lang = $this->request->get('lang');
            if ($lang == 'vi') {
                //$model = $this->model_repository->update($model, $input);
            } else {
                $modelDesc = $this->menu_desc_repository->findByAttributes(['id' => $id, 'lang' => $lang]);
                if (!$modelDesc) {
                    $modelDesc = $this->menu_desc_repository->create(array_merge($input, ['id' => $id, 'lang' => $lang]));
                } else {
                    $modelDesc = $this->menu_desc_repository->update($modelDesc, $input);
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
     * @OA\Get(
     *   path="/backend/pg_menus_nav",
     *   summary="Get Menu Nav",
     *   operationId="getMenuNav",
     *   tags={"BackendPgMenus"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function nav() {
        try {
            $results = $this->model_repository->getModel()->where('parent_id', 0)->orderBy('sort_order', 'asc')->get();
            $output = [];
            foreach ($results as $item_lv1) {
                $results_lv2 = $this->model_repository->getModel()->where('parent_id', $item_lv1->id)->orderBy('sort_order', 'asc')->get();
                $child_lv2 = [];
                foreach ($results_lv2 as $item_lv2) {
                    $item_lv2->children = $this->model_repository->getModel()->where('parent_id', $item_lv2->id)->orderBy('sort_order', 'asc')->get();
                    $child_lv2[] = $item_lv2;
                }
                $item_lv1->children = $child_lv2;
                $output[] = $item_lv1;
            }
            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
