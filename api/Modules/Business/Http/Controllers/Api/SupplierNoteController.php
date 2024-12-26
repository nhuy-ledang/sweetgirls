<?php namespace Modules\Business\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Business\Repositories\SupplierNoteRepository;

/**
 * Class SupplierNoteController
 *
 * @package Modules\Business\Http\Controllers\Api
 * @author Huy D <huydang1920@gmail.com>
 * Date: 2023-02-10
 */
class SupplierNoteController extends ApiBaseModuleController {

    public function __construct(Request $request,
                                SupplierNoteRepository $supplier_note_repository) {
        $this->model_repository = $supplier_note_repository;

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
            'note'        => 'required',
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
     *   path="/backend/sup_supplier_notes",
     *   summary="Get Supplier Notes",
     *   operationId="getPurSupplierNotes",
     *   tags={"BackendSupSupplierNotes"},
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
            //if (!($this->isView() || $this->isViewOwn())) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $sort = $sort ? strtolower($sort) : 'id';
            $order = (string)$this->request->get('order');
            $order = $order ? strtolower($order) : 'asc';
            $data = $this->getRequestData();
            $supplier_id = isset($data->{'supplier_id'}) ? (int)$data->{'supplier_id'} : 0;
            $queries = [
                'and'        => [['supplier_id', '=', $supplier_id]],
                'orWhereRaw' => [],
            ];
            // Query by keyword
            $q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(trim(utf8_strtolower($q)));
                $keys = ['note'];
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
     *   path="/backend/sup_supplier_notes/{id}",
     *   summary="Get a Supplier Note",
     *   operationId="getPurSupplierNote",
     *   tags={"BackendSupSupplierNotes"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Supplier Note Id", example=1),
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
            if (!($this->isView() || $this->isViewOwn())) return $this->errorForbidden();
            $model = $this->setUpQueryBuilder($this->model(), ['and' => [['id', '=', $id]]])->first();
            if (!$model) return $this->errorNotFound();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/sup_supplier_notes",
     *   summary="Create Supplier Note",
     *   operationId="createPurSupplierNote",
     *   tags={"BackendSupSupplierNotes"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="supplier_id", type="integer", example=0),
     *       @OA\Property(property="note", type="string", example=""),
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
            if (!$this->isCreate()) return $this->errorForbidden();
            $input = $this->request->all();
            $input['owner_id'] = $this->auth->id;
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Create model
            $model = $this->model_repository->create($input);
            // Update parent
            $supplier = $model->supplier;
            $supplier->fill(['note' => $model->note]);
            $supplier->save();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/sup_supplier_notes/{id}",
     *   summary="Update Supplier Note",
     *   operationId="updatePurSupplierNote",
     *   tags={"BackendSupSupplierNotes"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Supplier Note Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="note", type="string", example=""),
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
            if (!$this->isUpdate()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['note']);
            $input['owner_id'] = $this->auth->id;
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Update Model
            $model = $this->model_repository->update($model, $input);
            // Update parent
            $supplier = $model->supplier;
            $supplier->fill(['note' => $model->note]);
            $supplier->save();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/sup_supplier_notes/{id}",
     *   summary="Delete Supplier Note",
     *   operationId="deletePurSupplierNote",
     *   tags={"BackendSupSupplierNotes"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Supplier Note Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            // Check permission
            if (!$this->isDelete()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Delete model
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
