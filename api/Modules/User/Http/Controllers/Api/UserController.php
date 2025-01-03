<?php namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\User\Repositories\UserRankRepository;
use Modules\User\Repositories\UserRepository;

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
     * @var \Modules\User\Repositories\UserRankRepository
     */
    protected $user_rank_repository;

    public function __construct(Request $request,
                                FileRepository $file_repository,
                                FileService $fileService,
                                UserRepository $user_repository,
                                UserRankRepository $user_rank_repository) {
        $this->model_repository = $user_repository;
        $this->file_repository = $file_repository;
        $this->fileService = $fileService;
        $this->user_rank_repository = $user_rank_repository;

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
            'username'     => 'required|unique:users,username',
            'email'        => 'required|email|unique:users,email',
            'phone_number' => 'required|unique:users,phone_number',
            'password'     => 'required|min:6',
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
            'email'    => "unique:users,email,$id,id",
            'password' => [
                'min:6',
                //'regex:/^.*(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])|(?=.*[a-z])(?=.*[A-Z])(?=.*[ !\"#$%&\'()*+,-.\/:;<=>?@[\]^_`{|}~])|(?=.*[a-z])(?=.*[0-9])(?=.*[ !\"#$%&\'()*+,-.\/:;<=>?@[\]^_`{|}~])|(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"#$%&\'()*+,-.\/:;<=>?@[\]^_`{|}~]).*$/',
            ],
        ];
    }

    /**
     * Get Index
     * @param $page
     * @param $pageSize
     * @param string $sort
     * @param string $order
     * @return array
     */
    protected function getIndex($page, $pageSize, $sort = '', $order = '') {
        if (!$sort) {
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'id' : '' . strtolower($sort);
        }
        if (!$order) {
            $order = (string)$this->request->get('order');
            $order = $order ? strtolower($order) : 'asc';
        }
        $data = $this->getRequestData();
        $queries = ['and' => [], 'orWhereRaw' => []];
        // Query by keyword
        $q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
        if ($q) {
            $arrQ = $this->parseToArray(trim(utf8_strtolower($q)));
            $keys = ['email', 'phone_number', 'first_name', 'last_name'];
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

        $results = $this->setUpQueryBuilder($this->model(), $queries)
            ->orderBy($sort, $order)
            ->select(['*', 'spend as total_orders'])
            ->take($pageSize)
            ->skip($pageSize * ($page - 1))
            ->get();
        return [$queries, $results];
    }

    /**
     * @OA\Get(
     *   path="/backend/users",
     *   summary="Get Users",
     *   operationId="userGetUsers",
     *   tags={"BackendUsers"},
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
            if (!$this->isCRUD('user_list', 'view')) return $this->errorForbidden();
            // Check admin
            if (!$this->isAdmin()) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            list($queries, $results) = $this->getIndex($page, $pageSize);
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging === 'true' ? true : ($paging === 'false' ? false : (boolean)$paging);
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
     *   path="/backend/users/{id}",
     *   summary="Get a User",
     *   operationId="userGetUser",
     *   tags={"BackendUsers"},
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
     *   path="/backend/users",
     *   summary="Create User",
     *   operationId="userCreateUser",
     *   tags={"BackendUsers"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"email"},
     *       @OA\Property(property="email", type="string", format="email", example="email@gmail.com"),
     *       @OA\Property(property="phone_number", type="string", example="12345678"),
     *       @OA\Property(property="password", type="string", example="12345678"),
     *       @OA\Property(property="first_name", type="string", example=""),
     *       @OA\Property(property="birthday", type="string", example="2019-04-02"),
     *       @OA\Property(property="gender", type="string", example="0:unknown or 1:male or 2:female"),
     *       @OA\Property(property="group_id", type="integer", example=1),
     *       @OA\Property(property="source_id", type="integer", example="1"),
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
            if (!$this->isCRUD('user_list', 'create')) return $this->errorForbidden();
            $input = $this->request->only(['first_name', 'birthday', 'gender', 'group_id', 'source_id']);
            $rules = ['phone_number' => 'required'];
            $email = $this->request->get('email');
            if ($email) {
                $email = trim(strtolower((string)$email));
                $input['email'] = $email;
                $rules['email'] = 'required|email';
            }
            list($calling_code, $phone_number) = calling2phone($this->request->get('phone_number'));
            if ($phone_number) {
                $input['calling_code'] = $calling_code;
                $input['phone_number'] = $phone_number;
            }
            $password = $this->request->get('password');
            if ($password) {
                $input['password'] = $password;
                $rules['password'] = 'required|min:6';
            }
            // Check Valid
            $validatorErrors = $this->getValidator($input, $rules);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Check file
            $file_path = $this->request->get('file_path');
            $file = null;
            if (!$file_path) {
                list($file, $errorKey) = $this->getRequestFile();
                if ($errorKey) return $this->errorWrongArgs($errorKey);
            }
            // Check exist
            if ($email) {
                $find = $this->model_repository->getModel()->where('email', $email)->withTrashed()->first();
                if ($find) return $this->respondWithErrorKey('email.unique', 400, '', [], $find);
            }
            if ($phone_number) {
                $find = $this->model_repository->getModel()->whereRaw('(`calling_code` = ? and `phone_number` = ?)', [$calling_code, $phone_number])->withTrashed()->first();
                if ($find) return $this->respondWithErrorKey('phone_number.unique', 400, '', [], $find);
            }
            // Create user starter
            // Hash the password key // hashPassword fn
            if (isset($input['password'])) {
                $input['password'] = Hash::make($input['password']);
            }
            $model = $this->model_repository->create($input);
            // Upload avatar
            if ($file_path) {
                $model = $this->model_repository->update($model, ['avatar' => $file_path]);
            } else {
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => MEDIA_SUB_AVATAR, 'object_id' => $model->id]);
                    if (!is_string($savedFile)) $model = $this->model_repository->update($model, ['avatar' => $savedFile->path]);
                }
            }

            // Send push notification
            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/users/{id}",
     *   summary="Update User",
     *   operationId="userUpdateUser",
     *   tags={"BackendUsers"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="User Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="email", type="string", format="email", example="email@gmail.com"),
     *       @OA\Property(property="phone_number", type="string", example="12345678"),
     *       @OA\Property(property="password", type="string", example="12345678"),
     *       @OA\Property(property="first_name", type="string", example=""),
     *       @OA\Property(property="birthday", type="string", example="2019-04-02"),
     *       @OA\Property(property="gender", type="string", example="0:unknown or 1:male or 2:female"),
     *       @OA\Property(property="group_id", type="integer", example=1),
     *       @OA\Property(property="source_id", type="integer", example="1"),
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
            // Check permission
            if (!$this->isCRUD('user_list', 'edit')) return $this->errorForbidden();
            // Check admin
            if (!$this->isAdmin()) return $this->errorForbidden();

            $model = $this->model_repository->findByAttributes(['id' => $id]);
            if (!$model) return $this->errorNotFound();

            $input = $this->request->only(['first_name', 'birthday', 'gender', 'group_id', 'source_id', 'address']);
            $rules = [];
            $email = $this->request->get('email');
            if ($email) {
                $email = trim(strtolower((string)$email));
                $input['email'] = $email;
                $rules['email'] = 'required|email';
            }
            list($calling_code, $phone_number) = calling2phone($this->request->get('phone_number'));
            if ($phone_number) {
                $input['calling_code'] = $calling_code;
                $input['phone_number'] = $phone_number;
            }
            $password = $this->request->get('password');
            if ($password) {
                $input['password'] = $password;
                $rules['password'] = 'required|min:6';
            }
            // Check Valid
            $validatorErrors = $this->getValidator($input, $rules);
            if (!empty($validatorErrors)) {
                return $this->respondWithError($validatorErrors);
            }

            // Check file
            $file_path = $this->request->get('file_path');
            $file = null;
            if (!$file_path) {
                list($file, $errorKey) = $this->getRequestFile();
                if ($errorKey) return $this->errorWrongArgs($errorKey);
            }

            // Check exist
            if ($email) {
                $total = $this->model_repository->getModel()->where('id', '<>', $id)->where('email', $email)->withTrashed()->count();
                if ($total) {
                    return $this->respondWithErrorKey('email.unique', 400, '', [], $total);
                }
            }
            if ($phone_number) {
                $total = $this->model_repository->getModel()->where('id', '<>', $id)->whereRaw('(`calling_code` = ? and `phone_number` = ?)', [$calling_code, $phone_number])->withTrashed()->first();
                if ($total) {
                    return $this->respondWithErrorKey('phone_number.unique', 400, '', [], $total);
                }
            }

            // Upload avatar
            if ($file_path) {
                $input['avatar'] = $file_path;
            } else {
                if ($file) {
                    // Unlink old avatar
                    $oldFile = null;
                    if ($model->avatar) {
                        $oldFile = $this->file_repository->findByAttributes(['object' => MEDIA_SUB_AVATAR, 'path' => $model->avatar]);
                    }

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

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/users/{id}",
     *   summary="Delete User",
     *   operationId="userDeleteUser",
     *   tags={"BackendUsers"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="User Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            // Check permission
            if (!$this->isCRUD('user_list', 'delete')) return $this->errorForbidden();
            // Check admin
            if (!$this->isAdmin()) return $this->errorForbidden();
            $model = $this->model_repository->findByAttributes(['id' => $id]);
            if (!$model) return $this->errorNotFound();

            // Remove avatar
            if ($model->avatar && \Storage::exists($model->avatar)) \Storage::delete($model->avatar);
            parent::destroy($model);

            // Destroy
            $this->model_repository->destroy($model);
            /*if ($model->status == USER_STATUS_STARTER) {
                $model->forceDelete();
            } else {
                $this->model_repository->destroy($model);
            }*/

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/users/{id}/banned",
     *   summary="Banned User",
     *   operationId="bannedUser",
     *   tags={"BackendUsers"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="User Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"banned"},
     *       @OA\Property(property="banned", type="string", example="0 or 1"),
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
    public function banned($id) {
        try {
            // Check permission
            if (!$this->isCRUD('user_list', 'edit')) return $this->errorForbidden();
            $model = $this->model_repository->findByAttributes(['id' => $id]);
            if (!$model) return $this->errorNotFound();

            // Check admin
            if (!$this->isAdmin()) {
                return $this->errorForbidden();
            }

            $banned = (boolean)$this->request->get('banned');
            if ($model->status == USER_STATUS_STARTER) {
                return $this->errorWrongArgs('auth.ready_not_activated');
            } else if ($model->status == USER_STATUS_BANNED) {
                if ($banned) {
                    return $this->respondWithSuccess($model);
                } else {
                    $model = $this->model_repository->update($model, ['status' => USER_STATUS_ACTIVATED]);
                }
            } else if ($model->status == USER_STATUS_ACTIVATED) {
                if (!$banned) {
                    return $this->respondWithSuccess($model);
                } else {
                    $model = $this->model_repository->update($model, ['status' => USER_STATUS_BANNED]);
                }
            }

            $user = $model;
            $phone_number = phone2local('+' . $user->calling_code . $user->phone_number);

            // Send email notify
            if ($user->email) {
                $optional = [
                    'display'      => $user->display,
                    'email'        => $user->email,
                    'phone_number' => $phone_number,
                    'status'       => $user->status,
                ];
                dispatch(new \Modules\User\Jobs\SendUserBanned($this->email, $optional));
            }

            // Send sms notify
            if ($phone_number) {
                $model->sms_log = $this->sms->send($phone_number, $user->status == USER_STATUS_ACTIVATED ? Lang::get('messages.sms_content.user_unbanned') : Lang::get('messages.sms_content.user_banned'));
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/user_search",
     *   summary="Get User By Search",
     *   operationId="userGetUserBySearch",
     *   tags={"BackendUsers"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="q", in="query", description="Query", example=""),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function search() {
        try {
            $q = (string)$this->request->get('q');

            if (empty($q)) {
                return $this->respondWithSuccess([]);
            }

            $queries = [
                'orWhereRaw' => [],
            ];

            // Query by keyword
            if ($q) {
                $arrQ = $this->parseToArray(utf8_strtolower($q));
                $keys = ['first_name', 'email', 'phone_number'];
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

            $results = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false)
                ->orderBy('first_name', 'asc')
                ->take(20)
                ->get();

            $output = [];

            foreach ($results as $user) {
                $output[] = [
                    'id'      => $user->id,
                    'name'    => $user->display . ' (' . $user->email . ')',
                    'display' => $user->display,
                ];
            }

            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/users_exports",
     *   summary="Get User Exports",
     *   operationId="userGetUserExports",
     *   tags={"BackendUsers"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function exportExcel() {
        // Check permission
        if (!$this->isCRUD('user_list', 'view')) return $this->errorForbidden();
        list($queries, $results) = $this->getIndex(1, 10000);
        $fields = ['id', 'no', 'customer', 'email', 'phone_number', 'last_login', 'created_at'];
        //=== Get rows
        $rows = [];
        foreach ($results as $k => $result) {
            $item = $result->toArray();
            $newItem = $this->parseToRespond($item);
            $item['customer'] = $result->display;
            $item['phone_number'] = ' ' . $result->phone_number;
            $item['last_login'] = $item['last_login'] ? date('d/m/Y', strtotime($item['last_login'])) : '';
            $item['created_at'] = $item['created_at'] ? date('d/m/Y', strtotime($item['created_at'])) : '';
            $row = [$k + 1];
            foreach ($fields as $field) {
                $row[] = isset($item[$field]) ? $item[$field] : '';
            }
            $rows[] = $row;
        }

        return Excel::download(new \Modules\User\Exports\UserExport($rows), 'users-' . date('Y-m-d') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function resetUserLevel() {
        try {
            $list_rank = $this->user_rank_repository->getModel()->where('status', 1)->orderBy('rank', 'asc')->get();
            if ($list_rank) {
                $previous_rank = $list_rank[0];
                foreach ($list_rank as $index => $rank) {
                    if ($rank->value > 0 && $previous_rank) {
                        $is_last_rank = ($index === count($list_rank) - 1);
                        $this->model_repository->getModel()->where('points', '>', $previous_rank->value)->where('points', '<=', $rank->value)->update(['points' => $previous_rank->value]);
                        if($is_last_rank) {
                            $this->model_repository->getModel()->where('points', '>', $rank->value)->update(['points' => $rank->value]);
                        }
                    }
                    $previous_rank = $rank;
                }
            }
            return $this->respondWithSuccess([]);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    //// Import from Excel
    //private function getImportData() {
    //    $file = $this->request->file('file');
    //    $rows = [];
    //    $reader = Excel::toArray(null, $file->getRealPath(), null, \Maatwebsite\Excel\Excel::XLSX);
    //    $bulkInsertionFields = ['fullname', 'email', 'phone_number', 'temp_password', 'points'];
    //    if (!empty($reader[0])) {
    //        foreach ($reader[0] as $k => $row) {
    //            if ($k == 0) continue;
    //            $item = [];
    //            $numCol = 0;
    //            foreach ($bulkInsertionFields as $fieldName) {
    //                $v = isset($row[$numCol]) ? $row[$numCol] : null;
    //                $v = (!is_null($v) && trim((string)$v) != '') ? trim((string)$v) : null;
    //                $item[$fieldName] = $v;
    //                $numCol++;
    //            }
    //            $rows[] = $item;
    //        }
    //    }
    //    $output = ['invalid' => [], 'valid' => []];
    //    $rules = ['email' => 'required', 'temp_password' => 'required'];
    //    foreach ($rows as $input) {
    //        $newInput = $input;
    //        // Check Valid
    //        $validatorErrors = $this->getValidator($newInput, $rules);
    //        if (!empty($validatorErrors)) {
    //            $input['errors'] = $validatorErrors;
    //            $output['invalid'][] = $input;
    //        } else {
    //            // Check exist email
    //            $exist_email = $this->model_repository->findByAttributes(['email' => $newInput['email']]);
    //            if ($exist_email) {
    //                $input['errors'][0]['errorMessage'] = 'Email đã tồn tại';
    //                $output['invalid'][] = $input;
    //            } else {
    //                $input['insertData'] = $newInput;
    //                $output['valid'][] = $input;
    //            }
    //        }
    //    }
    //
    //    return $output;
    //}
    //
    /**
     * @OA\Post(
     *   path="/backend/users_import_check",
     *   summary="Import Partners Check Valid",
     *   operationId="importPartnersCheck",
     *   tags={"BackendUsers"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object",
     *         @OA\Property(property="file", type="string", format="binary"),
     *         @OA\Property(property="files[]", type="array", @OA\Items(type="string", format="binary")),
     *       ),
     *     )
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    //public function importCheck() {
    //    try {
    //        // Check Valid
    //        $validatorErrors = $this->getValidator($this->request->all(), ['file' => 'required|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    //        if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
    //
    //        $output = $this->getImportData();
    //
    //        return $this->respondWithSuccess($output);
    //    } catch (\Exception $e) {
    //        return $this->errorInternalError($e->getMessage());
    //    }
    //}
    //
    /**
     * @OA\Post(
     *   path="/backend/users_import",
     *   summary="Import Partners",
     *   operationId="importPartners",
     *   tags={"BackendUsers"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object",
     *         @OA\Property(property="file", type="string", format="binary"),
     *         @OA\Property(property="files[]", type="array", @OA\Items(type="string", format="binary")),
     *       ),
     *     )
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    //public function import() {
    //    try {
    //        // Check Valid
    //        $validatorErrors = $this->getValidator($this->request->all(), ['file' => 'required|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    //        if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
    //        $data = $this->getImportData();
    //        $data['newData'] = [];
    //        if (!empty($data['valid'])) {
    //            foreach ($data['valid'] as $input) {
    //                $temp_password = $input['temp_password'];
    //                $fullname = $input['fullname'];
    //                $input['first_name'] = $fullname;
    //                $e = explode('@', $input['email']);
    //                $username = strtolower($e[0]);
    //                $input['username'] = $username;
    //                if ($input['phone_number'] == 'null') unset($input['phone_number']);
    //                $user = $this->model_repository->createWithRoles($input, [USER_ROLE_USER], true);
    //                $user->password = $temp_password;
    //                $user->save();
    //                $output['newData'][] = $user;
    //            }
    //        }
    //        return $this->respondWithSuccess($output);
    //    } catch (\Exception $e) {
    //        return $this->errorInternalError($e->getMessage());
    //    }
    //}
}
