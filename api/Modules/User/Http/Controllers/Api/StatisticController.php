<?php namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Order\Repositories\OrderRepository;
use Modules\User\Repositories\UserRepository;

/**
 * Class StatisticController
 *
 * @package Modules\Order\Http\Controllers\Api
 */
class StatisticController extends ApiBaseModuleController {
    /**
     * @var \Modules\Order\Repositories\OrderRepository
     */
    protected $order_repository;

    /**
     * @var \Modules\User\Repositories\UserRepository
     */
    protected $user_repository;

    public function __construct(Request $request,
                                OrderRepository $order_repository,
                                UserRepository $user_repository) {
        $this->model_repository = $order_repository;
        $this->user_repository = $user_repository;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    // today,yesterday,this_week,last_week,this_month,last_month,this_year,last_year
    public function getReportDates($mode = '') {
        $data = $this->getRequestData();
        if (!$mode) $mode = !empty($data->{'mode'}) ? $data->{'mode'} : '';
        $start_date = !empty($data->{'start_date'}) ? $data->{'start_date'} : date('Y-m-d');
        $end_date = !empty($data->{'end_date'}) ? $data->{'end_date'} : date('Y-m-d');
        if ($mode == 'this_year' || $mode == 'last_year') {
            $start_date = date('Y-01-01', strtotime($start_date));
            $end_date = date('Y-12-31', strtotime($start_date));
            $last_start_date = date('Y-01-01', strtotime('-1 year', strtotime($start_date)));
            $last_end_date = date('Y-12-31', strtotime($last_start_date));
        } else if ($mode == 'this_month' || $mode == 'last_month') {
            $start_date = date('Y-m-01', strtotime($start_date));
            $end_date = date('Y-m-t', strtotime($start_date));
            $last_start_date = date('Y-m-01', strtotime('-1 month', strtotime($start_date)));
            $last_end_date = date('Y-m-t', strtotime($last_start_date));
        } else if ($mode == 'this_week' || $mode == 'last_week') {
            $start_date = date("Y-m-d", strtotime('monday this week', strtotime($start_date)));
            $end_date = date("Y-m-d", strtotime('sunday this week', strtotime($start_date)));
            $last_start_date = date("Y-m-d", strtotime('-7 day', strtotime($start_date)));
            $last_end_date = date("Y-m-d", strtotime('+6 day', strtotime($last_start_date)));
        } else if ($mode == 'today' || $mode == 'yesterday') {
            $end_date = $start_date;
            $last_start_date = date('Y-m-d', strtotime('-1 day', strtotime($start_date)));
            $last_end_date = $last_start_date;
        } else {
            $last_start_date = $start_date;
            $last_end_date = $end_date;
        }

        return [$mode, $start_date, $end_date, $last_start_date, $last_end_date];
    }

    /**
     * Get Buyer Index
     *
     * @param $page
     * @param $pageSize
     * @param string $sort
     * @param string $order
     * @return array
     */
    protected function getBuyerRankIndex($page, $pageSize, $sort = '', $order = '') {
        if (!$sort) {
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'buyers_rank' : '' . strtolower($sort);
        }
        if (!$order) {
            $order = (string)$this->request->get('order');
            $order = $order ? strtolower($order) : 'asc';
        }
        $queries = [
            'and'        => [
                //['agent_id', '=', null]
            ],
            'in'         => [],
            'whereRaw'   => [["`users`.`id` NOT IN (SELECT `user_id` FROM `aff__agents` WHERE `user_id` IS NOT NULL AND `status` = 1)"]],
            'orWhereRaw' => [],
        ];

        $data = $this->getRequestData();
        $start_date = (isset($data->{'start_date'}) && !is_null($data->{'start_date'}) && $data->{'start_date'} !== '') ? (string)$data->{'start_date'} : false;
        $end_date = (isset($data->{'end_date'}) && !is_null($data->{'end_date'}) && $data->{'end_date'} !== '') ? (string)$data->{'end_date'} : false;
        $name = trim(utf8_strtolower((isset($data->{'name'}) && !is_null($data->{'name'}) && $data->{'name'} !== '') ? trim((string)$data->{'name'}) : ''));
        if ($name) $queries['whereRaw'][] = ["lower(`users`.`name`) like ?", "%$name%"];

        if ($start_date && $end_date) {
            $queries['whereRaw'][] = ["`orders`.`order_status` = ? AND (orders.`created_at` BETWEEN ? AND ?)", [ORDER_SS_COMPLETED, $start_date, $end_date]];
        } else {
            $queries['whereRaw'][] = ["`orders`.`order_status` = ?", [ORDER_SS_COMPLETED]];
        }

        $fields = [
            'users.id',
            'users.first_name',
            'users.points',
            'users.email',
            'users.phone_number',
            \DB::raw("sum(orders.total) as total_orders"),
        ];
        $results = $this->setUpQueryBuilder($this->user_repository->getModel(), $queries, false, $fields)
            // ->leftJoin('aff__agents as aa', 'aa.user_id', '=', 'users.id')
            ->leftJoin('orders', 'orders.user_id', '=', 'users.id')
            ->groupBy('users.id')
            ->havingRaw("SUM(orders.total) > 0")
            ->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
        return [$queries, $results];
    }

    /**
     * @OA\Get(
     *   path="/backend/user_stats/buyers_rank",
     *   summary="Get Buyers Rank",
     *   operationId="getBuyersRank",
     *   tags={"BackendUsersStats"},
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
    public function indexBuyerRank() {
        try {
            // Check permission
            //if (!$this->isCRUD('orders', 'view')) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            list($queries, $results) = $this->getBuyerRankIndex($page, $pageSize);
            $output = [];
            if ($results) {
                foreach ($results as $item) {
                    $newItem = $this->parseToRespond($item->toArray());
                    $output[] = $newItem;
                }
            }

            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging === 'true' ? true : ($paging === 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($output);
            } else {
                $query = $this->setUpQueryBuilder($this->user_repository->getModel(), $queries, true)
                    // ->leftJoin('aff__agents as aa', 'aa.user_id', '=', 'users.id')
                    ->leftJoin('orders', 'orders.user_id', '=', 'users.id')
                    ->groupBy('users.id')
                    ->havingRaw("SUM(orders.total) > 0")
                    ->get();
                $totalCount = $query->count();
                return $this->respondWithPaging($output, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }


    /**
     * Get Birthday Index
     *
     * @param $page
     * @param $pageSize
     * @param string $sort
     * @param string $order
     * @return array
     */
    protected function getBirthdayIndex($page, $pageSize, $sort = '', $order = '') {
        if (!$sort) {
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'birthday' : '' . strtolower($sort);
        }
        if (!$order) {
            $order = (string)$this->request->get('order');
            $order = $order ? strtolower($order) : 'asc';
        }
        $queries = [
            'and'        => [
                //['agent_id', '=', null]
            ],
            'in'         => [],
            'whereRaw'   => [],
            'orWhereRaw' => [],
        ];

        $data = $this->getRequestData();
        $start_date = (isset($data->{'start_date'}) && !is_null($data->{'start_date'}) && $data->{'start_date'} !== '') ? (string)$data->{'start_date'} : false;
        $end_date = (isset($data->{'end_date'}) && !is_null($data->{'end_date'}) && $data->{'end_date'} !== '') ? (string)$data->{'end_date'} : false;
        if ($start_date && $end_date) $queries['whereRaw'][] = ["(MONTH(?) <= MONTH(`birthday`) and MONTH(`birthday`) <= MONTH(?))", [$start_date, $end_date]];
        $name = trim(utf8_strtolower((isset($data->{'name'}) && !is_null($data->{'name'}) && $data->{'name'} !== '') ? trim((string)$data->{'name'}) : ''));
        if ($name) $queries['whereRaw'][] = ["lower(`users`.`name`) like ?", "%$name%"];

        $fields = [
            'users.*',
        ];
        $results = $this->setUpQueryBuilder($this->user_repository->getModel(), $queries, false, $fields)
            ->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();

        return [$queries, $results];
    }

    /**
     * @OA\Get(
     *   path="/backend/user_stats/birthday",
     *   summary="Get Birthday",
     *   operationId="getBirthday",
     *   tags={"BackendUsersStats"},
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
    public function indexBirthday() {
        try {
            // Check permission
            //if (!$this->isCRUD('orders', 'view')) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            list($queries, $results) = $this->getBirthdayIndex($page, $pageSize);
            $output = [];
            if ($results) {
                foreach ($results as $item) {
                    $newItem = $this->parseToRespond($item->toArray());
                    $output[] = $newItem;
                }
            }

            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging === 'true' ? true : ($paging === 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($output);
            } else {
                $query = $this->setUpQueryBuilder($this->user_repository->getModel(), $queries, true)->get();
                $totalCount = $query->count();
                return $this->respondWithPaging($output, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
