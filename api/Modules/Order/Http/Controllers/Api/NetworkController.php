<?php namespace Modules\Order\Http\Controllers\Api;

use DateTime;
use Illuminate\Http\Request;
use Modules\Order\Networks\Lazada;
use Modules\Order\Networks\Shopee;
use Modules\Order\Networks\Tiktok;
use Modules\Order\Repositories\OrderRepository;
use Modules\User\Repositories\UserRepository;

/**
 * Class StatisticController
 *
 * @package Modules\Order\Http\Controllers\Api
 */
class NetworkController extends ApiBaseModuleController {
    /**
     * @var \Modules\Order\Repositories\OrderRepository;
     */
    protected $order_repository;
    /**
     * @var \Modules\User\Repositories\UserRepository;
     */
    protected $user_repository;

    public function __construct(Request $request,
                                OrderRepository $order_repository,
                                UserRepository $user_repository) {
        $this->order_repository = $order_repository;
        $this->user_repository = $user_repository;

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
        'basil'     => '#0b8043', // Màu xanh húng quế
        'banana'    => '#f6bf26', // Màu chuối
        'blueberry' => '#3f51b5', // Màu việt quất
        'sage'      => '#33b679', // Màu xanh lá nhạt
        'flamingo'  => '#e67c73', // Màu hồng hạc
        'peacock'   => '#039be5', // Màu xanh lam
        'tangerine' => '#f4511e', // Màu cam
        'lavender'  => '#7986cb', // Màu oải hương
        'grape'     => '#8e24aa', // Màu nho
        'graphite'  => '#616161', // Màu khói
        'calendar'  => '#4285f4', // Màu lịch
    ];

    // Lấy doanh thu theo website
    private function getRevenueWebsite($start_date, $end_date) {
        $fields = [\DB::raw('sum(total) as total')];
        $queries = [
            //'in'       => [['status', [0, 1]]],
            'in'       => [['order_status', ['completed']]],
            'whereRaw' => [['(? <= DATE(`created_at`)) and (DATE(`created_at`) <= ?)', [$start_date, $end_date]]],
        ];
        $model = $this->setUpQueryBuilder($this->order_repository->getModel(), $queries, false, $fields)->first();

        return $model ? (float)$model->total : 0;
    }

    // Lấy doanh thu theo tiktok
    private function get_all_orders_tiktok($start_date, $end_date) {
        try {
            $tiktok = new Tiktok();

            $orders = [];
            $page_token = '';
            while (true) {
                $filter = [
                    //"order_status"   => "COMPLETED",
                    "create_time_ge" => strtotime($start_date),
                    "create_time_lt" => strtotime($end_date . ' 23:59:59'),
                    "page_token"     => $page_token,
                    "page_size"      => 100,
                ];
                $response = $tiktok->connect()->Order->getOrderList($filter);
                if ($response) {
                    $data = $response;
                    if (isset($data["orders"])) {
                        $orders = array_merge($orders, $data["orders"]);
                    }
                    if (empty($data["next_page_token"])) {
                        break;
                    }
                    $page_token = $data["next_page_token"];
                } else {
                    break;
                }
            }

            return $orders;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Tiktok: ' . $e->getMessage());
        }
    }

    // Lấy doanh thu theo shopee
    private function get_all_orders_shopee($start_date, $end_date) {
        try {
            $shopee = new Shopee();

            $order_list = [];
            $start_timestamp = strtotime($start_date);
            $end_timestamp = strtotime($end_date . ' 23:59:59');

            while ($start_timestamp < $end_timestamp) {
                $temp_end_timestamp = min($start_timestamp + (15 * 24 * 60 * 60) - 1, $end_timestamp);

                $next_cursor = '';
                while (true) {
                    $filter = [
                        "time_range_field" => "create_time",
                        //"order_status"     => "COMPLETED", // Chỉ lấy các đơn đã hoàn thành
                        "time_from"        => $start_timestamp,
                        "time_to"          => $temp_end_timestamp, // Start time must be earlier than end time and diff in 15days
                        "cursor"           => $next_cursor,
                        "page_size"        => 100, // Max 100
                    ];
                    $response = $shopee->connect()->Order->getOrderList($filter);
                    if ($response) {
                        $data = $response;
                        if (isset($data["order_list"])) {
                            $order_list = array_merge($order_list, $data["order_list"]);
                        }
                        if ($data["more"] == false) {
                            break;
                        }
                        $next_cursor = $data["next_cursor"];
                    } else {
                        break;
                    }
                }

                $start_timestamp = $temp_end_timestamp + 1;
            }

            $order_detail_list = [];
            if ($order_list) {
                $size = 50; // Max 50
                $new_order_lists = [];
                while (count($order_list) > 0) {
                    $new_order_lists = array_slice($order_list, 0, $size);
                    $order_list = array_slice($order_list, $size);

                    $filter = [
                        "response_optional_fields"     => "total_amount",// "buyer_user_id,buyer_username,estimated_shipping_fee,recipient_address,actual_shipping_fee ,goods_to_declare,note,note_update_time,item_list,pay_time,dropshipper,dropshipper_phone,split_up,buyer_cancel_reason,cancel_by,cancel_reason,actual_shipping_fee_confirmed,buyer_cpf_id,fulfillment_flag,pickup_done_time,package_list,shipping_carrier,payment_method,total_amount,buyer_username,invoice_data, checkout_shipping_carrier, reverse_shipping_fee, order_chargeable_weight_gram, edt, prescription_images, prescription_check_status, ...",
                        "request_order_status_pending" => false,
                    ];
                    $ids = implode(',', array_column($new_order_lists, 'order_sn'));

                    $response = $shopee->connect()->Order->getOrderDetail($ids, $filter);
                    if ($response) {
                        $data = $response;
                        if (isset($data["order_list"])) {
                            $order_detail_list = array_merge($order_detail_list, $data["order_list"]);
                        }
                    }
                }
            }

            return $order_detail_list;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Shopee: ' . $e->getMessage());
        }
    }

