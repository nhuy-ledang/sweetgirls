<?php
class ControllerCheckoutPayment extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        if (!$this->user->isLogged() && (int)$this->config->get('config_login_first')) $this->response->redirect("/{$url_prefix}login");
        $this->load->language('checkout/checkout');
        $this->load->language('checkout/payment');
        $this->document->setTitle($this->language->get('heading_title'));
        if (!$this->cart->hasProducts()) $this->response->redirect("/{$url_prefix}checkout/cart");
        // Products
//        $data['products'] = [];
//        $products = $this->cart->getProducts();
//        foreach ($products as $row) {
//            $product_total = 0;
//            foreach ($products as $product_2) {
//                if ($product_2['product_id'] == $row['product_id']) {
//                    $product_total += $product_2['quantity'];
//                }
//            }
//            /*if ($row['minimum'] > $product_total) {
//                $data['error_warning'] = sprintf($this->language->get('error_minimum'), $row['name'], $row['minimum']);
//            }*/
//            // Display prices
//            //$unit_price = $this->tax->calculate($row['price'], $row['tax_class_id'], $this->config->get('config_tax'));
//            $unit_price = $row['price'];
//            $price = $this->currency->format($unit_price, $this->session->data['currency']);
//            $total = $this->currency->format($unit_price * $row['quantity'], $this->session->data['currency']);
//            $data['products'][] = array_merge($row, [
//                'price' => $price,
//                'total' => $total,
//                //'href'  => $this->config->get('config_url') . $url_prefix . (!empty($row['alias']) ? $row['alias'] : 'product/product?product_id=' . $row['id']),
//            ]);
//        }
//        // Coins
//        $data['coins'] = $this->cart->getCoins();
//        // Totals
//        $totals = [];
//        $taxes = $this->cart->getTaxes();
//        $total = 0;
//        $codes = ['sub_total', 'coupon', 'shipping', 'total'];
//        foreach ($codes as $code) {
//            $this->load->model('total/' . $code);
//            // __call can not pass-by-reference so we get PHP to call it as an anonymous function.
//            ($this->{'model_total_' . $code}->getTotal)($totals, $taxes, $total);
//        }
//        $data['totals'] = [];
//        foreach ($totals as $code => $total) {
//            $data['totals'][$code] = [
//                'title' => $total['title'],
//                'text'  => $this->currency->format($total['value'], $this->session->data['currency']),
//            ];
//        }
        // Shipping
        $shipping_methods = [
            'free.free' => ['title' => 'Free Shipping', 'code' => 'free.free', 'cost' => 0],
        ];
        $this->session->data['shipping_methods'] = $shipping_methods;
        $data['shipping_methods'] = $shipping_methods;
        // Payment
        $payment_methods = [
            'cod'           => ['title' => $this->language->get('text_cod'), 'code' => 'cod'],
            'momo'          => ['title' => 'Thanh toán bằng Momo', 'code' => 'momo'],
            'bank_transfer' => ['title' => 'Thanh toán chuyển khoản', 'code' => 'bank_transfer'],
            'domestic'      => ['title' => 'Thẻ ATM nội địa/Internet Banking', 'code' => 'domestic'],
            'international' => ['title' => 'Thanh toán bằng thẻ quốc tế Visa, Master, JCB', 'code' => 'international'],
        ];
        $this->session->data['payment_methods'] = $payment_methods;
        $data['payment_methods'] = $payment_methods;

        $data['hotline'] = $this->config->get('config_hotline');
        $data['logo'] = $this->config->get('config_logo');
        $data['logo_height'] = $this->config->get('config_logo_height');
        $data['onepay_terms'] = $this->config->get('config_onepay_terms');
        $data['user_invite_status'] = $this->config->get('config_user_invite_status');

        $orderInfo = [];
        $orderInfo['address_id'] = isset($this->session->data['shipping_address']) && isset($this->session->data['shipping_address']['id']) ? $this->session->data['shipping_address']['id'] : '';
        $orderInfo['addressInfo'] = isset($this->session->data['shipping_address']) ? $this->session->data['shipping_address'] : '';
        $orderInfo['coupon'] = isset($this->session->data['coupon']) ? $this->session->data['coupon'] : '';
        if (isset($this->session->data['order_info'])) $orderInfo = array_merge($this->session->data['order_info'], $orderInfo);
        // Tracking Code
        if (isset($this->request->cookie['tracking'])) $orderInfo['tracking'] = $this->request->cookie['tracking'];
        $data['orderInfo'] = $orderInfo;

        $data['header'] = $this->load->controller('common/header', ['cfg_full_body' => true]);
        $data['footer'] = $this->load->controller('common/footer', ['cfg_full_body' => true]);

        $this->response->setOutput($this->load->view('checkout/payment', $data));
    }

    public function confirm() {
        $this->load->language('checkout/checkout');
        $json = [];

        if (!empty($this->request->post['payment_method'])) {
            $payment_code = $this->request->post['payment_method'];
            if (!in_array($payment_code, ['international', 'domestic'])) {
                $json['error'] = $this->language->get('error_payment');
            }
            $this->session->data['payment_method'] = $this->session->data['payment_methods'][$payment_code];

            // Create order
            $redirect = '';
            if (!$this->user->isLogged()) {
                //$redirect = '/checkout/login';
                $redirect = '/login';
            }
            if ($this->cart->hasShipping()) {
                // Validate if shipping address has been set.
                if (!isset($this->session->data['shipping_address'])) {
                    $redirect = '/checkout/shipping';
                }
                // Validate if shipping method has been set.
                /*if (!isset($this->session->data['shipping_method'])) {
                    $redirect = '/checkout/payment';
                }*/
            } else {
                unset($this->session->data['shipping_address']);
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
            }
            // Validate if payment method has been set.
            if (!isset($this->session->data['payment_method'])) {
                $redirect = '/checkout/payment';
            }
            // Validate cart has products and has stock.
            if (!($this->cart->hasProducts())) {
                $redirect = '/checkout/cart';
            }
            $products = $this->cart->getProducts();

            /*// Validate minimum quantity requirements.
            foreach ($products as $product) {
                $product_total = 0;
                foreach ($products as $product_2) {
                    if ($product_2['product_id'] == $product['product_id']) {
                        $product_total += $product_2['quantity'];
                    }
                }
                if ($product['minimum'] > $product_total) {
                    $redirect = '/checkout/cart';
                    break;
                }
            }*/
            if (!$redirect) {
                $userData = $this->registry->get('userData');
                $user_info = $userData['info'];
                // Address
                $order_data = [
                    'user_id'       => $this->user->getId(),
                    'user_group_id' => $user_info['user_group_id'],
                    'first_name'    => $user_info['display'],
                    'phone_number'  => $user_info['phone_number'],
                    'email'         => $user_info['email'],
                ];
                $order_data['invoice_prefix'] = $this->config->get('pd_invoice_prefix');
                $order_data['store_name'] = $this->config->get('config_name');
                $order_data['store_url'] = $this->config->get('config_url');
                if (isset($this->session->data['payment_method']['title'])) {
                    $order_data['payment_method'] = $this->session->data['payment_method']['title'];
                }
                if (isset($this->session->data['payment_method']['code'])) {
                    $order_data['payment_code'] = $this->session->data['payment_method']['code'];
                }
                $order_data['shipping_first_name'] = $user_info['display'];
                $order_data['shipping_phone_number'] = $user_info['phone_number'];
                $order_data['shipping_address_1'] = $user_info['address'];
                if (isset($this->session->data['shipping_address'])) {
                    $order_data['shipping_first_name'] = $this->session->data['shipping_address']['first_name'];
                    $order_data['shipping_last_name'] = $this->session->data['shipping_address']['last_name'];
                    $order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
                    $order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
                    $order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
                    $order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
                    $order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
                    $order_data['shipping_province'] = $this->session->data['shipping_address']['province'];
                    $order_data['shipping_province_id'] = $this->session->data['shipping_address']['province_id'];
                    $order_data['shipping_district'] = $this->session->data['shipping_address']['district'];
                    $order_data['shipping_district_id'] = $this->session->data['shipping_address']['district_id'];
                    $order_data['shipping_ward'] = $this->session->data['shipping_address']['ward'];
                    $order_data['shipping_ward_id'] = $this->session->data['shipping_address']['ward_id'];
                }
                if (isset($this->session->data['shipping_method'])) {
                    if (isset($this->session->data['shipping_method']['title'])) {
                        $order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
                    }
                    if (isset($this->session->data['shipping_method']['code'])) {
                        $order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
                    }
                }
                // Products
                $order_data['products'] = [];
                foreach ($products as $row) {
                    $order_data['products'][] = array_merge($row, []);
                }
                // Gift Voucher
                $order_data['vouchers'] = [];
                if (!empty($this->session->data['vouchers'])) {
                    foreach ($this->session->data['vouchers'] as $voucher) {
                        $order_data['vouchers'][] = array_merge($voucher, [
                            'code' => token(10),
                        ]);
                    }
                }
                // Totals
                $totals = [];
                $taxes = $this->cart->getTaxes();
                $total = 0;
                $codes = ['sub_total', 'coupon', 'total'];
                foreach ($codes as $code) {
                    $this->load->model('total/' . $code);
                    // __call can not pass-by-reference so we get PHP to call it as an anonymous function.
                    ($this->{'model_total_' . $code}->getTotal)($totals, $taxes, $total);
                }
                $order_data['totals'] = $totals;
                $order_data['total'] = $total;
                $order_data['comment'] = isset($this->session->data['comment']) ? $this->session->data['comment'] : '';
                $order_data['tracking'] = isset($this->request->cookie['tracking']) ? $this->request->cookie['tracking'] : '';
                $order_data['lang'] = $this->config->get('config_language');
                //$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
                $order_data['currency_code'] = $this->session->data['currency'];
                //$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
                $order_data['ip'] = $this->request->server['REMOTE_ADDR'];
                if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
                    $order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
                } else if (!empty($this->request->server['HTTP_CLIENT_IP'])) {
                    $order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
                }
                if (isset($this->request->server['HTTP_USER_AGENT'])) {
                    $order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
                }
                if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
                    $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
                }
                $this->load->model('product/order');
                $order_data['order_id'] = $this->model_product_order->addOrder($order_data);
                $this->session->data['order_id'] = $order_data['order_id'];
                $json['payment'] = $this->load->controller('checkout/payment/' . $this->session->data['payment_method']['code'], $order_data);
            } else {
                $json['redirect'] = str_replace('&amp;', '&', $redirect);
            }
        } else {
            $json['error'] = $this->language->get('error_payment');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
