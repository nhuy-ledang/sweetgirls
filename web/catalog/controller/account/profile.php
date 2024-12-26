<?php
class ControllerAccountProfile extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';

        if (!$this->user->isLogged()) {
            $this->response->redirect("/{$url_prefix}login");
        }

        $this->load->language('account/profile');
        $this->load->language('account/account');
        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->session->data['error'])) {
            $data['error_warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => '/'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_account'), 'href' => ''];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_profile'), 'href' => ''];

        $data = array_merge($data, $this->getOrderStatus());

        $userData = $this->registry->get('userData');
        $data['userInfo'] = $userData['info'];
        $data['module_profile'] = $this->load->view('account/profile/profile', $data);

        $data['column_left'] = $this->load->controller('account/column_left');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account/profile', $data));
    }

    public function getOrderStatus() {
        $this->load->model('account/order');
        $orders = $this->model_account_order->getOrders();
        $data = [];
        $data['unpaid_total'] = 0;
        $data['confirm_total'] = 0;
        $data['shipping_total'] = 0;
        $data['completed_total'] = 0;
        foreach ($orders as $order) {
            if ($order['payment_status'] == PAYMENT_SS_INPROGRESS) {
                $data['unpaid_total'] += 1;
            }
            if ($order['order_status'] == ORDER_SS_PROCESSING) {
                $data['confirm_total'] += 1;
            }
            if ($order['order_status'] == ORDER_SS_SHIPPING) {
                $data['shipping_total'] += 1;
            }
            if ($order['shipping_status'] == SHIPPING_SS_DELIVERED && $order['order_status'] == ORDER_SS_COMPLETED) {
                $data['completed_total'] += 1;
            }
        }
        return $data;
    }
}
