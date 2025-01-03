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
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => '/'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_account'), 'href' => '/account/profile'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_orders_history'), 'href' => ''];

        $userData = $this->registry->get('userData');
        $data['userInfo'] = $userData['info'];
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
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => '/'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_account'), 'href' => '/account/profile'];
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

        $userData = $this->registry->get('userData');
        $data['userInfo'] = $userData['info'];
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account/order_details', $data));
    }
}
