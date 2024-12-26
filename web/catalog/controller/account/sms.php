<?php
class ControllerAccountSms extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';

        if (!$this->user->isLogged()) {
            $this->response->redirect("/{$url_prefix}register");
        }

        $this->document->addStyle('/assets/js/perfect-scrollbar/css/perfect-scrollbar.min.css');
        $this->document->addScript('/assets/js/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js');

        $this->load->language('account/profile');
        $this->load->language('account/sms');
        $this->document->setTitle($this->language->get('heading_title'));
        $data['heading_title'] = $this->language->get('heading_title');
        $data['code'] = $this->language->get('code');

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $limit = 20;

        $route = "{$url_prefix}tin-nhan";

        $filter_data = [
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        ];

        $this->load->model('catalog/support');
        $total = (int)$this->model_catalog_support->getTotalSupports($filter_data);
        $data['supports'] = $this->model_catalog_support->getSupports($filter_data);

        $url = '';

        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->plus($route, $url . '&page={page}')
        ]);

        // http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
        if ($page == 1) {
            $this->document->addLink($this->url->plus($route, $url), 'canonical');
        } else {
            $this->document->addLink($this->url->plus($route, $url . '&page=' . $page), 'canonical');
        }

        if ($page > 1) {
            $this->document->addLink($this->url->plus($route, $url . (($page - 2) ? '&page=' . ($page - 1) : '')), 'prev');
        }

        if ($limit && ceil($total / $limit) > $page) {
            $this->document->addLink($this->url->plus($route, $url . '&page=' . ($page + 1)), 'next');
        }

        $userData = $this->registry->get('userData');
        $data['userInfo'] = $userData['info'];
        $data['module_profile'] = $this->load->view('account/profile/profile', $data);
        $data['column_left'] = $this->load->controller('account/column_left');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account/sms', $data));
    }
}
