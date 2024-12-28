<?php
class ControllerAccountOrders extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';

        if (!$this->user->isLogged()) {
            $this->response->redirect("/{$url_prefix}login");
        }

        $this->load->language('account/account');
        $this->load->language('account/orders');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => 'Trang chủ', 'href' => '/'];
        $data['breadcrumbs'][] = ['text' => 'Tài khoản', 'href' => '/account/profile'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_orders_history'), 'href' => ''];

        $filter = isset($this->request->get['f']) ? (int)$this->request->get['f'] : 0;
        $array_filter = [];
        if ($filter) {
            if ($filter == 1) $array_filter = ['payment_status' => PAYMENT_SS_INPROGRESS];
            if ($filter == 2) $array_filter = ['order_status' => ORDER_SS_PROCESSING];
            if ($filter == 3) $array_filter = ['order_status' => ORDER_SS_SHIPPING];
            if ($filter == 4) $array_filter = ['shipping_status' => SHIPPING_SS_DELIVERED, 'order_status' => ORDER_SS_COMPLETED];
        }
        $data['filter'] = $array_filter;

        $data = array_merge($data, $this->load->controller('account/profile/getOrderStatus'));

        $userData = $this->registry->get('userData');
        $data['userInfo'] = $userData['info'];
        $data['module_profile'] = $this->load->view('account/profile/profile', $data);
        $data['column_left'] = $this->load->controller('account/column_left');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account/orders', $data));
    }
    public function details() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';

        if (!$this->user->isLogged()) {
            $this->response->redirect("/{$url_prefix}login");
        }

        $this->load->language('checkout/checkout');
        $this->load->language('account/account');
        $this->load->language('account/order_details');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => 'Trang chủ', 'href' => '/'];
        $data['breadcrumbs'][] = ['text' => 'Tài khoản', 'href' => '/account/profile'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_orders_history'), 'href' => '/account/orders'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_order_details'), 'href' => ''];

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }
        $this->load->model('account/order');
        $order_info = $this->model_account_order->getOrder($order_id);
        $data['order_info'] = [];
        if ($order_info) {
            $order_info['order_status_name'] = $this->language->get('text_' . $order_info['order_status']);
            $data['order_info'] = $order_info;

            $data['products'] = [];
            $data['included_products'] = [];
            $order_products = $this->model_account_order->getProducts($order_id);
            $this->load->model('product/product');
            foreach ($order_products as $order_product) {
                $order_product['info'] = $this->model_product_product->getProduct($order_product['product_id']);
                if (!$order_product['info']['is_included']) {
                    $data['products'][] = $order_product;
                } else {
                    $data['included_products'][] = $order_product;
                }
            }
        }

        $data = array_merge($data, $this->load->controller('account/profile/getOrderStatus'));

        $this->load->model('marketing/wheel');
        $data['hide_cancel_order'] = $this->model_marketing_wheel->getHasOrderId($order_id);

        $userData = $this->registry->get('userData');
        $data['userInfo'] = $userData['info'];
        $data['module_profile'] = $this->load->view('account/profile/profile', $data);
        $data['column_left'] = $this->load->controller('account/column_left');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account/order_details', $data));
    }
}
