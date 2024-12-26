<?php
class ControllerAccountLogin extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        if ($this->user->isLogged()) {
            $this->response->redirect(isset($this->session->data['redirect']) ? $this->session->data['redirect'] : rtrim($this->config->get('config_url') . $url_prefix, '/'));
        }
        $login_popup = $this->config->get('config_login_popup');
        if ($login_popup) $this->response->redirect('/');

        $this->load->language('account/login');
        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->session->data['error'])) {
            $data['error_warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error_warning'] = '';
        }

        $data['register_href'] = "/{$url_prefix}register";
        $data['forgot_href'] = "/{$url_prefix}forgot";

        $data['action'] = $this->url->plus("/{$url_prefix}account/login");

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $data['global'] = $this->registry->get('global');
        $data['layout'] = '';
        $this->response->setOutput($this->load->view("account/login{$data['layout']}", $data));
    }
}
