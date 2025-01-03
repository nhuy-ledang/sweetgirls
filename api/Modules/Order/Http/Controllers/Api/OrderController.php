<?php namespace Modules\Order\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use function Maatwebsite\Excel\Cache\delete;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Order\Repositories\OrderHistoryRepository;
use Modules\Order\Repositories\OrderProductRepository;
use Modules\Order\Repositories\OrderRepository;
use Modules\Order\Repositories\OrderShippingRepository;
use Modules\Order\Repositories\OrderTagsRepository;
use Modules\Order\Repositories\OrderTotalRepository;
use Modules\Order\Transport\Facade\Transport;
use Modules\Product\Entities\GiftSet;
use Modules\Product\Entities\Product;
use Modules\Product\Repositories\GiftOrderHistoryRepository;
use Modules\Product\Repositories\GiftOrderRepository;
use Modules\Product\Repositories\ProductQuantityRepository;
use Modules\Product\Repositories\ProductRepository;
use Modules\Stock\Repositories\RequestProductRepository;
use Modules\Stock\Repositories\RequestRepository;
use Modules\Stock\Repositories\StockProductRepository;
use Modules\Stock\Repositories\StockRepository;
use Modules\Stock\Repositories\StoProductRepository;
use Modules\Stock\Repositories\TicketRepository;
use Modules\System\Repositories\SettingRepository;
use Modules\User\Repositories\UserCoinRepository;
use Modules\User\Repositories\UserRankRepository;
use Modules\User\Repositories\UserRepository;

/**
 * Class OrderController
 *
 * @package Modules\Order\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-05-30
 */
class OrderController extends ApiBaseModuleController {
    /**
     * @var \Modules\Order\Repositories\OrderRepository
     */
    protected $model_repository;

    /**
     * @var \Modules\Order\Repositories\OrderProductRepository
     */
    protected $order_product_repository;

    /**
     * @var \Modules\Order\Repositories\OrderTotalRepository
     */
    protected $order_total_repository;

    /**
     * @var \Modules\Order\Repositories\OrderHistoryRepository
     */
    protected $order_history_repository;

    /**
     * @var \Modules\Order\Repositories\OrderShippingRepository
     */
    protected $order_shipping_repository;

    /**
     * @var \Modules\Order\Repositories\OrderTagsRepository
     */
    protected $order_tags_repository;

    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    /**
     * @var \Modules\Product\Repositories\ProductQuantityRepository
     */
    protected $product_quantity_repository;

    /**
     * @var \Modules\Stock\Repositories\StockRepository
     */
    protected $sto_stock_repository;

    /**
     * @var \Modules\Stock\Repositories\RequestRepository
     */
    protected $sto_request_repository;

    /**
     * @var \Modules\Stock\Repositories\RequestProductRepository
     */
    protected $sto_request_product_repository;

    /**
     * @var \Modules\Stock\Repositories\TicketRepository
     */
    protected $sto_ticket_repository;

    /**
     * @var \Modules\Stock\Repositories\StoProductRepository
     */
    protected $sto_product_repository;

    /**
     * @var \Modules\Stock\Repositories\StockProductRepository
     */
    protected $sto_stock_product_repository;

    /**
     * @var \Modules\Product\Repositories\GiftOrderRepository
     */
    protected $gift_order_repository;

    /**
     * @var \Modules\Product\Repositories\GiftOrderHistoryRepository
     */
    protected $gift_order_history_repository;

    /**
     * @var \Modules\User\Repositories\UserRepository
     */
    protected $user_repository;

    /**
     * @var \Modules\User\Repositories\UserCoinRepository
     */
    protected $user_coin_repository;

    /**
     * @var \Modules\User\Repositories\UserRankRepository
     */
    protected $user_rank_repository;

    public function __construct(Request $request,
                                OrderRepository $order_repository,
                                OrderProductRepository $order_product_repository,
                                OrderTotalRepository $order_total_repository,
                                OrderHistoryRepository $order_history_repository,
                                OrderShippingRepository $order_shipping_repository,
                                OrderTagsRepository $order_tags_repository,
                                ProductRepository $product_repository,
                                ProductQuantityRepository $product_quantity_repository,
                                StockRepository $sto_stock_repository,
                                RequestRepository $sto_request_repository,
                                RequestProductRepository $sto_request_product_repository,
                                TicketRepository $sto_ticket_repository,
                                StoProductRepository $sto_product_repository,
                                StockProductRepository $sto_stock_product_repository,
                                GiftOrderRepository $gift_order_repository,
                                GiftOrderHistoryRepository $gift_order_history_repository,
                                UserRepository $user_repository,
                                UserCoinRepository $user_coin_repository,
                                UserRankRepository $user_rank_repository,
                                SettingRepository $setting_repository) {
        $this->model_repository = $order_repository;
        $this->order_product_repository = $order_product_repository;
        $this->order_total_repository = $order_total_repository;
        $this->order_history_repository = $order_history_repository;
        $this->order_shipping_repository = $order_shipping_repository;
        $this->order_tags_repository = $order_tags_repository;
        $this->product_repository = $product_repository;
        $this->product_quantity_repository = $product_quantity_repository;
        $this->sto_stock_repository = $sto_stock_repository;
        $this->sto_request_repository = $sto_request_repository;
        $this->sto_request_product_repository = $sto_request_product_repository;
        $this->sto_ticket_repository = $sto_ticket_repository;
        $this->sto_product_repository = $sto_product_repository;
        $this->sto_stock_product_repository = $sto_stock_product_repository;
        $this->gift_order_repository = $gift_order_repository;
        $this->gift_order_history_repository = $gift_order_history_repository;
        $this->user_repository = $user_repository;
        $this->user_coin_repository = $user_coin_repository;
        $this->wheel_user_repository = $wheel_user_repository;
        $this->user_rank_repository = $user_rank_repository;
        $this->wheel_repository = $wheel_repository;
        $this->agent_repository = $agent_repository;
        $this->agent_point_repository = $agent_point_repository;
        $this->coupon_repository = $coupon_repository;
        $this->coupon_history_repository = $coupon_history_repository;
        $this->voucher_repository = $voucher_repository;
        $this->setting_repository = $setting_repository;

        $this->middleware('auth.usr')->except(['getPrint', 'cron']);

        parent::__construct($request);
    }

    /**
     * Get Index
     *
     * @param $page
     * @param $pageSize
     * @param string $sort
     * @param string $order
     * @return array
     */
    protected function getIndex($page, $pageSize, $sort = '', $order = '') {
        if (!$sort) {
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'orders.id' : strtolower($sort);
            if ($sort == 'id') $sort = 'orders.id';
        }
        if (!$order) {
            $order = (string)$this->request->get('order');
            $order = !$order ? 'desc' : strtoupper($order);
        }
        $queries = [
            'and'        => [
                //['orders.user_id', '=', $this->auth->id]
            ],
            'in'         => [],
            'whereRaw'   => [],
            'orWhereRaw' => [],
        ];
        $data = $this->getRequestData();
        $start_date = (isset($data->{'start_date'}) && !is_null($data->{'start_date'}) && $data->{'start_date'} !== '') ? (string)$data->{'start_date'} : false;
        $end_date = (isset($data->{'end_date'}) && !is_null($data->{'end_date'}) && $data->{'end_date'} !== '') ? (string)$data->{'end_date'} : false;
        if ($start_date && $end_date) $queries['whereRaw'][] = ["DATE(orders.created_at) >= ? and DATE(orders.created_at) <= ?", [$start_date, $end_date]];
        /*$user_id = (isset($data->{'user_id'}) && !is_null($data->{'user_id'}) && $data->{'user_id'} !== '') ? (int)$data->{'user_id'} : false;
        if ($user_id) $queries['and'][] = ['orders.user_id', '=', $user_id];*/
        $payment_status = trim(utf8_strtolower((isset($data->{'payment_status'}) && !is_null($data->{'payment_status'}) && $data->{'payment_status'} !== '') ? trim((string)$data->{'payment_status'}) : ''));
        if ($payment_status) $queries['and'][] = ['orders.payment_status', '=', $payment_status];
        $payment_code = trim(utf8_strtolower((isset($data->{'payment_code'}) && !is_null($data->{'payment_code'}) && $data->{'payment_code'} !== '') ? trim((string)$data->{'payment_code'}) : ''));
        if ($payment_code) $queries['in'][] = ['orders.payment_code', explode(',', $payment_code)];
        $shipping_status = trim(utf8_strtolower((isset($data->{'shipping_status'}) && !is_null($data->{'shipping_status'}) && $data->{'shipping_status'} !== '') ? trim((string)$data->{'shipping_status'}) : ''));
        if ($shipping_status) $queries['and'][] = ['orders.shipping_status', '=', $shipping_status];
        $order_status = trim(utf8_strtolower((isset($data->{'order_status'}) && !is_null($data->{'order_status'}) && $data->{'order_status'} !== '') ? trim((string)$data->{'order_status'}) : ''));
        if ($order_status) $queries['and'][] = ['orders.order_status', '=', $order_status];
        /*$order_status = (isset($data->{'order_status'}) && !is_null($data->{'order_status'}) && $data->{'order_status'} !== '') ? trim((string)$data->{'order_status'}) : '';
        if ($order_status) {
            $statuses = [];
            foreach (explode(',', $order_status) as $item) {
                if (trim($item)) $statuses[] = trim($item);
            }
            if ($statuses) $queries['in'][] = ['orders.order_status', array_merge($statuses)];
        }*/
        // Query by keyword
        /*$q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
        if ($q) {
            $arrQ = $this->parseToArray(trim(utf8_strtolower($q)));
            $keys = ["`u`.`first_name`"];
            foreach ($keys as $key) {
                $iQ = [];
                $iB = [];
                foreach ($arrQ as $i) {
                    $iQ[] = "lower($key) like ?";
                    $iB[] = "%$i%";
                }
                $queries['orWhereRaw'][] = ['(' . implode(' and ', $iQ) . ')', $iB];
            }
        }*/
        $q = trim(utf8_strtolower((isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : ''));
        if ($q) $queries['whereRaw'][] = ["lower(`u`.`first_name`) like ? or `u`.`phone_number` like ?", ["%$q%", "%$q%"]];
        $affiliate = trim(utf8_strtolower((isset($data->{'affiliate'}) && !is_null($data->{'affiliate'}) && $data->{'affiliate'} !== '') ? trim((string)$data->{'affiliate'}) : ''));
        if ($affiliate) $queries['whereRaw'][] = ["lower(`aa`.`fullname`) like ? or `aa`.`email` like ?", ["%$affiliate%", "%$affiliate%"]];
        $tags = trim(utf8_strtolower((isset($data->{'tags'}) && !is_null($data->{'tags'}) && $data->{'tags'} !== '') ? trim((string)$data->{'tags'}) : ''));
        if ($tags) $queries['whereRaw'][] = ["FIND_IN_SET(?, lower(`orders`.`tags`))", $tags];
        $invoice_no = (isset($data->{'invoice_no'}) && !is_null($data->{'invoice_no'}) && $data->{'invoice_no'} !== '') ? trim((string)$data->{'invoice_no'}) : '';
        if ($invoice_no) {
            $invoice_no = str_replace([" ", "\r\n", "\r"], ",", trim($invoice_no));
            $tmp = explode(',', $invoice_no);
            $nos = [];
            foreach ($tmp as $t) {
                if (trim($t)) $nos[] = trim($t);
                /*$e = explode('-', $t);
                if (count($e) == 2) {
                    $invoice_prefix = $e[0] . '-';
                    $invoice_no = trim(ltrim($e[1], '0'));
                    $nos[] = $invoice_prefix . $invoice_no;
                }*/
            }
            if ($nos) {
                $q = [];
                //foreach ($nos as $no) $q[] = "concat_ws('',`invoice_prefix`,`invoice_no`) like ?";
                foreach ($nos as $no) $q[] = "orders.`idx` like ?";
                $queries['whereRaw'][] = ['(' . implode(' or ', $q) . ')', $nos];
            }
            //$queries['whereRaw'][] = ["concat_ws('',`invoice_prefix`,`invoice_no`) like ?", [$invoice_no]];
        }
        $fields = [
            'orders.*',
            'u.id as u__id',
            'u.first_name as u__first_name',
            'u.last_name as u__last_name',
            'u.calling_code as u__calling_code',
            'u.phone_number as u__phone_number',
            'u.email as u__email',
            'u.avatar as u__avatar',
            'u.avatar_url as u__avatar_url',
            'aa.fullname as aa__fullname',
            'aa.email as aa__email',
        ];
        $is_invoice = (isset($data->{'is_invoice'}) && !is_null($data->{'is_invoice'}) && $data->{'is_invoice'} !== '') ? (int)$data->{'is_invoice'} : false;
        if ($is_invoice === 1) $queries['and'][] = ['orders.is_invoice', '=', 1];
        $results = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false, $fields)
            ->leftJoin('users as u', 'u.id', '=', 'user_id')
            // ->leftJoin('aff__agents as aa', 'aa.id', '=', 'affiliate_id')
            ->with('user')
            ->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();

        return [$queries, $results];
    }

    private function sendEmails($model) {
        // Setup email to admin
        $emails = [];
        /*$email = $this->setting_repository->findByKey('config_email');
        if (trim($email)) $emails[] = trim($email);*/
        $alert_email = $this->setting_repository->findByKey('config_mail_alert_email');
        if ($alert_email) {
            $alert_emails = explode("\n", str_replace(["\r\n", "\r"], "\n", trim($alert_email)));
            foreach ($alert_emails as $alert_email) {
                $e2 = explode(',', (string)$alert_email);
                foreach ($e2 as $i) if (trim($i)) $emails[] = trim($i);
            }
        }
        $emails = array_unique($emails);
        // Send email notify
        $data = $this->getOrderProducts($model);
        $data['emails'] = $emails;
        if ($model->payment_code == PAYMENT_MT_BANK_TRANSFER) {
            $config_card_holder = $this->setting_repository->findByKey('config_card_holder');
            $config_bank_number = $this->setting_repository->findByKey('config_bank_number');
            $config_bank_name = $this->setting_repository->findByKey('config_bank_name');
            $html = "<table cellpadding='10' style='width:100%'>
                    <thead style='border-bottom:2px solid #e5e5e5;font-weight:bold'>
                          <tr>
                            <th colspan='2' style='text-align: left'>THÔNG TIN CHUYỂN KHOẢN</th>
                          </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td style='border-collapse:collapse;border-spacing:0;border-top:1px solid #dee2e6'>Tên tài khoản: </td>
                        <td style='border-collapse:collapse;border-spacing:0;text-align:left;border-top:1px solid #dee2e6'><b>$config_card_holder</b></td>
                      </tr>
                      <tr>
                        <td style='border-collapse:collapse;border-spacing:0;border-top:1px solid #dee2e6'>Số tài khoản: </td>
                        <td style='border-collapse:collapse;border-spacing:0;text-align:left;border-top:1px solid #dee2e6'><b>$config_bank_number</b></td>
                      </tr>
                      <tr>
                        <td style='border-collapse:collapse;border-spacing:0;border-top:1px solid #dee2e6'>Tên ngân hàng: </td>
                        <td style='border-collapse:collapse;border-spacing:0;text-align:left;border-top:1px solid #dee2e6'><b>$config_bank_name</b></td>
                      </tr>
                      <tr>
                        <td style='border-collapse:collapse;border-spacing:0;border-top:1px solid #dee2e6;border-bottom:1px solid #dee2e6'>Nội dung chuyển khoản: </td>
                        <td style='border-collapse:collapse;border-spacing:0;text-align:left;border-top:1px solid #dee2e6;border-bottom:1px solid #dee2e6'><b>$model->no - $model->shipping_phone_number</b></td>
                      </tr>
                    </tbody>
                  </table>";
            $data['bank_transfer'] = $html;
        }
        $data['config_owner'] = $this->setting_repository->findByKey('config_owner');
        if ($model->email) dispatch(new \Modules\Order\Jobs\OrderAddJob($this->email, $data));
        /*$data['emails'] = ['vanhuy@tedfast.vn'];
        $model->email = 'huydang1920@gmail.com';//$order->email;
        $model = $data['model'];
        // Send to customer
        $this->email->send('order::mail/order_add', $data, function($message) use ($model) {
            $message->to($model->email)->subject("SweetGirl xác nhận đơn hàng #{$model->id}");
        });
        // Send alert to admin
        if (!empty($data['emails'])) {
            $emails = $data['emails'];
            $this->email->send('order::mail/order_add_alert', $data, function($message) use ($emails) {
                foreach ($emails as $email) $message->to($email)->subject('Đơn hàng');
            });
        }*/
    }

