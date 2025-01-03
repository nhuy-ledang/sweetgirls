<?php namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Order\Repositories\OrderRepository;
use Modules\Product\Repositories\ProductRepository;

/**
 * Class StatisticController
 *
 * @package Modules\Order\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2022-02-11
 */
class StatisticController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\OrderRepository
     */
    protected $order_repository;

    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    public function __construct(Request $request,
                                OrderRepository $order_repository,
                                ProductRepository $product_repository) {
        $this->model_repository = $order_repository;
        $this->product_repository = $product_repository;

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
     * Get Product Index
     *
     * @param $page
     * @param $pageSize
     * @param string $sort
     * @param string $order
     * @return array
     */
    protected function getProductRankIndex($page, $pageSize, $sort = '', $order = '') {
        if (!$sort) {
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'pd__products.id' : strtolower($sort);
            if ($sort == 'id') $sort = 'pd__products.id';
        }
        if (!$order) {
            $order = (string)$this->request->get('order');
            $order = !$order ? 'desc' : strtoupper($order);
        }
        $queries = [
            'and'        => [
                //['pd__products.user_id', '=', $this->auth->id]
            ],
            'in'         => [],
            'whereRaw'   => [],
            'orWhereRaw' => [],
        ];

        $data = $this->getRequestData();
        $start_date = (isset($data->{'start_date'}) && !is_null($data->{'start_date'}) && $data->{'start_date'} !== '') ? (string)$data->{'start_date'} : false;
        $end_date = (isset($data->{'end_date'}) && !is_null($data->{'end_date'}) && $data->{'end_date'} !== '') ? (string)$data->{'end_date'} : false;
        $name = trim(utf8_strtolower((isset($data->{'name'}) && !is_null($data->{'name'}) && $data->{'name'} !== '') ? trim((string)$data->{'name'}) : ''));
        if ($name) $queries['whereRaw'][] = ["lower(`pd__products`.`name`) like ?", "%$name%"];
        $category_name = trim(utf8_strtolower((isset($data->{'category_name'}) && !is_null($data->{'category_name'}) && $data->{'category_name'} !== '') ? trim((string)$data->{'category_name'}) : ''));
        if ($category_name) $queries['whereRaw'][] = ["lower(`pc`.`name`) like ?", "%$category_name%"];
        $model = (isset($data->{'model'}) && !is_null($data->{'model'}) && $data->{'model'} !== '') ? trim((string)$data->{'model'}) : '';
        if ($model) {
            $model = str_replace([" ", "\r\n", "\r"], ",", trim($model));
            $tmp = explode(',', $model);
            $nos = [];
            foreach ($tmp as $t) {
                if (trim($t)) $nos[] = trim($t);
                /*$e = explode('-', $t);
                if (count($e) == 2) {
                    $invoice_prefix = $e[0] . '-';
                    $model = trim(ltrim($e[1], '0'));
                    $nos[] = $invoice_prefix . $model;
                }*/
            }
            if ($nos) {
                $q = [];
                //foreach ($nos as $no) $q[] = "concat_ws('',`invoice_prefix`,`model`) like ?";
                foreach ($nos as $no) $q[] = "`pd__products`.`model` like ?";
                $queries['whereRaw'][] = ['(' . implode(' or ', $q) . ')', $nos];
            }
            //$queries['whereRaw'][] = ["concat_ws('',`invoice_prefix`,`model`) like ?", [$model]];
        }

        if ($start_date && $end_date) {
            $where_visit = "select count(upv.id) from user__product_viewed upv where upv.product_id = pd__products.id and (DATE(upv.viewed_at) >= '{$start_date}' and DATE(upv.viewed_at) <= '{$end_date}')";
            $where_quantity = "select SUM(op.quantity) from order__products op left join orders o on (o.id = op.order_id) where op.product_id = pd__products.id and op.price > 0 and (DATE(o.created_at) >= '{$start_date}' and DATE(o.created_at) <= '{$end_date}') and o.order_status = '" . ORDER_SS_COMPLETED ."'";
            $where_total = "select SUM(op.total) from order__products op left join orders o on (o.id = op.order_id) where op.product_id = pd__products.id and (DATE(o.created_at) >= '{$start_date}' and DATE(o.created_at) <= '{$end_date}') and o.order_status = '" . ORDER_SS_COMPLETED ."'";
        } else {
            $where_visit = "select count(upv.id) from user__product_viewed upv where upv.product_id = pd__products.id";
            $where_quantity = "select SUM(op.quantity) from order__products op where op.product_id = pd__products.id and op.price > 0";
            $where_total = "select SUM(op.total) from order__products op where op.product_id = pd__products.id";
        }

        $fields = [
            'pd__products.model',
            'pd__products.name',
            'pc.name as pc__name',
            \DB::raw("($where_visit) as total_visit"),
            \DB::raw("($where_quantity) as total_quantity"),
            \DB::raw("($where_total) as total_total"),
            \DB::raw("(($where_quantity) / ($where_visit) * 100) as conversion"),
        ];
        $results = $this->setUpQueryBuilder($this->product_repository->getModel(), $queries, false, $fields)
            ->leftJoin('pd__categories as pc', 'pc.id', '=', 'pd__products.category_id')
            ->leftJoin('user__product_viewed as upv','upv.product_id', '=', 'pd__products.id')
            ->groupBy('pd__products.id')
            ->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();

        return [$queries, $results];
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_stat/products_rank",
     *   summary="Get Products Rank",
     *   operationId="getProductsRank",
     *   tags={"BackendPdStats"},
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
    public function indexProductRank() {
        try {
            // Check permission
            //if (!$this->isCRUD('orders', 'view')) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            list($queries, $results) = $this->getProductRankIndex($page, $pageSize);
            $output = [];
            if ($results) {
                foreach ($results as $item) {
                    $newItem = $this->parseToRespond($item->toArray());
                    $newItem['category'] = new \Modules\Product\Entities\Category($newItem['pc']);
                    unset($newItem['pc']);
                    $output[] = $newItem;
                }
            }

            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging === 'true' ? true : ($paging === 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($output);
            } else {
                $query = $this->setUpQueryBuilder($this->product_repository->getModel(), $queries, true)
                    ->leftJoin('pd__categories as pc', 'pc.id', '=', 'pd__products.category_id')
                    ->leftJoin('user__product_viewed as upv','upv.product_id', '=', 'pd__products.id')
                    ->groupBy('pd__products.id')
                    ->get();
                $totalCount = $query->count();
                return $this->respondWithPaging($output, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_stat_products_rank_exports",
     *   summary="Get Export Products Rạnk",
     *   operationId="getExportProductsRank",
     *   tags={"BackendPdStats"},
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
    public function exportExcelProductRank() {
        list($queries, $results) = $this->getProductRankIndex(1, 10000);
        $fields = ['model', 'name', 'category_name', 'total_visit', 'total_quantity', 'conversion', 'total_total'];
        //=== Get rows
        $rows = [];
        foreach ($results as $k => $result) {
            $item = $result->toArray();
            $newItem = $this->parseToRespond($item);
            if ($result->pc__name) {
                $category = new  \Modules\Product\Entities\Category($newItem['pc']);
                $item['category_name'] = $category->name;
            }
            $row = [$k + 1];
            foreach ($fields as $field) {
                $row[] = isset($item[$field]) ? $item[$field] : '';
            }
            $rows[] = $row;
        }

        return Excel::download(new \Modules\Product\Exports\ProductRankExport($rows), 'products-rank' . date('Y-m-d') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_stat/revenue_percent",
     *   summary="Get Product Order Stat Revenue Percent",
     *   operationId="getProductOrderStatRevenuePercent",
     *   tags={"BackendPdStats"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function revenuePercent() {
        list($mode, $start_date, $end_date, $last_start_date, $last_end_date) = $this->getReportDates();
        $queries = ['in' => [['orders.status', ['completed', 'paid']]]];
        $fields = ['orders.channel', \DB::raw('SUM(total) as total')];
        $channels = [
            'online'  => ['name' => 'Từ website', 'now' => 0, 'last' => 0, 'percent' => 0, 'bgColor' => '#b94551'],
            'offline' => ['name' => 'Mua trực tiếp', 'now' => 0, 'last' => 0, 'percent' => 0, 'bgColor' => '#82acbc'],
            'other'   => ['name' => 'Khác', 'now' => 0, 'last' => 0, 'percent' => 0, 'bgColor' => '#e8ac34'],
        ];
        // Now
        $results = $this->setUpQueryBuilder($this->model_repository->getModel()->setTable('orders as orders'), array_merge($queries, [
            'whereRaw' => [['(? <= DATE(orders.created_at) and DATE(orders.created_at) <= ?)', [$start_date, $end_date]]],
        ]), false, $fields)->groupBy('orders.channel')->get();
        foreach ($results as $result) {
            if ($result->channel == 'online') {
                $channels['online']['now'] += $result->total;
            } else if ($result->channel == 'offline') {
                $channels['offline']['now'] += $result->total;
            } else {
                $channels['other']['now'] += $result->total;
            }
        }
        // Last
        $results = $this->setUpQueryBuilder($this->model_repository->getModel()->setTable('orders as orders'), array_merge($queries, [
            'whereRaw' => [['(? <= DATE(orders.created_at) and DATE(orders.created_at) <= ?)', [$last_start_date, $last_end_date]]],
        ]), false, $fields)->groupBy('orders.channel')->get();
        foreach ($results as $result) {
            if ($result->channel == 'online') {
                $channels['online']['last'] += $result->total;
            } else if ($result->channel == 'offline') {
                $channels['offline']['last'] += $result->total;
            } else {
                $channels['other']['last'] += $result->total;
            }
        }
        // Percent
        $output = [];
        foreach ($channels as $key => $method) {
            $method['code'] = $key;
            $method['percent'] = $method['last'] ? round(($method['now'] - $method['last']) / $method['last'] * 100, 1) : 0;
            $output[] = $method;
        }
        return $this->respondWithSuccess($output);
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_stat/payment_methods",
     *   summary="Get Product Order Stat Payment Method",
     *   operationId="getProductOrderStatPaymentMethod",
     *   tags={"BackendPdStats"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function paymentMethods() {
        try {
            list($mode, $start_date, $end_date, $last_start_date, $last_end_date) = $this->getReportDates();
            $filters = [
                'mode'            => $mode,
                'start_date'      => $start_date,
                'end_date'        => $end_date,
                'last_start_date' => $last_start_date,
                'last_end_date'   => $last_end_date
            ];
            $queries = ['in' => [['orders.status', ['completed', 'paid']]]];
            $fields = ['orders.payment_code', \DB::raw('SUM(total) as total')];
            $payment_methods = [
                'cash'          => ['name' => 'Cash', 'now' => 0, 'last' => 0, 'percent' => 0, 'bgColor' => '#677788', 'badgeClass' => 'badge-soft-success'],
                'internet_bank' => ['name' => 'Internet Bank', 'now' => 0, 'last' => 0, 'percent' => 0, 'bgColor' => '#a7b1ba', 'badgeClass' => 'badge-soft-warning'],
            ];
            // Now
            $results = $this->setUpQueryBuilder($this->model_repository->getModel()->setTable('orders as orders'), array_merge($queries, [
                'whereRaw' => [['(? <= DATE(orders.created_at) and DATE(orders.created_at) <= ?)', [$start_date, $end_date]]],
            ]), false, $fields)->groupBy('orders.payment_code')->get();
            foreach ($results as $result) {
                if ($result->payment_code == PAYMENT_MT_DOMESTIC || $result->payment_code == PAYMENT_MT_FOREIGN || $result->payment_code == PAYMENT_MT_MOMO) {
                    $payment_methods['internet_bank']['now'] += $result->total;
                } else {
                    $payment_methods['cash']['now'] += $result->total;
                }
            }
            // Last
            $results = $this->setUpQueryBuilder($this->model_repository->getModel()->setTable('orders as orders'), array_merge($queries, [
                'whereRaw' => [['(? <= DATE(orders.created_at) and DATE(orders.created_at) <= ?)', [$last_start_date, $last_end_date]]],
            ]), false, $fields)->groupBy('orders.payment_code')->get();
            foreach ($results as $result) {
                if ($result->payment_code == PAYMENT_MT_DOMESTIC || $result->payment_code == PAYMENT_MT_FOREIGN || $result->payment_code == PAYMENT_MT_MOMO) {
                    $payment_methods['internet_bank']['last'] += $result->total;
                } else {
                    $payment_methods['cash']['last'] += $result->total;
                }
            }
            // Percent
            $output = [];
            foreach ($payment_methods as $key => $method) {
                $method['code'] = $key;
                $method['percent'] = $method['last'] ? round(($method['now'] - $method['last']) / $method['last'] * 100, 1) : 0;
                $output[] = $method;
            }
            return $this->respondWithSuccess($output, ['filters' => $filters]);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    private function getStatLineChart($fields) {
        $data = $this->getRequestData();
        $mode = !empty($data->{'mode'}) ? $data->{'mode'} : 'this_year';
        if (!($mode === 'today' || $mode === 'this_week' || $mode === 'this_month' || $mode === 'this_year')) $mode = 'this_year';
        if ($mode === 'today') $mode = 'this_week';
        list($mode, $start_date, $end_date, $last_start_date, $last_end_date) = $this->getReportDates($mode);
        $queries = ['in' => [['orders.status', ['completed', 'paid']]], 'whereRaw' => []];
        if ($mode === 'this_year') {
            $fields[] = \DB::raw('MONTH(`payment_at`) as `month`');
            $groupBy = 'month';
        } else if ($mode === 'this_month') {
            $fields[] = \DB::raw('DAY(`payment_at`) as `date`');
            $groupBy = 'date';
        } else { // this_week
            $fields[] = \DB::raw('DAYOFWEEK(`payment_at`) as `date`');
            $groupBy = 'date';
        }
        if ($mode === 'this_year') {
            $max = 12;
        } else if ($mode === 'this_month') {
            $max = max((int)date('j', strtotime($end_date)), (int)date('j', strtotime($last_end_date)));
        } else { // this_week
            $max = 7;
        }
        $labels = [];
        for ($pos = 1; $pos <= $max; $pos++) {
            $labels[] = $pos;
        }
        // Now
        if ($mode === 'this_year') {
            $queries['whereRaw'] = [['YEAR(`payment_at`) = ?', [(int)date('Y', strtotime($start_date))]]];
        } else {
            $queries['whereRaw'] = [['(? <= DATE(`payment_at`)) and (DATE(`payment_at`) <= ?)', [$start_date, $end_date]]];
        }
        $results = $this->setUpQueryBuilder($this->model_repository->getModel()->setTable('orders as orders'), $queries, false, $fields)->orderBy($groupBy, 'asc')->groupBy($groupBy)->get();
        $nowRs = [];
        foreach ($results as $result) {
            $pos = (int)$result->{$groupBy};
            if ($mode === 'this_week') {
                $pos = $pos - 1;
                if ($pos == 0) $pos = 7; // CN
            }
            $nowRs[$pos] = $result->total;
        }
        $nowData = [];
        $nowTotal = 0;
        for ($pos = 1; $pos <= $max; $pos++) {
            $total = isset($nowRs[$pos]) ? $nowRs[$pos] : 0;
            $nowData[] = $total;
            $nowTotal += $total;
        }
        // Last
        if ($mode === 'this_year') {
            $queries['whereRaw'] = [['YEAR(`payment_at`) = ?', [(int)date('Y', strtotime($last_start_date))]]];
        } else {
            $queries['whereRaw'] = [['(? <= DATE(`payment_at`)) and (DATE(`payment_at`) <= ?)', [$last_start_date, $last_end_date]]];
        }
        $results = $this->setUpQueryBuilder($this->model_repository->getModel()->setTable('orders as orders'), $queries, false, $fields)->orderBy($groupBy, 'asc')->groupBy($groupBy)->get();
        $lastRs = [];
        foreach ($results as $result) {
            $pos = (int)$result->{$groupBy};
            if ($mode === 'this_week') {
                $pos = $pos - 1;
                if ($pos == 0) $pos = 7; // CN
            }
            $lastRs[$pos] = $result->total;
        }
        $lastData = [];
        $lastTotal = 0;
        for ($pos = 1; $pos <= $max; $pos++) {
            $total = isset($lastRs[$pos]) ? $lastRs[$pos] : 0;
            $lastData[] = $total;
            $lastTotal += $total;
        }
        $percent = $lastTotal ? round(($nowTotal - $lastTotal) / $lastTotal * 100, 1) : 0;
        $output = [
            'now'     => ['data' => $nowData, 'total' => $nowTotal],
            'last'    => ['data' => $lastData, 'total' => $lastTotal],
            'percent' => $percent,
            'labels'  => $labels,
        ];

        return $this->respondWithSuccess($output);
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_stat/revenues",
     *   summary="Get Product Order Stat revenues",
     *   operationId="getProductOrderStatRevenues",
     *   tags={"BackendPdStats"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function revenues() {
        try {
            $fields = [\DB::raw('SUM(`total`) as total')];

            return $this->getStatLineChart($fields);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_stat/orders",
     *   summary="Get Product Order Stat Orders",
     *   operationId="getProductOrderStatOrders",
     *   tags={"BackendPdStats"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function orders() {
        try {
            $fields = [\DB::raw('count(*) as total')];

            return $this->getStatLineChart($fields);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_stat/users",
     *   summary="Get Product Order Stat Users",
     *   operationId="getProductOrderStatUsers",
     *   tags={"BackendPdStats"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function users() {
        try {
            $fields = [\DB::raw('count(distinct user_id) as total')];

            return $this->getStatLineChart($fields);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    private function revenueTotal($start_date, $end_date) {
        $fields = [\DB::raw('SUM(`total`) as total')];
        $queries = [
            'in'       => [['status', ['completed', 'paid']]],
            'whereRaw' => [['(? <= DATE(`payment_at`)) and (DATE(`payment_at`) <= ?)', [$start_date, $end_date]]]
        ];
        $model = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false, $fields)->first();

        return $model ? (float)$model->total : 0;
    }

    private function orderNumTotal($start_date, $end_date) {
        $fields = [\DB::raw('count(*) as total')];
        $queries = [
            'in'       => [['status', ['completed', 'paid']]],
            'whereRaw' => [['(? <= DATE(`payment_at`)) and (DATE(`payment_at`) <= ?)', [$start_date, $end_date]]]
        ];
        $model = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false, $fields)->first();

        return $model ? (float)$model->total : 0;
    }

    private function orderCanceledTotal($start_date, $end_date) {
        $fields = [\DB::raw('SUM(`total`) as total')];
        $queries = [
            'in'       => [['order_status', ['canceled']]],
            'whereRaw' => [['(? <= DATE(`payment_at`)) and (DATE(`payment_at`) <= ?)', [$start_date, $end_date]]]
        ];
        $model = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false, $fields)->first();

        return $model ? (float)$model->total : 0;
    }

    private function orderCanceledNumTotal($start_date, $end_date) {
        $fields = [\DB::raw('count(*) as total')];
        $queries = [
            'in'       => [['order_status', ['canceled']]],
            'whereRaw' => [['(? <= DATE(`payment_at`)) and (DATE(`payment_at`) <= ?)', [$start_date, $end_date]]]
        ];
        $model = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false, $fields)->first();

        return $model ? (float)$model->total : 0;
    }

    private function userNumTotal($start_date, $end_date) {
        $fields = [\DB::raw('count(distinct user_id) as total')];
        $queries = [
            'in'       => [['status', ['completed', 'paid']]],
            'whereRaw' => [['(? <= DATE(`payment_at`)) and (DATE(`payment_at`) <= ?)', [$start_date, $end_date]]]
        ];
        $model = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false, $fields)->first();

        return $model ? (float)$model->total : 0;
    }

    private function userCurrentNumTotal($start_date, $end_date) {
        $fields = [\DB::raw('count(distinct user_id) as total')];
        $queries = [
            'in'       => [['status', ['completed', 'paid']]],
            'whereRaw' => [['(DATE(`payment_at`) <= ?)', [$end_date]]]
        ];
        $model = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false, $fields)->first();

        return $model ? (float)$model->total : 0;
    }

    private function userNewNumTotal($start_date, $end_date) {
        $fields = [
            \DB::raw('count(distinct user_id) as total'),
            \DB::raw("(select count(distinct user_id) as total from `orders` where `status` in ('completed', 'paid') and DATE(`payment_at`) < '$start_date') as old_total"),
        ];
        $queries = [
            'in'       => [['status', ['completed', 'paid']]],
            'whereRaw' => [['(? <= DATE(`payment_at`)) and (DATE(`payment_at`) <= ?)', [$start_date, $end_date]]]
        ];
        $model = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false, $fields)->having('old_total', '=', 0)->first();

        return $model ? (float)$model->total : 0;
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_stat/overview",
     *   summary="Get Product Stat Overview",
     *   operationId="getProductStatOverview",
     *   tags={"BackendPdStats"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function overview() {
        try {
            list(, $start_date, $end_date, $last_start_date, $last_end_date) = $this->getReportDates();

            $revenuesNow = $this->revenueTotal($start_date, $end_date);
            $revenuesLast = $this->revenueTotal($last_start_date, $last_end_date);
            $revenuesPercent = $revenuesLast ? round(($revenuesNow - $revenuesLast) / $revenuesLast * 100, 1) : 0;
            $revenues = ['now' => $revenuesNow, 'last' => $revenuesLast, 'percent' => $revenuesPercent];

            $ordersNow = $this->orderNumTotal($start_date, $end_date);
            $ordersLast = $this->orderNumTotal($last_start_date, $last_end_date);
            $ordersPercent = $ordersLast ? round(($ordersNow - $ordersLast) / $ordersLast * 100, 1) : 0;
            $orders = ['now' => $ordersNow, 'last' => $ordersLast, 'percent' => $ordersPercent];

            $canceledNow = $this->orderCanceledTotal($start_date, $end_date);
            $canceledLast = $this->orderCanceledTotal($last_start_date, $last_end_date);
            $canceledPercent = $canceledLast ? round(($canceledNow - $canceledLast) / $canceledLast * 100, 1) : 0;
            $canceled = ['now' => $canceledNow, 'last' => $canceledLast, 'percent' => $canceledPercent];

            $canceledNumNow = $this->orderCanceledNumTotal($start_date, $end_date);
            $canceledNumLast = $this->orderCanceledNumTotal($last_start_date, $last_end_date);
            $canceledRatioNow = ($ordersNow + $canceledNumNow) ? round(($canceledNumNow) / ($ordersNow + $canceledNumNow) * 100, 1) : 0;
            $canceledRatioLast = ($ordersLast + $canceledNumLast) ? round(($canceledNumLast) / ($ordersLast + $canceledNumLast) * 100, 1) : 0;
            $canceledRatioPercent = $canceledRatioLast ? round(($canceledRatioNow - $canceledRatioLast) / $canceledRatioLast * 100, 1) : 0;
            $canceledRatio = ['now' => $canceledRatioNow, 'last' => $canceledRatioLast, 'percent' => $canceledRatioPercent];

            $usersNow = $this->userNumTotal($start_date, $end_date);
            $usersLast = $this->userNumTotal($last_start_date, $last_end_date);
            $usersPercent = $usersLast ? round(($usersNow - $usersLast) / $usersLast * 100, 1) : 0;
            $users = ['now' => $usersNow, 'last' => $usersLast, 'percent' => $usersPercent];

            $userCurrentNow = $this->userCurrentNumTotal($start_date, $end_date);
            $userCurrentLast = $this->userCurrentNumTotal($last_start_date, $last_end_date);
            $userCurrentPercent = $userCurrentLast ? round(($userCurrentNow - $userCurrentLast) / $userCurrentLast * 100, 1) : 0;
            $userCurrent = ['now' => $userCurrentNow, 'last' => $userCurrentLast, 'percent' => $userCurrentPercent];

            $userNewNow = $this->userNewNumTotal($start_date, $end_date);
            $userNewLast = $this->userNewNumTotal($last_start_date, $last_end_date);
            $userNewPercent = $userNewLast ? round(($userNewNow - $userNewLast) / $userNewLast * 100, 1) : 0;
            $userNew = ['now' => $userNewNow, 'last' => $userNewLast, 'percent' => $userNewPercent];

            $visitorsNow = 0;
            $visitorsLast = 0;
            $visitorsPercent = $visitorsLast ? round(($visitorsNow - $visitorsLast) / $visitorsLast * 100, 1) : 0;
            $visitors = ['now' => $visitorsNow, 'last' => $visitorsLast, 'percent' => $visitorsPercent];

            $viewsNow = 0;
            $viewsLast = 0;
            $viewsPercent = $viewsLast ? round(($viewsNow - $viewsLast) / $viewsLast * 100, 1) : 0;
            $views = ['now' => $viewsNow, 'last' => $viewsLast, 'percent' => $viewsPercent];

            $convertRateNow = 0;
            $convertRateLast = 0;
            $convertRatePercent = $convertRateLast ? round(($convertRateNow - $convertRateLast) / $convertRateLast * 100, 1) : 0;
            $convertRate = ['now' => $convertRateNow, 'last' => $convertRateLast, 'percent' => $convertRatePercent];

            $output = [
                'revenues'      => $revenues,
                'orders'        => $orders,
                'canceled'      => $canceled,
                'canceledRatio' => $canceledRatio,
                'users'         => $users,
                'userCurrent'   => $userCurrent,
                'userNew'       => $userNew,
                'visitors'      => $visitors,
                'views'         => $views,
                'convertRate'   => $convertRate,
            ];

            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_stat/bestseller",
     *   summary="Get Product Bestseller",
     *   operationId="getProductBestseller",
     *   tags={"BackendPdStats"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query",description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query",description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query",description="Item total on page", example=20),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function bestseller() {
        try {
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            /*$sort = (string)$this->request->get('sort');
            $order = (string)$this->request->get('order');
            $sort = !$sort ? 'id' : strtolower($sort);
            $order = !$order ? 'asc' : strtolower($order);*/
            $queries = [
                'and'      => [],
                'whereRaw' => [],
                'in'       => [['orders.status', ['completed', 'paid']]]
            ];
            $fields = ['pd__products.*'];
            $fields[] = \DB::raw('SUM(od.quantity) as quantity_num');
            $results = $this->setUpQueryBuilder($this->product_repository->getModel(), $queries, false, $fields)
                ->rightJoin('order__products as od', 'product_id', '=', 'pd__products.id')
                ->rightJoin('orders as orders', 'order_id', '=', 'orders.id')
                ->whereNotNull('pd__products.id')
                ->groupBy('pd__products.id')
                ->orderBy('quantity_num', 'desc')->take($pageSize)->skip($pageSize * ($page - 1))->get();
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging === 'true' ? true : ($paging === 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($results);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->product_repository->getModel(), $queries, true)
                    ->rightJoin('order__products as od', 'product_id', '=', 'pd__products.id')
                    ->rightJoin('orders as orders', 'order_id', '=', 'orders.id')
                    ->whereNotNull('pd__products.id')
                    ->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/pd_stat/top_revenue",
     *   summary="Get Top Product Revenue",
     *   operationId="getTopProductRevenue",
     *   tags={"BackendPdStats"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query",description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query",description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query",description="Item total on page", example=20),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function topRevenue() {
        try {
            try {
                $page = (int)$this->request->get('page');
                if (!$page) $page = 1;
                $pageSize = (int)$this->request->get('pageSize');
                if (!$pageSize) $pageSize = $this->pageSize;
                if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
                /*$sort = (string)$this->request->get('sort');
                $order = (string)$this->request->get('order');
                $sort = !$sort ? 'id' : strtolower($sort);
                $order = !$order ? 'asc' : strtolower($order);*/
                $queries = [
                    'and'      => [],
                    'whereRaw' => [],
                    'in'       => [['orders.status', ['completed', 'paid']]]
                ];
                $fields = ['pd__products.*'];
                $fields[] = \DB::raw('SUM(od.total) as total_num');
                $results = $this->setUpQueryBuilder($this->product_repository->getModel(), $queries, false, $fields)
                    ->rightJoin('order__products as od', 'product_id', '=', 'pd__products.id')
                    ->rightJoin('orders as orders', 'order_id', '=', 'orders.id')
                    ->whereNotNull('pd__products.id')
                    ->groupBy('pd__products.id')
                    ->orderBy('total_num', 'desc')->take($pageSize)->skip($pageSize * ($page - 1))->get();
                $paging = $this->request->get('paging');
                $paging = is_null($paging) || $paging === 'true' ? true : ($paging === 'false' ? false : (boolean)$paging);
                if (!$paging) {
                    return $this->respondWithSuccess($results);
                } else {
                    $totalCount = $this->setUpQueryBuilder($this->product_repository->getModel(), $queries, true)
                        ->rightJoin('order__products as od', 'product_id', '=', 'pd__products.id')
                        ->rightJoin('orders as orders', 'order_id', '=', 'orders.id')
                        ->whereNotNull('pd__products.id')
                        ->count();
                    return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
                }
            } catch (\Exception $e) {
                return $this->errorInternalError($e->getMessage());
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
