<?php

class ControllerAccountPassword extends Controller {
    private $error = array();

    public function index() {
        if (!$this->user->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/password');

            $this->response->redirect($this->url->link('account/login'));
        }

        $this->load->language('account/password');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->load->model('account/user');

            $this->model_account_user->editPassword($this->user->getEmail(), $this->request->post['password']);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('account/account'));
        }

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => '/'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_account'), 'href' => '/account/profile'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_change_pass'), 'href' => ''];

        if (isset($this->error['password'])) {
            $data['error_password'] = $this->error['password'];
        } else {
            $data['error_password'] = '';
        }

        if (isset($this->error['confirm'])) {
            $data['error_confirm'] = $this->error['confirm'];
        } else {
            $data['error_confirm'] = '';
        }

        $data['action'] = $this->url->link('account/password');

        if (isset($this->request->post['password'])) {
            $data['password'] = $this->request->post['password'];
        } else {
            $data['password'] = '';
        }

        if (isset($this->request->post['confirm'])) {
            $data['confirm'] = $this->request->post['confirm'];
        } else {
            $data['confirm'] = '';
        }

        $data['back'] = $this->url->link('account/account');

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
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
        $data['column_left'] = $this->load->controller('account/column_left');
        $data['module_profile'] = $this->load->view('account/profile/profile', $data);

        $data['global'] = $this->registry->get('global');

        $this->response->setOutput($this->load->view('account/password', $data));
    }

    protected function validate() {
        if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
            $this->error['password'] = $this->language->get('error_password');
        }

        if ($this->request->post['confirm'] != $this->request->post['password']) {
            $this->error['confirm'] = $this->language->get('error_confirm');
        }

        return !$this->error;
    }
}
