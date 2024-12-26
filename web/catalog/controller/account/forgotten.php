<?php
class ControllerAccountForgotten extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        if ($this->user->isLogged()) {
            $this->response->redirect("/{$url_prefix}profile");
        }
        $login_popup = $this->config->get('config_login_popup');
        if ($login_popup) $this->response->redirect('/');

        $this->load->language('account/forgotten');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['login_href'] = "/{$url_prefix}login";

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $data['global'] = $this->registry->get('global');
        $data['layout'] = '';
        $this->response->setOutput($this->load->view("account/forgotten{$data['layout']}", $data));
    }
}