    private function handleOrder($model) {
        // Add coupon histories
        if ($model->discount_code) {
            $coupon = $this->coupon_repository->getModel()->where('code', $model->discount_code)->first();
            if ($coupon) {
                $user_id = $model->user_id ? $model->user_id : null;
                $session_id = $this->getSessionId();
                $this->coupon_history_repository->create(['coupon_id' => $coupon->id, 'code' => $coupon->code, 'order_id' => $model->id, 'user_id' => $user_id, 'session_id' => $session_id, 'amount' => $model->discount_total]);
            }
        }
        $order_products = $model->order_products;
        // Update product quantity
        foreach ($order_products as $order_product) {
            $this->product_quantity_repository->create(['type' => 'out', 'product_id' => $order_product->product_id, 'order_id' => $order_product->order_id, 'quantity' => $order_product->quantity]);
            $product = $order_product->product;
            if ($product) {
                $product->quantity = $product->quantity - $order_product->quantity;
                $product->save();
            }
        }
    }

    public function handleReward($model) {
        $dateNow = date('Y-m-d H:i:s');
        // Add coins
        $user = $model->user;
        if ($user) {
            // Save rank before
            $user_rank = $user->rank ? $user->rank->rank : '';
            // Set expiration date of coins
            $next_expired = date('Y-m-t', strtotime('+12 months'));
            $this->user_repository->update($user, ['coins' => $user->coins + $model->coins, 'coins_expired' => $next_expired, 'points' => $user->points + $model->coins, 'spend' => $user->spend + $model->total]);
            $this->user_coin_repository->create(['user_id' => $model->user_id, 'type' => 'order', 'obj_id' => $model->id, 'coins' => $model->coins, 'total' => $user->coins]);
            // Send email rank notification upgrade
            $notification = $this->setting_repository->findByKey('config_user_rank_notification');
            if ($notification && ($user_rank && $user_rank < $user->rank->rank)) {
                $html = $notification;
                $html = str_replace('{TEN_KH}', "{$user->display}", $html);
                $html = str_replace('{HANG}', "{$user->rank->name}", $html);
                $data['html'] = $html;

                $addresses = $this->setting_repository->findByKey('config_address');
                $setting = [
                    'email_support' => $this->setting_repository->findByKey('config_email_support'),
                    'hotline'       => $this->setting_repository->findByKey('config_hotline'),
                    'phone_number'  => $this->setting_repository->findByKey('config_telephone'),
                    'address'       => $addresses && is_array($addresses) && isset($addresses[$this->locale]) ? $addresses[$this->locale] : '',
                    'facebook_url'  => $this->setting_repository->findByKey('config_facebook_url'),
                    'instagram_url' => $this->setting_repository->findByKey('config_instagram_url'),
                    'zalo_url'      => $this->setting_repository->findByKey('config_zalo_url'),
                    'config_owner'  => $this->setting_repository->findByKey('config_owner'),
                ];
                $data['setting'] = $setting;
                $data['email'] = $user->email;
                dispatch(new \Modules\User\Jobs\RankNotificationJob($this->email, $data));
            }
        }
        $agent = $model->affiliate_id ? $model->affiliate : null;
        // Add affiliate
        if ($agent) {
            /*$commission = (int)$this->setting_repository->findByKey('config_ord_commission_' . $agent->level);
            if (is_null($commission)) $commission = (int)$this->setting_repository->findByKey('config_ord_commission', 0);
            if ($commission && 0 < $commission && $commission < 100) {
                $balance = round(($model->sub_total / 100) * $commission / 100) * 100;
                $this->agent_point_repository->create(['agent_id' => $agent->id, 'type' => 'in', 'order_id' => $model->id, 'commission' => $commission, 'points' => $balance, 'amount' => $model->total]);
                $this->agent_repository->update($agent, ['commission' => $commission, 'balance' => $agent->balance + $balance, 'amount' => $agent->amount + $model->total]);
            }*/

            // Đã chuyển sang ApiPublic OrderController
            /*$cfgCommission = 0;//(int)$this->setting_repository->findByKey('config_ord_commission', 0); // Xóa khi khách hàng đã cập nhật rõ ràng
            $balance = 0;
            $model->commissions;
            foreach ($model->commissions as $order_product) {
                $commission = $cfgCommission;
                if ($order_product->commission && 0 < $order_product->commission && $order_product->commission < 100) {
                    $commission = $order_product->commission;
                }
                $balance += round(($order_product->total / 100) * $commission / 100) * 100;
            }
            if ($balance > 0) {
                $this->agent_point_repository->create(['agent_id' => $agent->id, 'type' => 'in', 'order_id' => $model->id, 'commission' => $commission, 'points' => $balance, 'amount' => $model->total]);
                $this->agent_repository->update($agent, ['commission' => $commission, 'balance' => $agent->balance + $balance, 'amount' => $agent->amount + $model->total]);
            }*/
            $aff_points = $this->agent_point_repository->findByAttributes(['agent_id' => $agent->id, 'order_id' => $model->id, 'status' => 0]);
            if ($aff_points) {
                $this->agent_point_repository->update($aff_points, ['status' => 1, 'completed_at' => $dateNow]);
                $this->agent_repository->update($agent, ['commission' => $aff_points->commission, 'balance' => $agent->balance + $aff_points->points, 'amount' => $agent->amount + $model->total]);
            }
        } else if ($model->referral_code) { // Referral code
            $referral_user = $this->user_repository->getModel()->where('share_code', $model->referral_code)->first();
            if ($referral_user) {
                //=== Create voucher and coin
                // Tài khoản của bạn (người giới thiệu) sẽ được 100 Coin + Voucher 50.000Đ cho đơn hàng 800.000Đ
                $code = $this->createVoucherCode($referral_user->id);
                $this->voucher_repository->create(['user_id' => $referral_user->id, 'order_id' => $model->id, 'code' => $code, 'amount' => 50000, 'total' => 800000, 'quantity' => 1]);
                // Add 100 coin
                $this->user_repository->update($referral_user, ['coins' => $referral_user->coins + 100, 'points' => $referral_user->points + 100]);
                $this->user_coin_repository->create(['user_id' => $referral_user->id, 'type' => 'referral', 'obj_id' => $model->id, 'coins' => 100, 'total' => $referral_user->coins + 100]);
                // Bạn của bạn (người được giới thiệu) sẽ được tặng Voucher 30.000Đ cho đơn hàng từ 500.000Đ
                $code = $this->createVoucherCode($user->id);
                $this->voucher_repository->create(['user_id' => $user->id, 'order_id' => $model->id, 'code' => $code, 'amount' => 30000, 'total' => 500000, 'quantity' => 1]);
            }
        }
        // Send email notify
        if ($model->email) {
            $data = $this->getOrderProducts($model);
            // Override
            $setting = $data['setting'];
            $order = $data['model'];
            /*$html = $this->setting_repository->findByKey('config_email_pd_order_completed', '', $this->locale);
            $html = str_replace('{ID}', "{$order['master_id']}", $html);
            $html = str_replace('{SHIPPING_AT}', date('d/m/Y', strtotime($data['shipping_at'])), $html);
            $html = str_replace('{EMAIL_SUPPORT}', "<a style=\"color: #B94551;text-decoration: none;\" href=\"mailto:{$setting['email_support']}\" target=\"_blank\">{$setting['email_support']}</a>", $html);
            $html = str_replace('{HOTLINE}', "<a href=\"tel:{$setting['hotline']}\" style=\"color: #B94551;text-decoration: none;\" target=\"_blank\">{$setting['hotline']}</a>", $html);
            $html = str_replace('{SETTING_ADDRESS}', "{$setting['address']}", $html);
            $html = str_replace('{SETTING_PHONE_NUMBER}', "{$setting['phone_number']}", $html);
            $html = str_replace('{SETTING_FACEBOOK}', "{$setting['facebook_url']}", $html);
            $html = str_replace('{SETTING_INSTAGRAM}', "{$setting['instagram_url']}", $html);*/
            $data['html'] = '';//$html;
            $data['config_owner'] = $this->setting_repository->findByKey('config_owner');
            dispatch(new \Modules\Order\Jobs\OrderCompletedJob($this->email, $data));
        }
    }

