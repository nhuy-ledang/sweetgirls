<?php
class ControllerAccountColumnLeft extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        $this->load->language('account/account');

        // Nav
        $route = isset($this->request->get['route']) ? $this->request->get['route'] : 'common/home';
        $data['route'] = $route;
        // Menus
        $menus = [];
        $menus[] = [
            'name'     => 'Tài khoản',          //$this->language->get('tab_account') . '<!-- tab_account -->',
            'icon'     => 'ic_info',
            'selected' => in_array($route, ['account/profile']),
            'href'     => $url_prefix . 'account/profile'
        ];
        /*$menus[] = [
            'name'     => $this->language->get('tab_notification') . ' ' . '<span id="notify-badge" class="badge badge-danger font-size-sm ml-3">0</span>',
            'icon'     => 'ic_bell',
            'selected' => in_array($route, ['account/notification']),
            'href'     => $url_prefix . 'account/notification'
        ];*/
        /*$menus[] = [
            'name'     => $this->language->get('tab_calendar') . ' ' . '<span id="calendar-badge" class="badge badge-danger font-size-sm ml-3">0</span>',
            'icon'     => 'ic_calendar',
            'selected' => in_array($route, ['account/calendar']),
            'href'     => $url_prefix . 'account/calendar'
        ];*/
        $menus[] = [
            'name'     => $this->language->get('tab_order') . '<!-- tab_order -->',
            'icon'     => 'ic_clock',
            'selected' => in_array($route, ['account/orders']),
            'href'     => $url_prefix . 'account/orders'
        ];
        $menus[] = [
            'name'     => $this->language->get('tab_address') . '<!-- tab_address -->',
            'icon'     => 'ic_address',
            'selected' => in_array($route, ['account/addresses']),
            'href'     => $url_prefix . 'account/addresses'
        ];
        /*$menus[] = [
            'name'     => 'Đánh giá sản phẩm', // $this->language->get('tab_favourite'),
            'icon'     => 'ic_like',
            'selected' => in_array($route, ['account/review']),
            'href'     => $url_prefix . 'account/review'// $this->url->plus("{$url_prefix}yeu-thich"),
        ];*/
        // Da bo tu khach hang
        /*$menus[] = [
            'name'     => 'Yêu thích', // $this->language->get('tab_favourite'),
            'icon'     => 'ic_heart',
            'selected' => in_array($route, ['account/wishlist']),
            'href'     => $url_prefix . 'account/wishlist'//$this->url->plus("{$url_prefix}yeu-thich"),
        ];*/
        /*$menus[] = [
            'name'     => 'Khuyến mãi', // $this->language->get('tab_favourite'),
            'icon'     => 'ic_promotion',
            'selected' => in_array($route, ['account/promotion']),
            'href'     => $url_prefix . 'account/promotion'// $this->url->plus("{$url_prefix}yeu-thich"),
        ];*/
        /*$menus[] = [
            'name'     => 'Góp ý/Than phiền', // $this->language->get('tab_feedback'),
            'icon'     => 'ic_note',
            'selected' => in_array($route, ['account/feedback']),
            'href'     => $url_prefix . 'account/feedback'
        ];*/
        /*$menus[] = [
            'name'     => 'FAQ',
            'icon'     => 'ic_faq',
            'selected' => in_array($route, ['account/faq']),
            'href'     => $url_prefix . 'account/faq'
        ];*/
        /*$menus[] = [
            'name'     => $this->language->get('tab_sms') . ' ' . '<span id="sms-badge" class="badge badge-danger font-size-sm ml-3">0</span>',
            'icon'     => 'ic_message',
            'selected' => in_array($route, ['account/sms']),
            'href'     => $url_prefix . 'account/sms',
        ];*/
        $data['menus'] = $menus;
        $data['user_invite_status'] = $this->config->get('config_user_invite_status');

        return $this->load->view('account/column_left', $data);
    }
}
