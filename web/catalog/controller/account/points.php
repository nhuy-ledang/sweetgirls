<?php
class ControllerAccountPoints extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';

        if (!$this->user->isLogged()) {
            $this->response->redirect("/{$url_prefix}login");
        }

        $this->load->language('account/account');
        $this->load->language('account/points');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => 'Trang chủ', 'href' => '/'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_points'), 'href' => ''];

        $userData = $this->registry->get('userData');
        $data['userInfo'] = $userData['info'];
        $data['config_coin_promotion'] = $this->config->get('config_ord_cns_promotion');
        $data['levelInfo'] = $this->getLevelInfo($data['userInfo']['points']);

        $data['module_profile'] = $this->load->view('account/profile/profile_points', $data);
        $data['column_left'] = $this->load->view('account/column_left_points');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account/points', $data));
    }
    public function redeem () {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';

        if (!$this->user->isLogged()) {
            $this->response->redirect("/{$url_prefix}login");
        }

        $this->load->language('account/account');
        $this->load->language('account/redeem_points');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('product/product');
        $data['redeem_products'] = $this->model_product_product->getRedeemProducts();

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => 'Trang chủ', 'href' => '/'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_points'), 'href' => '/account/points'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_redeem_points'), 'href' => ''];

        $userData = $this->registry->get('userData');
        $data['userInfo'] = $userData['info'];
        $data['levelInfo'] = $this->getLevelInfo($data['userInfo']['points']);

        $data['module_profile'] = $this->load->view('account/profile/profile_points', $data);
        $data['column_left'] = $this->load->view('account/column_left_points');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account/redeem_points', $data));
    }

    public function histories () {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';

        if (!$this->user->isLogged()) {
            $this->response->redirect("/{$url_prefix}login");
        }

        $this->load->language('account/account');
        $this->load->language('account/points_histories');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => 'Trang chủ', 'href' => '/'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_points'), 'href' => '/account/points'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_points_histories'), 'href' => ''];

        $userData = $this->registry->get('userData');
        $data['userInfo'] = $userData['info'];
        $data['levelInfo'] = $this->getLevelInfo($data['userInfo']['points']);

        $data['module_profile'] = $this->load->view('account/profile/profile_points', $data);
        $data['column_left'] = $this->load->view('account/column_left_points');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account/points_histories', $data));
    }

    protected function getLevelInfo($userPoints) {
        /*$this->load->model('account/user_rank');
        $listLevel = $this->model_account_user_rank->getUserRanks();
        $nextLevel = null;
        $nextMilestone = null;

        if(count($listLevel) > 1) {
            if ($userPoints == 0) {
                $nextLevel = $listLevel[1]['name'];
                $nextMilestone = $listLevel[1]['value'];
            } else {
                foreach ($listLevel as $level) {
                    if ($userPoints > 0 && $userPoints < $level['value']) {
                        $nextLevel = $level['name'];
                        $nextMilestone = $level['value'];
                        break;
                    }
                }
            }
            if (!$nextLevel) {
                $lastLevel = end($listLevel);
                $nextLevel = $lastLevel['name'];
                $nextMilestone = $lastLevel['value'];
            }

            $data['levelPercents'] = ($userPoints / $nextMilestone) * 100 . '%';
            $data['nextMilestone'] = $nextMilestone;
            $data['nextLevel'] = $nextLevel;

            return $data;
        }
        return null;*/
    }
}