    public function handleCancelOrder($model) {
        $reason = $this->request->get('reason');
        $comment = $this->request->get('comment');
        $text = '';
        if ($model->payment_status == PAYMENT_SS_FAILED) {
            $text = $model->summary;
        } else if (!empty($reason)) {
            $text = $reason;
        } else if (!empty($comment)) {
            $text = $comment;
        }

        // Update Model
        $model = $this->model_repository->update($model, ['order_status' => ORDER_SS_CANCELED, 'reason' => $text, 'comment' => $text]);
        // Create history
        $this->order_history_repository->create(['order_id' => $model->id, 'order_status' => ORDER_SS_CANCELED, 'comment' => $text]);

        // Return affiliate
        $agent = $model->affiliate_id ? $model->affiliate : null;
        if ($agent) {
            $aff_points = $this->agent_point_repository->findByAttributes(['agent_id' => $agent->id, 'order_id' => $model->id, 'status' => 1]);
            if ($aff_points) {
                $this->agent_point_repository->update($aff_points, ['status' => 0, 'completed_at' => null]);
                $this->agent_repository->update($agent, ['balance' => $agent->balance - $aff_points->points, 'amount' => $agent->amount - $model->total]);
            }
        }

        // Thiếu return khi có referral_code

        // Return coins
        $user = $model->user;
        if ($user) {
            if ($model->total_coins > 0) {
                $user->coins = $user->coins + $model->total_coins;
                $this->user_coin_repository->create(['user_id' => $model->user_id, 'type' => 'order', 'obj_id' => $model->id, 'coins' => $model->total_coins, 'total' => $user->coins]);
            }
            $exist_user_coin = $this->user_coin_repository->findByAttributes(['user_id' => $model->user_id, 'type' => 'order', 'obj_id' => $model->id, 'coins' => $model->coins]);
            if ($exist_user_coin) {
                $user->coins = $user->coins - $model->coins;
                $user->points = $user->points - $model->coins;
                $this->user_coin_repository->create(['user_id' => $model->user_id, 'type' => 'order', 'obj_id' => $model->id, 'coins' => -$model->coins, 'total' => $user->coins]);
                // coins_expired: trừ ngày hết hạn coin. Nếu có đơn hàng phía sau hoàn thành trước thì???
            }
            $user->spend = $user->spend - $model->total;
            $user->save();
        }

        // Return total wheel && voucher
        $model_user_wheel = $this->wheel_user_repository->findByAttributes(['order_id' => $model->id]);
        if ($model_user_wheel && $model_user_wheel->wheel_id) {
            $this->wheel_repository->getModel()->where('id', $model_user_wheel->wheel_id)->increment('total', 1);
            $this->wheel_user_repository->destroy($model_user_wheel);
            $this->voucher_repository->getModel()->where('order_id', $model->id)->delete();
        }

        // Return coupon histories
        if ($model->discount_code) {
            $coupon = $this->coupon_repository->getModel()->where('code', $model->discount_code)->first();
            if ($coupon) {
                $this->coupon_repository->update($coupon, ['uses_total' => $coupon->uses_total - 1]);
                $this->coupon_history_repository->getModel()->where('code', $model->discount_code)->where('order_id', $model->id)->delete();
            }
        }

        // Return pd__gift_orders
        $gift_order_histories = $this->gift_order_history_repository->getModel()->where('order_id', $model->id)->get();
        if ($gift_order_histories) {
            foreach ($gift_order_histories as $item) {
                $this->gift_order_repository->getModel()->where('id', $item['gift_order_id'])->decrement('uses_total');
            }
        }

        $order_products = $model->order_products;
        // Return product quantity
        foreach ($order_products as $order_product) {
            $this->product_quantity_repository->create(['type' => 'in', 'product_id' => $order_product->product_id, 'order_id' => $order_product->order_id, 'quantity' => $order_product->quantity]);
            $product = $order_product->product;
            if ($product) {
                $product->quantity = $product->quantity + $order_product->quantity;
                $product->save();
            }
        }

        // Cancel ViettelPost
        try {
            if ($model->shipping_status && $model->shipping_status !== SHIPPING_SS_CREATE_ORDER) return $this->errorWrongArgs('order.status.invalid');
            $shipping = $model->shipping;
            if ($shipping) {
                $params = [
                    'TYPE'         => 4,
                    'ORDER_NUMBER' => $shipping->order_number,
                    'NOTE'         => $text,
                ];
                $response = Transport::updateStatusOrder($params);
                // 201: Hủy nhập phiếu gửi, 203: Order does not exist or status has been changed
                if (!in_array($response['status'], [200, 201, 203])) {
                    return $this->respondWithErrorKey('system.wrong_arguments', 400, $response['message'], [], ['vtp' => $response]);
                }
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
        /*// Update product quantity
        $results = $this->product_quantity_repository->getModel()->where('type', 'out')->where('order_id', $id)->get();
        foreach ($results as $result) {
            $product = $result->product;
            $product->quantity = $product->quantity + $result->quantity;
            $product->save();
        }
        $this->product_quantity_repository->getModel()->where('type', 'out')->where('order_id', $id)->delete();*/
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_orders",
     *   summary="Get Orders",
     *   operationId="getOrders",
     *   tags={"BackendOrdOrders"},
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
    public function index() {
        try {
            // Check permission
            if (!$this->isCRUD('orders', 'view')) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            list($queries, $results) = $this->getIndex($page, $pageSize);
            $output = [];
            foreach ($results as $item) {
                $newItem = $this->parseToRespond($item->toArray());
                /*$names = [];
                foreach ($item->products as $product) {
                    $names[] = $product->name;
                }
                $newItem['name'] = implode(', ', $names);*/
                //$newOrder['products'] = $item->products;
                //$newItem['user'] = new \Modules\User\Entities\Sentinel\User($newItem['u']);
                //unset($newItem['u']);
                $newItem['has_wheel'] = \DB::table('user__wheels')->where('order_id', $newItem['id'])->count();
                $output[] = $newItem;
            }
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging === 'true' ? true : ($paging === 'false' ? false : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($output);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, true)
                    ->leftJoin('users as u', 'u.id', '=', 'user_id')
                    // ->leftJoin('aff__agents as aa', 'aa.id', '=', 'affiliate_id')
                    ->count();
                return $this->respondWithPaging($output, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_orders/{id}",
     *   summary="Get a Order",
     *   description="Get a Order",
     *   operationId="getOrder",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Order Id", example=1),
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
            $model = $this->setUpQueryBuilder($this->model_repository->getModel(), [], false)->where('id', $id)->first();
            if (!$model) return $this->errorNotFound();

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/ord_orders",
     *   summary="Create Order",
     *   operationId="createOrder",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     description="Create Order: order_type:product,exhibition,activity | payment_code:cod|bank_transfer",
     *     @OA\JsonContent(),
     *   ),
     *   @OA\Parameter(name="tz", in="query",description="TimezoneOffset", example="-420"),
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
            // Check permission
            if (!$this->isCRUD('orders', 'create')) return $this->errorForbidden();
            $tz = (int)$this->request->get('tz');
            $dateNow = $this->getDateLocalFromTz($tz);
            $input = $this->request->only(['first_name', 'email', 'phone_number', 'payment_code', 'company', 'company_tax', 'company_email', 'company_address', 'note']);
            $validatorErrors = $this->getValidator($input, [
                'first_name'   => 'required',
                'payment_code' => 'required|in:' . PAYMENT_MT_COD . ',' . PAYMENT_MT_BANK_TRANSFER, // PAYMENT_MT_CASH
            ]);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            //=== Get Affiliate
            $tracking = $this->request->get('tracking');
            if (!is_null($tracking)) $tracking = (string)$tracking;
            $affiliate = $tracking ? $this->agent_repository->getModel()->where('status', 1)->where('code', $tracking)->first() : null;
            $agent = null;

            $user_id = (int)$this->request->get('user_id');
            $user = false;
            if ($user_id) {
                $user = $this->user_repository->find($user_id);
                if ($user) {
                    $input['user_id'] = $user->id;
                    $input['user_group_id'] = $user->user_group_id;
                    if (empty($input['first_name'])) $input['first_name'] = $user->display;
                    if (empty($input['email'])) $input['email'] = $user->email;
                    if (empty($input['phone_number'])) $input['phone_number'] = $user->phone_number;
                }
                // Agent
                $agent = $this->agent_repository->getModel()->where('status', 1)->where('user_id', $user_id)->first();
            }
            if (!$user && !empty($input['email'])) {
                $user = $this->user_repository->findByAttributes(['email' => $input['email']]);
                if ($user) {
                    $input['user_id'] = $user->id;
                    $input['user_group_id'] = $user->user_group_id;
                }
            }
            if ($affiliate || $agent) {
                $input['affiliate_id'] = $affiliate ? $affiliate->id : $agent->id;
                $input['tracking'] = $affiliate ? $tracking : $agent->code;
            }
            $payment_code = $this->request->get('payment_code');
            $input['payment_code'] = $payment_code;
            $input['payment_method'] = Lang::get("transaction.payment_method.$payment_code");

            $input['shipping_first_name'] = $this->request->get('shipping_first_name') ? $this->request->get('shipping_first_name') : $this->auth->display;
            $input['shipping_phone_number'] = $this->request->get('shipping_phone_number') ? $this->request->get('shipping_phone_number') : $this->auth->phone_number;
            $input['shipping_address_1'] = $this->request->get('shipping_address_1');
            $input['shipping_province'] = $this->request->get('shipping_province');
            $input['shipping_province_id'] = $this->request->get('shipping_province_id');
            $input['shipping_district'] = $this->request->get('shipping_district');
            $input['shipping_district_id'] = $this->request->get('shipping_district_id');
            $input['shipping_ward'] = $this->request->get('shipping_ward');
            $input['shipping_ward_id'] = $this->request->get('shipping_ward_id');
            $input['shipping_code'] = $this->request->get('shipping_code');

            $shipping_fee = $this->request->get('shipping_fee');
            $input['shipping_fee'] = $shipping_fee;
            $shipping_discount = $this->request->get('shipping_discount') ? $this->request->get('shipping_discount') : 0;
            $input['shipping_discount'] = $shipping_discount;
            $input['vat'] = 0;
            $is_invoice = $this->request->get('is_invoice');
            $is_invoice = (is_null($is_invoice) || $is_invoice === 'false') ? false : ($is_invoice === 'true' ? true : (boolean)$is_invoice);
            if ($is_invoice) $input['is_invoice'] = $is_invoice;
            // Products
            $tmpProducts = (array)$this->request->get('products');
            $products = [];
            if (!empty($tmpProducts)) {
                foreach ($tmpProducts as $item) {
                    $validatorErrors = $this->getValidator($item, [
                        'id'       => 'required|integer',
                        'quantity' => 'required|integer|min:1',
                    ]);
                    if (empty($validatorErrors)) {
                        if (intval($item['id'])) {
                            $product = $this->product_repository->getModel()->where('id', $item['id'])->select(['*', \DB::raw("(select price from pd__product_specials ps where ps.product_id = pd__products.id and ((ps.start_date is null or UNIX_TIMESTAMP(ps.start_date) <= UNIX_TIMESTAMP('$dateNow')) and (ps.end_date is null or UNIX_TIMESTAMP('$dateNow') <= UNIX_TIMESTAMP(ps.end_date))) order by ps.priority asc, price asc limit 1) as special")])->first();
                            if ($product) {
                                $newProduct = new \stdClass();
                                $newProduct->quantity = $item['quantity'];
                                foreach (['id', 'name', 'model', 'price', 'special', 'master_id', 'gift_set_id'] as $fieldName) {
                                    $newProduct->{$fieldName} = $product->{$fieldName};
                                }

                                $gift_set_id = 0;
                                if (isset($newProduct->master_id) && $newProduct->master_id && !$newProduct->gift_set_id) {
                                    // Gift products
                                    $master_pd = Product::where('id', $newProduct->master_id)->select(['gift_set_id'])->first();
                                    if ($master_pd) $gift_set_id = $master_pd->gift_set_id;
                                }
                                if ($newProduct->gift_set_id) $gift_set_id = $newProduct->gift_set_id;
                                if ($gift_set_id) {
                                    $gift_set = GiftSet::where('id', $gift_set_id)->first();
                                    if ($gift_set) $newProduct->gifts = $gift_set->products;
                                }
                                $products[] = $newProduct;
                            }
                        }
                    }
                }
            }
            if (!$products) return $this->respondWithErrorKey('product_id.required');
            //$no_cod = in_array(true, array_column($products, 'no_cod'));
            //if ($no_cod && $payment_code === PAYMENT_MT_COD) return $this->errorWrongArgs('order.payment.cod');
            // Totals
            $input['totals'] = [];
            $total = 0;

            $sub_total = 0;
            foreach ($products as $product) {
                $sub_total += ($product->special ? $product->special : $product->price) * $product->quantity;
            }
            $total += $sub_total;

            // Shipping fee
            $total = $total + ((int)$input['shipping_fee'] - (int)$input['shipping_discount']);

            // Coupon
            $coupon_total = 0;

            $input['totals']['sub_total'] = $sub_total;
            $input['totals']['coupon'] = $coupon_total;
            $input['totals']['total'] = max(0, $total);

            $input['sub_total'] = $sub_total;
            $input['total'] = $total;
            $input['coins'] = $total >= 1000 ? round($total / 1000) : 0;
            $input['lang'] = $this->locale;
            $input['currency_code'] = $this->getCurrency();

            $input['invoice_prefix'] = $this->setting_repository->findByKey("config_ord_invoice_prefix", 'INV-');
            $invoice_no = $this->model_repository->getModel()->selectRaw('max(invoice_no) as invoice_no')->where('invoice_prefix', $input['invoice_prefix'])->first();
            $input['invoice_no'] = $invoice_no ? ((int)$invoice_no->invoice_no + 1) : 1;
            $input['idx'] = $input['invoice_prefix'] . date('dmy') . '-' . $input['invoice_no'];
            // Create Model
            //$input['status'] = ORDER_SS_COMPLETED;
            $input['order_status'] = ORDER_SS_PROCESSING;
            $input['payment_status'] = PAYMENT_SS_INPROGRESS;
            $model = $this->model_repository->create($input);
            // Create Products
            $products_gift = [];
            foreach ($products as $product) {
                $this->order_product_repository->create([
                    'order_id'   => $model->id,
                    'product_id' => $product->id,
                    'name'       => $product->name,
                    'model'      => $product->model,
                    'quantity'   => $product->quantity,
                    'price'      => $product->special ? $product->special : $product->price,
                    'priceo'     => $product->price,
                    'total'      => ($product->special ? $product->special : $product->price) * $product->quantity,
                ]);
                // Create gift products
                if (!empty($product->gifts)) {
                    foreach ($product->gifts as $gift) {
                        if (isset($products_gift[$gift['id']])) {
                            $products_gift[$gift['id']]['quantity'] += ($gift['quantity'] * $product->quantity);
                        } else {
                            $products_gift[$gift['id']] = [
                                'order_id'   => $model->id,
                                'product_id' => $gift['id'],
                                'name'       => $gift['name'],
                                'quantity'   => $gift['quantity'] * $product->quantity,
                            ];
                        }
                    }
                }
            }

            // Create gift products
            foreach ($products_gift as $product_gift) {
                $this->order_product_repository->create($product_gift);
            }
            // Gifts order
            /*if ($current_gifts) {
                foreach ($current_gifts as $order_gift) {
                    $this->order_product_repository->create([
                        'order_id'   => $model->id,
                        'product_id' => $order_gift['id'],
                        'name'       => $order_gift['name'],
                        'quantity'   => $order_gift['quantity'],
                    ]);
                }
                if ($gift_orders_id) $this->gift_order_history_repository->create(['gift_order_id' => $gift_orders_id, 'order_id' => $model->id, 'user_id' => $this->auth->id]);
            }*/

            // Create Totals
            $sort_order = 0;
            $total_titles = ['sub_total' => 'Tạm tính', 'total' => 'Thành tiền'];
            foreach ($input['totals'] as $code => $value) {
                $this->order_total_repository->create([
                    'order_id'   => $model->id,
                    'code'       => $code,
                    'title'      => isset($total_titles[$code]) ? $total_titles[$code] : '',
                    'value'      => $value,
                    'sort_order' => ++$sort_order,
                ]);
            }
            // Create history
            $this->order_history_repository->create([
                'order_id'       => $model->id,
                'order_status'   => $model->order_status,
                'payment_status' => $model->payment_status,
                'comment'        => 'Tạo đơn hàng từ admin',
            ]);
            // Send email
            $this->sendEmails($model);
            // Plus uses_total in gift_orders
            //if ($gift_orders_id) $this->gift_order_repository->getModel()->where('id', $gift_orders_id)->increment('uses_total');
            if ($affiliate || $agent) {
                if ($total > 0) {
                    $total_without_shipping_fee = $total - ($shipping_fee - $shipping_discount);
                    $promo_total = 0;
                    $cfgCommission = 0;//(int)$this->setting_repository->findByKey('config_ord_commission', 0); // Xóa khi khách hàng đã cập nhật rõ ràng
                    $balance = 0;
                    $model->commissions;
                    foreach ($model->commissions as $order_product) {
                        $commission = $cfgCommission;
                        if ($order_product->commission && 0 < $order_product->commission && $order_product->commission < 100) {
                            $commission = $order_product->commission;
                        }
                        $product_discounted = $order_product->total - (($order_product->total / $sub_total) * $promo_total);
                        $balance += ($product_discounted * $commission) / 100;
                    }

                    if ($balance > 0) {
                        $agent_id = $affiliate ? $affiliate->id : $agent->id;
                        $avg_commission = round(($balance / $total_without_shipping_fee) * 100, 3);
                        $this->agent_point_repository->create(['agent_id' => $agent_id, 'type' => 'in', 'order_id' => $model->id, 'commission' => $avg_commission, 'points' => round($balance), 'amount' => $total]);
                    }
                }
            }

            $this->handleOrder($model);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/ord_orders/{id}",
     *   summary="Delete Order",
     *   operationId="deleteOrder",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Order Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function destroy($id) {
        /*try {
            //if (!$this->isAdmin()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            // Check payment online
            if ($model->order_status == ORDER_SS_COMPLETED && !in_array($model->payment_code, [PAYMENT_MT_BANK_TRANSFER, PAYMENT_MT_COD])) {
                return $this->errorWrongArgs('transaction.status.' . $model->order_status);
            }
            // Destroy
            $this->model_repository->destroy($model);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }*/
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_orders/{id}/products",
     *   summary="Get Products Order Products",
     *   operationId="createProductsOrderProducts",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Order Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function products($id) {
        /*if (!$this->isAccessAdmin()) {
            exit(401);
        }*/
        try {
            // Check permission
            // if (!$this->isCRUD('orders', 'edit')) return $this->errorForbidden();
            // Check admin
            //if (!$this->isAccessAdmin()) return $this->errorForbidden();

            $results = $this->order_product_repository->getModel()->where('order_id', $id)->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * Get code
     *
     * @param $user_id
     * @return string
     */
    private function createVoucherCode($user_id) {
        $code = strtoupper(str_random_alpha_numeric(1, false, false) . str_random_not_cap(9));
        $find = $this->voucher_repository->getModel()->where('user_id', $user_id)->where('code', $code)->first();
        if (!$find) {
            return $code;
        } else {
            return $this->createVoucherCode($user_id);
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/ord_orders/{id}/order_status",
     *   summary="Change Order Status",
     *   operationId="ordChangeOrderStatus",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Order Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example=""),
     *       @OA\Property(property="comment", type="string", example=""),
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
    public function changeOrderStatus($id) {
        try {
            // Check permission
            if (!$this->isCRUD('orders', 'edit')) return $this->errorForbidden();
            // Check admin
            //if (!$this->isAccessAdmin()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $status = $this->request->get('status');
            $comment = $this->request->get('comment');
            $tags = $this->request->get('tags');
            //$validatorErrors = $this->getValidator(compact('status'), ['status' => 'required|in:pending,processing,shipping,completed,canceled,returning,returned']);
            $validatorErrors = $this->getValidator(compact('status'), ['status' => 'required|in:pending,processing,shipping,completed,canceled']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            if ($model->order_status == $status) {
                if ($tags) {
                    $model = $this->model_repository->update($model, ['tags' => $tags]);
                    foreach (explode(',', $tags) as $tag) {
                        $order_tag = $this->order_tags_repository->findByAttributes(['name' => $tag]);
                        if (!$order_tag) $this->order_tags_repository->create(['name' => $tag]);
                    }
                }
                return $this->respondWithSuccess($model);
            } else if ($model->order_status == ORDER_SS_CANCELED && $status != ORDER_SS_COMPLETED) {
                return $this->errorWrongArgs('order.status.canceled');
            } else if ($model->order_status == ORDER_SS_COMPLETED && $status != ORDER_SS_CANCELED) {
                return $this->errorWrongArgs('order.status.completed');
            } else {
                $newData = ['pusr_id' => $this->auth->id, 'order_status' => $status];
                if ($status == 'shipping' || ($status == 'completed' && !$model->shipping_at)) {
                    //$tz = (int)$this->request->get('tz');
                    $newData['shipping_at'] = date('Y-m-d H:i:s'); //$this->getDateLocalFromTz($tz);
                }
                // Check payment status
                /*if ($model->payment_status != PAYMENT_SS_COMPLETED && in_array($model->payment_code, [PAYMENT_MT_BANK_TRANSFER, PAYMENT_MT_COD])) {
                    $newData['status'] = ORDER_SS_COMPLETED;
                }*/
                if ($tags) {
                    $newData['tags'] = $tags;
                    foreach (explode(',', $tags) as $tag) {
                        $order_tag = $this->order_tags_repository->findByAttributes(['name' => $tag]);
                        if (!$order_tag) $this->order_tags_repository->create(['name' => $tag]);
                    }
                }
                // Update model
                $model = $this->model_repository->update($model, $newData);

                if ($status == 'completed') {
                    $this->handleReward($model);
                } else if ($status == 'canceled') {
                    $this->handleCancelOrder($model);
                }

                if ($status != 'canceled') { // Because 'canceled' was created history in handleCancelOrder
                    // Create history
                    $this->order_history_repository->create(['order_id' => $model->id, 'order_status' => $status, 'comment' => $comment ? $comment : '']);
                }
            }
            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    public function changePaymentSS($model, $payment_status) {
        $tz = (int)$this->request->get('tz');
        $payment_at = $this->getDateLocalFromTz($tz);
        if ($model->order_status == ORDER_SS_COMPLETED) {
            if (in_array($model->payment_code, [PAYMENT_MT_BANK_TRANSFER, PAYMENT_MT_COD])) {
                $model = $this->model_repository->update($model, compact('payment_status', 'payment_at'));
            }
        } else {
            if ($payment_status == PAYMENT_SS_PAID && $model->shipping_status == SHIPPING_SS_DELIVERED) {
                // $order_status = ORDER_SS_COMPLETED; // Nếu cập nhật order_status phải xử lý hoa hồng, coin
                // $model = $this->model_repository->update($model, compact('payment_status', 'payment_at', 'order_status'));
                $model = $this->model_repository->update($model, compact('payment_status', 'payment_at'));
            }  else {
                if ($payment_status == PAYMENT_SS_PAID) {
                    if ($model->order_status == ORDER_SS_PENDING) {
                        $order_status = ORDER_SS_PROCESSING;
                    } else {
                        $order_status = $model->order_status;
                    }
                    $model = $this->model_repository->update($model, compact('payment_status', 'payment_at', 'order_status'));
                    // Send link wheel
                    if ($model->email && $this->setting_repository->findByKey('config_wheel_order_status')) {
                        $data = $this->getOrderProducts($model);
                        $data['link'] = config('app.url') . ($this->locale == 'en' ? '/en' : '') . "/checkout/success?id={$model->id}&s={$model->order_status}";
                        $data['config_owner'] = $this->setting_repository->findByKey('config_owner');
                        dispatch(new \Modules\Order\Jobs\OrderWheelJob($this->email, $data));
                    }
                } else {
                    $model = $this->model_repository->update($model, compact('payment_status'));
                }
            }
        }

        return $model;
    }

    /**
     * @OA\Post(
     *   path="/backend/ord_orders/{id}/payment_status",
     *   summary="Change Order Payment Status",
     *   operationId="ordChangeOrderPaymentStatus",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Order Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"payment_status"},
     *       @OA\Property(property="payment_status", type="string", example="paid|canceled"),
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
    public function changePaymentStatus($id) {
        try {
            // Check permission
            if (!$this->isCRUD('orders', 'edit')) return $this->errorForbidden();
            // Check admin
            //if (!$this->isAccessAdmin()) return $this->errorForbidden();
            $payment_status = (string)$this->request->get('payment_status');
            $validatorErrors = $this->getValidator(compact('payment_status'), ['payment_status' => 'required|in:' . PAYMENT_SS_PAID . ',' . PAYMENT_SS_CANCELED]);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();

            $model = $this->changePaymentSS($model, $payment_status);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/ord_orders_change_payments_status",
     *   summary="Chagne Payments Status",
     *   operationId="ordChangePaymentsStatus",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="ids", type="string", example=""),
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
    public function changePaymentsStatus() {
        try {
            // Check permission
            if (!$this->isCRUD('orders', 'edit')) return $this->errorForbidden();
            // Check admin
            //if (!$this->isAccessAdmin()) return $this->errorForbidden();
            $tmpIds = $this->request->get('ids');
            $ids = [];
            if (is_array($tmpIds)) {
                $ids = $tmpIds;
            } else if (!empty($tmpIds)) {
                foreach (explode(',', (string)$tmpIds) as $id) {
                    if (intval($id)) $ids[] = intval($id);
                }
            }
            $ids = array_unique($ids);
            if (!$ids) return $this->errorWrongArgs('order.required');
            $results = $this->model_repository->getModel()->whereIn('id', $ids)->where('payment_status', '!=', PAYMENT_SS_CANCELED)->get();
            if ($results->count() != count($ids)) return $this->errorWrongArgs('payment.state.has_failed');
            foreach ($results as $model) {
                $this->changePaymentSS($model, PAYMENT_SS_PAID);
            }

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/ord_orders/{id}/shipping_status",
     *   summary="Change Order Shipping Status",
     *   operationId="ordChangeOrderShippingStatus",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Order Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"shipping_status"},
     *       @OA\Property(property="shipping_status", type="string", example="create_order|delivering|delivered|return"),
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
    public function changeShippingStatus($id) {
        try {
            // Check permission
            if (!$this->isCRUD('orders', 'edit')) return $this->errorForbidden();
            // Check admin
            //if (!$this->isAccessAdmin()) return $this->errorForbidden();
            $shipping_status = (string)$this->request->get('shipping_status');
            //'create_order', 'delivering', 'delivered', 'return'
            $validatorErrors = $this->getValidator(compact('shipping_status'), ['shipping_status' => 'required|in:' . SHIPPING_SS_CREATE_ORDER . ',' . SHIPPING_SS_DELIVERING . ',' . SHIPPING_SS_DELIVERED . ',' . SHIPPING_SS_RETURN]);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $model = $this->model_repository->getModel()->where('id', $id)->has('shipping')->first();
            if (!$model) return $this->errorNotFound();
            if ($shipping_status != $model->shipping_status) {
                $data = compact('shipping_status');
                if ($shipping_status == SHIPPING_SS_DELIVERED) {
                    if ($model->payment_status == PAYMENT_SS_PAID) { // Update order status
                        $data['order_status'] = ORDER_SS_COMPLETED;
                    }
                }
                // Update model
                $model = $this->model_repository->update($model, $data);
                // Send email order shipping success
                // code ???
                if ($shipping_status == SHIPPING_SS_DELIVERED) {

                }
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * Create Shipping Order
     *
     * @param $model
     * @return array
     */
    private function createShippingOrder($model) {
        $order_products = $model->order_products;
        $weight = 0;
        $quantity = 0;
        $listItems = [];
        $productNames = [];
        $shortProducts = [];
        $messageProducts = [];
        foreach ($order_products as $product) {
            $weight += $product->weight * $product->quantity;
            $quantity += $product->quantity;
            if ($product->type == 'I' && $product->message) $pd_name = $product->name . ' - Lời chúc: ' . $product->message;
            $listItems[] = [
                'PRODUCT_NAME'     => isset($pd_name) ? $pd_name : $product->name,
                'PRODUCT_PRICE'    => $product->price,
                'PRODUCT_WEIGHT'   => $product->weight,
                'PRODUCT_QUANTITY' => $product->quantity,
            ];
            $short_desc = $product->quantity . 'x' . ($product->type !== 'I' && $product->short_description ? $product->short_description : $product->name);
            $productNames[] = $product->quantity . 'x' . $product->name;
            $shortProducts[] = $short_desc;
        }
        $PRODUCT_PRICE = $model->total - ($model->shipping_fee - $model->shipping_discount);
        if ($model->payment_code == PAYMENT_MT_COD) {
            if ($model->shipping_fee - $model->shipping_discount > 0) {
                $ORDER_PAYMENT = 2; // 2. Thu hộ tiền hàng và tiền cước
                $MONEY_COLLECTION = $model->total - $model->shipping_fee; // Tiền hàng cần thu hộ(VNĐ), không bao gồm tiền cước cần thu hộ.
            } else {
                $ORDER_PAYMENT = 3; // 3. Thu hộ tiền hàng, không thu hộ tiền cước.
                $MONEY_COLLECTION = $model->total; // Tiền hàng cần thu hộ thu hộ(VNĐ), tiền cước 0đ.
            }
        } else {
            $ORDER_PAYMENT = 1; // 1. Không thu hộ
            $MONEY_COLLECTION = 0;
        }
        $name = implode(', ', $productNames);
        $shortProduct = implode(', ', $shortProducts);
        $message = implode(', ', $messageProducts);
        $PRODUCT_NAME = $name . ' (' . $shortProduct . ' ' . $message . ')';
        if ($name == $shortProduct) $PRODUCT_NAME = $name;

        $params = [
            'ORDER_NUMBER'      => $model->no, // . '#' . $model->id,
            'GROUPADDRESS_ID'   => 0,
            /*'CUS_ID'              => 722,
            'DELIVERY_DATE'       => '11/10/2018 15:09:52',*/
            'SENDER_FULLNAME'   => $this->setting_repository->findByKey('config_ord_fullname', 'SweetGirl Shop'),
            'SENDER_ADDRESS'    => $this->setting_repository->findByKey('config_ord_address', 'SweetGirl Shop'),
            'SENDER_PHONE'      => $this->setting_repository->findByKey('config_ord_phone', ''),
            'SENDER_EMAIL'      => $this->setting_repository->findByKey('config_ord_email', ''),
            'SENDER_WARD'       => (int)$this->setting_repository->findByKey('config_ord_ward', 0),
            'SENDER_DISTRICT'   => (int)$this->setting_repository->findByKey('config_ord_district', 0),
            'SENDER_PROVINCE'   => (int)$this->setting_repository->findByKey('config_ord_province', 0),
            //'SENDER_LATITUDE'     => 0,
            //'SENDER_LONGITUDE'    => 0,
            'RECEIVER_FULLNAME' => $model->shipping_first_name,
            'RECEIVER_ADDRESS'  => $model->shipping_address_1,
            'RECEIVER_PHONE'    => $model->shipping_phone_number,
            'RECEIVER_EMAIL'    => $model->email,
            //'RECEIVER_WARD'     => $model->shipping_ward_id,
            'RECEIVER_DISTRICT' => $model->shipping_district_id,
            'RECEIVER_PROVINCE' => $model->shipping_province_id,
            //'RECEIVER_LATITUDE'   => 0,
            //'RECEIVER_LONGITUDE'  => 0,
            'PRODUCT_NAME'      => $PRODUCT_NAME,
            //'PRODUCT_DESCRIPTION' => 'Máy xay sinh tố Philips HR2118 2.0L ',
            'PRODUCT_QUANTITY'  => $quantity,
            'PRODUCT_PRICE'     => $PRODUCT_PRICE,
            'PRODUCT_WEIGHT'    => $weight,
            //'PRODUCT_LENGTH'      => 38,
            //'PRODUCT_WIDTH'       => 24,
            //'PRODUCT_HEIGHT'      => 25,
            'PRODUCT_TYPE'      => 'HH',
            'ORDER_PAYMENT'     => $ORDER_PAYMENT,
            'ORDER_SERVICE'     => $model->shipping_code,
            //'ORDER_SERVICE_ADD'   => '',
            'ORDER_VOUCHER'     => $model->voucher_code ? $model->voucher_code : '',
            //'ORDER_NOTE'          => 'cho xem hàng, không cho thử',
            'MONEY_COLLECTION'  => $MONEY_COLLECTION,
            'CHECK_UNIQUE'      => true,
            'LIST_ITEM'         => $listItems,
        ];
        if ($model->shipping_ward_id) $params['RECEIVER_WARD'] = $model->shipping_ward_id;
        $shipping = null;
        $exception = null;
        try {
            $res = Transport::createOrder($params);
            $data = [];
            foreach ($res as $k => $v) $data[strtolower($k)] = $v;
            $data['data'] = $res;
            $data['params'] = $params;
            $data['order_id'] = $model->id;
            $shipping = $this->order_shipping_repository->create($data);
            // Update model
            $tz = (int)$this->request->get('tz');
            $shipping_at = $this->getDateLocalFromTz($tz);
            $model = $this->model_repository->update($model, ['order_status' => 'shipping', 'shipping_status' => SHIPPING_SS_CREATE_ORDER, 'shipping_at' => $shipping_at]);
        } catch (\InvalidArgumentException $e) {
            $exception = $e;
        }
        return [$shipping, $model, $exception];
    }

    /**
     * @OA\Post(
     *   path="/backend/ord_orders/{id}/create_shipping",
     *   summary="Create Order Shipping",
     *   operationId="ordCreateOderShipping",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Order Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function createShipping($id) {
        try {
            // Check permission
            // if (!$this->isCRUD('orders', 'create')) return $this->errorForbidden();
            // Check admin
            //if (!$this->isAccessAdmin()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $shipping = $model->shipping;
            //return $this->respondWithSuccess($shipping);
            if ($shipping) return $this->errorWrongArgs('shipping.status.create_order');
            /*if (in_array($model->order_status, [ORDER_SS_COMPLETED]) && in_array($model->payment_code, [PAYMENT_MT_BANK_TRANSFER, PAYMENT_MT_COD])) {
                $tz = (int)$this->request->get('tz');
                $payment_at = $this->getDateLocalFromTz($tz);
                $model = $this->model_repository->update($model, compact('payment_status', 'payment_at'));
            }*/
            list($shipping, $model, $exception) = $this->createShippingOrder($model);
            if ($exception) return $this->respondWithError([['errorMessage' => $exception->getMessage()]]);
            // Output
            $output = $model->toArray();
            $output['shipping'] = $shipping;
            // Send mail
            if ($model->email) {
                $data = $this->getOrderProducts($model);
                $data['html'] = '';//$html;
                $data['viettel_post'] = $shipping;
                $data['config_owner'] = $this->setting_repository->findByKey('config_owner');
                dispatch(new \Modules\Order\Jobs\OrderShippingJob($this->email, $data));
            }

            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/ord_orders_create_shipping",
     *   summary="Create Shipping Orders",
     *   operationId="ordCreateShippingOrders",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="ids", type="string", example=""),
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
    public function createShippingOrders() {
        try {
            // Check permission
            if (!$this->isCRUD('orders', 'create')) return $this->errorForbidden();
            // Check admin
            //if (!$this->isAccessAdmin()) return $this->errorForbidden();
            $tmpIds = $this->request->get('ids');
            $ids = [];
            if (is_array($tmpIds)) {
                $ids = $tmpIds;
            } else if (!empty($tmpIds)) {
                foreach (explode(',', (string)$tmpIds) as $id) {
                    if (intval($id)) $ids[] = intval($id);
                }
            }
            $ids = array_unique($ids);
            if (!$ids) return $this->errorWrongArgs('order.required');
            $results = $this->model_repository->getModel()->whereIn('id', $ids)->doesntHave('shipping')->get();
            if ($results->count() != count($ids)) return $this->errorWrongArgs('order.shipping.create_order');
            $output = [];
            $errorModels = [];
            $exceptionArr = [];
            foreach ($results as $model) {
                list($shipping, $model, $exception) = $this->createShippingOrder($model);
                if ($shipping) {
                    $item = $model->toArray();
                    $item['shipping'] = $shipping;
                    $output[] = $item;
                    // Send mail
                    if ($model->email) {
                        $data = $this->getOrderProducts($model);
                        $data['html'] = '';//$html;
                        $data['viettel_post'] = $shipping;
                        $data['config_owner'] = $this->setting_repository->findByKey('config_owner');
                        dispatch(new \Modules\Order\Jobs\OrderShippingJob($this->email, $data));
                    }
                } else {
                    $errorModels[] = $model;
                }
                if ($exception) $exceptionArr[] = ['id' => $model->id, 'idx' => $model->idx, 'error' => $exception->getMessage()];
            }

            return $this->respondWithSuccess($output, ['errorModels' => $errorModels, 'exceptionArr' => $exceptionArr]);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/ord_orders_create_requests",
     *   summary="Create Stock Requests",
     *   operationId="ordCreateRequests",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="ids", type="string", example=""),
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
    public function createRequests() {
        try {
            // Check permission
            // if (!$this->isCRUD('orders', 'create')) return $this->errorForbidden();
            // Check admin
            //if (!$this->isAccessAdmin()) return $this->errorForbidden();
            $tmpIds = $this->request->get('ids');
            $ids = [];
            if (is_array($tmpIds)) {
                $ids = $tmpIds;
            } else if (!empty($tmpIds)) {
                foreach (explode(',', (string)$tmpIds) as $id) {
                    if (intval($id)) $ids[] = intval($id);
                }
            }
            $ids = array_unique($ids);
            if (!$ids) return $this->errorWrongArgs('order.required');
            $results = $this->model_repository->getModel()->whereIn('id', $ids)->whereNull('sto_request_id')->get();
            if ($results->count() != count($ids)) return $this->errorWrongArgs('order.stock.request.exists');

            $staff = $this->auth->staff ? $this->auth->staff : $this->auth;
            $department_idx = $staff->department ? $staff->department->idx : $staff->id;
            $stock = $this->sto_stock_repository->getModel()->select('id', 'idx')->orderBy('id', 'desc')->first();
            if (!$stock) return $this->respondWithError([['errorMessage' => 'Bạn chưa tạo kho!']]);
            foreach ($results as $model) {
                if (!in_array($model->order_status, [ORDER_SS_PENDING, ORDER_SS_PROCESSING])) return $this->respondWithError([['errorMessage' => 'Có đơn hàng không thuộc trạng thái <b>Chờ xác nhận</b> hoặc <b>Đang xử lý</b>']]);
                $input = [];
                $stock_idx = $stock->idx; // lấy kho có hàng gần nhất || kho mặc định
                $max_idx = $this->sto_request_repository->getModel()->select('idx', 'created_at')->orderBy('id', 'desc')->first();
                // $max_idx_compare = $max_idx ? substr($max_idx->idx, 0, 11) : null;
                $max_idx_compare = $max_idx && date('ymd', strtotime($max_idx->created_at)) == date('ymd');
                $exp_idx = $max_idx ? explode('-', $max_idx->idx) : [];
                $idx = ($max_idx_compare && !empty($exp_idx)) ? ((int)end($exp_idx) + 1) : 1;
                $input['idx'] = "RWR-$department_idx-$stock_idx-" . date('ymd') . "-$idx";

                // Products
                $tmpProducts = $model->order_products;
                list($products) = app('\Modules\Stock\Http\Controllers\Api\RequestController')->getStoProducts($tmpProducts->toArray());
                if (!$products) return $this->respondWithErrorKey('product_id.required');
                $input['type'] = 'out';
                $input['platform'] = 'website';
                $input['owner_id'] = $staff->id;
                $input['stock_id'] = $stock->id;
                $input['invoice_id'] = $model->id;
                // Create model
                $sto_request_model = $this->sto_request_repository->create($input);
                // Create ticket products
                list($total) = app('\Modules\Stock\Http\Controllers\Api\RequestController')->createProducts($products, $sto_request_model);
                $sto_request_model = $this->sto_request_repository->update($sto_request_model, ['total' => $total]);

                // Add request_id to orders
                if ($sto_request_model->platform == 'website' && $sto_request_model->invoice_id) $model->sto_request_id = $sto_request_model->id;
                if ($model->order_status == ORDER_SS_PENDING) $model->order_status = ORDER_SS_PROCESSING;

                ///// Auto approve ticket
                $tz = (int)$this->request->get('tz');
                $ticket = $this->sto_ticket_repository->filterByAttribute(['request_id' => $sto_request_model->id]);
                if (!empty($ticket)) $ticket_model = $this->sto_ticket_repository->create([
                    'type'        => 'out',
                    'idx'         => 'T-' . $sto_request_model->idx,
                    'request_id'  => $sto_request_model->id,
                    'stock_id'    => $sto_request_model->stock_id,
                    'owner_id'    => $this->auth->id,
                    'note'        => (isset($input['note']) ? $input['note'] : ''),
                    'status'      => STO_TICKET_SS_COMPLETED,
                    'reviewer_id' => $this->auth->id,
                    'approved_at' => $this->getDateLocalFromTz($tz),
                ]);
                $this->sto_request_product_repository->getModel()->where('stock_id', $sto_request_model->stock_id)->where('request_id', $sto_request_model->id)->update(['status' => 1]);
                foreach ($sto_request_model->products as $product) {
                    $this->sto_product_repository->create(['stock_id' => $sto_request_model->stock_id, 'ticket_id' => $ticket_model->id, 'product_id' => $product->product_id, 'quantity' => $product->quantity, 'price' => $product->price, 'total' => $product->total, 'shipment' => $product->shipment, 'due_date' => $product->due_date, 'code' => $product->code, 'type' => $product->type, 'status' => 1]);
                }
                $sto_request_model->approved_at = $this->getDateLocalFromTz($tz);
                $sto_request_model->reviewer_id = $this->auth->id;
                $sto_request_model->status = STO_REQUEST_SS_COMPLETED;
                $sto_request_model->out_type = STO_OUT_TYPE_SALE;
                $sto_request_model->type = 'out';
                foreach ($sto_request_model->products as $p) {
                    $stock_product = $this->sto_stock_product_repository->getModel()->where('stock_id', $sto_request_model->stock_id)->where('product_id', $p->product_id)->first();
                    if ($stock_product) {
                        $this->sto_stock_product_repository->update($stock_product, ['quantity' => $stock_product->quantity - $p->quantity]);
                    }
                    // Reduce quantity to pd__products // Đã trừ khi đặt hàng
                    // $this->product_repository->getModel()->where('id', $p->product_id)->decrement('quantity', $p->quantity);
                }

                // Handle shipping
                if ($sto_request_model->platform == 'website' && $sto_request_model->invoice_id) {
                    $this->createShipping($sto_request_model->invoice_id);
                }
                $sto_request_model->shipping_status = STO_SHIPPING_SS_WAIT_OUT;
                $sto_request_model->save();
                $model->save();

                /////////////////////////
            }

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/ord_orders/{id}/invoiced",
     *   summary="Update Order Invoiced",
     *   operationId="UpdateOrderInvoiced",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Order Id", example=1),
     *   @OA\RequestBody(),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function invoiced($id) {
        try {
            // Check permission
            if (!$this->isCRUD('orders', 'create')) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            if (!($model->is_invoice && $model->order_status == 'completed')) {
                return $this->errorWrongArgs();
            }
            $input = $this->request->only('company', 'tax_code', 'address');
            $input['invoiced'] = 1;
            // Update order
            $model = $this->model_repository->update($model, $input);
            // Create history
            $this->order_history_repository->create(['order_id' => $model->id, 'comment' => 'Đã xuất VAT']);
            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_orders/{id}/supervisor",
     *   summary="Update Product Supervisor",
     *   operationId="UpdateProductSupervisor",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Order Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="usr_id", type="integer", example=0),
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
    public function supervisor($id) {
        /*try {
            // Check admin
            if (!$this->isAccessAdmin()) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $usr_id = (int)$this->request->get('usr_id');
            $model->fill(['usr_id' => $usr_id ? $usr_id : null]);
            $model->save();
            // Create history
            $this->order_history_repository->create(['order_id' => $model->id, 'comment' => 'Cập nhật người phụ trách']);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }*/
    }

    /**
     * Save Order To File
     *
     * @param $model
     * @return array
     * @throws \Throwable
     */
    protected function saveOrderToFile($model) {
        $results = $this->order_product_repository->getModel()->where('order_id', $model->id)->get();
        $config_url = config('app.url');
        $config_email = $this->setting_repository->findByKey('config_email');

        $status = '';
        if ($model->order_status == 'completed' || $model->payment_status == 'paid') {
            $status = '<div style="font-weight: bold;color: #0acf97">ĐÃ THANH TOÁN/ <i>PAID</i></div>';
        } else if ($model->payment_status == 'in_process') {
            $status = '<div style="font-weight: bold;color: #fd7e14">ĐÃ THANH TOÁN MỘT PHẦN/ <i>PARTIAL PAID</i></div>';
        }
        $notify_footer = "<p><strong>Lưu ý/ <em>Notices</em>:</strong><br />
- Giá trên đã bao gồm VAT./ <em>The price above has included VAT Invoice.</em><br />
- {$config_url} xin chân thành cảm ơn và rất mong được hợp tác cùng Quý khách..<br />
<em>&nbsp; {$config_url} sincerely thank you and we are looking forward to cooperate with you.</em></p>";
        $data = compact('model', 'results', 'status', 'notify_footer', 'config_email');
        // Save to local
        $filepath = 'orders/' . date('Y-m') . '/' . $model->id . '.html';
        $html = (view('order::print_order', $data))->render();
        \Storage::disk('files')->getDriver()->put($filepath, $html, ['visibility' => 'public']);
        // Save to pdf
        $html = (view('order::print_order_pdf', $data))->render();
        $pdf = \PDF::loadHTML($html);
        $pdf->setOptions(['dpi' => 150]);
        $filepath = 'orders/' . date('Y-m') . '/' . $model->id . '.pdf';
        \Storage::disk('files')->getDriver()->put($filepath, $pdf->output(), ['visibility' => 'public']);
        $attach_file = storage_path('app/files/' . $filepath);

        return [$pdf, $attach_file, $data];
    }

    /**
     * Get Info For Email/Print
     *
     * @param $model
     * @return array
     */
    public function getOrderProducts($model) {
        $order_products = $model->order_products;
        $products = [];
        foreach ($order_products as $item) {
            $product = [];
            $product['id'] = $item->product_id;
            $product['name'] = $item->name;
            $product['type'] = $item->type;
            $product['quantity'] = $item->quantity;
            $product['href'] = $item->href ? $item->href : false;
            $product['thumb_url'] = $item->thumb_url ? $item->thumb_url : false;
            $product['price'] = $item->price;
            $product['priceo'] = $item->priceo != $item->price ? $item->priceo : false;
            $product['total'] = $item->total;
            $product['coins'] = $item->coins;
            $product['message'] = $item->message;
            $products[] = $product;
        }
        $addresses = $this->setting_repository->findByKey('config_address');
        $setting = [
            'email_support' => $this->setting_repository->findByKey('config_email_support'),
            'hotline'       => $this->setting_repository->findByKey('config_hotline'),
            'phone_number'  => $this->setting_repository->findByKey('config_telephone'),
            'address'       => $addresses && is_array($addresses) && isset($addresses[$this->locale]) ? $addresses[$this->locale] : '',
            'facebook_url'  => $this->setting_repository->findByKey('config_facebook_url'),
            'instagram_url' => $this->setting_repository->findByKey('config_instagram_url'),
            'zalo_url'      => $this->setting_repository->findByKey('config_zalo_url'),
            'config_owner'  => $this->setting_repository->findByKey('config_owner'),
        ];
        /*$html = $this->setting_repository->findByKey('config_email_pd_order', '', $this->locale);
        $html = str_replace('{ID}', "{$model->id}", $html);
        $html = str_replace('{CREATED_AT}', date('d/m/Y', strtotime($model->created_at)), $html);
        $html = str_replace('{PAYMENT_FULL_NAME}', "{$model->payment_info}", $html);
        $html = str_replace('{PAYMENT_EMAIL}', "{$model->payment_email}", $html);
        $html = str_replace('{PAYMENT_PHONE_NUMBER}', "{$model->payment_phone_number}", $html);
        $html = str_replace('{PAYMENT_METHOD}', "{$model->payment_method}", $html);
        $html = str_replace('{SHIPPING_FULL_NAME}', "{$model->shipping_info}", $html);
        $html = str_replace('{SHIPPING_EMAIL}', "{$model->shipping_email}", $html);
        $html = str_replace('{SHIPPING_PHONE_NUMBER}', "{$model->shipping_phone_number}", $html);
        $html = str_replace('{SHIPPING_ADDRESS}', "{$model->shipping_address}", $html);
        $html = str_replace('{SHIPPING_FEE}', number_format($model->shipping_fee, 0, ',', '.'), $html);
        $html = str_replace('{DISCOUNT_CODE}', "{$model->discount_code}", $html);
        $html = str_replace('{DISCOUNT_TOTAL}', number_format($model->discount_total, 0, ',', '.'), $html);
        $html = str_replace('{DISCOUNT_DISPLAY}', ($model->discount_total ? '' : 'none'), $html);
        $html = str_replace('{TOTAL}', number_format($model->total, 0, ',', '.'), $html);
        $html = str_replace('{EMAIL_SUPPORT}', "<a style=\"color: #B94551;text-decoration: none;\" href=\"mailto:{$setting['email_support']}\" target=\"_blank\">{$setting['email_support']}</a>", $html);
        $html = str_replace('{HOTLINE}', "<a href=\"tel:{$setting['hotline']}\" style=\"color: #B94551;text-decoration: none;\" target=\"_blank\">{$setting['hotline']}</a>", $html);
        $html = str_replace('{SETTING_ADDRESS}', "{$setting['address']}", $html);
        $html = str_replace('{SETTING_PHONE_NUMBER}', "{$setting['phone_number']}", $html);
        $html = str_replace('{SETTING_FACEBOOK}', "{$setting['facebook_url']}", $html);
        $html = str_replace('{SETTING_INSTAGRAM}', "{$setting['instagram_url']}", $html);
        $product_html = '';
        foreach ($products as $item) {
            $product_html .= "<tr>
  <td colspan=\"2\" style=\"border-collapse: collapse;border-spacing: 0;text-align: left;padding: 1px 0;\">
    <table style=\"width:100%;border-collapse: collapse;border-spacing: 0;font-size: 14px;\" border=\"0\" valign=\"top\" cellspacing=\"0\" cellpadding=\"0\">
      <tbody>
      <tr>
        <td valign=\"top\" rowspan=\"4\" style=\"border-collapse: collapse;border-spacing: 0;width: 80px;\">" . ($item->thumb_url ? "<img width=\"80\" src=\"{$item->thumb_url}\">" : '') . "</td>
        <td valign=\"top\" style=\"border-collapse: collapse;border-spacing: 0;padding-left: 5px;\">Tên sản phẩm:</td>
        <td valign=\"top\" style=\"border-collapse: collapse;border-spacing: 0;text-align: right;\">{$item->name}</td>
      </tr>
      <tr>
        <td valign=\"top\" style=\"border-collapse: collapse;border-spacing: 0;padding-left: 5px;\">Đơn giá:</td>
        <td valign=\"top\" style=\"border-collapse: collapse;border-spacing: 0;text-align: right;\">" . number_format($item->price, 0, ',', '.') . " đ</td>
      </tr>
      <tr>
        <td valign=\"top\" style=\"border-collapse: collapse;border-spacing: 0;padding-left: 5px;\">Số lượng:</td>
        <td valign=\"top\" style=\"border-collapse: collapse;border-spacing: 0;text-align: right;\">{$item->quantity}</td>
      </tr>
      </tbody>
    </table>
  </td>
</tr>";
        }*/
        $html = '';//str_replace('{PRODUCT_LIST}', $product_html, $html);

        return compact('setting', 'model', 'products', 'html');
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_orders/{id}/print",
     *   summary="Print Order",
     *   operationId="PrintOrder",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Order Id", example=1),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function getPrint($id) {
        try {
            // Disable debug
            if (config('app.debug')) app('debugbar')->disable();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            $data = $this->getOrderProducts($model);
            $products = [];
            foreach ($data['products'] as $item) {
                //if ($item['thumb_url']) $item['thumb_url'] = 'data:image/jpg;base64,' . base64_encode(file_get_contents(str_replace('https://', 'http://', $item['thumb_url'])));
                $products[] = $item;
            }
            $data['products'] = $products;
            //return $this->respondWithSuccess($data);
            //return view('order::mail/order_completed', $data);
            //return view('order::pdf/order', $data);
            // Save to pdf
            $html = (view('order::pdf/order', $data))->render();
            //echo $html; exit();
            $pdf = \PDF::loadHTML($html);
            $pdf->setOptions(['dpi' => 150]);
            //$filepath = 'orders/' . date('Ym') . '/' . $model->master_id . '.pdf';
            //\Storage::disk('files')->getDriver()->put($filepath, $pdf->output(), ['visibility' => 'public']);
            //$attach_file = storage_path('app/files/' . $filepath);
            return $pdf->stream();
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_orders_cron",
     *   summary="Get Orders Cron",
     *   operationId="getOrdersCron",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    // Will check
    public function destroyInProcessExpired() {
        try {
            $this->model_repository->getModel()->whereIn('payment_code', [PAYMENT_MT_DOMESTIC, PAYMENT_MT_FOREIGN, PAYMENT_MT_MOMO])->where('payment_status', PAYMENT_SS_INPROGRESS)->where('created_at', '<', date('Y-m-d H:i:s', strtotime('-3 day')))->delete();
            return $this->respondWithSuccess([]);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_qrcode/{filename}",
     *   summary="Get Order Qrcode",
     *   operationId="getOrderQrcode",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="filename", in="path", description="Filename", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    /*public function getQrcode($filename) {
        $filename = public_path('qrcode') . DIRECTORY_SEPARATOR . 'ord' . DIRECTORY_SEPARATOR . $filename;

        $fp = fopen($filename, "rb");

        // send the right headers
        header("Content-Type: image/png");
        header("Content-Length: " . filesize($filename));

        // dump the picture and stop the script
        fpassthru($fp);
        exit();
    }*/

    /**
     * @OA\Get(
     *   path="/backend/ord_stats",
     *   summary="Get Order Stats",
     *   operationId="getOrderStats",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="years", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Parameter(name="tz", in="query",description="TimezoneOffset", example="-420"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
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
            $tz = (int)$this->request->get('tz');
            $years = [];
            $temp = (string)$this->request->get('years');
            if ($temp) {
                foreach (explode(',', $temp) as $year) {
                    if (intval($year)) $years[] = intval($year);
                }
                $years = array_unique($years);
            }
            if (!$years) $years[] = date('Y');
            $yearsString = implode(',', $years);
            $total = $this->model_repository->getModel()->selectRaw('sum(total) as total')->whereRaw('YEAR(payment_at) IN (' . $yearsString . ')')->whereIn('status', ['completed', 'paid'])->first();
            $results = [
                'total' => $total->total,
            ];

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
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
    protected function getProductIndex($page, $pageSize, $sort = '', $order = '') {
        if (!$sort) {
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'order__products.id' : strtolower($sort);
            if ($sort == 'id') $sort = 'order__products.id';
        }
        if (!$order) {
            $order = (string)$this->request->get('order');
            $order = !$order ? 'desc' : strtoupper($order);
        }
        $queries = [
            'and'        => [
                //['order__products.user_id', '=', $this->auth->id]
                ['order__products.price', '>', 0],
            ],
            'in'         => [],
            'whereRaw'   => [],
            'orWhereRaw' => [],
        ];

        $data = $this->getRequestData();
        $start_date = (isset($data->{'start_date'}) && !is_null($data->{'start_date'}) && $data->{'start_date'} !== '') ? (string)$data->{'start_date'} : false;
        $end_date = (isset($data->{'end_date'}) && !is_null($data->{'end_date'}) && $data->{'end_date'} !== '') ? (string)$data->{'end_date'} : false;
        if ($start_date && $end_date) $queries['whereRaw'][] = ["DATE(o.created_at) >= ? and DATE(o.created_at) <= ?", [$start_date, $end_date]];
        $payment_status = trim(utf8_strtolower((isset($data->{'payment_status'}) && !is_null($data->{'payment_status'}) && $data->{'payment_status'} !== '') ? trim((string)$data->{'payment_status'}) : ''));
        if ($payment_status) $queries['and'][] = ['o.payment_status', '=', $payment_status];
        $order_status = trim(utf8_strtolower((isset($data->{'order_status'}) && !is_null($data->{'order_status'}) && $data->{'order_status'} !== '') ? trim((string)$data->{'order_status'}) : 'completed'));
        if ($order_status) $queries['and'][] = ['o.order_status', '=', $order_status];
        $name = trim(utf8_strtolower((isset($data->{'name'}) && !is_null($data->{'name'}) && $data->{'name'} !== '') ? trim((string)$data->{'name'}) : ''));
        if ($name) $queries['whereRaw'][] = ["lower(`order__products`.`name`) like ?", "%$name%"];
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
                foreach ($nos as $no) $q[] = "`order__products`.`model` like ?";
                $queries['whereRaw'][] = ['(' . implode(' or ', $q) . ')', $nos];
            }
            //$queries['whereRaw'][] = ["concat_ws('',`invoice_prefix`,`model`) like ?", [$model]];
        }
        $fields = [
            'order__products.*',
            'o.payment_at as o__payment_at',
            'o.payment_status as o__payment_status',
            'o.order_status as o__order_status',
            'pc.name as pc__name',
            \DB::raw('sum(order__products.quantity) as total_quantity'),
            \DB::raw('sum(order__products.priceo) as total_priceo'),
            \DB::raw('sum(order__products.price) as total_price'),
            \DB::raw('sum(order__products.total) as total_total'),
            \DB::raw('(sum(order__products.priceo) - sum(order__products.price)) as discount'),
            \DB::raw('round((((sum(order__products.priceo) - sum(order__products.price)) / sum(order__products.priceo)) * 100), 2) as discount_percent'),
        ];
        $is_invoice = (isset($data->{'is_invoice'}) && !is_null($data->{'is_invoice'}) && $data->{'is_invoice'} !== '') ? (int)$data->{'is_invoice'} : false;
        if ($is_invoice === 1) $queries['and'][] = ['o.is_invoice', '=', 1];
        $results = $this->setUpQueryBuilder($this->order_product_repository->getModel(), $queries, false, $fields)->with('product')
            ->leftJoin('orders as o', 'o.id', '=', 'order_id')
            ->leftJoin('pd__products as p', 'p.id', '=', 'product_id')
            ->leftJoin('pd__categories as pc', 'pc.id', '=', 'p.category_id')
            ->groupBy('order__products.product_id')
            ->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();

        return [$queries, $results];
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
    protected function getProductDetailIndex($page, $pageSize, $sort = '', $order = '') {
        if (!$sort) {
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'order__products.id' : strtolower($sort);
            if ($sort == 'id') $sort = 'order__products.id';
        }
        if (!$order) {
            $order = (string)$this->request->get('order');
            $order = !$order ? 'desc' : strtoupper($order);
        }
        $queries = [
            'and'        => [
                //['order__products.user_id', '=', $this->auth->id]
            ],
            'in'         => [],
            'whereRaw'   => [],
            'orWhereRaw' => [],
        ];

        $data = $this->getRequestData();
        $start_date = (isset($data->{'start_date'}) && !is_null($data->{'start_date'}) && $data->{'start_date'} !== '') ? (string)$data->{'start_date'} : false;
        $end_date = (isset($data->{'end_date'}) && !is_null($data->{'end_date'}) && $data->{'end_date'} !== '') ? (string)$data->{'end_date'} : false;
        if ($start_date && $end_date) $queries['whereRaw'][] = ["DATE(o.created_at) >= ? and DATE(o.created_at) <= ?", [$start_date, $end_date]];
        $payment_status = trim(utf8_strtolower((isset($data->{'payment_status'}) && !is_null($data->{'payment_status'}) && $data->{'payment_status'} !== '') ? trim((string)$data->{'payment_status'}) : ''));
        if ($payment_status) $queries['and'][] = ['o.payment_status', '=', $payment_status];
        $order_status = trim(utf8_strtolower((isset($data->{'order_status'}) && !is_null($data->{'order_status'}) && $data->{'order_status'} !== '') ? trim((string)$data->{'order_status'}) : ''));
        if ($order_status) $queries['and'][] = ['o.order_status', '=', $order_status];
        $name = trim(utf8_strtolower((isset($data->{'name'}) && !is_null($data->{'name'}) && $data->{'name'} !== '') ? trim((string)$data->{'name'}) : ''));
        if ($name) $queries['whereRaw'][] = ["lower(`order__products`.`name`) like ?", "%$name%"];
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
                foreach ($nos as $no) $q[] = "`order__products`.`model` like ?";
                $queries['whereRaw'][] = ['(' . implode(' or ', $q) . ')', $nos];
            }
            //$queries['whereRaw'][] = ["concat_ws('',`invoice_prefix`,`model`) like ?", [$model]];
        }
        $fields = [
            'order__products.*',
        ];
        $is_invoice = (isset($data->{'is_invoice'}) && !is_null($data->{'is_invoice'}) && $data->{'is_invoice'} !== '') ? (int)$data->{'is_invoice'} : false;
        if ($is_invoice === 1) $queries['and'][] = ['o.is_invoice', '=', 1];
        $results = $this->setUpQueryBuilder($this->order_product_repository->getModel(), $queries, false, $fields)->with('product','shipping','order.user')
            ->leftJoin('orders as o', 'o.id', '=', 'order_id')
            ->leftJoin('pd__products as p', 'p.id', '=', 'product_id')
            ->leftJoin('pd__categories as pc', 'pc.id', '=', 'p.category_id')
            //->groupBy('order__products.product_id')
            ->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();

        $output = [];
        foreach ($results as $result) {
            $output[] = $result->toArray();
            // Add row product in combo
            $pd_incombo = \DB::table('pd__product_incombo')->where('product_id', $result->product_id)->get();
            if ($pd_incombo->isNotEmpty()) {
                $quantity = $result->quantity;
                unset($result->product_id);
                unset($result->name);
                unset($result->model);
                unset($result->quantity);
                unset($result->priceo);
                unset($result->price);
                unset($result->total);
                unset($result->product);
                foreach ($pd_incombo as $item) {
                    $product = $this->product_repository->find($item->incombo_id);
                    $output[] = array_merge($result->toArray(), [
                        'product_id' => $product->id,
                        'name'       => $product->name,
                        'model'      => $product->model,
                        'quantity'   => $item->quantity * $quantity,
                        'priceo'     => 0,
                        'price'      => 0,
                        'total'      => 0,
                        'product'    => $product->toArray(),
                    ]);
                }
            }
        }

        return [$queries, $output];
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_orders_products",
     *   summary="Get Orders Products",
     *   operationId="getOrdersProducts",
     *   tags={"BackendOrdOrders"},
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
    public function indexProductOrder() {
        try {
            // Check permission
            if (!$this->isCRUD('orders', 'view')) return $this->errorForbidden();
            $page = (int)$this->request->get('page');
            if (!$page) $page = 1;
            $pageSize = (int)$this->request->get('pageSize');
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            list($queries, $results) = $this->getProductIndex($page, $pageSize);
            $output = [];
            if ($results) {
                foreach ($results as $item) {
                    $newItem = $this->parseToRespond($item->toArray());
                    $newItem['order'] = new \Modules\Order\Entities\Order($newItem['o']);
                    unset($newItem['o']);
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
                $query = $this->setUpQueryBuilder($this->order_product_repository->getModel(), $queries, true)
                    ->leftJoin('orders as o', 'o.id', '=', 'order_id')
                    ->leftJoin('pd__products as p', 'p.id', '=', 'product_id')
                    ->leftJoin('pd__categories as pc', 'pc.id', '=', 'p.category_id')
                    ->groupBy('order__products.product_id')->get();
                $totalCount = $query->count();
                return $this->respondWithPaging($output, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_orders_exports",
     *   summary="Get Orders Exports",
     *   operationId="getOrdersExports",
     *   tags={"BackendOrdOrders"},
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
        list($queries, $results) = $this->getIndex(1, 10000);
        $fields = ['id', 'no', 'sub_total', 'discount_total', 'shipping_fee', 'total', 'created_at', 'customer', 'customer_no', 'customer_email', 'customer_phone', 'customer_address', 'payment_status', 'shipping_status', 'order_status', 'payment_at', 'payment_method'];
        //=== Get rows
        $rows = [];
        $max = 0;
        foreach ($results as $k => $result) {
            $item = $result->toArray();
            $newItem = $this->parseToRespond($item);
            $item['discount_total'] = $result->discount_total + $result->voucher_total;
            $item['created_at'] = $item['created_at'] ? date('d/m/Y', strtotime($item['created_at'])) : '';
            /*if ($result->u__id) {
                $user = new \Modules\User\Entities\Sentinel\User($newItem['u']);
                $item['customer'] = $user->display;
            }*/
            $item['customer'] = $item['shipping_first_name'];
            $item['customer_email'] = $item['email'];
            $item['customer_phone'] = $item['phone_number'];
            $item['customer_address'] = $item['shipping_address_1'] . ', ' . $item['shipping_ward'] . ', ' . $item['shipping_district'] . ', ' . $item['shipping_province'];
            if ($result->user) {
                $item['customer_no'] = $result->user->no;
                //$item['customer_email'] = $result->user->email;
            }
            $item['payment_status'] = !empty($item['payment_status_name']) ? $item['payment_status_name'] : '';
            $item['shipping_status'] = !empty($item['shipping_status_name']) ? $item['shipping_status_name'] : '';
            $item['order_status'] = !empty($item['order_status_name']) ? $item['order_status_name'] : '';
            $item['payment_at'] = $item['payment_at'] ? date('d/m/Y', strtotime($item['payment_at'])) : '';
            $max = max($max, $result->order_products->count());
            foreach ($result->order_products as $index => $product) {
                $product_info = $product->product;
                if (!in_array("product_model$index", $fields)) $fields[] = "product_model$index";
                if (!in_array("product_quantity$index", $fields)) $fields[] = "product_quantity$index";
                if (!in_array("product_name$index", $fields)) $fields[] = "product_name$index";

                $model = isset($product_info->model) ? $product_info->model : '';
                $quantity = $product->quantity;
                $productName = $product->p__name ? $product->p__name : $product->name;
                $item["product_model$index"] = $model;
                $item["product_quantity$index"] = $quantity;
                $item["product_name$index"] = $productName;
            }
            $row = [$k + 1];
            foreach ($fields as $field) {
                $row[] = isset($item[$field]) ? $item[$field] : '';
            }
            $rows[] = $row;
        }

        return Excel::download(new \Modules\Order\Exports\OrderExport($rows, $max), 'orders-' . date('Y-m-d') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * Get Index
     *
     * @param $page
     * @param $pageSize
     * @param string $sort
     * @param string $order
     * @return array
     */
    protected function getIndexDetail($page, $pageSize, $sort = '', $order = '') {
        if (!$sort) {
            $sort = (string)$this->request->get('sort');
            $sort = !$sort ? 'orders.id' : strtolower($sort);
            if ($sort == 'id') $sort = 'orders.id';
        }
        if (!$order) {
            $order = (string)$this->request->get('order');
            $order = !$order ? 'desc' : strtoupper($order);
        }
        $queries = [
            'and'        => [
                //['orders.user_id', '=', $this->auth->id]
            ],
            'in'         => [],
            'whereRaw'   => [],
            'orWhereRaw' => [],
        ];
        $data = $this->getRequestData();
        $start_date = (isset($data->{'start_date'}) && !is_null($data->{'start_date'}) && $data->{'start_date'} !== '') ? (string)$data->{'start_date'} : false;
        $end_date = (isset($data->{'end_date'}) && !is_null($data->{'end_date'}) && $data->{'end_date'} !== '') ? (string)$data->{'end_date'} : false;
        if ($start_date && $end_date) $queries['whereRaw'][] = ["DATE(orders.created_at) >= ? and DATE(orders.created_at) <= ?", [$start_date, $end_date]];
        /*$user_id = (isset($data->{'user_id'}) && !is_null($data->{'user_id'}) && $data->{'user_id'} !== '') ? (int)$data->{'user_id'} : false;
        if ($user_id) $queries['and'][] = ['orders.user_id', '=', $user_id];*/
        $payment_status = trim(utf8_strtolower((isset($data->{'payment_status'}) && !is_null($data->{'payment_status'}) && $data->{'payment_status'} !== '') ? trim((string)$data->{'payment_status'}) : ''));
        if ($payment_status) $queries['and'][] = ['orders.payment_status', '=', $payment_status];
        $payment_code = trim(utf8_strtolower((isset($data->{'payment_code'}) && !is_null($data->{'payment_code'}) && $data->{'payment_code'} !== '') ? trim((string)$data->{'payment_code'}) : ''));
        if ($payment_code) $queries['in'][] = ['orders.payment_code', explode(',', $payment_code)];
        $shipping_status = trim(utf8_strtolower((isset($data->{'shipping_status'}) && !is_null($data->{'shipping_status'}) && $data->{'shipping_status'} !== '') ? trim((string)$data->{'shipping_status'}) : ''));
        if ($shipping_status) $queries['and'][] = ['orders.shipping_status', '=', $shipping_status];
        $order_status = trim(utf8_strtolower((isset($data->{'order_status'}) && !is_null($data->{'order_status'}) && $data->{'order_status'} !== '') ? trim((string)$data->{'order_status'}) : ''));
        if ($order_status) $queries['and'][] = ['orders.order_status', '=', $order_status];
        /*$order_status = (isset($data->{'order_status'}) && !is_null($data->{'order_status'}) && $data->{'order_status'} !== '') ? trim((string)$data->{'order_status'}) : '';
        if ($order_status) {
            $statuses = [];
            foreach (explode(',', $order_status) as $item) {
                if (trim($item)) $statuses[] = trim($item);
            }
            if ($statuses) $queries['in'][] = ['orders.order_status', array_merge($statuses)];
        }*/
        // Query by keyword
        /*$q = (isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : '';
        if ($q) {
            $arrQ = $this->parseToArray(trim(utf8_strtolower($q)));
            $keys = ["`u`.`first_name`"];
            foreach ($keys as $key) {
                $iQ = [];
                $iB = [];
                foreach ($arrQ as $i) {
                    $iQ[] = "lower($key) like ?";
                    $iB[] = "%$i%";
                }
                $queries['orWhereRaw'][] = ['(' . implode(' and ', $iQ) . ')', $iB];
            }
        }*/
        $q = trim(utf8_strtolower((isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : ''));
        if ($q) $queries['whereRaw'][] = ["lower(`u`.`first_name`) like ?", "%$q%"];
        $invoice_no = (isset($data->{'invoice_no'}) && !is_null($data->{'invoice_no'}) && $data->{'invoice_no'} !== '') ? trim((string)$data->{'invoice_no'}) : '';
        if ($invoice_no) {
            $invoice_no = str_replace([" ", "\r\n", "\r"], ",", trim($invoice_no));
            $tmp = explode(',', $invoice_no);
            $nos = [];
            foreach ($tmp as $t) {
                if (trim($t)) $nos[] = trim($t);
                /*$e = explode('-', $t);
                if (count($e) == 2) {
                    $invoice_prefix = $e[0] . '-';
                    $invoice_no = trim(ltrim($e[1], '0'));
                    $nos[] = $invoice_prefix . $invoice_no;
                }*/
            }
            if ($nos) {
                $q = [];
                //foreach ($nos as $no) $q[] = "concat_ws('',`invoice_prefix`,`invoice_no`) like ?";
                foreach ($nos as $no) $q[] = "`idx` like ?";
                $queries['whereRaw'][] = ['(' . implode(' or ', $q) . ')', $nos];
            }
            //$queries['whereRaw'][] = ["concat_ws('',`invoice_prefix`,`invoice_no`) like ?", [$invoice_no]];
        }
        $fields = [
            'orders.*',
            'op.product_id as op_product_id',
            'op.name as op_name',
            'op.quantity as op_quantity',
            'op.priceo as op_priceo',
            'op.price as op_price',
            'op.total as op_total',
            'op.coins as op_coins',
            'p.model as p_model',
            'p.weight as p_weight',
        ];
        $is_invoice = (isset($data->{'is_invoice'}) && !is_null($data->{'is_invoice'}) && $data->{'is_invoice'} !== '') ? (int)$data->{'is_invoice'} : false;
        if ($is_invoice === 1) $queries['and'][] = ['orders.is_invoice', '=', 1];
        $results = $this->setUpQueryBuilder($this->model_repository->getModel(), $queries, false, $fields)
            ->leftJoin('order__products as op', 'orders.id', '=', 'op.order_id')
            ->leftJoin('pd__products as p', 'p.id', '=', 'op.product_id')
            ->leftJoin('users as u', 'u.id', '=', 'user_id')
            ->with('user', 'shipping')
            ->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();

        $output = [];
        foreach ($results as $result) {
            unset($result->products);
            $output[] = $result->toArray();
            // Add row product in combo
            $pd_incombo = \DB::table('pd__product_incombo')->where('product_id', $result->op_product_id)->get();
            if ($pd_incombo->isNotEmpty()) {
                $quantity = $result->op_quantity;
                unset($result->op_name);
                unset($result->op_priceo);
                unset($result->op_price);
                unset($result->op_total);
                unset($result->p_model);
                unset($result->p_weight);
                foreach ($pd_incombo as $item) {
                    $product = $this->product_repository->find($item->incombo_id);
                    $output[] = array_merge($result->toArray(), [
                        'op_name'     => $product->name,
                        'op_priceo'   => 0,
                        'op_price'    => 0,
                        'op_total'    => 0,
                        'p_model'     => $product->model,
                        'p_weight'    => $product->weight,
                        'op_quantity' => $item->quantity * $quantity,
                    ]);
                }
            }
        }

        return [$queries, $output];
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_orders_exports_details",
     *   summary="Get Orders Export Details",
     *   operationId="getOrdersExportDetails",
     *   tags={"BackendOrdOrders"},
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
    public function exportExcelDetail() {
        list($queries, $results) = $this->getIndexDetail(1, 10000);
        $fields = [
            'no', 'created_at', 'order_status_name', /*'reason',*/ 'shipping__order_number', 'shipping__brand', 'shipping_method', 'shipping__created_at', 'p_model',
            'op_name', 'p_weight', 'op_priceo', 'op_price', 'op_quantity', 'op_total', 'total', 'discount_total', 'coins', 'shipping_fee', 'shipping_pay', 'shipping_discount',
            'updated_at', 'payment_at', 'payment_method', 'user__display', 'shipping_first_name', 'shipping_phone_number', 'shipping_province',
            'shipping_district', 'shipping_ward', 'shipping_address_1', 'note', 'vat', 'company', 'company_tax', 'company_address', 'company_email',
        ];

        //=== Get rows
        $rows = [];
        foreach ($results as $k => $result) {
            $item = is_object($result) ? $result->toArray() : $result;

            if (isset($item['shipping']['created_at'])) $item['shipping']['created_at'] = date('d/m/Y', strtotime($item['shipping']['created_at']));
            if (isset($item['created_at'])) $item['created_at'] = date('d/m/Y', strtotime($item['created_at']));
            if (isset($item['updated_at'])) $item['updated_at'] = date('d/m/Y', strtotime($item['updated_at']));
            if (isset($item['payment_at'])) $item['payment_at'] = date('d/m/Y', strtotime($item['payment_at']));
            if (isset($item['discount_total'])) $item['discount_total'] = $item['discount_total'] ? $item['discount_total'] : $item['voucher_total'];
            $item['shipping_pay'] = (isset($item['shipping_fee']) ? $item['shipping_fee'] : 0) - (isset($item['shipping_discount']) ? $item['shipping_discount'] : 0);
//            if (isset($item['order']['discount_total'])) $item['order']['discount_total'] = $item['order']['discount_total'] ? $item['order']['discount_total'] : $item['order']['voucher_total'];
            $item['shipping']['brand'] = 'ViettelPost';

            $row = [$k + 1];
            foreach ($fields as $field) {
                $key = '';
                $value = '';
                if ((strpos($field, "__") > 0)) {
                    $newFields = explode('__', $field);
                    $key = $newFields[0];
                    $value = $newFields[1];
                }

                if ($key && isset($item[$key])) {
                    $row[] = isset($item[$key][$value]) ? $item[$key][$value] : '';
                } else {
                    $row[] = isset($item[$field]) ? $item[$field] : '';
                }
            }
            $rows[] = $row;
        }

        return Excel::download(new \Modules\Order\Exports\OrderProductDetailExport($rows), 'order-products-detail' . date('Y-m-d') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_orders_products_exports",
     *   summary="Get Orders Export Products",
     *   operationId="getOrdersExportProducts",
     *   tags={"BackendOrdOrders"},
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
    public function exportExcelProduct() {
        list($queries, $results) = $this->getProductIndex(1, 10000);
        $fields = ['model', 'name', 'category_name', 'total_quantity', 'discount_percent', 'quantity_return', 'total_return', 'discount', 'total_total'];
        //=== Get rows
        $rows = [];
        foreach ($results as $k => $result) {
            $item = $result->toArray();
            $newItem = $this->parseToRespond($item);
            if ($result->pc__name) {
                $category = new  \Modules\Product\Entities\Category($newItem['pc']);
                $item['category_name'] = $category->name;
            }
            if ($item['product']) {
                $item['model'] = $item['product']['model'];
                $item['name'] = $item['product']['name'];
            }
            $row = [$k + 1];
            foreach ($fields as $field) {
                $row[] = isset($item[$field]) ? $item[$field] : '';
            }
            $rows[] = $row;
        }

        return Excel::download(new \Modules\Order\Exports\OrderProductExport($rows), 'order-products-' . date('Y-m-d') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_orders_products_export_details",
     *   summary="Get Orders Export Products Detail",
     *   operationId="getOrdersExportProductsDetail",
     *   tags={"BackendOrdOrders"},
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
    public function exportExcelProductDetail() {
        list($queries, $results) = $this->getProductDetailIndex(1, 10000);
        $fields = [
            'order__no', 'order__created_at', 'order__order_status_name', /*'order__reason',*/ 'shipping__order_number', 'shipping__brand', 'order__shipping_method', 'shipping__created_at', 'product__model',
            'product__name', 'product__weight', 'priceo', 'price', 'quantity', 'total', 'order__total', 'order__discount_total', 'order__coins', 'order__shipping_fee', 'order__shipping_pay', 'order__shipping_discount',
            'order__updated_at', 'order__payment_at', 'order__payment_method', 'user__display', 'order__shipping_first_name', 'order__shipping_phone_number', 'order__shipping_province',
            'order__shipping_district', 'order__shipping_ward', 'order__shipping_address_1', 'order__note', 'order__vat', 'order__company', 'order__company_tax', 'order__company_address', 'order__company_email',
        ];

        //=== Get rows
        $rows = [];
        foreach ($results as $k => $result) {
            $item = is_object($result) ? $result->toArray() : $result;
            if (isset($item['order']['user'])) {
                $item['user'] = $item['order']['user'];
                unset($item['order']['user']);
            }

            if (isset($item['order']['created_at'])) $item['order']['created_at'] = date('d/m/Y', strtotime($item['order']['created_at']));
            if (isset($item['shipping']['created_at'])) $item['shipping']['created_at'] = date('d/m/Y', strtotime($item['shipping']['created_at']));
            if (isset($item['order']['discount_total'])) $item['order']['discount_total'] = $item['order']['discount_total'] ? $item['order']['discount_total'] : $item['order']['voucher_total'];
            if (isset($item['order']['updated_at'])) $item['order']['updated_at'] = date('d/m/Y', strtotime($item['order']['updated_at']));
            if (isset($item['order']['payment_at'])) $item['order']['payment_at'] = date('d/m/Y', strtotime($item['order']['payment_at']));
            if (isset($item['order']['vat'])) $item['order']['vat'] = $item['order']['vat'] ? 'Có' : 'Không';
            $item['order']['shipping_pay'] = (isset($item['order']['shipping_fee']) ? $item['order']['shipping_fee'] : 0) - (isset($item['order']['shipping_discount']) ? $item['order']['shipping_discount'] : 0);
            $item['shipping']['brand'] = 'ViettelPost';

            $row = [$k + 1];
            foreach ($fields as $field) {
                $key = '';
                $value = '';
                if ((strpos($field, "__") > 0)) {
                    $newFields = explode('__', $field);
                    $key = $newFields[0];
                    $value = $newFields[1];
                }

                if ($key && isset($item[$key])) {
                    $row[] = isset($item[$key][$value]) ? $item[$key][$value] : '';
                } else {
                    $row[] = isset($item[$field]) ? $item[$field] : '';
                }
            }
            $rows[] = $row;
        }

        return Excel::download(new \Modules\Order\Exports\OrderProductDetailExport($rows), 'order-products-detail' . date('Y-m-d') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * @OA\Get(
     *   path="/backend/ord_orders_get_shipping_fee",
     *   summary="Get Order Shipping Fee",
     *   operationId="getOrderShippingFee",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Order Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="shipping_province_id", type="integer", example=2),
     *       @OA\Property(property="shipping_district_id", type="integer", example=0),
     *       @OA\Property(property="shipping_ward_id", type="integer", example=0),
     *       @OA\Property(property="shipping_address_1", type="string", example=""),
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
    public function getShippingFee() {
        try {
            $input = $this->request->only(['shipping_province_id', 'shipping_district_id', 'shipping_ward_id']);

            $order_products = $this->request->get('products');

            if (!$order_products) return $this->respondWithErrorKey('product_id.required');

            $tz = (int)$this->request->get('tz');
            $dateNow = $this->getDateLocalFromTz($tz);

            if ($input['shipping_province_id']) {
                $province_id = $input['shipping_province_id'];
                $shipping_province = Province::where('vt_id', $province_id)->first();
                $input['shipping_province'] = $shipping_province->name;
            }
            if ($input['shipping_district_id']) {
                $district_id = $input['shipping_district_id'];
                $shipping_district = District::where('vt_id', $district_id)->first();
                $input['shipping_district'] = $shipping_district->name;
            }
            if ($input['shipping_ward_id']) {
                $ward_id = $input['shipping_ward_id'];
                $shipping_ward = Ward::where('vt_id', $ward_id)->first();
                $input['shipping_ward'] = $shipping_ward->name;
            }

            // shipping_code
            $weight = 0;
            if (isset($province_id) && isset($district_id) && isset($ward_id)) {
                $products = json_decode($order_products, true);
                foreach ($products as $product) {
                    $gift_set_id = 0;
                    if (isset($product['master_id']) && $product['master_id'] && !$product['gift_set_id']) {
                        // Gift products
                        $master_pd = Product::where('id', $product['master_id'])
                            ->select(['gift_set_id'])->first();
                        if ($master_pd) $gift_set_id = $master_pd->gift_set_id;
                    }
                    if ($product['gift_set_id']) $gift_set_id = $product['gift_set_id'];
                    $gifts = [];
                    $weight_gifts = 0;
                    if ($gift_set_id) {
                        $tmp = Product::rightJoin('pd__gift_set_products as gsp', 'gsp.product_id', 'pd__products.id')->leftJoin('pd__gift_sets as gs', 'gs.id', 'gsp.gift_set_id')
                            ->where('pd__products.status', 1)->where('gsp.gift_set_id', intval($gift_set_id))
                            ->where('gs.status', 1)->whereRaw("((`gs`.`start_date` is null or DATE(`gs`.`start_date`) <= DATE('$dateNow')) and (`gs`.`end_date` is null or DATE('$dateNow') <= DATE(`gs`.`end_date`)))")
                            ->select(['pd__products.id', 'pd__products.image', 'pd__products.alias', 'pd__products.name', 'gsp.name as long_name', 'gsp.price', \DB::raw('sum(gsp.quantity *' . $product['quantity'] .') as quantity'), \DB::raw('sum(pd__products.weight * gsp.quantity * ' . $product['quantity'] .') as weight')])
                            ->groupBy('pd__products.id')
                            ->orderBy('gsp.name', 'asc')->get();
                        $gifts = $tmp->toArray();
                        if ($gifts) $weight_gifts = array_sum(array_column($gifts, 'weight'));
                    }
                    $weight += $product['weight'] * $product['quantity'] + $weight_gifts;
                }

                try {
                    $params = [
                        'SENDER_PROVINCE'   => (int)$this->setting_repository->findByKey('config_ord_province', 0),
                        'SENDER_DISTRICT'   => (int)$this->setting_repository->findByKey('config_ord_district', 0),
                        'SENDER_WARD'       => (int)$this->setting_repository->findByKey('config_ord_ward', 0),
                        'RECEIVER_PROVINCE' => $province_id,
                        'RECEIVER_DISTRICT' => $district_id,
                        'RECEIVER_WARD'     => $ward_id,
                        'PRODUCT_WEIGHT'    => $weight,
                        //'PRODUCT_PRICE'     => $model->total,
                        //'MONEY_COLLECTION'  => $model->total,
                    ];

                    $res = Transport::getPriceAll($params);
                    $shipping_code = $res[0]['MA_DV_CHINH'];
                    $input['shipping_code'] = $shipping_code;
                } catch (\InvalidArgumentException $e) {
                    return $this->errorInternalError($e->getMessage());
                }

                $user_id = $this->request->get('user_id');
                $user = null;
                if ($user_id) {
                    $user = $this->user_repository->find($user_id);
                }

                if ($shipping_code) {
                    $input['shipping_fee'] = $res[0]['GIA_CUOC'];
                    $shipping_discount = 0;
                    if ($user){
                        // Shipping discount
                        $shipping_discount = 0;
                        $user_point = $user->points;
                        $ranks = $this->user_rank_repository->getModel()->where('status', 1)->orderBy('rank', 'asc')->get();
                        $user_rank = 0;
                        foreach ($ranks as $rank) if ($user_point >= $rank->value) $user_rank = $rank->rank;
                        // 2 : SILVER MEMBER, 3 : GOLD MEMBER, 4 : DIAMOND MEMBER
                        if ($user_rank === 2) $shipping_discount = min($res[0]['GIA_CUOC'], 10000);
                        else if ($user_rank === 3) $shipping_discount = min($res[0]['GIA_CUOC'], 20000);
                        else if ($user_rank === 4) $shipping_discount = $shipping_code !== 'VHT' ? $res[0]['GIA_CUOC'] : 0;

                        $input['shipping_discount'] = $shipping_discount;
                    }
                    $input['shipping_total'] = $input['shipping_fee'] - $shipping_discount;
                }

            }

            $output = $input;

            return $this->respondWithSuccess($output);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/backend/ord_orders/{id}/address",
     *   summary="Update Order Address",
     *   operationId="updateOrderAddress",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Order Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="shipping_province_id", type="integer", example=2),
     *       @OA\Property(property="shipping_district_id", type="integer", example=0),
     *       @OA\Property(property="shipping_ward_id", type="integer", example=0),
     *       @OA\Property(property="shipping_address_1", type="string", example=""),
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
    public function updateAddress($id) {
        try {
            // Check permission
            if (!$this->isCRUD('orders', 'edit')) return $this->errorForbidden();
            $input = $this->request->only(['shipping_province_id', 'shipping_district_id', 'shipping_ward_id', 'shipping_address_1']);

            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();

            if ($input['shipping_province_id']) {
                $province_id = $input['shipping_province_id'];
                $shipping_province = Province::where('vt_id', $province_id)->first();
                $input['shipping_province'] = $shipping_province->name;
            }
            if ($input['shipping_district_id']) {
                $district_id = $input['shipping_district_id'];
                $shipping_district = District::where('vt_id', $district_id)->first();
                $input['shipping_district'] = $shipping_district->name;
            }
            if ($input['shipping_ward_id']) {
                $ward_id = $input['shipping_ward_id'];
                $shipping_ward = Ward::where('vt_id', $ward_id)->first();
                $input['shipping_ward'] = $shipping_ward->name;
            }

            // shipping_code
            $weight = 0;
            if (isset($province_id) && isset($district_id) && isset($ward_id)) {
                $order_products = $model->order_products;
                foreach ($order_products as $product) {
                    $weight += $product->weight * $product->quantity;
                }

                try {
                    $params = [
                        'SENDER_PROVINCE'   => (int)$this->setting_repository->findByKey('config_ord_province', 0),
                        'SENDER_DISTRICT'   => (int)$this->setting_repository->findByKey('config_ord_district', 0),
                        'SENDER_WARD'       => (int)$this->setting_repository->findByKey('config_ord_ward', 0),
                        'RECEIVER_PROVINCE' => $province_id,
                        'RECEIVER_DISTRICT' => $district_id,
                        'RECEIVER_WARD'     => $ward_id,
                        'PRODUCT_WEIGHT'    => $weight,
                        'PRODUCT_PRICE'     => $model->total,
                        'MONEY_COLLECTION'  => $model->total,
                    ];

                    $res = Transport::getPriceAll($params);
                    $shipping_code = $res[0]['MA_DV_CHINH'];
                    $input['shipping_code'] = $shipping_code;
                } catch (\InvalidArgumentException $e) {
                    return $this->errorInternalError($e->getMessage());
                }

                $user = $model->user ? $model->user : null;
                if ($shipping_code) {
                    list($res, $shipping_discount) = app('\Modules\Order\Http\Controllers\ApiPublic\CartController')->handleShippingFee($province_id, $district_id, $ward_id, $shipping_code, $weight, $model->total, $user->id);

                    $total = 0;
                    $total += $model->sub_total;
                    $total -= $model->discount_total;
                    $total -= $model->voucher_total;
                    $total = max(0, $total);
                    if ($res) {
                        $input['shipping_fee'] = $res['MONEY_TOTAL'];
                        $input['shipping_discount'] = $shipping_discount;
                        $shipping_total = $input['shipping_fee'] - $shipping_discount;
                        $total += $shipping_total;
                    }
                    $input['total'] = $total;
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
     * @OA\Put(
     *   path="/backend/ord_orders/{id}",
     *   summary="Update Order",
     *   operationId="updateOrder",
     *   tags={"BackendOrdOrders"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Order Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="shipping_province_id", type="integer", example=2),
     *       @OA\Property(property="shipping_district_id", type="integer", example=0),
     *       @OA\Property(property="shipping_ward_id", type="integer", example=0),
     *       @OA\Property(property="shipping_address_1", type="string", example=""),
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
    public function update($id) {
        try {
            // Check permission
            if (!$this->isCRUD('orders', 'edit')) return $this->errorForbidden();
            $input = $this->request->only(['shipping_first_name', 'shipping_phone_number']);

            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();

            // Update Model
            $model = $this->model_repository->update($model, $input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    public function storePos() {
        try {
            // Check permission
            if (!$this->isCRUD('orders', 'create')) return $this->errorForbidden();
            $tz = (int)$this->request->get('tz');
            $dateNow = $this->getDateLocalFromTz($tz);
            $input = $this->request->only(['first_name', 'email', 'phone_number', 'payment_code', 'company', 'company_tax', 'company_email', 'company_address', 'note']);
            // return $this->respondWithError($input);
            $validatorErrors = $this->getValidator($input, [
                // 'first_name'   => 'required',
                // 'payment_code' => 'required|in:' . PAYMENT_MT_COD . ',' . PAYMENT_MT_BANK_TRANSFER, // PAYMENT_MT_CASH
            ]);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            
            $user_id = (int)$this->request->get('user_id');
            $user = false;
            if ($user_id) {
                $user = $this->user_repository->find($user_id);
                if ($user) {
                    $input['user_id'] = $user->id;
                    $input['user_group_id'] = $user->user_group_id;
                    if (empty($input['first_name'])) {
                        $input['first_name'] = $user->display;
                    } else {
                        $input['first_name'] = 'Khách lẻ';
                    }
                    if (empty($input['email'])) $input['email'] = $user->email;
                    if (empty($input['phone_number'])) $input['phone_number'] = $user->phone_number;
                }
            }
            if (!$user && !empty($input['email'])) {
                $user = $this->user_repository->findByAttributes(['email' => $input['email']]);
                if ($user) {
                    $input['user_id'] = $user->id;
                    $input['user_group_id'] = $user->user_group_id;
                }
            }
            
            // $payment_code = $this->request->get('payment_code');
            $payment_code = PAYMENT_MT_COD;
            $input['payment_code'] = $payment_code;
            $input['payment_method'] = Lang::get("transaction.payment_method.$payment_code");

            $input['shipping_first_name'] = $this->request->get('shipping_first_name') ? $this->request->get('shipping_first_name') : $this->auth->display;
            $input['shipping_phone_number'] = $this->request->get('shipping_phone_number') ? $this->request->get('shipping_phone_number') : $this->auth->phone_number;
            $input['shipping_address_1'] = $this->request->get('shipping_address_1');
            $input['shipping_province'] = $this->request->get('shipping_province');
            $input['shipping_province_id'] = $this->request->get('shipping_province_id');
            $input['shipping_district'] = $this->request->get('shipping_district');
            $input['shipping_district_id'] = $this->request->get('shipping_district_id');
            $input['shipping_ward'] = $this->request->get('shipping_ward');
            $input['shipping_ward_id'] = $this->request->get('shipping_ward_id');
            $input['shipping_code'] = $this->request->get('shipping_code');

            $shipping_fee = $this->request->get('shipping_fee');
            $input['shipping_fee'] = $shipping_fee;
            $shipping_discount = $this->request->get('shipping_discount') ? $this->request->get('shipping_discount') : 0;
            $input['shipping_discount'] = $shipping_discount;
            $input['vat'] = 0;
            $is_invoice = $this->request->get('is_invoice');
            $is_invoice = (is_null($is_invoice) || $is_invoice === 'false') ? false : ($is_invoice === 'true' ? true : (boolean)$is_invoice);
            if ($is_invoice) $input['is_invoice'] = $is_invoice;
            // Products
            $tmpProducts = (array)$this->request->get('products');
            $products = [];
            if (!empty($tmpProducts)) {
                foreach ($tmpProducts as $item) {
                    $validatorErrors = $this->getValidator($item, [
                        'id'       => 'required|integer',
                        'quantity' => 'required|integer|min:1',
                    ]);
                    if (empty($validatorErrors)) {
                        if (intval($item['id'])) {
                            $product = $this->product_repository->getModel()->where('id', $item['id'])->select(['*', \DB::raw("(select price from pd__product_specials ps where ps.product_id = pd__products.id and ((ps.start_date is null or UNIX_TIMESTAMP(ps.start_date) <= UNIX_TIMESTAMP('$dateNow')) and (ps.end_date is null or UNIX_TIMESTAMP('$dateNow') <= UNIX_TIMESTAMP(ps.end_date))) order by ps.priority asc, price asc limit 1) as special")])->first();
                            if ($product) {
                                $newProduct = new \stdClass();
                                $newProduct->quantity = $item['quantity'];
                                foreach (['id', 'name', 'model', 'price', 'special', 'master_id', 'gift_set_id'] as $fieldName) {
                                    $newProduct->{$fieldName} = $product->{$fieldName};
                                }

                                $gift_set_id = 0;
                                if (isset($newProduct->master_id) && $newProduct->master_id && !$newProduct->gift_set_id) {
                                    // Gift products
                                    $master_pd = Product::where('id', $newProduct->master_id)->select(['gift_set_id'])->first();
                                    if ($master_pd) $gift_set_id = $master_pd->gift_set_id;
                                }
                                if ($newProduct->gift_set_id) $gift_set_id = $newProduct->gift_set_id;
                                if ($gift_set_id) {
                                    $gift_set = GiftSet::where('id', $gift_set_id)->first();
                                    if ($gift_set) $newProduct->gifts = $gift_set->products;
                                }
                                $products[] = $newProduct;
                            }
                        }
                    }
                }
            }
            if (!$products) return $this->respondWithErrorKey('product_id.required');
            //$no_cod = in_array(true, array_column($products, 'no_cod'));
            //if ($no_cod && $payment_code === PAYMENT_MT_COD) return $this->errorWrongArgs('order.payment.cod');
            // Totals
            $input['totals'] = [];
            $total = 0;

            $sub_total = 0;
            foreach ($products as $product) {
                $sub_total += ($product->special ? $product->special : $product->price) * $product->quantity;
            }
            $total += $sub_total;

            // Shipping fee
            $total = $total + ((int)$input['shipping_fee'] - (int)$input['shipping_discount']);

            // Coupon
            $coupon_total = 0;

            $input['totals']['sub_total'] = $sub_total;
            $input['totals']['coupon'] = $coupon_total;
            $input['totals']['total'] = max(0, $total);

            $input['sub_total'] = $sub_total;
            $input['total'] = $total;
            $input['coins'] = $total >= 1000 ? round($total / 1000) : 0;
            $input['lang'] = $this->locale;
            $input['currency_code'] = $this->getCurrency();

            $input['invoice_prefix'] = 'POS-';
            $invoice_no = $this->model_repository->getModel()->selectRaw('max(invoice_no) as invoice_no')->where('invoice_prefix', $input['invoice_prefix'])->first();
            $input['invoice_no'] = $invoice_no ? ((int)$invoice_no->invoice_no + 1) : 1;
            $input['idx'] = $input['invoice_prefix'] . date('dmy') . '-' . $input['invoice_no'];
            // Create Model
            //$input['status'] = ORDER_SS_COMPLETED;
            $input['order_status'] = ORDER_SS_COMPLETED;
            $input['payment_status'] = PAYMENT_SS_PAID;
            $model = $this->model_repository->create($input);
            // Create Products
            $products_gift = [];
            foreach ($products as $product) {
                $this->order_product_repository->create([
                    'order_id'   => $model->id,
                    'product_id' => $product->id,
                    'name'       => $product->name,
                    'model'      => $product->model,
                    'quantity'   => $product->quantity,
                    'price'      => $product->special ? $product->special : $product->price,
                    'priceo'     => $product->price,
                    'total'      => ($product->special ? $product->special : $product->price) * $product->quantity,
                ]);
            }

            // Create gift products
            foreach ($products_gift as $product_gift) {
                $this->order_product_repository->create($product_gift);
            }
            
            // Create Totals
            $sort_order = 0;
            $total_titles = ['sub_total' => 'Tạm tính', 'total' => 'Thành tiền'];
            foreach ($input['totals'] as $code => $value) {
                $this->order_total_repository->create([
                    'order_id'   => $model->id,
                    'code'       => $code,
                    'title'      => isset($total_titles[$code]) ? $total_titles[$code] : '',
                    'value'      => $value,
                    'sort_order' => ++$sort_order,
                ]);
            }
            // Create history
            $this->order_history_repository->create([
                'order_id'       => $model->id,
                'order_status'   => $model->order_status,
                'payment_status' => $model->payment_status,
                'comment'        => 'Tạo đơn hàng từ admin',
            ]);
            // Send email
            $this->sendEmails($model);
            // Plus uses_total in gift_orders
            //if ($gift_orders_id) $this->gift_order_repository->getModel()->where('id', $gift_orders_id)->increment('uses_total');

            $this->handleOrder($model);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
