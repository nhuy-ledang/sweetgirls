<?php
class ControllerAccountInvite extends Controller {
    public function index() {
        $user_invite_status = $this->config->get('config_user_invite_status');
        if ($user_invite_status) {
            $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';

            if (!$this->user->isLogged()) {
                $this->response->redirect("/{$url_prefix}register");
            }

            $this->load->language('account/account');
            $this->load->language('account/invite');
            $this->document->setTitle($this->language->get('heading_title'));

            $data['breadcrumbs'] = [];
            $data['breadcrumbs'][] = ['text' => 'Trang chủ', 'href' => '/'];
            $data['breadcrumbs'][] = ['text' => $this->language->get('text_account'), 'href' => '/account/profile'];
            $data['breadcrumbs'][] = ['text' => $this->language->get('text_invite_friends'), 'href' => ''];

            $userData = $this->registry->get('userData');
            $data['userInfo'] = $userData['info'];
            $data['profile_option'] = 'invite_code';
            $data['invite_promotion'] = $this->config->get('config_ord_inv_promotion');

            $data['module_profile'] = $this->load->view('account/profile/profile', $data);
            $data['column_left'] = $this->load->controller('account/column_left');
            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('account/invite', $data));
        } else {
            $this->response->redirect('/');
        }
    }

    public function histories () {
        $user_invite_status = $this->config->get('config_user_invite_status');
            if ($user_invite_status) {
            $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';

            if (!$this->user->isLogged()) {
                $this->response->redirect("/{$url_prefix}login");
            }

            $this->load->language('account/account');
            $this->load->language('account/invite');
            $this->load->language('account/invite_histories');

            $this->document->setTitle($this->language->get('heading_title'));

            $data['breadcrumbs'] = [];
            $data['breadcrumbs'][] = ['text' => 'Trang chủ', 'href' => '/'];
            $data['breadcrumbs'][] = ['text' => $this->language->get('text_account'), 'href' => '/account/points'];
            $data['breadcrumbs'][] = ['text' => $this->language->get('text_invite_friends'), 'href' => '/account/invite'];
            $data['breadcrumbs'][] = ['text' => $this->language->get('text_invite_histories'), 'href' => ''];

            $userData = $this->registry->get('userData');
            $data['userInfo'] = $userData['info'];
            $data['profile_option'] = 'invite_code';

            $data['module_profile'] = $this->load->view('account/profile/profile', $data);
            $data['column_left'] = $this->load->controller('account/column_left');
            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('account/invite_histories', $data));
        } else {
            $this->response->redirect('/');
        }
    }
}
