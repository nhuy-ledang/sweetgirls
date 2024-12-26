<?php namespace Modules\Order\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Traits\MomoTrait;
use Modules\Core\Traits\OnepayTrait;
use Modules\Notify\Repositories\NotificationRepository;
use Modules\Order\Repositories\CartRepository;
use Modules\Order\Repositories\OrderRepository;
use Modules\Order\Repositories\OrderHistoryRepository;
use Modules\Order\Repositories\OrderProductRepository;
use Modules\Order\Repositories\OrderShippingRepository;
use Modules\Order\Repositories\OrderTotalRepository;
use Modules\Order\Transport\Facade\Transport;
use Modules\Product\Repositories\GiftOrderHistoryRepository;
use Modules\Product\Repositories\GiftOrderRepository;
use Modules\Product\Repositories\ProductQuantityRepository;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductSpecialRepository;
use Modules\System\Repositories\SettingRepository;
use Modules\User\Repositories\AddressRepository as UserAddressRepository;
use Modules\User\Repositories\NotifyRepository as UserNotifyRepository;
use Modules\User\Repositories\UserCoinRepository;
use Modules\User\Repositories\UserRepository;

/**
 * Class OrderController
 *
 * @package Modules\Order\Http\Controllers\ApiPublic
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-05-30
 */
class OrderController extends ApiBaseModuleController {
    use OnepayTrait, MomoTrait;

    /**
     * Maximum limit that can be set via $_GET['limit'].
     *
     * @var int|bool
     */
    protected $maximumLimit = 1000;

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
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    /**
     * @var \Modules\Product\Repositories\ProductQuantityRepository
     */
    protected $product_quantity_repository;

    /**
     * @var \Modules\Product\Repositories\GiftOrderRepository
     */
    protected $gift_order_repository;


    /**
     * @var \Modules\Product\Repositories\GiftOrderHistoryRepository
     */
    protected $gift_order_history_repository;

    /**
     * @var \Modules\Order\Repositories\CartRepository
     */
    protected $cart_repository;

    /**
     * @var \Modules\User\Repositories\UserRepository
     */
    protected $user_repository;

    /**
     * @var \Modules\User\Repositories\UserCoinRepository
     */
    protected $user_coin_repository;

    /**
     * @var \Modules\User\Repositories\AddressRepository
     */
    protected $user_address_repository;

    /**
     * @var \Modules\Notify\Repositories\NotificationRepository
     */
    protected $notification_repository;

    /**
     * @var \Modules\User\Repositories\NotifyRepository
     */
    protected $user_notify_repository;

    /**
     * @var \Modules\System\Repositories\SettingRepository
     */
    protected $setting_repository;

    /**
     * @var \Modules\Product\Repositories\ProductSpecialRepository
     */
    protected $product_special_repository ;

    public function __construct(Request $request,
                                SettingRepository $setting_repository,
                                OrderRepository $order_repository,
                                OrderProductRepository $order_product_repository,
                                OrderTotalRepository $order_total_repository,
                                OrderHistoryRepository $order_history_repository,
                                OrderShippingRepository $order_shipping_repository,
                                CartRepository $cart_repository,
                                ProductRepository $product_repository,
                                ProductQuantityRepository $product_quantity_repository,
                                GiftOrderRepository $gift_order_repository,
                                GiftOrderHistoryRepository $gift_order_history_repository,
                                UserRepository $user_repository,
                                UserCoinRepository $user_coin_repository,
                                UserAddressRepository $user_address_repository,
                                UserNotifyRepository $user_notify_repository,
                                NotificationRepository $notification_repository,
                                ProductSpecialRepository $product_special_repository) {
        $this->model_repository = $order_repository;
        $this->order_product_repository = $order_product_repository;
        $this->order_total_repository = $order_total_repository;
        $this->order_history_repository = $order_history_repository;
        $this->order_shipping_repository = $order_shipping_repository;
        $this->cart_repository = $cart_repository;
        $this->product_repository = $product_repository;
        $this->product_quantity_repository = $product_quantity_repository;
        $this->gift_order_repository = $gift_order_repository;
        $this->gift_order_history_repository = $gift_order_history_repository;
        $this->user_repository = $user_repository;
        $this->user_coin_repository = $user_coin_repository;
        $this->user_address_repository = $user_address_repository;
        $this->wheel_user_repository = $wheel_user_repository;
        $this->wheel_repository = $wheel_repository;
        $this->coupon_repository = $coupon_repository;
        $this->coupon_history_repository = $coupon_history_repository;
        $this->voucher_repository = $voucher_repository;
        $this->notification_repository = $notification_repository;
        $this->user_notify_repository = $user_notify_repository;
        $this->agent_repository = $agent_repository;
        $this->setting_repository = $setting_repository;
        $this->agent_point_repository = $agent_point_repository;
        $this->product_special_repository = $product_special_repository;

        $this->middleware('auth.user')->except(['storeGuest', 'onepayCallback']);

        parent::__construct($request);
    }

