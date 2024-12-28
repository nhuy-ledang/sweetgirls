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
use Modules\Order\Repositories\OrderTotalRepository;
use Modules\Order\Transport\Facade\Transport;
use Modules\Product\Repositories\ProductQuantityRepository;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductSpecialRepository;
use Modules\System\Repositories\SettingRepository;
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
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    /**
     * @var \Modules\Product\Repositories\ProductQuantityRepository
     */
    protected $product_quantity_repository;

    /**
     * @var \Modules\Order\Repositories\CartRepository
     */
    protected $cart_repository;

    /**
     * @var \Modules\User\Repositories\UserRepository
     */
    protected $user_repository;

    /**
     * @var \Modules\Notify\Repositories\NotificationRepository
     */
    protected $notification_repository;

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
                                CartRepository $cart_repository,
                                ProductRepository $product_repository,
                                ProductQuantityRepository $product_quantity_repository,
                                UserRepository $user_repository,
                                NotificationRepository $notification_repository,
                                ProductSpecialRepository $product_special_repository) {
        $this->model_repository = $order_repository;
        $this->order_product_repository = $order_product_repository;
        $this->order_total_repository = $order_total_repository;
        $this->order_history_repository = $order_history_repository;
        $this->cart_repository = $cart_repository;
        $this->product_repository = $product_repository;
        $this->product_quantity_repository = $product_quantity_repository;
        $this->user_repository = $user_repository;
        $this->notification_repository = $notification_repository;
        $this->setting_repository = $setting_repository;
        $this->product_special_repository = $product_special_repository;

        $this->middleware('auth.user')->except(['storeGuest', 'onepayCallback']);

        parent::__construct($request);
    }

    public function store() {
        try {
            $input = $this->request->only(['phone_number', 'address_id', 'shipping_code', 'shipping_method', 'shipping_time', 'payment_code', 'is_invoice', 'note']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, [
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
            // Totals
            list($sub_total, $discount_code, $discount_total, $voucher_code, $voucher_total, $shipping_code, $shipping_fee, $shipping_discount, $vat, $total, $included_total, $coupon, $next_gifts, $current_gifts, $gift_orders_id) = $this->cart_repository->getTotals($this->auth->id, $session_id);

            // Check voucher exist
            $voucher = null;
            $dateNow = date('Y-m-d');
          
            // Check coin expired
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
            $input['total_coins'] = 0;
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
            $model = $this->model_repository->create($input);
            // Create products
            $products_gift = [];
            foreach ($input['products'] as $product) {
                $order_product = $this->order_product_repository->create(array_merge($product, ['order_id' => $model->id]));
                // Check special product
                if ($product['special_id']) {
                    $special = $this->product_special_repository->find($product['special_id']);
                    if ($product['is_sale'] && $special) {
                        //$valid_quantity = min($special->quantity, $product['quantity']);
                        $this->product_special_repository->update($special, ['quantity' => $special->quantity - $product['quantity'], 'used' => $special->used + $product['quantity']]);
                    }
                }
                // Update message included productf
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
            $model->success_url = config('app.url') . ($this->locale == 'en' ? '/en' : '') . "/checkout/success?id={$model->id}&s={$model->order_status}";
            // Create history
            $this->order_history_repository->create(['order_id' => $model->id, 'order_status' => $model->order_status, 'payment_status' => $model->payment_status, 'comment' => 'Chờ thanh toán sau']);


            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
