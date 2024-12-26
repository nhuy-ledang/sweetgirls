<?php
class ControllerAccountRegister extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        if ($this->user->isLogged()) {
            $this->response->redirect("/{$url_prefix}profile");
        }
        $login_popup = $this->config->get('config_login_popup');
        if ($login_popup) $this->response->redirect('/');

        $this->load->language('account/register');

        $this->document->setTitle($this->language->get('heading_title'));

        if (!empty($this->config->get('config_user_terms_of_use'))) {
            $data['text_terms_of_use'] = html_entity_decode($this->config->get('config_user_terms_of_use'), ENT_QUOTES, 'UTF-8');
        } else {
            $data['text_terms_of_use'] = $this->language->get('text_terms_of_use');
        }

        if (!empty($this->config->get('config_user_agree'))) {
            $data['text_agree'] = html_entity_decode($this->config->get('config_user_agree'), ENT_QUOTES, 'UTF-8');
        } else {
            $data['text_agree'] = $this->language->get('text_agree');
        }

        $data['login_href'] = "/{$url_prefix}login";

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $data['global'] = $this->registry->get('global');
        $data['layout'] = '';
        $this->response->setOutput($this->load->view("account/register{$data['layout']}", $data));
    }
}
