<?php namespace Modules\Order\Http\Controllers\Api;

use DateTime;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Order\Repositories\OrderRepository;

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

    public function __construct(Request $request,
                                OrderRepository $order_repository) {
        $this->order_repository = $order_repository;

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

            $output = [];

            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