    public function store() {
        try {
            $input = $this->request->only(['phone_number', 'address_id', 'shipping_code', 'shipping_method', 'shipping_time', 'payment_code', 'is_invoice', 'note']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, [
                'address_id'    => 'required',
                'payment_code'  => 'required|in:' . PAYMENT_MT_BANK_TRANSFER . ',' . PAYMENT_MT_DOMESTIC . ',' . PAYMENT_MT_FOREIGN . ',' . PAYMENT_MT_MOMO . ',' . PAYMENT_MT_COD,
            ]);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
    
            
            //== Default input
            $input = array_merge($input, ['first_name' => $this->auth->display, 'email' => $this->auth->email]);
            if (empty($input['phone_number'])) $input['phone_number'] = $this->auth->phone_number;
            $input['user_id'] = $this->auth->id;
            $payment_code = $this->request->get('payment_code');
            $input['payment_code'] = $payment_code;
            $input['payment_method'] = Lang::get("transaction.payment_method.$payment_code");
            $input['shipping_first_name'] = $this->auth->display;
            $input['shipping_phone_number'] = $this->auth->phone_number;
            $input['shipping_address_1'] = $this->auth->address;
            $address_id = (int)$this->request->get('address_id');
            $shipping_code = $this->request->get('shipping_code');
            if ($shipping_code) {
                $input['shipping_code'] = $shipping_code;
            }
            $is_invoice = $this->request->get('is_invoice');
            $is_invoice = (is_null($is_invoice) || $is_invoice === 'false') ? false : ($is_invoice === 'true' ? true : (boolean)$is_invoice);
            if ($is_invoice) $input['is_invoice'] = $is_invoice;
            $session_id = $this->getSessionId();
            // Check Has shipping
            /*if ($this->cart_repository->hasShipping()) {

            }*/
            // Check cart valid
            $valid = $this->cart_repository->isValidCart($this->auth->id, $session_id);
            if (!$valid) return $this->errorWrongArgs('cart.empty');
            // Kiểm tra số lượng sản phẩm đổi quà
            $pd_valid = $this->cart_repository->isValidProductCoin($this->auth->id, $session_id);
            if ($pd_valid) return $this->errorWrongArgs('cart.coin_product.invalid');
            // Validate cart has products and has stock.
            //if (!($this->cart_repository->hasProducts($this->auth->id, $session_id)) || !($this->cart_repository->hasStock($this->auth->id, $session_id))) {
            if (!($this->cart_repository->hasProducts($this->auth->id, $session_id))) return $this->errorWrongArgs('product_id.required');
            $input['invoice_prefix'] = $this->setting_repository->findByKey("config_ord_invoice_prefix", 'INV-');
            // Get from cart
            $products = $this->cart_repository->getProducts($this->auth->id, $session_id);
            $input['products'] = $products;
            //$input['coins'] = $this->cart_repository->getCoins($this->auth->id, $session_id);;
            // Validate minimum quantity requirements.
            // ???
            // Gift Voucher
            // ???
            // Totals
            list($sub_total, $discount_code, $discount_total, $voucher_code, $voucher_total, $shipping_code, $shipping_fee, $shipping_discount, $vat, $total, $included_total, $coupon, $next_gifts, $current_gifts, $gift_orders_id) = $this->cart_repository->getTotals($this->auth->id, $session_id);
            
            if ($referral_user) { //=== Check have enough total
                if ($sub_total < REFERRAL_ORDER_TOTAL) return $this->errorWrongArgs('referral_code.order_total');
                $input['referral_code'] = $referral_code;
            }
            // Check voucher exist
            $voucher = null;
            $dateNow = date('Y-m-d');
          
            // Check coin expired
            if ($coins > 0 && $this->auth->coins_expired && $this->auth->coins_expired < $dateNow) return $this->errorWrongArgs('cart.coins.expired');
            if ($input['shipping_code'] != $shipping_code) return $this->errorWrongArgs();
            $no_cod = in_array(true, array_column($products, 'no_cod'));
            if ($no_cod && $payment_code === PAYMENT_MT_COD) return $this->errorWrongArgs('order.payment.cod');
            $input['totals'] = [];
            $input['totals']['sub_total'] = $sub_total;
            if ($discount_code) {
                $input['discount_code'] = $discount_code;
                $input['discount_total'] = $discount_total;
                $input['totals']['coupon'] = $discount_total;
            }
            if ($voucher) {
                $input['voucher_code'] = $voucher_code;
                $input['voucher_total'] = $voucher_total;
                $input['totals']['voucher'] = $voucher_total;
            }
            $input['totals']['total'] = $total;
            $input['shipping_code'] = $shipping_code;
            $input['shipping_fee'] = $shipping_fee;
            $input['shipping_discount'] = $shipping_discount;
            $input['vat'] = $vat;
            $input['sub_total'] = $sub_total;
            $input['total'] = $total;
            $input['coins'] = $total >= 1000 ? round($total / 1000) : 0;
            $input['total_coins'] = $coins;
            $input['user_id'] = $this->auth->id;
            $input['user_group_id'] = $this->auth->user_group_id;
            $input['lang'] = $this->locale;
            $input['currency_code'] = $this->getCurrency();
            $input['ip'] = $this->request->server->get('REMOTE_ADDR');
            if (!empty($this->request->server->get('HTTP_X_FORWARDED_FOR'))) {
                $input['forwarded_ip'] = $this->request->server->get('HTTP_X_FORWARDED_FOR');
            } else if (!empty($this->request->server->get('HTTP_CLIENT_IP'))) {
                $input['forwarded_ip'] = $this->request->server->get('HTTP_CLIENT_IP');
            }
            if (!empty($this->request->server->get('HTTP_USER_AGENT'))) $input['user_agent'] = $this->request->server->get('HTTP_USER_AGENT');
            if (!empty($this->request->server->get('HTTP_ACCEPT_LANGUAGE'))) $input['accept_language'] = $this->request->server->get('HTTP_ACCEPT_LANGUAGE');
            $invoice_no = $this->model_repository->getModel()->selectRaw('max(invoice_no) as invoice_no')->where('invoice_prefix', $input['invoice_prefix'])->first();
            $input['invoice_no'] = $invoice_no ? ((int)$invoice_no->invoice_no + 1) : 1;
            $input['idx'] = $input['invoice_prefix'] . date('dmy') . '-' . $input['invoice_no'];
            $input['payment_status'] = $total == 0 ? PAYMENT_SS_PAID : PAYMENT_SS_INPROGRESS;
            // Create Model
            // if ($total == 0) { // Nếu sản phẩm 0đ, thêm mã freeship thì $total vẫn có thể = 0
            if (false) {
                $model = $this->model_repository->create($input);
                $model->success_url = config('app.url') . ($this->locale == 'en' ? '/en' : '') . "/checkout/success?id={$model->id}&s={$model->order_status}";
            } else {
                $model = $this->model_repository->create($input);
                // Create products
                $products_gift = [];
                foreach ($input['products'] as $product) {
                    $order_product = $this->order_product_repository->create(array_merge($product, ['order_id' => $model->id]));
                    // Check special product
                    if ($product['special_id']) {
                        $special = $this->product_special_repository->find($product['special_id']);
                        if  ($product['is_sale'] && $special){
                            //$valid_quantity = min($special->quantity, $product['quantity']);
                            $this->product_special_repository->update($special, ['quantity' => $special->quantity - $product['quantity'], 'used' => $special->used +  $product['quantity']]);
                        }
                    }
                    // Update message included product
                    if ($product['type'] === 'I') $this->order_product_repository->update($order_product, ['message' => $this->request->get('message')]);
                    // Create gift products
                    if (!empty($product['gifts'])) {
                        foreach ($product['gifts'] as $gift) {
                            if (isset($products_gift[$gift['id']])) {
                                $products_gift[$gift['id']]['quantity'] += $gift['quantity'];
                            } else {
                                $products_gift[$gift['id']] = [
                                    'order_id'   => $model->id,
                                    'product_id' => $gift['id'],
                                    'name'       => $gift['name'],
                                    'quantity'   => $gift['quantity'],
                                ];
                            }
                        }
                    }
                }
                foreach ($products_gift as $product_gift) {
                    $this->order_product_repository->create($product_gift);
                }
                // Gifts order
                if ($current_gifts) {
                    foreach ($current_gifts as $order_gift) {
                        $this->order_product_repository->create([
                            'order_id'   => $model->id,
                            'product_id' => $order_gift['id'],
                            'name'       => $order_gift['name'],
                            'quantity'   => $order_gift['quantity'],
                        ]);
                    }
                    if ($gift_orders_id) $this->gift_order_history_repository->create(['gift_order_id' => $gift_orders_id, 'order_id' => $model->id, 'user_id' => $this->auth->id]);
                }
                // Create Totals
                $sort_order = 0;
                $total_titles = ['sub_total' => 'Tạm tính', 'coupon' => 'Phiếu giảm giá', 'voucher' => 'Mã giảm giá', 'total' => 'Thành tiền'];
                foreach ($input['totals'] as $code => $value) {
                    $this->order_total_repository->create([
                        'order_id'   => $model->id,
                        'code'       => $code,
                        'title'      => isset($total_titles[$code]) ? $total_titles[$code] : '',
                        'value'      => $value,
                        'sort_order' => ++$sort_order,
                    ]);
                }
                if (in_array($payment_code, [PAYMENT_MT_DOMESTIC, PAYMENT_MT_FOREIGN, PAYMENT_MT_MOMO])) {
                    // Create payment url
                    $model->payment_url = $this->createOnepayUrl($model->id, $model->total, $model->currency_code, $payment_code, $model->no, $input['email']);
                    // Create history
                    $this->order_history_repository->create(['order_id' => $model->id, 'order_status' => $model->order_status, 'payment_status' => $model->payment_status, 'comment' => 'Đang thanh toán với Onepay']);
                } /*else if ($payment_code == PAYMENT_MT_MOMO) {
                    // Create payment url
                    $model->payment_url = $this->createMomoUrl($model->id, $model->total, $model->currency_code);
                    // Create history
                    $this->order_history_repository->create(['order_id' => $model->id, 'order_status' => $model->order_status, 'payment_status' => $model->payment_status, 'comment' => 'Đang thanh toán với Momo']);
                } */ else { // PAYMENT_MT_BANK_TRANSFER || PAYMENT_MT_COD
                    // Send email notify
                    //$data = $this->getOrderProducts($model);
                    //if ($model->email) dispatch(new \Modules\Activity\Jobs\OrderAddJob($this->email, $data));
                    $model->success_url = config('app.url') . ($this->locale == 'en' ? '/en' : '') . "/checkout/success?id={$model->id}&s={$model->order_status}";
                    // Create history
                    $this->order_history_repository->create(['order_id' => $model->id, 'order_status' => $model->order_status, 'payment_status' => $model->payment_status, 'comment' => 'Chờ thanh toán sau']);
                    // Send email
                    $this->sendEmails($model);
                }
            }
            // Increase use_total in coupon
            $coupon_model = null;
            if ($discount_code) {
                $coupon_model = $this->coupon_repository->getModel()->where('code', $discount_code)->first();
            }
            if ($coupon_model) $this->coupon_repository->update($coupon_model, ['uses_total' => $coupon_model->uses_total + 1]);
            // Trừ số lượng voucher và cộng số lượng đã sử dụng
            if ($voucher) $this->voucher_repository->update($voucher, ['quantity' => $voucher->quantity - 1, 'used' => $voucher->used + 1]);
            // Trừ coin và lưu lịch sử coin
            if ($coins > 0) {
                $user = $model->user;
                $this->user_repository->update($user, ['coins' => $user->coins - $model->total_coins]);
                $this->user_coin_repository->create(['user_id' => $model->user_id, 'type' => 'order', 'obj_id' => $model->id, 'coins' => -$model->total_coins, 'total' => $user->coins]);
            }
            // Plus uses_total in gift_orders
            if ($gift_orders_id) $this->gift_order_repository->getModel()->where('id', $gift_orders_id)->increment('uses_total');
            if ($affiliate || $agent) {
                if ($total > 0) {
                    $total_without_shipping_fee = $total - ($shipping_fee - $shipping_discount);
                    $promo_total = $discount_total ? $discount_total : $voucher_total;
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
}
