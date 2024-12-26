<?php
class ControllerCheckoutShipping extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        if (!$this->user->isLogged() && (int)$this->config->get('config_login_first')) $this->response->redirect("/{$url_prefix}login");
        $this->load->language('checkout/checkout');
        $this->load->language('checkout/shipping');
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
        if (!isset($this->session->data['shipping_address'])) {
            $address_id = $this->user->getAddressId();
            if ($address_id) {
                $this->load->model('account/address');
                $address_info = $this->model_account_address->getAddress($address_id);
                if ($address_info) $this->session->data['shipping_address'] = $address_info;
            }
        }
        $data['addressInfo'] = isset($this->session->data['shipping_address']) ? $this->session->data['shipping_address'] : '';
        $data['orderInfo'] = isset($this->session->data['order_info']) ? $this->session->data['order_info'] : '';
        $userData = $this->registry->get('userData');
        $data['userInfo'] = $userData['info'];
        $data['logo'] = $this->config->get('config_logo');
        $data['logo_height'] = $this->config->get('config_logo_height');
        $data['hotline'] = $this->config->get('config_hotline');

        $data['header'] = $this->load->controller('common/header', ['cfg_full_body' => true]);
        $data['footer'] = $this->load->controller('common/footer', ['cfg_full_body' => true]);

        $this->response->setOutput($this->load->view('checkout/shipping', $data));
    }

    public function save() {
        $json = [];
        if (!empty($this->request->post['address_id'])) {
            $this->load->model('account/address');
            $address_info = $this->model_account_address->getAddress($this->request->post['address_id']);
            if ($address_info) {
                $this->session->data['shipping_address'] = $address_info;
                $json['address_info'] = $address_info;
            } else {
                unset($this->session->data['shipping_address']);
            }
        } else if (!empty($this->request->post['data'])) {
            $this->session->data['shipping_address'] = $this->request->post['data'];
            $json['address_info'] = $this->request->post['data'];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function order_info() {
        $order_info = [];
        foreach (['email', 'phone_number', 'shipping_code', 'shipping_method', 'shipping_time', 'company', 'company_tax', 'company_email', 'company_address', 'note', 'message'] as $field) {
            $order_info[$field] = !empty($this->request->post[$field]) ? $this->request->post[$field] : '';
        }
        $order_info['is_invoice'] = isset($this->request->post['is_invoice']) ? (int)$this->request->post['is_invoice'] : 0;
        $this->session->data['order_info'] = $order_info;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($order_info));
    }
}
