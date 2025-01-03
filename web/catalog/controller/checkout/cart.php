<?php
class ControllerCheckoutCart extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        /*if (!$this->user->isLogged()) {
            $this->session->data['redirect'] = $this->url->plus("/{$url_prefix}checkout/cart");
            $this->response->redirect("/{$url_prefix}login");
        }*/
        $this->load->language('checkout/checkout');
        $this->load->language('checkout/cart');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => '/'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $this->url->plus("/{$url_prefix}checkout/cart")];

        // Gift products
        $this->load->model('product/product');
        $data['redeem_products'] = $this->model_product_product->getRedeemProducts();
        // Deals for you
        $products = $this->model_product_product->getProductRecentlyViewed(0, 10);
        if (!$products) {
            $products = $this->model_product_product->getProducts([
                'sort'  => 'p.viewed',
                'order' => 'desc',
                'start' => 0,
                'limit' => 10,
            ]);
        }
        $data['products'] = $products;

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
        $data['global'] = $this->registry->get('global');

        $data['initData'] = $this->registry->get('initData');
        $userData = $this->registry->get('userData');
        $data['userData'] = $userData;
        $data['userInfo'] = $userData['info'];

        // Address
        if (isset($data['userData']['info'])) {
            $data['addressInfo'] = [
                'id'           => $data['userData']['info']['address_id'] ? (int)$data['userData']['info']['address_id'] : 0,
                'address_1'    => $data['userData']['info']['address'],
                'first_name'   => $data['userData']['info']['display'],
                'phone_number' => $data['userData']['info']['phone_number'],
            ];
        }
        if ($this->user->isLogged()) {
            $address_id = $this->user->getProperty('address_id');
            if ($address_id) {
                $this->load->model('account/address');
                $address_info = $this->model_account_address->getAddress($address_id);
                if ($address_info) {
                    $data['addressInfo'] = $address_info;
                }
            }
        }

        $this->response->setOutput($this->load->view('checkout/cart', $data));
    }

    /*// Start remove
    private function addPost() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        $json = [];

        // Only one product
        //$this->cart->clear();

        $product_id = isset($this->request->post['product_id']) ? (int)$this->request->post['product_id'] : 0;
        $info = $this->model_product_product->getProduct($product_id);
        if ($info) {
            $quantity = isset($this->request->post['quantity']) ? (int)$this->request->post['quantity'] : 1;
            $option = isset($this->request->post['option']) ? array_filter($this->request->post['option']) : [];

            if (!$json) {
                $this->cart->add($product_id, $quantity, $option);

                $json['success'] = sprintf($this->language->get('text_success'), $info['href'], $info['name'], "/{$url_prefix}checkout/cart");

                // Unset all shipping and payment methods
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);

                return $this->order($json);
            } else {
                $json['redirect'] = str_replace('&amp;', '&', $info['href']);
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function addGet() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';

        // Only one product
        //$this->cart->clear();

        $product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
        $option = isset($this->request->post['option']) ? array_filter($this->request->post['option']) : [];

        $info = $this->model_product_product->getProduct($product_id);
        if ($info) {
            $quantity = isset($this->request->get['quantity']) ? (int)$this->request->get['quantity'] : 1;
            $this->cart->add($product_id, $quantity, $option);

            // Unset all shipping and payment methods
            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
        }

        $this->response->redirect("/{$url_prefix}checkout/cart");
    }

    public function add() {
        $this->load->language('checkout/cart');
        $this->load->model('product/product');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            // Add products
            if (isset($this->request->post['products'])) {
                $json = [];
                $quantity = isset($this->request->post['quantity']) ? (int)$this->request->post['quantity'] : 1;

                $option = isset($this->request->post['option']) ? array_filter($this->request->post['option']) : [];

                $products = $this->request->post['products'];

                if (!empty($products) && is_array($products)) {
                    foreach ($products as $product_id) {
                        $info = $this->model_product_product->getProduct($product_id);
                        if ($info) {
                            $this->cart->add($product_id, $quantity, $option);
                        }
                    }
                }

                $json['success'] = true;
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
            } else {
                return $this->addPost();
            }
        } else {
            return $this->addGet();
        }
    }

    public function edit() {
        $this->load->language('checkout/cart');

        $json = [];

        // Update
        if (!empty($this->request->post['key']) && !empty($this->request->post['quantity'])) {
            $this->cart->update($this->request->post['key'], $this->request->post['quantity']);

            $this->session->data['success'] = $this->language->get('text_remove');

            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['reward']);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function remove() {
        $this->load->language('checkout/cart');

        $json = [];

        // Remove
        if (isset($this->request->post['key'])) {
            $this->cart->remove($this->request->post['key']);

            unset($this->session->data['vouchers'][$this->request->post['key']]);

            $json['success'] = $this->language->get('text_remove');

            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['reward']);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // End remove*/

    public function check() {
        $this->load->language('checkout/cart');
        $json = [];
        if (isset($this->request->post['product_id'])) {
            $product_id = (int)$this->request->post['product_id'];
        } else {
            $product_id = 0;
        }
        $this->load->model('product/product');
        $info = $this->model_product_product->getProduct($product_id);
        if ($info) {
            if (!$json) {
                $json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $info['name'], 'checkout/cart');
            } else {
                $json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']));
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function order($json = []) {
        $this->load->language('checkout/checkout');

        if (!$json) {
            $redirect = '';

            // Validate cart has products and has stock.
            if (!$this->cart->hasProducts()/* || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))*/) {
                $redirect = 'checkout/cart';
            }

            // Validate minimum quantity requirements.
            $products = $this->cart->getProducts();
            foreach ($products as $product) {
                $product_total = 0;
                foreach ($products as $product_2) {
                    if ($product_2['product_id'] == $product['product_id']) {
                        $product_total += $product_2['quantity'];
                    }
                }
                if ($product['minimum'] > $product_total) {
                    $redirect = 'checkout/cart';
                    break;
                }
            }

            if ($redirect) {
                $data['redirect'] = $redirect;
            }
        }

        // Success
        if (!$json) {
            $user_group_id = $this->config->get('config_user_group_id');
            $first_name = '';//$this->request->post['first_name'];
            $phone_number = '';//$this->request->post['phone_number'];
            $country_id = $this->config->get('config_country_id');//$this->request->post['country_id'];
            $zone_id = '';//$this->request->post['zone_id'];
            $address_1 = '';//$this->request->post['address_1'];
            $comment = '';//$this->request->post['comment'];

            $this->session->data['account'] = 'guest';

            $this->session->data['guest']['user_group_id'] = $user_group_id;
            $this->session->data['guest']['first_name'] = $first_name;
            $this->session->data['guest']['last_name'] = '';
            $this->session->data['guest']['email'] = '';
            $this->session->data['guest']['phone_number'] = $phone_number;

            $this->session->data['payment_address']['first_name'] = $first_name;
            $this->session->data['payment_address']['last_name'] = '';
            $this->session->data['payment_address']['company'] = '';
            $this->session->data['payment_address']['address_1'] = $address_1;
            $this->session->data['payment_address']['address_2'] = '';
            $this->session->data['payment_address']['postcode'] = '';
            $this->session->data['payment_address']['city'] = '';
            $this->session->data['payment_address']['country_id'] = $country_id;
            $this->session->data['payment_address']['zone_id'] = $zone_id;

            $this->load->model('localisation/country');

            $country_info = $this->model_localisation_country->getCountry($country_id);
            if ($country_info) {
                $this->session->data['payment_address']['country'] = $country_info['name'];
                $this->session->data['payment_address']['iso_code_2'] = $country_info['iso_code_2'];
                $this->session->data['payment_address']['iso_code_3'] = $country_info['iso_code_3'];
                $this->session->data['payment_address']['address_format'] = $country_info['address_format'];
            } else {
                $this->session->data['payment_address']['country'] = '';
                $this->session->data['payment_address']['iso_code_2'] = '';
                $this->session->data['payment_address']['iso_code_3'] = '';
                $this->session->data['payment_address']['address_format'] = '';
            }

            $this->load->model('localisation/zone');

            $zone_info = $this->model_localisation_zone->getZone($zone_id);
            if ($zone_info) {
                $this->session->data['payment_address']['zone'] = $zone_info['name'];
                $this->session->data['payment_address']['zone_code'] = $zone_info['code'];
            } else {
                $this->session->data['payment_address']['zone'] = '';
                $this->session->data['payment_address']['zone_code'] = '';
            }

            $this->session->data['shipping_address']['first_name'] = $first_name;
            $this->session->data['shipping_address']['last_name'] = '';
            $this->session->data['shipping_address']['company'] = '';
            $this->session->data['shipping_address']['address_1'] = $address_1;
            $this->session->data['shipping_address']['address_2'] = '';
            $this->session->data['shipping_address']['postcode'] = '';
            $this->session->data['shipping_address']['city'] = '';
            $this->session->data['shipping_address']['country_id'] = $country_id;
            $this->session->data['shipping_address']['zone_id'] = $zone_id;

            if ($country_info) {
                $this->session->data['shipping_address']['country'] = $country_info['name'];
                $this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];
                $this->session->data['shipping_address']['iso_code_3'] = $country_info['iso_code_3'];
                $this->session->data['shipping_address']['address_format'] = $country_info['address_format'];
            } else {
                $this->session->data['shipping_address']['country'] = '';
                $this->session->data['shipping_address']['iso_code_2'] = '';
                $this->session->data['shipping_address']['iso_code_3'] = '';
                $this->session->data['shipping_address']['address_format'] = '';
            }

            if ($zone_info) {
                $this->session->data['shipping_address']['zone'] = $zone_info['name'];
                $this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
            } else {
                $this->session->data['shipping_address']['zone'] = '';
                $this->session->data['shipping_address']['zone_code'] = '';
            }

            // Huy Custom
            $this->session->data['shipping_method'] = [
                'title'        => 'Free Shipping',
                'code'         => 'free.free',
                'cost'         => 0,
                'tax_class_id' => 0,
            ];
            $this->session->data['payment_method'] = [
                'title' => 'Cash On Delivery',
                'code'  => 'cod',
            ];
            $this->session->data['comment'] = strip_tags($comment);

//            unset($this->session->data['shipping_method']);
//            unset($this->session->data['shipping_methods']);
//            unset($this->session->data['payment_method']);
//            unset($this->session->data['payment_methods']);

            // Create Order
            $order_data = [];

            $totals = [];
            $taxes = $this->cart->getTaxes();
            $total = 0;

            // Because __call can not keep var references so we put them into an array.
            $total_data = [
                'totals' => &$totals,
                'taxes'  => &$taxes,
                'total'  => &$total,
            ];

            $this->load->model('setting/extension');

            $sort_order = [];
            $results = $this->model_setting_extension->getExtensions('total');
            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);
            foreach ($results as $result) {
                if ($this->config->get('total_' . $result['code'] . '_status')) {
                    $this->load->model('extension/total/' . $result['code']);
                    // We have to put the totals in an array so that they pass by reference.
                    $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                }
            }

            $sort_order = [];
            foreach ($totals as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $totals);

            $order_data['totals'] = $totals;

            $this->load->language('checkout/checkout');

            $order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
            $order_data['store_name'] = $this->config->get('config_name');

            if ($this->request->server['HTTPS']) {
                $order_data['store_url'] = HTTPS_SERVER;
            } else {
                $order_data['store_url'] = HTTP_SERVER;
            }

            $this->load->model('account/user');

            if ($this->user->isLogged()) {
                $user_info = $this->model_account_user->getCustomer($this->user->getId());

                $order_data['user_id'] = $this->user->getId();
                $order_data['user_group_id'] = $user_info['user_group_id'];
                $order_data['first_name'] = $user_info['first_name'];
                $order_data['last_name'] = $user_info['last_name'];
                $order_data['email'] = $user_info['email'];
                $order_data['phone_number'] = $user_info['phone_number'];
            } else if (isset($this->session->data['guest'])) {
                $order_data['user_id'] = 0;
                $order_data['user_group_id'] = $this->session->data['guest']['user_group_id'];
                $order_data['first_name'] = $this->session->data['guest']['first_name'];
                $order_data['last_name'] = $this->session->data['guest']['last_name'];
                $order_data['email'] = $this->session->data['guest']['email'];
                $order_data['phone_number'] = $this->session->data['guest']['phone_number'];
            }

            $order_data['payment_first_name'] = $this->session->data['payment_address']['first_name'];
            $order_data['payment_last_name'] = $this->session->data['payment_address']['last_name'];
            $order_data['payment_company'] = $this->session->data['payment_address']['company'];
            $order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
            $order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
            $order_data['payment_city'] = $this->session->data['payment_address']['city'];
            $order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
            $order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
            $order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
            $order_data['payment_country'] = $this->session->data['payment_address']['country'];
            $order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
            $order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];

            if (isset($this->session->data['payment_method']['title'])) {
                $order_data['payment_method'] = $this->session->data['payment_method']['title'];
            } else {
                $order_data['payment_method'] = '';
            }

            if (isset($this->session->data['payment_method']['code'])) {
                $order_data['payment_code'] = $this->session->data['payment_method']['code'];
            } else {
                $order_data['payment_code'] = '';
            }

            if ($this->cart->hasShipping()) {
                $order_data['shipping_first_name'] = $this->session->data['shipping_address']['first_name'];
                $order_data['shipping_last_name'] = $this->session->data['shipping_address']['last_name'];
                $order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
                $order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
                $order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
                $order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
                $order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
                $order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
                $order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
                $order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
                $order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
                $order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];

                if (isset($this->session->data['shipping_method']['title'])) {
                    $order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
                } else {
                    $order_data['shipping_method'] = '';
                }

                if (isset($this->session->data['shipping_method']['code'])) {
                    $order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
                } else {
                    $order_data['shipping_code'] = '';
                }
            } else {
                $order_data['shipping_first_name'] = '';
                $order_data['shipping_last_name'] = '';
                $order_data['shipping_company'] = '';
                $order_data['shipping_address_1'] = '';
                $order_data['shipping_address_2'] = '';
                $order_data['shipping_city'] = '';
                $order_data['shipping_postcode'] = '';
                $order_data['shipping_zone'] = '';
                $order_data['shipping_zone_id'] = '';
                $order_data['shipping_country'] = '';
                $order_data['shipping_country_id'] = '';
                $order_data['shipping_address_format'] = '';
                $order_data['shipping_method'] = '';
                $order_data['shipping_code'] = '';
            }

            $order_data['products'] = [];
            foreach ($this->cart->getProducts() as $product) {
                $order_data['products'][] = [
                    'product_id' => $product['product_id'],
                    'name'       => $product['name'],
                    'model'      => $product['model'],
                    'quantity'   => $product['quantity'],
                    'subtract'   => $product['subtract'],
                    'price'      => $product['price'],
                    'total'      => $product['total'],
                ];
            }

            $order_data['comment'] = $this->session->data['comment'];
            $order_data['total'] = $total_data['total'];

            /*if (isset($this->request->cookie['tracking'])) {
                $order_data['tracking'] = $this->request->cookie['tracking'];

                $subtotal = $this->cart->getSubTotal();

                // Affiliate
                $affiliate_info = $this->model_account_user->getAffiliateByTracking($this->request->cookie['tracking']);

                if ($affiliate_info) {
                    $order_data['affiliate_id'] = $affiliate_info['user_id'];
                    $order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
                } else {
                    $order_data['affiliate_id'] = 0;
                    $order_data['commission'] = 0;
                }

                // Marketing
                $this->load->model('checkout/marketing');

                $marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

                if ($marketing_info) {
                    $order_data['marketing_id'] = $marketing_info['marketing_id'];
                } else {
                    $order_data['marketing_id'] = 0;
                }
            } else {
                $order_data['affiliate_id'] = 0;
                $order_data['commission'] = 0;
                $order_data['marketing_id'] = 0;
                $order_data['tracking'] = '';
            }*/

            $order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
            $order_data['currency_code'] = $this->session->data['currency'];
            $order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
            $order_data['ip'] = $this->request->server['REMOTE_ADDR'];

            if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
                $order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
            } else if (!empty($this->request->server['HTTP_CLIENT_IP'])) {
                $order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
            } else {
                $order_data['forwarded_ip'] = '';
            }

            if (isset($this->request->server['HTTP_USER_AGENT'])) {
                $order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
            } else {
                $order_data['user_agent'] = '';
            }

            if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
                $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
            } else {
                $order_data['accept_language'] = '';
            }

            $this->load->model('checkout/order');

            $this->session->data['order_id'] = $this->model_product_checkout_order->addOrder($order_data);

            $data['products'] = [];
            foreach ($this->cart->getProducts() as $product) {
                $data['products'][] = [
                    'cart_id'    => $product['cart_id'],
                    'product_id' => $product['product_id'],
                    'name'       => $product['name'],
                    'model'      => $product['model'],
                    'quantity'   => $product['quantity'],
                    'subtract'   => $product['subtract'],
                    //'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                    //'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']),
                    'price'      => $this->currency->format($product['price'], $this->session->data['currency']),
                    'total'      => $this->currency->format($product['price'] * $product['quantity'], $this->session->data['currency']),
                    'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                ];
            }

            $data['totals'] = [];
            foreach ($order_data['totals'] as $total) {
                $data['totals'][] = [
                    'title' => $total['title'],
                    'text'  => $this->currency->format($total['value'], $this->session->data['currency']),
                ];
            }

            // Confirm
            /*if ($this->session->data['payment_method']['code'] == 'cod') {
                $this->load->model('checkout/order');
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_cod_order_status_id'));
            }*/
            $this->load->model('checkout/order');
            $this->model_product_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_cod_order_status_id'));

            $json['redirect'] = $this->url->link('checkout/success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function total() {
        if (isset($this->request->get['payment_code'])) {
            $payment_code = (string)$this->request->get['payment_code'];
            if (in_array($payment_code, ['cod', 'bank_transfer'])) {
                $this->session->data['shipping_method'] = [
                    'title'        => 'Miễn phí vận chuyển',
                    'code'         => 'free.free',
                    'cost'         => 0,
                    'tax_class_id' => 0,
                ];
                $this->session->data['payment_method'] = [
                    'title' => $payment_code == 'cod' ? 'Thu tiền khi giao hàng' : 'Chuyển khoản ngân hàng',
                    'code'  => $payment_code,
                ];
            }
        }

        // Totals
        $this->load->model('setting/extension');

        $totals = [];
        $taxes = $this->cart->getTaxes();
        $total = 0;

        // Because __call can not keep var references so we put them into an array.
        $total_data = [
            'totals' => &$totals,
            'taxes'  => &$taxes,
            'total'  => &$total,
        ];

        // Display prices
        if ($this->user->isLogged() || !$this->config->get('config_user_price')) {
            $sort_order = [];

            $results = $this->model_setting_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get('total_' . $result['code'] . '_status')) {
                    $this->load->model('extension/total/' . $result['code']);

                    // We have to put the totals in an array so that they pass by reference.
                    $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                }
            }

            $sort_order = [];

            foreach ($totals as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $totals);
        }

        $data['totals'] = [];

        foreach ($totals as $total) {
            $data['totals'][] = [
                'title' => $total['title'],
                'text'  => $this->currency->format($total['value'], $this->session->data['currency']),
                'fee'   => $payment_code == 'cod' and $total['value'] < 2000000 ? true : false,
            ];
        }

        $this->response->setOutput($this->load->view('checkout/cart_total', $data));
    }
}
