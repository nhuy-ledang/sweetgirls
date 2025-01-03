<?php namespace Modules\Staff\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\Staff\Repositories\SalaryRepository;
use Modules\Staff\Repositories\UserRepository;
use Modules\Usr\Repositories\UserRepository as UsrRepository;

/**
 * Class UserController
 *
 * @package Modules\User\Http\Controllers\Api
 * @author Huy D <huydang1920@gmail.com>
 */
class UserController extends ApiBaseModuleController {
    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    protected $file_repository;

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    /**
     * @var \Modules\Staff\Repositories\SalaryRepository
     */
    protected $salary_repository;

    /**
     * @var \Modules\Usr\Repositories\UserRepository
     */
    protected $usr_repository;

    public function __construct(Request $request,
                                FileRepository $file_repository,
                                FileService $fileService,
                                UserRepository $user_repository,
                                SalaryRepository $salary_repository,
                                UsrRepository $usr_repository) {
        $this->model_repository = $user_repository;
        $this->file_repository = $file_repository;
        $this->fileService = $fileService;
        $this->salary_repository = $salary_repository;
        $this->usr_repository = $usr_repository;

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
            //'email' => 'required|email|unique:st__users,email',
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
            //'email' => "unique:st__users,email,$id,id",
        ];
    }

    /**
     * Create salary
     * @param $user
     */
    private function generateSalary($user) {
        $start_date = $user->start_date;
        $end_date = ($user->end_date && $user->end_date != '0000-00-00') ? $user->end_date : date('Y-m-01');
        $salary = $user->salary ? $user->salary : 0;
        $real = $user->real ? $user->real : $salary;
        if ($start_date && $salary) {
            $start_date = date('Y-m-01', strtotime('+1 month', strtotime($start_date)));
            $this->salary_repository->getModel()->where('user_id', $user->id)->where('date', '<', $start_date)->delete();
            while (strtotime($start_date) <= strtotime($end_date)) {
                $temp = $this->salary_repository->findByAttributes(['user_id' => $user->id, 'date' => $start_date]);
                if (!$temp) {
                    $this->salary_repository->create([
                        'user_id'   => $user->id,
                        'date'      => $start_date,
                        'date_num'  => date('t', strtotime($start_date)),
                        'salary'    => $salary,
                        'real'      => $real,
                        'debt'      => $salary - $real,
                        'salary_at' => date('Y-m-05', strtotime($start_date))
                    ]);
                }
                $start_date = date('Y-m-01', strtotime('+1 month', strtotime($start_date)));
            }
            if ($user->end_date) $this->salary_repository->getModel()->where('user_id', $user->id)->where('date', '>', $user->end_date)->delete();
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/st_users_all",
     *   summary="Get Users All",
     *   operationId="getUsersAll",
     *   tags={"BackendStUsers"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function all() {
        try {
            $results = $this->model_repository->getModel()->where('status', 1)->select(['id', 'fullname as name'])->orderBy('name', 'asc')->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/st_users",
     *   summary="Get Users",
     *   operationId="getUsers",
     *   tags={"BackendStUsers"},
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
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $sort = $sort ? strtolower($sort) : 'id';
            $order = (string)$this->request->get('order');
            $order = $order ? strtolower($order) : 'asc';
            $queries = [
                'and'        => [],
                'whereRaw'   => [],
                'orWhereRaw' => [],
            ];
            $data = $this->getRequestData();
            $status = (isset($data->{'status'}) && !is_null($data->{'status'}) && $data->{'status'} !== '') ? (int)$data->{'status'} : false;
            if (!(is_null($status) || $status === false)) $queries['and'][] = ['status', '=', $status];
            // Query by keyword
            $q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(trim(utf8_strtolower($q)));
                $keys = ['email', 'phone_number', 'first_name', 'company'];
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
     *   path="/backend/st_users/{id}",
     *   summary="Get a User",
     *   operationId="getUser",
     *   tags={"BackendStUsers"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="User Id", example="1"),
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
            $model = $this->setUpQueryBuilder($this->model(), ['and' => [['id', '=', $id]]])->first();
            if (!$model) return $this->errorNotFound();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/st_users",
     *   summary="Create User",
     *   operationId="createUser",
     *   tags={"BackendStUsers"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="User Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"email"},
     *       @OA\Property(property="idx", type="string", example=""),
     *       @OA\Property(property="email", type="string", format="email", example="email@gmail.com"),
     *       @OA\Property(property="phone_number", type="string", example="12345678"),
     *       @OA\Property(property="first_name", type="string", example=""),
     *       @OA\Property(property="birthday", type="string", example="2019-04-02"),
     *       @OA\Property(property="gender", type="string", example="0:unknown or 1:male or 2:female"),
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
            $input = $this->request->only(['fullname', 'gender', 'birthday', 'address', 'fixed_address', 'bank_id', 'bank_name', 'start_date', 'end_date', 'position', 'mission', 'description', 'salary', 'real', 'method', 'status']);
            $end_date = $this->request->get('end_date');
            if ($end_date == 'null') $input['end_date'] = null;
            $department_id = $this->request->get('department_id');
            if (!is_null($department_id) && intval($department_id)) $input['department_id'] = $department_id;
            $rules = ['email' => 'required|email'];
            $email = $this->request->get('email');
            if ($email) {
                $email = trim(strtolower((string)$email));
                $input['email'] = $email;
            }
            list($calling_code, $phone_number) = calling2phone($this->request->get('phone_number'));
            if ($phone_number) {
                $input['calling_code'] = $calling_code;
                $input['phone_number'] = $phone_number;
            }
            // Check Valid
            $validatorErrors = $this->getValidator($input, $rules);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $idx = $this->request->get('idx');
            if ($idx) $idx = to_idx($idx);
            if ($idx) {
                $temp = $this->model_repository->getModel()->where('idx', $idx)->first();
                if ($temp) return $this->respondWithErrorKey('idx.exists');
                $input['idx'] = $idx;
            }
            /*// Check exist
            if ($email) {
                $total = $this->model_repository->getModel()->where('email', $email)->withTrashed()->count();
                if ($total) {
                    return $this->respondWithErrorKey('email.unique');
                }
            }
            if ($phone_number) {
                $total = $this->model_repository->getModel()->whereRaw('(`calling_code` = ? and `phone_number` = ?)', [$calling_code, $phone_number])->withTrashed()->count();
                if ($total) {
                    return $this->respondWithErrorKey('phone_number.unique');
                }
            }*/
            // Create user starter
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
            // Create salary
            $this->generateSalary($model);
            // Link to usr
            $usr = $this->usr_repository->getModel()->where('email', $model->email)->whereNotNull('email')->first();
            if ($usr) $model = $this->model_repository->update($model, ['usr_id' => $usr->id]);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/st_users/{id}",
     *   summary="Update User",
     *   operationId="updateUser",
     *   tags={"BackendStUsers"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="User Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="email", type="string", format="email", example="email@gmail.com"),
     *       @OA\Property(property="phone_number", type="string", example="12345678"),
     *       @OA\Property(property="first_name", type="string", example=""),
     *       @OA\Property(property="birthday", type="string", example="2019-04-02"),
     *       @OA\Property(property="gender", type="string", example="0:unknown or 1:male or 2:female"),
     *       @OA\Property(property="address", type="string", example="address"),
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
            $input = $this->request->only(['fullname', 'gender', 'birthday', 'address', 'fixed_address', 'bank_id', 'bank_name', 'start_date', 'end_date', 'position', 'mission', 'description', 'salary', 'real', 'method', 'status']);
            $end_date = $this->request->get('end_date');
            if ($end_date == 'null') $input['end_date'] = null;
            $department_id = $this->request->get('department_id');
            $input['department_id'] = !is_null($department_id) && intval($department_id) ? $department_id : null;
            $rules = [];
            $email = $this->request->get('email');
            if ($email) {
                $email = trim(strtolower((string)$email));
                $input['email'] = $email;
                $rules['email'] = 'email';
            }
            list($calling_code, $phone_number) = calling2phone($this->request->get('phone_number'));
            if ($phone_number) {
                $input['calling_code'] = $calling_code;
                $input['phone_number'] = $phone_number;
            }
            // Check Valid
            $validatorErrors = $this->getValidator($input, $rules);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $idx = $this->request->get('idx');
            if ($idx) $idx = to_idx($idx);
            if ($idx && $idx != $model->idx) {
                $temp = $this->model_repository->getModel()->where('idx', $idx)->first();
                if ($temp) return $this->respondWithErrorKey('idx.exists');
                $input['idx'] = $idx;
            }
            /*// Check exist
            if ($email) {
                $total = $this->model_repository->getModel()->where('id', '<>', $id)->where('email', $email)->withTrashed()->count();
                if ($total) {
                    return $this->respondWithErrorKey('email.unique');
                }
            }
            if ($phone_number) {
                $total = $this->model_repository->getModel()->where('id', '<>', $id)->whereRaw('(`calling_code` = ? and `phone_number` = ?)', [$calling_code, $phone_number])->withTrashed()->count();
                if ($total) {
                    return $this->respondWithErrorKey('phone_number.unique');
                }
            }*/
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
            // Update Model
            $model = $this->model_repository->update($model, $input);
            // Create salary
            $this->generateSalary($model);
            // Link to usr
            $usr = $this->usr_repository->getModel()->where('email', $model->email)->whereNotNull('email')->first();
            $model = $this->model_repository->update($model, ['usr_id' => $usr ? $usr->id : null]);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *   path="/backend/st_users/{id}",
     *   summary="Update User Partial",
     *   operationId="UpdateUserPartial",
     *   tags={"BackendStUsers"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="User Id", example="1"),
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
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['contact_id']);
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/st_users/{id}",
     *   summary="Delete User",
     *   operationId="deleteUser",
     *   tags={"BackendStUsers"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="User Id", example=1),
     *   @OA\Parameter(name="user_id", in="path", description="User Id", example="1"),
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

            // Destroy
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
