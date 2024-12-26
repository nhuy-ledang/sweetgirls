<?php namespace Modules\Usr\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\Staff\Repositories\UserRepository as StaffRepository;
use Modules\System\Repositories\SettingRepository;
use Modules\Usr\Repositories\UserRepository;

/**
 * Class UsrController
 *
 * @package Modules\User\Http\Controllers\Api
 * @author Huy D <huydang1920@gmail.com>
 * Date: 2022-07-29
 */
class UsrController extends ApiBaseModuleController {
    /**
     * @var \Modules\Usr\Sentinel\UserRepositoryInterface
     */
    protected $model_repository;

    /**
     * @var \Modules\Staff\Repositories\UserRepository
     */
    protected $staff_repository;

    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    protected $file_repository;

    /**
     * @var \Modules\Media\Services\FileService
     */
    protected $fileService;

    /**
     * @var \Modules\System\Repositories\SettingRepository;
     */
    protected $setting_repository;

    public function __construct(Request $request,
                                FileRepository $file_repository,
                                FileService $fileService,
                                UserRepository $usr_repository,
                                StaffRepository $staff_repository,
                                SettingRepository $setting_repository) {
        $this->model_repository = $usr_repository;
        $this->file_repository = $file_repository;
        $this->fileService = $fileService;
        $this->staff_repository = $staff_repository;
        $this->setting_repository = $setting_repository;

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
     * @OA\Get(
     *   path="/backend/usrs_all",
     *   summary="Get Usrs All",
     *   operationId="getUsrsAll",
     *   tags={"BackendUsrs"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function all() {
        try {
            $results = $this->model_repository->getModel()->orderBy('first_name', 'asc')->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/usrs",
     *   summary="Get Usrss",
     *   operationId="getUsrs",
     *   tags={"BackendUsrs"},
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
            if (!$this->isCRUD('administrator', 'view')) return $this->errorForbidden();
            // Check admin
            //if (!$this->isAdmin()) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'id' : '' . strtolower($sort);
            $order = (string)$this->request->get('order');
            $order = $order ? strtolower($order) : 'asc';
            $data = $this->getRequestData();
            $queries = [
                'and'        => [],
                'or'         => [],
                'in'         => [],
                'whereRaw'   => [],
                'orWhereRaw' => [],
            ];
            $role_id = isset($data->{'role_id'}) ? (int)$data->{'role_id'} : false;
            if ($role_id) {
                $queries['withHas'] = [
                    'roles' => [
                        ['role_id', '=', $role_id],
                    ],
                ];
            }

            // Query by keyword
            $q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(trim(utf8_strtolower($q)));
                $keys = ['email', 'phone_number', 'first_name', 'last_name'];
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

            $results = $this->setUpQueryBuilder($this->model(), $queries)
                ->orderBy($sort, $order)
                ->take($pageSize)
                ->skip($pageSize * ($page - 1))
                ->get();

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
     *   path="/backend/usrs/{id}",
     *   summary="Get a Usr",
     *   operationId="getUsr",
     *   tags={"BackendUsrs"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Usr Id", example="1"),
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
            $model = $this->setUpQueryBuilder($this->model(), [], false)->where('id', $id)->first();
            if (!$model) {
                return $this->errorNotFound();
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usrs",
     *   summary="Create Usr",
     *   operationId="createUsr",
     *   tags={"BackendUsrs"},
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
            if (!$this->isCRUD('administrator', 'create')) return $this->errorForbidden();
            // Check admin
            //if (!$this->isAdmin()) return $this->errorForbidden();
            $input = $this->request->only(['first_name', 'birthday', 'gender', 'group_id', 'avatar', 'avatar_url']);
            $rules = ['email' => 'required|email'];
            $email = $this->request->get('email');
            if ($email) {
                $email = trim(strtolower((string)$email));
                $input['email'] = $email;
            }
            $calling_code = null;
            $phone_number = $this->request->get('phone_number');
            if ($phone_number) {
                list($calling_code, $phone_number) = calling2phone($phone_number);
                if ($phone_number) {
                    $input['calling_code'] = $calling_code;
                    $input['phone_number'] = $phone_number;
                }
            }
            $password = $this->request->get('password');
            if ($password) {
                $input['password'] = $password;
                $rules['password'] = 'required|min:6';
            } else {
                $random_password = str_random_alpha_numeric(10, true, true);
                $input['password'] = $random_password;
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
            $model = null;
            if ($email) {
                $model = $this->model_repository->getModel()->where('email', $email)->withTrashed()->first();
                if ($model && !$model->trashed()) return $this->respondWithErrorKey('email.unique');
            }
            /*if ($calling_code && $phone_number) {
                $model = $this->model_repository->getModel()->whereRaw('(`calling_code` = ? and `phone_number` = ?)', [$calling_code, $phone_number])->withTrashed()->first();
                if ($model && !$model->trashed()) return $this->respondWithErrorKey('phone_number.unique');
            }*/
            // Restore if delete
            if ($model && $model->trashed()) $model->restore();
            // Create user starter
            if ($model) {
                $model = $this->model_repository->activateUser($model, $input, [USR_ROLE_USER], true);
            } else {
                $model = $this->model_repository->createWithRoles($input, [USR_ROLE_USER], true);
            }
            // Upload avatar
            if ($file_path) {
                $model = $this->model_repository->update($model, ['avatar' => $file_path]);
            } else {
                if ($file) {
                    $savedFile = $this->fileService->store($file, ['sub' => MEDIA_SUB_AVATAR, 'object_id' => $model->id]);
                    if (!is_string($savedFile)) $model = $this->model_repository->update($model, ['avatar' => $savedFile->path]);
                }
            }
            // Send email notification
            if ($model->email) {
                $data['html'] = '';
                $data['model'] = $model;
                $data['password'] = $input['password'];
                $data['config_owner'] = $this->setting_repository->findByKey('config_owner');
                dispatch(new \Modules\Usr\Jobs\SendUserCreated($this->email, $data));
            }
            /*// Send push notification
            $toUser = $user;
            $fromUser = $this->auth;
            $pushType = NOTIFY_TYPE_PLACE_USER_CREATE;
            $pushMessage = sprintf(Lang::get('core::messages.push_notify.' . $pushType), $place->name);
            $object_id = $place->id;
            $pushData = [
                'user'     => [
                    'id'         => $fromUser->id,
                    'display'    => $fromUser->display,
                    'avatar_url' => $fromUser->avatar_url,
                ],
                'place'    => [
                    'id'            => $place->id,
                    'name'          => $place->name,
                    'thumb_url'     => $place->thumb_url,
                ],
                'messages' => [Lang::get('core::messages.push_notify.' . $pushType), $place->name]
            ];
            $this->pushNotification($toUser, $pushMessage, $pushType, $pushData, $object_id);*/
            // Link to staff
            $this->staff_repository->getModel()->where('email', $model->email)->whereNotNull('email')->update(['usr_id' => $model->id]);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/usrs/{id}",
     *   summary="Update Usr",
     *   operationId="updateUsr",
     *   tags={"BackendUsrs"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="email", type="string", format="email", example="email@gmail.com"),
     *       @OA\Property(property="phone_number", type="string", example="12345678"),
     *       @OA\Property(property="password", type="string", example="12345678"),
     *       @OA\Property(property="first_name", type="string", example=""),
     *       @OA\Property(property="birthday", type="string", example="2019-04-02"),
     *       @OA\Property(property="gender", type="string", example="0:unknown or 1:male or 2:female"),
     *       @OA\Property(property="address", type="string", example="address"),
     *       @OA\Property(property="group_id", type="integer", example=1),
     *     ),
     *   ),
     *   @OA\Parameter(name="id", in="path", description="Usr Id", example="1"),
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
            if (!$this->isCRUD('administrator', 'edit')) return $this->errorForbidden();
            // Check admin
            //if (!$this->isAdmin()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $input = $this->request->only(['first_name', 'birthday', 'gender', 'address', 'group_id']);
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
                $total = $this->model_repository->getModel()->where('id', '<>', $id)->where('email', $email)->withTrashed()->count();
                if ($total) return $this->respondWithErrorKey('email.unique');
            }
            if ($phone_number) {
                $total = $this->model_repository->getModel()->where('id', '<>', $id)->whereRaw('(`calling_code` = ? and `phone_number` = ?)', [$calling_code, $phone_number])->withTrashed()->count();
                if ($total) return $this->respondWithErrorKey('phone_number.unique');
            }
            // Upload avatar
            if ($file_path) {
                $input['avatar'] = $file_path;
            } else {
                if ($file) {
                    // Unlink old avatar
                    /*$oldFile = null;
                    if ($model->avatar) $oldFile = $this->file_repository->findByAttributes(['object' => MEDIA_SUB_AVATAR, 'path' => $model->avatar]);*/
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
            // Link to staff
            $this->staff_repository->getModel()->where('email', $model->email)->whereNotNull('email')->update(['usr_id' => $model->id]);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/usrs/{id}",
     *   summary="Delete Usr",
     *   operationId="deleteUsr",
     *   tags={"BackendUsrs"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Usr Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        try {
            // Check permission
            if (!$this->isCRUD('administrator', 'delete')) return $this->errorForbidden();
            // Check admin
            //if (!$this->isAdmin()) return $this->errorForbidden();
            $model = $this->model_repository->getModel()->where('id', $id)->withTrashed()->first();
            if (!$model) return $this->errorNotFound();
            // Link to staff
            $this->staff_repository->getModel()->where('usr_id', $model->id)->update(['usr_id' => null]);
            // Delete model
            $this->model_repository->destroy($model);
            //$model->forceDelete();
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
     *   path="/backend/usrs/{id}/banned",
     *   summary="Banned Usr",
     *   operationId="bannedUsr",
     *   tags={"BackendUsrs"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"banned"},
     *       @OA\Property(property="banned", type="string", example="0 or 1"),
     *     ),
     *   ),
     *   @OA\Parameter(name="id", in="path", description="Usr Id", example="1"),
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
            if (!$this->isCRUD('administrator', 'edit')) return $this->errorForbidden();
            // Check admin
            //if (!$this->isAdmin()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
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
            /*$user = $model;
            $phone_number = phone2local('+' . $user->calling_code . $user->phone_number);

            // Send email notify
            if ($user->email) {
                $optional = [
                    'display'      => $user->display,
                    'email'        => $user->email,
                    'phone_number' => $phone_number,
                    'status'       => $user->status,
                ];
                dispatch(new \Modules\Usr\Jobs\SendUserBanned($this->email, $optional));
            }
            // Send sms notify
            if ($phone_number) {
                $model->sms_log = $this->sms->send($phone_number, $user->status == USER_STATUS_ACTIVATED ? Lang::get('messages.sms_content.user_unbanned') : Lang::get('messages.sms_content.user_banned'));
            }*/

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/usrs/{id}/roles",
     *   summary="Update Usr Roles",
     *   operationId="updateUsrRoles",
     *   tags={"BackendUsrs"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="role_ids", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Parameter(name="id", in="path", description="Usr Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function roles($id) {
        try {
            // Check permission
            if (!$this->isCRUD('administrator', 'edit')) return $this->errorForbidden();
            // Check admin
            //if (!$this->isAdmin()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $ids = (string)$this->request->get('role_ids');
            $role_ids = [];
            foreach (explode(',', $ids) as $id) {
                if (intval($id)) $role_ids[] = (int)$id;
            }
            $role_ids = array_unique($role_ids);
            $roles = $this->model_repository->syncRoles($model, $role_ids);
            return $this->respondWithSuccess($roles);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
