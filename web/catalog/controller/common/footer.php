<?php
class ControllerCommonFooter extends Controller {
    public function index($data = []) {
        $this->load->language('common/footer');

        $data['address'] = $this->config->get('config_address');
        $data['email'] = $this->config->get('config_email');
        $data['hotline'] = $this->config->get('config_hotline');
        $data['telephone'] = $this->config->get('config_telephone');
        $data['recruit'] = $this->config->get('config_recruit');
        $data['facebook_url'] = $this->config->get('config_facebook_url');
        $data['youtube_url'] = $this->config->get('config_youtube_url');
        $data['linkedin_url'] = $this->config->get('config_linkedin_url');
        $data['zalo_url'] = $this->config->get('config_zalo_url');
        $data['alibaba_url'] = $this->config->get('config_alibaba_url');
        $data['copyright'] = $this->config->get('config_copyright');
        $data['logo'] = $this->config->get('config_logo');

        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $data['lang'] = $this->config->get('config_language');
        } else {
            $data['lang'] = '';
        }

        $data['global'] = $this->registry->get('global');
        $this->load->model('setting/setting');
        $data['cart_status'] = $this->model_setting_setting->getSettingValue('pd_cart_status');

        $data['mkt'] = [];
        $mkt = $this->model_setting_setting->getSetting('mkt');
        foreach ($mkt as $k => $v) {
            if ($k == 'mkt_popup_image') {
                $data['mkt']['mkt_popup_image_thumb_url'] = $v ? media_url_file(html_entity_decode($v, ENT_QUOTES, 'UTF-8')) : '';
            }
            $data['mkt'][$k] = $v;
        }

        $data['scripts'] = array_merge($this->document->getScripts('footer'));

        //$data['compiled'] = $this->load->view('common/compiled');

        // Load footer content
        $data['footer_content'] = '';
        if (!(!empty($data['cfg_full_body']) && $data['cfg_full_body'] == true)) {
            $data['footer_content'] = $this->load->controller('module/common/footer', $data);
        }

        return $this->load->view('common/footer', $data);
    }
}
