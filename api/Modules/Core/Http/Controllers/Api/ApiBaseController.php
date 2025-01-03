<?php namespace Modules\Core\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Modules\Core\Exceptions\Api\ValidateException;
use Modules\Core\Helper\ErrorFormat;
use Modules\Core\Traits\CommonTrait;

/**
 * Class ApiBaseController
 *
 * @package Modules\Core\Http\Controllers\Api
 */
abstract class ApiBaseController extends Controller {
    use DispatchesJobs, ValidatesRequests, CommonTrait, ApiHelperControllerTrait;

    /**
     * HTTP header status code.
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Eloquent model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model;
     */
    protected $model;

    /**
     * @var \Modules\Core\Repositories\BaseRepository
     */
    protected $model_repository;

    /**
     * @var \Modules\System\Repositories\SettingRepository
     */
    protected $setting_repository;

    /**
     * Illuminate\Http\Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * Locale
     *
     * @var string
     */
    protected $locale = 'en';

    /**
     * Number of items displayed at once if not specified.
     * There is no limit if it is 0 or false.
     *
     * @var int|bool
     */
    protected $defaultLimit = false;

    /**
     * Maximum limit that can be set via $_GET['limit'].
     *
     * @var int|bool
     */
    protected $maximumLimit = 100;

    /**
     * @var int
     */
    protected $pageSize = 12;

    /**
     * @var $apiKeyName
     */
    protected $apiKeyName = 'access_token';

    /**
     * @var $access_token
     */
    protected $access_token = '';

    /**
     * @var Validator
     */
    public $validator;

    /**
     * @var \Modules\Notify\Services\SMS\FptSMS
     */
    public $sms;

    /**
     * @var $email
     */
    public $email;

    /**
     * @var $mobileNotification
     */
    public $mobileNotification;

    /**
     * @var $errorCodes
     */
    public $errorCodes;

    /**
     * @var $validatorMessages
     */
    public $validatorMessages = [];

    /**
     * @var array
     */
    public $defaultQueries = [];

    /**
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->request = $request;
        $this->locale = $request->getLocale();
        $notify = new \Modules\Notify\Services\Notify();
        $this->sms = $notify->sms();
        $this->email = $notify->email();
        $this->mobileNotification = $notify->mobileNotification();
        $this->errorCodes = Lang::get('errorcodes');
        foreach ($this->errorCodes as $key => $value) $this->validatorMessages[$key] = $key;
    }

    /**
     * @param $data
     * @param array $skip_fields : 'deleted_at'
     * @param array $date_fields : 'created_at', 'updated_at'
     * @return array
     */
    protected function getDataTransformer($data, $skip_fields = ['deleted_at'], $date_fields = ['created_at', 'updated_at']) {
        if ($data instanceof Model || $data instanceof Collection) {
            return $this->getDataTransformer($data->toArray());
        } else if (is_array($data)) {
            $response = [];
            foreach ($data as $k => $v) {
                if ($v instanceof Model || $v instanceof Collection) {
                    $response[$k] = $this->getDataTransformer($v->toArray());
                } else if (is_array($v)) {
                    $response[$k] = $this->getDataTransformer($v);
                } else {
                    if (is_string($k)) {
                        if (in_array($k, $date_fields) && strlen($v) > 10) {
                            $response[$k] = date_iso8601($v);
                        } else if (!in_array($k, $skip_fields)) {
                            $response[$k] = $v;
                        }
                    } else {
                        $response[$k] = $v;
                    }
                }
            }
            return $response;
        } else {
            return $data;
        }
    }

    /**
     * Getter for statusCode.
     *
     * @return int
     */
    protected function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * Setter for statusCode.
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    protected function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function responseRawData($data, array $headers = []) {
        return response()->json($data, $this->statusCode, $headers);
    }

    /**
     * Respond with a given array of items.
     *
     * @param array $array
     * @param array $headers
     *
     * @return mixed
     */
    protected function respondWithArray(array $array, array $headers = []) {
        return response()->json($array, $this->statusCode, $headers);
    }

