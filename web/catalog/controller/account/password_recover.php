<?php
class ControllerAccountPasswordRecover extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        /*if ($this->user->isLogged()) {
            $this->response->redirect("/{$url_prefix}profile");
        }*/

        $this->load->language('account/password_recover');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['stateParams'] = [];

        if (isset($this->request->get['email'])) {
            $data['stateParams']['email'] = (string)$this->request->get['email'];
        } else {
            $data['stateParams']['email'] = '';
        }

        if (isset($this->request->get['recover_code'])) {
            $data['stateParams']['code'] = (string)$this->request->get['recover_code'];
        } else {
            $data['stateParams']['code'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $data['global'] = $this->registry->get('global');
        $data['layout'] = '';
        $this->response->setOutput($this->load->view("account/password_recover{$data['layout']}", $data));
    }
}
