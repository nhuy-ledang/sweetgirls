<?php namespace Modules\Business\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Business\Repositories\SupplierRepository;
use Modules\Business\Repositories\SupplierContactRepository;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\Usr\Repositories\ActivityRepository as UsrActivityRepository;

/**
 * Class SupplierContactController
 *
 * @package Modules\Business\Http\Controllers\Api
 * @author Huy D <huydang1920@gmail.com>
 * Date: 2023-02-10
 */
class SupplierContactController extends ApiBaseModuleController {
    /**
     * @var string
     */
    protected $module_id = 'purchases';

    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    protected $file_repository;

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    /**
     * @var \Modules\Business\Repositories\SupplierRepository
     */
    protected $supplier_repository;

    /**
     * @var \Modules\Usr\Repositories\ActivityRepository
     */
    protected $usr_activity_repository;

    public function __construct(Request $request,
                                FileRepository $file_repository,
                                FileService $fileService,
                                SupplierRepository $supplier_repository,
                                SupplierContactRepository $supplier_contact_repository,
                                UsrActivityRepository $usr_activity_repository) {
        $this->model_repository = $supplier_contact_repository;
        $this->supplier_repository = $supplier_repository;
        $this->usr_activity_repository = $usr_activity_repository;
        $this->file_repository = $file_repository;
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
            'supplier_id' => 'required|integer|exists:sup__suppliers,id',
        ];
    }

    /**
     * Get the validation rules for update.
     *
     * @param int $id
     * @return array
     */
    protected function rulesForUpdate($id) {
        return [];
    }

    /**
     * @OA\Get(
     *   path="/backend/sup_supplier_contacts",
     *   summary="Get Supplier Contacts",
     *   operationId="getPurSupplierContacts",
     *   tags={"BackendSupSupplierContacts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields, extend_fields: Extend fields query} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
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
            //if (!($this->isView($this->module_id) || $this->isViewOwn($this->module_id))) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $sort = $sort ? strtolower($sort) : 'id';
            $order = (string)$this->request->get('order');
            $order = $order ? strtolower($order) : 'asc';
            $queries = ['and' => [], 'orWhereRaw' => []];
            $data = $this->getRequestData();
            $supplier_id = isset($data->{'supplier_id'}) ? (int)$data->{'supplier_id'} : 0;
            if ($supplier_id) $queries['and'][] = ['supplier_id', '=', $supplier_id];
            // Query by keyword
            $q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(trim(utf8_strtolower($q)));
                $keys = ['email', 'phone_number', 'fullname', 'contact_title'];
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
            $fields = ['*'];
            //$fields[] = \DB::raw('(exists (select * from `sup__suppliers` where `contact_id` = `sup__supplier_contacts`.`id` limit 1)) as is_default');
            $results = $this->setUpQueryBuilder($this->model(), $queries, false, $fields)->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'true' ? true : ($paging == 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($results);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/sup_supplier_contacts/{id}",
     *   summary="Get Supplier Contact",
     *   operationId="getPurContact",
     *   tags={"BackendSupSupplierContacts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Supplier Contact Id", example="1"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
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
            // Check permission
            if (!($this->isView($this->module_id) || $this->isViewOwn($this->module_id))) return $this->errorForbidden();
            $model = $this->setUpQueryBuilder($this->model(), ['and' => [['id', '=', $id]]])->first();
            if (!$model) return $this->errorNotFound();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/sup_supplier_contacts",
     *   summary="Create Supplier Contact",
     *   operationId="createPurContact",
     *   tags={"BackendSupSupplierContacts"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="supplier_id", type="integer", example=0),
     *       @OA\Property(property="email", type="string", format="email", example="email@gmail.com"),
     *       @OA\Property(property="phone_number", type="string", example="12345678"),
     *       @OA\Property(property="fullname", type="string", example=""),
     *       @OA\Property(property="gender", type="string", example="0:unknown or 1:male or 2:female"),
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
            // Check permission
            if (!$this->isCreate($this->module_id)) return $this->errorForbidden();
            $input = $this->request->all();
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $input['owner_id'] = $this->auth->id;
            // Create model
            $model = $this->model_repository->create($input);
            // Upload avatar
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $model = $this->model_repository->update($model, ['avatar' => $file_path]);
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                //if ($errorKey) return $this->errorWrongArgs($errorKey);
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => MEDIA_SUB_AVATAR, 'object_id' => $model->id]);
                    if (!is_string($savedFile)) $model = $this->model_repository->update($model, ['avatar' => $savedFile->path]);
                }
            }
            // Update progress
            $fillable = ['fullname', 'contact_title', 'email', 'phone_number', 'birthday', 'interests', 'personality', 'contact_tool', 'contact_time', 'address', 'marital_status', 'home_town', 'religion', 'dreams', 'favorite_activity', 'social_achievements', 'social_groups', 'social_network', 'avatar'];
            $total = count($fillable);
            $row_total = 0;
            foreach ($fillable as $fieldName) {
                if (!empty($model->{$fieldName})) $row_total++;
            }
            $progress = round($row_total / $total * 100);
            $model = $this->model_repository->update($model, ['progress' => $progress]);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/sup_supplier_contacts/{id}",
     *   summary="Update Supplier Contact",
     *   operationId="updatePurContact",
     *   tags={"BackendSupSupplierContacts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Supplier Contact Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="email", type="string", format="email", example="email@gmail.com"),
     *       @OA\Property(property="phone_number", type="string", example="12345678"),
     *       @OA\Property(property="fullname", type="string", example=""),
     *       @OA\Property(property="gender", type="string", example="0:unknown or 1:male or 2:female"),
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
            // Check permission
            if (!$this->isUpdate($this->module_id)) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['type_id', 'fullname', 'contact_title', 'gender', 'email', 'phone_number', 'birthday', 'interests', 'personality', 'contact_tool', 'contact_time', 'address', 'marital_status', 'home_town', 'religion', 'dreams', 'favorite_activity', 'social_achievements', 'social_groups', 'social_network', 'avatar', 'note', 'rating_id', 'status']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Upload avatar
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $input['avatar'] = $file_path;
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                if ($errorKey) return $this->errorWrongArgs($errorKey);
                if ($file) {
                    // Unlink old avatar
                    //$oldFile = null;
                    //if ($model->avatar) $oldFile = $this->file_repository->findByAttributes(['object' => MEDIA_SUB_AVATAR, 'path' => $model->avatar]);
                    // New avatar
                    $savedFile = $this->fileService->store($file, ['sub' => MEDIA_SUB_AVATAR, 'object_id' => $model->id]);
                    if (!is_string($savedFile)) {
                        $input['avatar'] = $savedFile->path;
                        // Unlink old avatar
                        //if ($oldFile) $this->file_repository->destroy($oldFile);
                    }
                }
            }
            if (!$model->owner_id) $input['owner_id'] = $this->auth->id;
            // Update Model
            $model = $this->model_repository->update($model, $input);
            // Update progress
            $fillable = ['fullname', 'contact_title', 'email', 'phone_number', 'birthday', 'interests', 'personality', 'contact_tool', 'contact_time', 'address', 'marital_status', 'home_town', 'religion', 'dreams', 'favorite_activity', 'social_achievements', 'social_groups', 'social_network', 'avatar'];
            $total = count($fillable);
            $row_total = 0;
            foreach ($fillable as $fieldName) {
                if (!empty($model->{$fieldName})) $row_total++;
            }
            $progress = round($row_total / $total * 100);
            $model = $this->model_repository->update($model, ['progress' => $progress]);
            // Update to supplier
            $supplier = $this->supplier_repository->findByAttributes(['id' => $model->supplier_id, 'contact_id' => $model->id]);
            if ($supplier) {
                $newData = [];
                foreach (['fullname', 'gender', 'contact_title', 'phone_number', 'email', 'note', 'type_id', 'birthday', 'interests', 'personality', 'contact_tool', 'contact_time', 'address', 'marital_status', 'home_town', 'religion', 'dreams', 'favorite_activity', 'social_achievements', 'social_groups', 'social_network', 'avatar'] as $fieldName) {
                    $newData[$fieldName] = $model->{$fieldName};
                }
                $this->supplier_repository->update($supplier, $newData);
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/sup_supplier_contacts/{id}",
     *   summary="Delete Supplier Contact",
     *   operationId="deletePurContact",
     *   tags={"BackendSupSupplierContacts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Supplier Contact Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            // Check permission
            if (!$this->isDelete($this->module_id)) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Update supplier
            $this->supplier_repository->getModel()->where('contact_id', $id)->update(['contact_id' => null]);
            // Delete model
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