    /***
     * @param string $errorKey
     * @param int $statusCode
     * @param string $messageOverride
     * @param array $headers
     * @param null $data
     * @return mixed
     */
    protected function respondWithErrorKey($errorKey, $statusCode = 400, $messageOverride = '', array $headers = [], $data = null) {
        $parseErrors = [];

        if ($error = $this->getError($errorKey, $messageOverride)) {
            $parseErrors[] = $error;
        } else {
            $error = new ErrorFormat([0, $errorKey], $errorKey);

            if (!empty($messageOverride)) {
                $error->errorMessage = $messageOverride;
            }

            $parseErrors[] = $error;
        }

        $response = [
            'data'   => $data,
            'errors' => $parseErrors,
        ];

        return $this->setStatusCode($statusCode)->respondWithArray($response, $headers);
    }

    /**
     * @param array $errorKeys
     * @param int $statusCode
     * @param array $headers
     * @param null $data
     * @return mixed
     */
    protected function respondWithErrorKeys(array $errorKeys, $statusCode = 400, array $headers = [], $data = null) {
        $parseErrors = [];
        foreach ($errorKeys as $errorKey) {
            if ($error = $this->getError($errorKey)) {
                $parseErrors[] = $error;
            } else {
                $parseErrors[] = new ErrorFormat([0, $errorKey], $errorKey);
            }
        }

        $response = [
            'data'   => $data,
            'errors' => $parseErrors,
        ];

        return $this->setStatusCode($statusCode)->respondWithArray($response, $headers);
    }

    /**
     * @param $message
     * @param int $optional
     * @param int $statusCode
     * @param null $data
     * @return mixed
     */
    protected function respondWithError($message, $optional = 0, $statusCode = 400, $data = null) {
        $optional_data = [];

        if (is_array($optional)) {
            $optional_data = $optional;
        }

        $response = [
            'data'   => $data,
            'errors' => $message,
        ];

        $response = array_merge($response, $optional_data);

        return $this->setStatusCode($statusCode)->respondWithArray($response);
    }

    /***
     * @param string $message
     * @param int $optional
     * @param int $statusCode
     * @param array $skip_fields
     * @param array $date_fields
     * @return mixed
     */
    protected function respondWithSuccess($message = '', $optional = 0, $statusCode = 200, $skip_fields = [], $date_fields = []) {
        $optional_data = [];

        if (is_array($optional)) {
            $optional_data = $optional;
        }

        $responseData = [
            'data' => ($skip_fields && $date_fields) ? $this->getDataTransformer($message, $skip_fields, $date_fields) : $message,
        ];

        $responseData = array_merge($responseData, $optional_data);

        $response = [
            'data'   => $responseData,
            'errors' => null,
        ];

        return $this->setStatusCode($statusCode)->respondWithArray($response);
    }

    /***
     * @param $data
     * @param int $totalCount
     * @param int $pageSize
     * @param int $page
     * @param array $optional
     * @return mixed
     */
    protected function respondWithPaging($data, $totalCount = 0, $pageSize = 0, $page = 0, $optional = []) {
        $optional_data = [
            'pagination' => [
                'total'      => $totalCount,
                'pageSize'   => $pageSize,
                'page'       => $page,
                'totalPages' => ($totalCount == 0 || $pageSize == 0) ? null : ceil($totalCount / $pageSize),
            ],
        ];

        if (is_array($optional) && !empty($optional)) {
            $optional_data = array_merge($optional, $optional_data);
        }

        return $this->respondWithSuccess($data, $optional_data);
    }

    /**
     * Generate a Response with a 500 HTTP header and a given message.
     *
     * @param string $message
     * @param int $statusCode
     *
     * @return Response
     */
    protected function errorInternalError($message = '', $statusCode = 500) {
        return $this->respondWithErrorKey('system.internal_error', $statusCode, $message);
    }

