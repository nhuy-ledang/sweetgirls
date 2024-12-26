<?php
class ControllerAccountWishlist extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';

        if (!$this->user->isLogged()) {
            $this->response->redirect("/{$url_prefix}register");
        }

        $this->load->language('account/account');
        $this->load->language('account/wishlist');
        $this->load->model('product/product');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => '/'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_account'), 'href' => '/account/profile'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_favorite_product'), 'href' => ''];

        $data['items'] = $this->model_product_product->getProductLikes();

        $this->load->model('account/order');
        $orders = $this->model_account_order->getOrders();
        $data['unpaid_total'] = 0;
        $data['confirm_total'] = 0;
        $data['shipping_total'] = 0;
        $data['completed_total'] = 0;
        foreach ($orders as $order) {
            if ($order['payment_status'] == 'in_process') {
                $data['unpaid_total'] += 1;
            } else if ($order['order_status'] == 'processing') {
                $data['confirm_total'] += 1;
            } else if ($order['order_status'] == 'shipping') {
                $data['shipping_total'] += 1;
            } else if ($order['order_status'] == 'completed') {
                $data['completed_total'] += 1;
            }
        }

        $userData = $this->registry->get('userData');
        $data['userInfo'] = $userData['info'];

        $data['module_profile'] = $this->load->view('account/profile/profile', $data);
        $data['column_left'] = $this->load->controller('account/column_left');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account/wishlist', $data));
    }
}