    // Lấy doanh thu theo lazada
    private function get_all_orders_lazada($start_date, $end_date) {
        try {
            $startDate = new DateTime($start_date);
            $start_date = $startDate->format('c');
            $endDate = new DateTime($end_date . ' 23:59:59');
            $end_date = $endDate->format('c');

            $lazada = new Lazada();
            $orders = [];
            $offset = 0;
            $limit = 100;
            while (true) {
                $filter = [
                    //"update_before"  => '',
                    //"update_after"   => '',
                    //"sort_direction" => '',
                    "offset"         => $offset,
                    "limit"          => $limit,
                    //"sort_by"        => '', // created_at , updated_at
                    "created_before" => $end_date,
                    "created_after"  => $start_date,
                    //"status"         => 'delivered',
                ];
                $response = $lazada->getOrders($filter);
                if ($response) {
                    $data = json_decode($response, true)['data'];
                    if (isset($data["orders"])) {
                        $orders = array_merge($orders, $data["orders"]);
                    }
                    $offset += $limit;
                    if ($offset >= $data['countTotal']) {
                        break;
                    }
                } else {
                    break;
                }
            }

            return $orders;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Lazada: ' . $e->getMessage());
        }
    }

    private function getRevenueTiktok($start_date, $end_date) {
        $orders = $this->get_all_orders_tiktok($start_date, $end_date);
        $total_revenue = 0;
        foreach ($orders as $order) {
            $total_revenue += $order['payment']['total_amount'];
        }
        return $total_revenue;
    }

    private function getRevenueShopee($start_date, $end_date) {
        $orders = $this->get_all_orders_shopee($start_date, $end_date);
        $total_revenue = 0;
        foreach ($orders as $order) {
            $total_revenue += $order['total_amount'];
        }
        return $total_revenue;
    }

    private function getRevenueLazada($start_date, $end_date) {
        $orders = $this->get_all_orders_lazada($start_date, $end_date);
        $total_revenue = 0;
        $orders = is_object($orders) ? json_decode($orders, true) : $orders;
        foreach ($orders as $order) {
            $total_revenue += $order['price'] - $order['voucher'] + $order['shipping_fee'];
        }
        return $total_revenue;
    }

    private function getPieData($results) {
        $colors = array_values($this->colors);
        $output = ['items' => [], 'colors' => $colors, 'labels' => [], 'data' => []];
        $total = 0;
        foreach ($results as $item) $total += (int)$item['value'];
        $sub_total = 0;
        foreach ($results as $index => $item) {
            $value = (int)$item['value'];
            $rate = $total ? round($value / $total * 100) : 0;
            if ($index == count($results) - 1) {
                if ($rate > 0) $rate = 100 - $sub_total;
            } else {
                $sub_total += $rate;
            }
            $output['items'][] = ['name' => $item['name'], 'value' => $value, 'rate' => $rate, 'color' => $colors[$index % count($colors)]];
            $output['labels'][] = $item['name'];
            $output['data'][] = $value;
        }

        return $output;
    }

    // Biểu đồ tỷ trọng doanh thu theo nền tảng
    private function getChartsNetworksResults($start_date, $end_date) {
        $websiteTotal = $this->getRevenueWebsite($start_date, $end_date);
        $tiktokTotal = $this->getRevenueTiktok($start_date, $end_date);
        $shopeeTotal = $this->getRevenueShopee($start_date, $end_date);
        $lazadaTotal = $this->getRevenueLazada($start_date, $end_date);

        $results = [
            ['name' => 'Website', 'value' => $websiteTotal],
            ['name' => 'Tiktok Shop', 'value' => $tiktokTotal],
            ['name' => 'Shopee', 'value' => $shopeeTotal],
            ['name' => 'Lazada', 'value' => $lazadaTotal],
        ];
        $output = $this->getPieData($results);

        return $output;
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_networks/overview",
     *   summary="Get Order Networks Overview",
     *   operationId="getOrderNetworkOverview",
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
            //return $this->respondWithError(strtotime($end_date));
            $output = [
                'networks_doughnut_charts' => [
                    // Biểu đồ tỷ trọng khách hàng
                    'networks_results' => $this->getChartsNetworksResults($start_date, $end_date),
                ],
                //'network_index'  => $this->getIndexOrderResults($start_date, $end_date, $last_start_date, $last_end_date),
            ];

            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
