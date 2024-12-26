<?php namespace Modules\Business\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Media\Services\FileService;
use Modules\Business\Repositories\SupplierCategoryRepository;
use Modules\Business\Repositories\SupplierContactRepository;
use Modules\Business\Repositories\SupplierRepository;

/**
 * Class ProviderController
 *
 * @package Modules\Business\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-05-22
 */
class ProviderController extends SupplierController {
    /**
     * @var string
     */
    protected $supplier_type = 'provider';

    public function __construct(Request $request,
                                SupplierRepository $supplier_repository,
                                SupplierCategoryRepository $supplier_category_repository,
                                SupplierContactRepository $supplier_contact_repository,
                                FileService $fileService) {
        parent::__construct($request, $supplier_repository, $supplier_category_repository, $supplier_contact_repository, $fileService);
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreate() {
        return [
            'category_id' => 'required|integer',
            'group_id'    => 'required|integer',
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
            'category_id' => 'required|integer',
            'group_id'    => 'required|integer',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/sup_providers",
     *   summary="Get Providers",
     *   operationId="supGetProviders",
     *   tags={"BackendSupProviders"},
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
            $queries = ['and' => [['supplier_type', '=', $this->supplier_type]], 'whereRaw' => [], 'orWhereRaw' => []];
            $data = $this->getRequestData();
            $category_id = (isset($data->{'category_id'}) && !is_null($data->{'category_id'}) && $data->{'category_id'} !== '') ? (int)$data->{'category_id'} : false;
            if ($category_id) $queries['and'][] = ['category_id', '=', $category_id];
            $group_id = (isset($data->{'group_id'}) && !is_null($data->{'group_id'}) && $data->{'group_id'} !== '') ? (int)$data->{'group_id'} : false;
            if ($group_id) $queries['and'][] = ['group_id', '=', $group_id];
            // Query by keyword
            /*$q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(trim(utf8_strtolower($q)));
                $keys = ['`company`'];
                foreach ($keys as $key) {
                    $iQ = [];
                    $iB = [];
                    foreach ($arrQ as $i) {
                        $iQ[] = "lower($key) like ?";
                        $iB[] = "%$i%";
                    }
                    $queries['orWhereRaw'][] = ['(' . implode(' and ', $iQ) . ')', $iB];
                }
            }*/
            $q = trim(utf8_strtolower((isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : ''));
            if ($q) $queries['whereRaw'][] = ["lower(`fullname`) like ?", "%$q%"];
            $results = $this->setUpQueryBuilder($this->model(), $queries)->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
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
     *   path="/backend/sup_providers/{id}",
     *   summary="Get a Provider",
     *   operationId="supGetProvider",
     *   tags={"BackendSupProviders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Supplier Id", example=1),
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
        return parent::show($id);
    }

    /**
     * @OA\Post(
     *   path="/backend/sup_providers",
     *   summary="Create Provider",
     *   operationId="supCreateProvider",
     *   tags={"BackendSupProviders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Supplier Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="idx", type="string", example=""),
     *       @OA\Property(property="email", type="string", format="email", example="email@gmail.com"),
     *       @OA\Property(property="phone_number", type="string", example="12345678"),
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
            $input = $this->request->only(['idx', 'group_id', 'category_id', 'email', 'phone_number', 'fullname', 'bank_number', 'bank_name', 'card_holder', 'address', 'tax', 'website', 'note', 'description', 'image', 'status']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $idx = $this->request->get('idx');
            if ($idx) $idx = to_idx($idx);
            if ($idx) {
                $temp = $this->model_repository->getModel()->where('idx', $idx)->first();
                if ($temp) return $this->respondWithErrorKey('idx.exists');
                $input['idx'] = $idx;
            }
            // Check image
            $file_path = $this->request->get('file_path');
            $file = null;
            if (!$file_path) {
                list($file, $errorKey) = $this->getRequestFile();
                if ($errorKey) return $this->errorWrongArgs($errorKey);
            }
            $input['supplier_type'] = $this->supplier_type;
            $input['company'] = $input['fullname'];
            // Create model
            $model = $this->model_repository->create($input);
            // Upload image
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $model = $this->model_repository->update($model, ['image' => $file_path]);
            } else if ($file) {
                $savedFile = $this->fileService->store($file, ['sub' => MEDIA_SUB_AVATAR, 'object_id' => $model->id]);
                if (!is_string($savedFile)) $model = $this->model_repository->update($model, ['image' => $savedFile->path]);
            }
            // Create contact
            $contactInput = array_merge($input, ['supplier_id' => $model->id, 'avatar' => $model->image]);
            if (isset($contactInput['status'])) unset($contactInput['status']);
            $contactModel = $this->supplier_contact_repository->create($contactInput);
            $model = $this->model_repository->update($model, ['contact_id' => $contactModel->id]);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/sup_providers/{id}",
     *   summary="Update Provider",
     *   operationId="supUpdateProvider",
     *   tags={"BackendSupProviders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Supplier Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="email", type="string", format="email", example="email@gmail.com"),
     *       @OA\Property(property="phone_number", type="string", example="12345678"),
     *       @OA\Property(property="address", type="string", example="address"),
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
            $input = $this->request->only(['idx', 'group_id', 'category_id', 'email', 'phone_number', 'fullname', 'bank_number', 'bank_name', 'card_holder', 'address', 'tax', 'website', 'note', 'description', 'image', 'status']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $idx = $this->request->get('idx');
            if ($idx) $idx = to_idx($idx);
            if ($idx && $idx != $model->idx) {
                $temp = $this->model_repository->getModel()->where('idx', $idx)->first();
                if ($temp) return $this->respondWithErrorKey('idx.exists');
                $input['idx'] = $idx;
            }
            // Upload image
            $file_path = $this->request->get('file_path');
            if ($file_path) {
                $input['image'] = $file_path;
            } else {
                list($file, $errorKey) = $this->getRequestFile();
                if ($errorKey) return $this->errorWrongArgs($errorKey);
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => MEDIA_SUB_AVATAR, 'object_id' => $model->id]);
                    if (!is_string($savedFile)) $input['image'] = $savedFile->path;
                }
            }
            $input['company'] = $input['fullname'];
            // Update Model
            $model = $this->model_repository->update($model, $input);
            // Create contact
            if (!$model->contact_id) {
                $contactInput = ['supplier_id' => $model->id, 'avatar' => $model->image];
                foreach (['fullname', 'gender', 'contact_title', 'phone_number', 'email', 'note', 'type_id', 'birthday', 'interests', 'personality', 'contact_tool', 'contact_time', 'address', 'marital_status', 'home_town', 'religion', 'dreams', 'favorite_activity', 'social_achievements', 'social_groups', 'social_network', 'avatar'] as $fieldName) {
                    if (!is_null($model->{$fieldName})) $contactInput[$fieldName] = $model->{$fieldName};
                }
                $contactModel = $this->supplier_contact_repository->create($contactInput);
                $model = $this->model_repository->update($model, ['contact_id' => $contactModel->id]);
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *   path="/backend/sup_providers/{id}",
     *   summary="Update Provider Partial",
     *   operationId="supUpdateProviderPartial",
     *   tags={"BackendSupProviders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Supplier Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="due", type="integer", example=1),
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
        return parent::patch($id);
    }

    /**
     * @OA\Delete(
     *   path="/backend/sup_providers/{id}",
     *   summary="Delete Provider",
     *   operationId="supDeleteProvider",
     *   tags={"BackendSupProviders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Supplier Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        return parent::destroy($id);
    }

    /**
     * @OA\Get(
     *   path="/backend/sup_providers_stats",
     *   summary="Get Provider Stats",
     *   operationId="supGetProviderStats",
     *   tags={"BackendSupProviders"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function stats() {
        try {
            $fields = [
                '*',
                \DB::raw('(select count(*) from `sup__suppliers` where `category_id` = `sup__categories`.`id`) as total'),
            ];
            $results = $this->supplier_category_repository->getModel()->orderBy('name', 'asc')->select($fields)->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
