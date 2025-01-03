<?php namespace Modules\Staff\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Staff\Repositories\SalaryRepository;
use Modules\Staff\Repositories\UserRepository;

/**
 * Class SalaryController
 *
 * @package Modules\Salary\Http\Controllers\Api
 * @author Huy D <huydang1920@gmail.com>
 */
class SalaryController extends ApiBaseModuleController {

    /**
     * @var \Modules\Staff\Repositories\UserRepository
     */
    protected $user_repository;

    public function __construct(Request $request,
                                UserRepository $user_repository,
                                SalaryRepository $salary_repository) {
        $this->model_repository = $salary_repository;
        $this->user_repository = $user_repository;

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
            'user_id' => 'required|integer',
            'date'    => 'required|date_format:"Y-m-d"',
            'salary'  => 'required|numeric|min:0',
            'real'    => 'required|numeric|min:0',
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
            'salary' => 'required|numeric|min:0',
            'real'   => 'required|numeric|min:0',
        ];
    }

    /**
     * @OA\Get(
     *   path="/backend/st_salaries_all",
     *   summary="Get Salaries All",
     *   operationId="getSalariesAll",
     *   tags={"BackendStSalaries"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function all() {
        try {
            $queries = ['and' => []];
            $data = $this->getRequestData();
            $status = (isset($data->{'status'}) && !is_null($data->{'status'}) && $data->{'status'} !== '') ? (int)$data->{'status'} : false;
            if (!(is_null($status) || $status === false)) {
                $temps = $this->user_repository->getModel()->where('status', $status)->select([\DB::raw('MIN(start_date) as start_date')])->first();
            } else {
                $temps = $this->user_repository->getModel()->select([\DB::raw('MIN(start_date) as start_date')])->first();
            }
            $start_date = $temps && $temps->start_date ? $temps->start_date : date('Y-m-d');
            $start_date = date('Y-m-01', strtotime('+1 month', strtotime($start_date)));
            $end_date = date('Y-m-01');
            $dates = [];
            while (strtotime($start_date) <= strtotime($end_date)) {
                $dates[] = $start_date;
                $start_date = date('Y-m-01', strtotime('+1 month', strtotime($start_date)));
            }
            $fields = ['*'];
            $results = $this->setUpQueryBuilder($this->model(), $queries, false, $fields)->get();
            $obj = [];
            foreach ($results as $result) {
                if (!isset($obj[$result->user_id])) $obj[$result->user_id] = [];
                $obj[$result->user_id][$result->date] = $result;
            }
            if (!(is_null($status) || $status === false)) {
                $users = $this->user_repository->getModel()->where('status', 1)->orderBy('fullname', 'asc')->get();
            } else {
                $users = $this->user_repository->getModel()->orderBy('fullname', 'asc')->get();
            }
            $output = [];
            foreach ($users as $user) {
                $tDates = [];
                $start_date = $user->start_date ? $user->start_date : date('Y-m-d');
                $start_date = date('Y-m-01', strtotime('+1 month', strtotime($start_date)));
                $end_date = $user->end_date ? $user->end_date : date('Y-m-01');
                foreach ($dates as $date) {
                    $v = isset($obj[$user->id]) && isset($obj[$user->id][$date]) ? $obj[$user->id][$date] : [
                        'date'   => $date,
                        'salary' => $user->salary,
                        'real'   => $user->salary,
                    ];
                    if (!(strtotime($start_date) <= strtotime($date) && strtotime($date) <= strtotime($end_date))) {
                        $v = null;
                    }
                    $tDates[] = $v;
                }
                $user->dates = $tDates;
                $output[] = $user;
            }

            return $this->respondWithSuccess($output, ['dates' => $dates]);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/st_salaries",
     *   summary="Get Salaries",
     *   operationId="getSalaries",
     *   tags={"BackendStSalaries"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields, extend_fields: Extend fields query} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example="%7B%22extend_fields%22%3A%22total_students%22%7D"),
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
            $data = $this->getRequestData();
            $user_id = isset($data->{'user_id'}) ? (int)$data->{'user_id'} : 0;
            $queries = [
                'and'        => [
                    ['user_id', '=', $user_id],
                ],
                'in'         => [],
                'whereRaw'   => [],
                'orWhereRaw' => [],
            ];
            /*// Query by keyword
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
            }*/
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
     *   path="/backend/st_salaries/{id}",
     *   summary="Get a Salary",
     *   operationId="getSalary",
     *   tags={"BackendStSalaries"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Salary Id", example="1"),
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
     *   path="/backend/st_salaries",
     *   summary="Create Salary",
     *   operationId="createSalary",
     *   tags={"BackendStSalaries"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="user_id", type="integer", example=0),
     *       @OA\Property(property="date", type="string", example="2019-04-02"),
     *       @OA\Property(property="salary", type="integer", example=0),
     *       @OA\Property(property="real", type="integer", example=0),
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
            $input = $this->request->only(['user_id', 'date', 'salary', 'real']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $user = $this->user_repository->find($input['user_id']);
            if (!$user) return $this->errorWrongArgs('user_id.required');
            $start_date = $user->start_date ? $user->start_date : date('Y-m-d');
            $start_date = date('Y-m-01', strtotime('+1 month', strtotime($start_date)));
            $end_date = $user->end_date ? $user->end_date : date('Y-m-01');
            $date = date('Y-m-01', strtotime($input['date']));
            if (!(strtotime($start_date) <= strtotime($date) && strtotime($date) <= strtotime($end_date))) {
                return $this->errorWrongArgs('date.date_format');
            }
            $salary = (float)$this->request->get('salary');
            $real = (float)$this->request->get('real');
            //if ($real > $salary) $real = $salary;
            $input['date'] = $date;
            $input['salary'] = $salary;
            $input['real'] = $real;
            $input['debt'] = $salary - $real;
            // Check exist
            $model = $this->model_repository->findByAttributes(['user_id' => $input['user_id'], 'date' => $input['date']]);
            if (!$model) {
                $model = $this->model_repository->create($input);
            } else {
                $model = $this->model_repository->update($model, $input);
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/st_salaries/{id}",
     *   summary="Update Salary",
     *   operationId="updateSalary",
     *   tags={"BackendStSalaries"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Salary Id", example="1"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="user_id", type="integer", example=0),
     *       @OA\Property(property="date", type="string", example="2019-04-02"),
     *       @OA\Property(property="salary", type="integer", example=0),
     *       @OA\Property(property="real", type="integer", example=0),
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
            $input = $this->request->only(['salary', 'real']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForUpdate($id));
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $salary = (float)$this->request->get('salary');
            $real = (float)$this->request->get('real');
            //if ($real > $salary) $real = $salary;
            $input['salary'] = $salary;
            $input['real'] = $real;
            $input['debt'] = $salary - $real;
            // Update Model
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *   path="/backend/st_salaries/{id}",
     *   summary="Update Salary Partial",
     *   operationId="UpdateSalaryPartial",
     *   tags={"BackendStSalaries"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Salary Id", example="1"),
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
     *   path="/backend/st_salaries/{id}",
     *   summary="Delete Salary",
     *   operationId="deleteSalary",
     *   tags={"BackendStSalaries"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Salary Id", example=1),
     *   @OA\Parameter(name="salary_id", in="path", description="Salary Id", example="1"),
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

    /**
     * @OA\Get(
     *   path="/backend/st_salaries_stats",
     *   summary="Get Salaries Stats",
     *   operationId="getSalariesStats",
     *   tags={"BackendStSalaries"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function stats() {
        try {
            $data = $this->getRequestData();
            $status = (isset($data->{'status'}) && !is_null($data->{'status'}) && $data->{'status'} !== '') ? (int)$data->{'status'} : false;
            if (!(is_null($status) || $status === false)) {
                $temps = $this->user_repository->getModel()->where('status', $status)->select([\DB::raw('MIN(start_date) as start_date')])->first();
            } else {
                $temps = $this->user_repository->getModel()->select([\DB::raw('MIN(start_date) as start_date')])->first();
            }
            $start_date = $temps && $temps->start_date ? $temps->start_date : date('Y-m-d');
            $start_year = (int)date('Y', strtotime($start_date));
            $end_year = (int)date('Y');
            $fields = [
                'user_id',
                \DB::raw('YEAR(`date`) as `year`'),
                \DB::raw('SUM(`salary`) as `salary`'),
                \DB::raw('SUM(`real`) as `real`'),
                \DB::raw('SUM(`debt`) as `debt`'),
            ];
            $results = $this->setUpQueryBuilder($this->model(), [], false, $fields)->groupBy(['user_id', 'year'])->orderBy('year')->get();
            $obj = [];
            foreach ($results as $result) {
                if (!isset($obj[$result->user_id])) $obj[$result->user_id] = [];
                $obj[$result->user_id][$result->year] = $result;
                if ((int)$result->year < $start_year) $start_year = (int)$result->year;
                if ((int)$result->year > $end_year) $end_year = (int)$result->year;
            }
            $years = [];
            while ($start_year <= $end_year) {
                $years[] = $start_year;
                $start_year++;
            }
            if (!(is_null($status) || $status === false)) {
                $users = $this->user_repository->getModel()->where('status', 1)->orderBy('fullname', 'asc')->get();
            } else {
                $users = $this->user_repository->getModel()->orderBy('fullname', 'asc')->get();
            }
            $output = [];
            foreach ($users as $user) {
                $tYears = [];
                $salary = 0;
                $real = 0;
                $debt = 0;
                foreach ($years as $year) {
                    $v = isset($obj[$user->id]) && isset($obj[$user->id][$year]) ? $obj[$user->id][$year] : null;
                    if ($v) {
                        $salary += $v->salary;
                        $real += $v->real;
                        $debt += $v->debt;
                    }
                    $tYears[] = $v;
                }
                $user->years = $tYears;
                $user->salary = $salary;
                $user->real = $real;
                $user->debt = $debt;
                $output[] = $user;
            }

            return $this->respondWithSuccess($output, ['years' => $years]);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
