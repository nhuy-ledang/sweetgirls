<?php
class ControllerCheckoutLogin extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        $this->load->language('checkout/checkout');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['header'] = $this->load->controller('common/header', ['styleRaw' => '.header{display: none;}']);
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('checkout/login', $data));
    }
}