    /**
     * Generate a Response with a 404 HTTP header and a given message.
     *
     * @param string $message
     * @param int $statusCode
     *
     * @return Response
     */
    protected function errorNotFound($message = '', $statusCode = 404) {
        return $this->respondWithErrorKey('system.not_found', $statusCode, $message);
    }

    /**
     * Generate a Response with a 401 HTTP header and a given message.
     *
     * @param string $message
     * @param int $statusCode
     *
     * @return Response
     */
    protected function errorUnauthorized($message = '', $statusCode = 403) {
        return $this->respondWithErrorKey('system.unauthorized', $statusCode, $message);
    }

    /**
     * Generate a Response with a 400 HTTP header and a given message.
     *
     * @param string $message
     * @param int $statusCode
     *
     * @return Response
     */
    protected function errorWrongArgs($message = '', $statusCode = 400) {
        return $this->respondWithErrorKey('system.wrong_arguments', $statusCode, $message);
    }

    /**
     * Generate a Response with a 403 HTTP header and a given message.
     *
     * @param string $message
     * @param int $statusCode
     *
     * @return Response
     */
    protected function errorForbidden($message = '', $statusCode = 403) {
        return $this->respondWithErrorKey('system.forbidden', $statusCode, $message);
    }

    /**
     * Generate a Response with a 501 HTTP header and a given message.
     *
     * @param string $message
     * @param int $statusCode
     *
     * @return Response
     */
    protected function errorNotImplemented($message = '', $statusCode = 501) {
        return $this->respondWithErrorKey('system.not_implemented', $statusCode, $message);
    }

    /**
     * @param Request $request
     * @param Validator $validator
     * @throws ValidateException
     */
    protected function throwValidationException(Request $request, $validator) {
        throw new ValidateException($validator->getMessageBag());
    }

    /**
     * Get ErrorFormat width key
     *
     * @param $errorKey
     * @param string $messageOverride
     * @return ErrorFormat|bool
     */
    protected function getError($errorKey, $messageOverride = '') {
        if (!empty($messageOverride) && isset($this->errorCodes[$messageOverride])) {
            $errorKey = $messageOverride;
            $messageOverride = '';
        }

        if (isset($this->errorCodes[$errorKey])) {
            $error = new ErrorFormat($this->errorCodes[$errorKey], $errorKey);

            if (!empty($messageOverride)) {
                $error->errorMessage = $messageOverride;
            }

            return $error;
        }

        return false;
    }

    /***
     * @param $validator
     * @param array $overrides
     * @return array
     */
    protected function getErrorsWithValidator($validator, $overrides = []) {
        $errors = [];

        foreach ($validator->errors()->all() as $errorKey) {
            if (isset($overrides[$errorKey]) && $error = $this->getError($overrides[$errorKey])) {
                $errors[] = $error;
            } else if ($error = $this->getError($errorKey)) {
                $errors[] = $error;
            } else {
                $errors[] = new ErrorFormat([0, $errorKey], $errorKey);
            }
        }

        return $errors;
    }

    /***
     * @param array $data
     * @param array $role
     * @param array $overrides
     * @param array $messages
     * @return array
     */
    protected function getValidator(array $data, array $role, array $overrides = [], array $messages = []) {
        if (!empty($messages)) {
            $validator = Validator::make($data, $role, $messages);
        } else {
            $validator = Validator::make($data, $role, $this->validatorMessages);
        }

        if ($validator->fails()) {
            return $this->getErrorsWithValidator($validator, $overrides);
        }

        return [];
    }

    /************************************************************
     ********************** PUSH NOTIFICATION FUNCTION ***********************
     ************************************************************/

