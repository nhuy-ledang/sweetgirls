<?php namespace Modules\System\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\System\Repositories\TranslateRepository;

/**
 * Class TranslateController
 * @package Modules\Core\Http\Controllers\Api
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class TranslateController extends ApiBaseModuleController {
    protected $hidden = [];

    public function __construct(Request $request, TranslateRepository $translate_repository) {
        $this->model_repository = $translate_repository;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     * @return array
     */
    protected function rulesForCreate() {
        return [
            'key'       => 'required',
            'lang'      => 'required',
            'value'     => 'required',
            'translate' => 'required',
        ];
    }

    /**
     * Get the validation rules for update.
     * @param int $id
     * @return array
     */
    protected function rulesForUpdate($id) {
        return [];
    }

    /**
     * @OA\Get(
     *   path="/backend/sys_translates",
     *   summary="Get System Translates",
     *   operationId="getSystemTranslates",
     *   tags={"BackendSysTranslates"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="App-Env", in="query", description="ENV", example="cms"),
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields, extend_fields:total_courses} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
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
            $queries = [
                'and'        => [
                    ['lang', '=', 'vi'],
                ],
                'orWhereRaw' => []
            ];
            $data = $this->getRequestData();
            // Query by keyword
            $q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
            if ($q) {
                $arrQ = $this->parseToArray(utf8_strtolower($q));
                $keys = ['key', 'value', 'translate'];
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
                ->groupBy('key')
                ->orderBy('value', 'asc')
                ->take($pageSize)
                ->skip($pageSize * ($page - 1))
                ->get();
            $output = [];
            foreach ($results as $result) {
                $temps = $this->model_repository->getModel()->where('key', $result->key)->orderBy('lang', 'asc')->get();
                $translates = ['vi' => [], 'en' => []];
                foreach ($temps as $temp) {
                    $translates[$temp->lang] = ['lang' => $temp->lang, 'translate' => $temp->translate];
                }
                $result->translates = array_values($translates);
                $output[] = $result;
            }
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging === 'true' ? true : ($paging === 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($output);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count(\DB::raw('distinct `key`'));
                return $this->respondWithPaging($output, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/sys_translates/{id}",
     *   summary="Get System Translates",
     *   operationId="getSystemTranslates",
     *   tags={"BackendSysTranslates"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Translate Id - Home Top: 1", example="1"),
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
            $temps = $this->model_repository->getModel()->where('key', $model->key)->orderBy('lang', 'asc')->get();
            $translates = ['vi' => [], 'en' => []];
            foreach ($temps as $temp) {
                $translates[$temp->lang] = ['lang' => $temp->lang, 'translate' => $temp->translate];
            }
            $model->translates = array_values($translates);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/sys_translates",
     *   summary="Create System Translate",
     *   operationId="createSystemTranslate",
     *   tags={"BackendSysTranslates"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="key", type="string", example=""),
     *       @OA\Property(property="value", type="string", example=""),
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
    public function store() {
        try {
            // Check Valid
            $validatorErrors = $this->getValidator($this->request->all(), ['key' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $key = str_replace('-', '_', to_alias((string)$this->request->get('key')));
            $value = (string)$this->request->get('value');
            $translates = $this->request->get('translates');
            if (is_string($translates)) $translates = json_decode($translates, true);
            if (!($key && $value && $translates && is_array($translates))) return $this->errorWrongArgs();
            $this->model_repository->getModel()->where('key', $key)->delete();
            $models = [];
            foreach ($translates as $trans) {
                $models[] = $this->model_repository->create(['key' => $key, 'value' => $value, 'lang' => $trans['lang'], 'translate' => $trans['translate']]);
            }

            return $this->respondWithSuccess($models);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/sys_translates/{id}",
     *   summary="Delete System Translate",
     *   operationId="deteleSystemTranslate",
     *   tags={"BackendSysTranslates"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Translate Id - Home Top: 1", example="1"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function destroy($id) {
        try {
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();

            $this->model_repository->getModel()->where('key', $model->key)->delete();

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
