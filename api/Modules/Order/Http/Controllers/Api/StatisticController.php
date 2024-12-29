<?php namespace Modules\Order\Http\Controllers\Api;

use DateTime;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Affiliate\Repositories\AgentPointRepository;
use Modules\Order\Repositories\OrderRepository;
use Modules\System\Repositories\VisitorRepository;

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
     * @var \Modules\System\Repositories\VisitorRepository;
     */
    protected $visitor_repository;

    public function __construct(Request $request,
                                OrderRepository $order_repository,
                                VisitorRepository $visitor_repository,
                                AgentPointRepository $agent_point_repository) {
        $this->order_repository = $order_repository;
        $this->visitor_repository = $visitor_repository;
        //$this->agent_repository = $agent_repository;
        $this->agent_point_repository = $agent_point_repository;

        $this->middleware('auth.usr')->except(['overview']);

        parent::__construct($request);
    }

    // day, week, month, year
    public function getReportDates($mode = '') {
        $data = $this->getRequestData();
        if (!$mode) $mode = !empty($data->{'mode'}) ? $data->{'mode'} : '';
        $start_date = !empty($data->{'start_date'}) ? $data->{'start_date'} : date('Y-m-d');
        $end_date = !empty($data->{'end_date'}) ? $data->{'end_date'} : date('Y-m-d');
        $last = '-1 year';
        if ($mode == 'year') {
            $start_date = date('Y-01-01', strtotime($start_date));
            $end_date = date('Y-12-31', strtotime($start_date));
        } else if ($mode == 'month') {
            $start_date = date('Y-m-01', strtotime($start_date));
            $end_date = date('Y-m-t', strtotime($start_date));
            $last = '-1 month';
        } else if ($mode == 'week') {
            $start_date = date("Y-m-d", strtotime('monday this week', strtotime($start_date)));
            $end_date = date("Y-m-d", strtotime('sunday this week', strtotime($start_date)));
            $last = '-1 week';
        } else if ($mode == 'day') {
            $end_date = $start_date;
            $last = '-1 day';
        }
        $last_start_date = date('Y-m-d', strtotime($last, strtotime($start_date)));
        $last_end_date = date('Y-m-d', strtotime($last, strtotime($end_date)));

        return [$mode, $start_date, $end_date, $last_start_date, $last_end_date];
    }

    private $colors = [
        'tomato'    => '#d50000', // Màu đỏ cà chua
        'flamingo'  => '#e67c73', // Màu hồng hạc
        'tangerine' => '#f4511e', // Màu cam
        'banana'    => '#f6bf26', // Màu chuối
        'sage'      => '#33b679', // Màu xanh lá nhạt
        'basil'     => '#0b8043', // Màu xanh húng quế
        'peacock'   => '#039be5', // Màu xanh lam
        'blueberry' => '#3f51b5', // Màu việt quất
        'lavender'  => '#7986cb', // Màu oải hương
        'grape'     => '#8e24aa', // Màu nho
        'graphite'  => '#616161', // Màu khói
        'calendar'  => '#4285f4', // Màu lịch
    ];

    private $mode_labels = [
        'year' => ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
        'week' => ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'],
        'day' => ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'],
        'customRange' => [],
    ];

    private function getLabelsByMode($mode, $start_date, $end_date) {
        if ($mode === 'month') {
            $date = new DateTime($start_date);
            $daysInMonth = $date->format('t');
            $labels = [];

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $labels[] = (string)$day;
            }

            return $labels;
        }

        return $this->mode_labels[$mode] ? $this->mode_labels[$mode] : [];
    }

    private function handleDataChart($data_results, $groupBy, $mode, $start_date, $end_date, $month, $year, $labels) {
        $sale_tmpRs = [];
        foreach ($data_results as $result) {
            if ($mode == 'customRange') {
                $sale_tmpRs[$result->month . "_" . $result->$groupBy] = (float)$result->total;
            } else {
                $sale_tmpRs[$result->$groupBy] = (float)$result->total;
            }
        }
        $saleNow = [];
        $saleLast = [];

        switch ($mode) {
            case 'year':
                for ($i = 1; $i <= 12; $i++) {
                    $saleNow[] = isset($sale_tmpRs[$i]) ? $sale_tmpRs[$i] : 0;
                }
                // Chưa cần so sánh
                $saleLast = array_fill(0, 12, 0);
                break;
            case 'month':
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $saleNow[] = isset($sale_tmpRs[$i]) ? $sale_tmpRs[$i] : 0;
                }
                // Chưa cần so sánh
                $saleLast = array_fill(0, $daysInMonth, 0);
                break;
            case 'week':
                $start_timestamp = strtotime($start_date);
                $end_timestamp = strtotime($end_date);
                $dates = [];
                for ($current_timestamp = $start_timestamp; $current_timestamp <= $end_timestamp; $current_timestamp += 86400) {
                    $dates[] = date('j', $current_timestamp);
                }
                foreach ($dates as $value) {
                    $saleNow[] = isset($sale_tmpRs[(int)$value]) ? $sale_tmpRs[(int)$value] : 0;
                    $saleLast[] = 0; // Chưa cần so sánh
                }
                break;
            case 'customRange':
                $start_timestamp = strtotime($start_date);
                $end_timestamp = strtotime($end_date);
                $dates = [];
                for ($current_timestamp = $start_timestamp; $current_timestamp <= $end_timestamp; $current_timestamp += 86400) {
                    $dates[] = date('Y-m-d', $current_timestamp);
                }
                foreach ($dates as $value) {
                    $saleNow[] = isset($sale_tmpRs[date('n_j', strtotime($value))]) ? $sale_tmpRs[date('n_j', strtotime($value))] : 0;
                    $saleLast[] = 0; // Chưa cần so sánh
                    $labels[] = date('d/m', strtotime($value));
                }
                break;
            case 'day':
                for ($i = 0; $i < count($labels); $i++) {
                    $saleNow[] = isset($sale_tmpRs[$i]) ? $sale_tmpRs[$i] : 0;
                    $saleLast[] = 0;  // Chưa cần so sánh
                }
                break;
        }

        // Tính phần trăm
        $salePercent = array_sum($saleLast) ? round((array_sum($saleNow) - array_sum($saleLast)) / array_sum($saleLast) * 100, 1) : 0;

        return [$saleNow, $saleLast, $salePercent, $labels];
    }

    // Doanh số
    private function saleTotal($start_date, $end_date) {
        $fields = [\DB::raw('sum(total) as total')];
        $queries = [
            //'in'       => [['status', [0, 1]]],
            //'in'       => [['order_status', ['pending', 'shipping', 'completed']]],
            'whereRaw' => [['(? <= DATE(`created_at`)) and (DATE(`created_at`) <= ?)', [$start_date, $end_date]]],
        ];
        $model = $this->setUpQueryBuilder($this->order_repository->getModel(), $queries, false, $fields)->first();

        return $model ? (float)$model->total : 0;
    }

    // Doanh số đã xác nhận
    private function saleConfirmTotal($start_date, $end_date) {
        $fields = [\DB::raw('sum(total) as total')];
        $queries = [
            //'in'       => [['status', [0, 1]]],
            'or'       => [['payment_status', '<>', 'in_process'], ['order_status', '<>', 'pending']],
            'whereRaw' => [['(? <= DATE(`created_at`)) and (DATE(`created_at`) <= ?)', [$start_date, $end_date]]],
        ];
        $model = $this->setUpQueryBuilder($this->order_repository->getModel(), $queries, false, $fields)->first();

        return $model ? (float)$model->total : 0;
    }

    // Doanh thu
    private function revenueQuantity($start_date, $end_date) {
        $fields = [\DB::raw('sum(total) as sum')];
        $queries = [
            //'in'       => [['status', [0, 1]]],
            'in'       => [['order_status', ['completed']]],
            'whereRaw' => [['(? <= DATE(`created_at`)) and (DATE(`created_at`) <= ?)', [$start_date, $end_date]]],
        ];
        $model = $this->setUpQueryBuilder($this->order_repository->getModel(), $queries, false, $fields)->first();

        return $model ? $model->sum : 0;
    }

    // Tổng đơn hàng đã xác nhận trừ đơn hủy
    private function orderQuantity($start_date, $end_date) {
        $fields = [\DB::raw('count(*) as total'), \DB::raw('sum(total) as sum')];
        $queries = [
            //'in'       => [['status', [0, 1]]],
            'or'       => [['payment_status', '=', 'paid'],  ['order_status', '=', ORDER_SS_PROCESSING], ['order_status', '=', ORDER_SS_SHIPPING], ['order_status', '=', ORDER_SS_COMPLETED]],
            'whereRaw' => [['(? <= DATE(`created_at`)) and (DATE(`created_at`) <= ?)', [$start_date, $end_date]]],
        ];
        $model = $this->setUpQueryBuilder($this->order_repository->getModel(), $queries, false, $fields)->first();

        return $model ? [(float)$model->total, $model->sum] : [0, 0];
    }

    // Tổng đơn hàng hủy
    private function orderCancelQuantity($start_date, $end_date) {
        $fields = [\DB::raw('count(*) as total')];
        $queries = [
            //'in'       => [['status', [0, 1]]],
            'and'       => [['order_status', '=', ORDER_SS_CANCELED]],
            'whereRaw' => [['(? <= DATE(`created_at`)) and (DATE(`created_at`) <= ?)', [$start_date, $end_date]]],
        ];
        $model = $this->setUpQueryBuilder($this->order_repository->getModel(), $queries, false, $fields)->first();

        return $model ? (float)$model->total : 0;
    }

    // Lượt truy cập
    private function visitQuantity($start_date, $end_date) {
        $fields = [\DB::raw('count(*) as total')];
        $queries = [
            //'in'       => [['status', [0, 1]]],
            'whereRaw' => [['(? <= DATE(`created_at`)) and (DATE(`created_at`) <= ?)', [$start_date, $end_date]]],
        ];
        $model = $this->setUpQueryBuilder($this->visitor_repository->getModel(), $queries, false, $fields)->first();

        return $model ? (float)$model->total : 0;
    }

    // Lượt xem
    private function viewQuantity($start_date, $end_date) {
        $fields = [\DB::raw('sum(`clicks`) as total')];
        $queries = [
            //'in'       => [['status', [0, 1]]],
            'whereRaw' => [['(? <= DATE(`created_at`)) and (DATE(`created_at`) <= ?)', [$start_date, $end_date]]],
        ];
        $model = $this->setUpQueryBuilder($this->visitor_repository->getModel(), $queries, false, $fields)->first();

        return $model ? (float)$model->total : 0;
    }

    // Affiliate
    private function affiliateQuantity($start_date, $end_date) {
        $fields = [\DB::raw('sum(`amount`) as total')];
        $queries = [
            'in'       => [['status', [1]]],
            'and'      => [['type', '=', 'in']],
            'whereRaw' => [['(? <= DATE(`created_at`)) and (DATE(`created_at`) <= ?)', [$start_date, $end_date]]],
        ];
        $model = $this->setUpQueryBuilder($this->agent_point_repository->getModel(), $queries, false, $fields)->first();

        return $model ? (float)$model->total : 0;
    }

    // Biểu đồ phân tích bán hàng
    private function getSalesIndexSalesResults($start_date, $end_date, $last_start_date, $last_end_date) {
        \DB::beginTransaction();
        // Đơn hàng
        list($orderNow, $sumNow) = $this->orderQuantity($start_date, $end_date);
        list($orderLast, $sumLast) = $this->orderQuantity($last_start_date, $last_end_date);
        $orderPercent = $orderLast ? round(($orderNow - $orderLast) / $orderLast * 100, 1) : 0;
        $order = ['last' => $orderLast, 'now' => $orderNow, 'percent' => $orderPercent];
        // Đơn hủy
        $orderCancelNow = $this->orderCancelQuantity($start_date, $end_date);
        $orderCancelLast = $this->orderCancelQuantity($last_start_date, $last_end_date);
        $orderCancelPercent = $orderCancelLast ? round(($orderCancelNow - $orderCancelLast) / $orderCancelLast * 100, 1) : 0;
        $orderCancel = ['last' => $orderCancelLast, 'now' => $orderCancelNow, 'percent' => $orderCancelPercent];
        // Doanh thu
        $revenueNow = $this->revenueQuantity($start_date, $end_date);
        $revenueLast = $this->revenueQuantity($last_start_date, $last_end_date);
        $revenuePercent = $revenueLast ? round(($revenueNow - $revenueLast) / $revenueLast * 100, 1) : 0;
        $revenue = ['last' => $revenueLast, 'now' => $revenueNow, 'percent' => $revenuePercent];
        // Doanh số
        $saleNow = $this->saleTotal($start_date, $end_date);
        $saleLast = $this->saleTotal($last_start_date, $last_end_date);
        $salePercent = $saleLast ? round(($saleNow - $saleLast) / $saleLast * 100, 1) : 0;
        $sale = ['last' => $saleLast, 'now' => $saleNow, 'percent' => $salePercent];
        // Doanh số đã xác nhận
        $saleConfirmNow = $this->saleConfirmTotal($start_date, $end_date);
        $saleConfirmLast = $this->saleConfirmTotal($last_start_date, $last_end_date);
        $saleConfirmPercent = $saleConfirmLast ? round(($saleConfirmNow - $saleConfirmLast) / $saleConfirmLast * 100, 1) : 0;
        $saleConfirm = ['last' => $saleConfirmLast, 'now' => $saleConfirmNow, 'percent' => $saleConfirmPercent];
        // Lượt truy cập
        $visitNow = $this->visitQuantity($start_date, $end_date);
        $visitLast = $this->visitQuantity($last_start_date, $last_end_date);
        $visitPercent = $visitLast ? round(($visitNow - $visitLast) / $visitLast * 100, 1) : 0;
        $visit = ['last' => $visitLast, 'now' => $visitNow, 'percent' => $visitPercent];
        // Lượt xem
        $viewNow = $this->viewQuantity($start_date, $end_date);
        $viewLast = $this->viewQuantity($last_start_date, $last_end_date);
        $viewPercent = $viewLast ? round(($viewNow - $viewLast) / $viewLast * 100, 1) : 0;
        $view = ['last' => $viewLast, 'now' => $viewNow, 'percent' => $viewPercent];
        // Tỷ lệ chuyển đổi
        $conversionNow = $visitNow ? round(($orderNow / $visitNow) * 100, 1) : 0;
        $conversionLast = $visitLast? ($orderLast / $visitLast) * 100 : 0;
        $conversionPercent = $conversionLast ? round(($conversionNow - $conversionLast) / $conversionLast * 100, 1) : 0;
        $conversion = ['last' => $conversionLast, 'now' => $conversionNow, 'percent' => $conversionPercent];
        // Affiliate
        $affiliateNow = $this->affiliateQuantity($start_date, $end_date);
        $affiliateLast = $this->affiliateQuantity($last_start_date, $last_end_date);
        $affiliatePercent = $affiliateLast ? round(($affiliateNow - $affiliateLast) / $affiliateLast * 100, 1) : 0;
        $affiliate = ['last' => $affiliateLast, 'now' => $affiliateNow, 'percent' => $affiliatePercent];

        \DB::commit();
        return [
            'order_total'        => $order,
            'order_cancel_total' => $orderCancel,
            'revenue_total'      => $revenue,
            'sale_total'         => $sale,
            'sale_confirm_total' => $saleConfirm,
            'visit_total'        => $visit,
            'view_total'         => $view,
            'conversion_total'   => $conversion,
            'affiliate_total'    => $affiliate,
        ];
    }

    // Biểu đồ kết quả doanh số/doanh thu
    private function getSalesChartsSalesResults($mode, $start_date, $end_date) {
        $colors = array_values($this->colors);
        $labels = $this->getLabelsByMode($mode, $start_date, $end_date);
        $output = ['mode' => $mode, 'labels' => $labels, 'data' => []];

        $year = (int)date('Y', strtotime($start_date));
        $month = (int)date('m', strtotime($start_date));
        $day = (int)date('d', strtotime($start_date));

        switch ($mode) {
            case 'year':
                $whereRaw = 'YEAR(`created_at`) = ?';
                $whereRawParam = [$year];
                $groupBy = 'month';
                break;
            case 'month':
                $whereRaw = 'YEAR(`created_at`) = ? AND MONTH(`created_at`) = ?';
                $whereRawParam = [$year, $month];
                $groupBy = 'day';
                break;
            case 'week':
                $whereRaw = '`created_at` >= ? AND `created_at` <= ?';
                $whereRawParam = [$start_date, $end_date . ' 23:59:59'];
                $groupBy = 'day';
                break;
            case 'customRange':
                $whereRaw = '`created_at` >= ? AND `created_at` <= ?';
                $whereRawParam = [$start_date, $end_date . ' 23:59:59'];
                $groupBy = 'day';
                break;
            case 'day':
                $whereRaw = 'YEAR(`created_at`) = ? AND MONTH(`created_at`) = ? AND DAY(`created_at`) = ?';
                $whereRawParam = [$year, $month, $day];
                $groupBy = 'hour';
                break;
            default:
                throw new InvalidArgumentException('Invalid mode');
        }

        $select = $mode == 'customRange'
            ? [\DB::raw($groupBy . "(`created_at`) as " . $groupBy), \DB::raw('sum(`total`) as total'), \DB::raw("month(`created_at`) as month")]
            : [\DB::raw($groupBy . "(`created_at`) as " . $groupBy), \DB::raw('sum(`total`) as total')];

        // Doanh số
        $sale_results = $this->order_repository->getModel()
            ->whereRaw($whereRaw, $whereRawParam)
            ->groupBy([$groupBy]);
        if ($mode == 'customRange') $sale_results = $sale_results->groupBy(['month']);
        $sale_results = $sale_results->select($select)->get();
        list($saleNow, $saleLast, $salePercent, $output['labels']) = $this->handleDataChart($sale_results, $groupBy, $mode, $start_date, $end_date, $month, $year, $labels);

        // Doanh số đã xác nhận
        $sale_confirm_results = $this->order_repository->getModel()
            ->where(function($query) {
                $query->where('payment_status', '<>', 'in_process')
                    ->orWhere('order_status', '<>', 'pending');
            })
            ->whereRaw($whereRaw, $whereRawParam)
            ->groupBy([$groupBy]);
        if ($mode == 'customRange') $sale_confirm_results = $sale_confirm_results->groupBy(['month']);
        $sale_confirm_results = $sale_confirm_results->select($select)->get();
        list($saleConfirmNow, $saleConfirmLast, $saleConfirmPercent, $output['labels']) = $this->handleDataChart($sale_confirm_results, $groupBy, $mode, $start_date, $end_date, $month, $year, $labels);

        // Doanh thu
        $revenue_results = $this->order_repository->getModel()
            ->whereIn('order_status', ['completed'])
            ->whereRaw($whereRaw, $whereRawParam)
            ->groupBy([$groupBy]);
        if ($mode == 'customRange') $revenue_results = $revenue_results->groupBy(['month']);
        $revenue_results = $revenue_results->select($select)->get();
        list($revenueNow, $revenueLast, $revenuePercent, $output['labels']) = $this->handleDataChart($revenue_results, $groupBy, $mode, $start_date, $end_date, $month, $year, $labels);

        $output['data'] = [
            ['name' => 'Doanh số', 'last' => array_sum($saleLast), 'now' => array_sum($saleNow), 'percent' => $salePercent, 'color' => '#b0e1ff', 'data' => array_values($saleNow),],
            ['name' => 'Doanh số đã xác nhận', 'last' => array_sum($saleConfirmLast), 'now' => array_sum($saleConfirmNow), 'percent' => $saleConfirmPercent, 'color' => '#4285f4', 'data' => array_values($saleConfirmNow),],
            ['name' => 'Doanh thu', 'last' => array_sum($revenueLast), 'now' => array_sum($revenueNow), 'percent' => $revenuePercent, 'color' => '#32D593', 'data' => array_values($revenueNow),],
        ];

        return $output;
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_stats/overview",
     *   summary="Get Order Stat Overview",
     *   operationId="getOrderStatOverview",
     *   tags={"BackendOrdStats"},
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
            list($mode, $start_date, $end_date, $last_start_date, $last_end_date) = $this->getReportDates();
            $year = (int)date('Y', strtotime($start_date));

            $output = [
                'sales_charts' => [
                    // Biểu đồ phân tích bán hàng
                    'sales_results' => $this->getSalesChartsSalesResults($mode, $start_date, $end_date),
                ],
                'sales_index'  => $this->getSalesIndexSalesResults($start_date, $end_date, $last_start_date, $last_end_date),
            ];

            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    protected function getIndexOrderByDate($page, $pageSize, $sort = '', $order = '') {
        $table = 'sys__visitors';
        if (!$sort) {
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? "$table.id" : strtolower($sort);
            if ($sort == 'id') $sort = "$table.id";
        }
        if (!$order) {
            $order = (string)$this->request->get('order');
            $order = !$order ? 'desc' : strtoupper($order);
        }
        $queries = [
            'and'        => [],
            'in'         => [],
            'whereRaw'   => [],
            'orWhereRaw' => [],
        ];

        $data = $this->getRequestData();
        $start_date = (isset($data->{'start_date'}) && !is_null($data->{'start_date'}) && $data->{'start_date'} !== '') ? (string)$data->{'start_date'} : false;
        $end_date = (isset($data->{'end_date'}) && !is_null($data->{'end_date'}) && $data->{'end_date'} !== '') ? (string)$data->{'end_date'} : false;

        if ($start_date && $end_date) {
            $queries['whereRaw'][] = ["DATE($table.created_at) >= ? and DATE($table.created_at) <= ?", [$start_date, $end_date]];
        }

        $fields = [
            \DB::raw("date($table.created_at) AS day"),
            \DB::raw("count(*) as visit"),
            //"sum(v.clicks) as view",
            \DB::raw("(select sum(viewed) FROM user__product_viewed WHERE date(viewed_at) = date($table.created_at)) as seen_pd"),
            \DB::raw("(select count(*) FROM orders WHERE date(created_at) = date($table.created_at)) as count_order"), //  and order_status = 'completed'
            \DB::raw("(select sum(total) FROM orders WHERE date(created_at) = date($table.created_at)) as sum_order"), //  and order_status = 'completed'
            \DB::raw("(select count(*) FROM orders WHERE date(created_at) = date($table.created_at) and order_status = 'canceled') as count_order_canceled"),
            \DB::raw("(select sum(total) FROM orders WHERE date(created_at) = date($table.created_at) and order_status = 'canceled') as sum_order_canceled"),
            \DB::raw("(select count(*) FROM orders WHERE date(created_at) = date($table.created_at) and order_status = 'returned') as count_order_returned"),
            \DB::raw("(select sum(total) FROM orders WHERE date(created_at) = date($table.created_at) and order_status = 'returned') as sum_order_returned"),
            \DB::raw("((select count(distinct user_id) FROM orders WHERE date(created_at) = date($table.created_at) and user_id is not null) + (select count(*) FROM orders WHERE date(created_at) = date($table.created_at) and user_id is null)) as count_order_user_guess"),
            \DB::raw("((select count(distinct user_id) FROM orders o left join users u on u.id = o.user_id WHERE date(u.created_at) = date($table.created_at) and date(o.created_at) = date($table.created_at) and o.user_id is not null) + (select count(*) FROM orders WHERE date(created_at) = date($table.created_at) and user_id is null)) as count_order_new_user"),
        ];
        $results = $this->setUpQueryBuilder($this->visitor_repository->getModel(), $queries, false, $fields)
            ->groupBy("day")
            ->orderBy("day", "ASC")->take($pageSize)->skip($pageSize * ($page - 1))->get();

        return [$queries, $results];
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_stats_export_by_date",
     *   summary="Get Order Export By Date",
     *   operationId="getOrderExportByDate",
     *   tags={"BackendOrdStats"},
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
    public function exportExcelOrderByDate() {
        list($queries, $results) = $this->getIndexOrderByDate(1, 10000);
        $fields = ['day', 'sum_order', 'count_order', 'seen_pd', 'visit', 'conversion', 'count_order_canceled', 'sum_order_canceled', 'count_order_returned', 'sum_order_returned', 'count_order_user_guess', 'count_order_new_user', 'order_user_exist'];
        //=== Get rows
        $rows = [];
        foreach ($results as $k => $result) {
            $item = $result->toArray();
            $item['day'] = $item['day'] ? date('d/m/Y', strtotime($item['day'])) : '';
            $item['conversion'] = round(($item['count_order'] / $item['visit']) * 100, 2);
            $item['order_user_exist'] = $item['count_order_user_guess'] - $item['count_order_new_user'];

            $row = []; //[$k + 1];
            foreach ($fields as $field) {
                $row[] = isset($item[$field]) ? $item[$field] : '';
            }
            $rows[] = $row;
        }

        return Excel::download(new \Modules\Order\Exports\OrderByDateExport($rows), 'order-by-date' . date('Y-m-d') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