    /**
     * @param \Modules\User\Entities\Sentinel\User $toUser
     * @param $pushMessage
     * @param $pushType
     * @param $pushData
     * @param null $object_id
     * @param string|null|boolean $setting
     * @return array|bool|void
     */
    protected function pushNotification($toUser, $pushMessage, $pushType, $pushData, $object_id = null, $setting = 'is_notify') {
        $data = [
            'user_id' => $toUser->id, 'message' => $pushMessage, 'type' => $pushType, 'object_id' => $object_id, 'data' => json_encode($pushData),
            'setting' => $setting, 'pushData' => $pushData,
        ];
        //(new \Modules\Core\Jobs\PushNotification(null, $toUser, $data))->handle();
        dispatch(new \Modules\Core\Jobs\PushNotification(null, $toUser, $data));
    }

    /***
     * Push Notification
     * @param array | object $userData | receiver
     * @param string $pushMessage
     * @param string | integer $pushType
     * @param array $pushData
     * @param null | integer $object_id
     * @param bool | string $setting
     * @return mixed
     */
    protected function pushNotifications($userData, $pushMessage, $pushType, $pushData, $object_id = null, $setting = 'is_notify') {
        $pushData['type'] = $pushType;

        if (is_array($userData) || $userData instanceof Collection) {
            foreach ($userData as $user) {
                $this->pushNotification($user, $pushMessage, $pushType, $pushData, $object_id, $setting);
            }
        } else {
            $this->pushNotification($userData, $pushMessage, $pushType, $pushData, $object_id, $setting);
        }
    }

    /************************************************************
     ********************** CRUD FUNCTION ***********************
     ************************************************************/

    /**
     * Eloquent model.
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    protected function model() {
        return $this->model_repository->getModel();
    }

    /**
     * Transformer for the current model.
     *
     * @return \League\Fractal\TransformerAbstract
     */
    protected function transformer() {
        return $this->transformer;
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreate() {
        return [];
    }

    /**
     * Get the validation rules for update.
     *
     * @param int $id
     *
     * @return array
     */
    protected function rulesForUpdate($id) {
        return [];
    }

    //<editor-fold desc="Common Publish Fn">

    /**
     * Show all resource without paging
     */
    public function all() {
        $results = $this->model_repository->all();

        return $this->respondWithSuccess($results);
    }

    /**
     * Show all resource with paging
     */
    public function index() {
        try {
            $page = (int)$this->request->get('page');
            $pageSize = (int)$this->request->get('pageSize');
            $sort = (string)$this->request->get('sort');
            $order = (string)$this->request->get('order');
            if (!$page) {
                $page = 1;
            }
            if (!$pageSize) {
                $pageSize = $this->pageSize;
            }
            if (!$sort) {
                $sort = 'id';
            } else {
                $sort = strtolower($sort);
            }
            if (!$order) {
                $order = 'DESC';
            } else {
                $order = strtoupper($order);
            }

            $queries = $this->defaultQueries;

            $results = $this->setUpQueryBuilder($this->model(), $queries, false)
                ->orderBy($sort, $order)
                ->take($pageSize)
                ->skip($pageSize * ($page - 1))
                ->get();
            $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();

            return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * Show detail resource
     *
     * @param int $id
     * @return mixed|Response
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
     * Create new resource
     */
    public function store() {
        try {
            $input = $this->request->all();

            // Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), $this->rulesForCreate());
            if (!empty($validatorErrors)) {
                return $this->respondWithError($validatorErrors);
            }

            // Create Model
            $model = $this->model_repository->create(array_merge($input, []));

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * Update resource
     *
     * @param int $id
     * @return mixed|Response
     */
    public function update($id) {
        try {
            $input = $this->request->all();

            $model = $this->model_repository->find($id);
            if (!$model) {
                return $this->errorNotFound();
            }

            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) {
                return $this->respondWithError($validatorErrors);
            }

            // Update Model
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * Delete resource
     *
     * @param int $id
     * @return mixed|Response
     */
    public function destroy($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) {
                return $this->errorNotFound();
            }
            // Delete model
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans('Delete success'));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
    //</editor-fold>
}
