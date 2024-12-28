<?php
class ControllerCampaignCampaign extends Controller {
    public function index() {
        $this->load->language('campaign/campaign');
        $this->load->model('campaign/campaign');
        $data = $this->registry->get('global');
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => 'Trang chủ', 'href' => '/'];
        $id = isset($this->request->get['campaign_id']) ? (int)$this->request->get['campaign_id'] : 0;
        $info = $this->model_campaign_campaign->getCampaign($id);
        if ($info) {
            $this->document->setTitle($info['meta_title'] ? $info['meta_title'] : $info['name']);
            $this->document->setDescription($info['meta_description']);
            $this->document->setKeywords($info['meta_keyword']);
            $data['breadcrumbs'][] = ['text' => $info['name'], 'href' => $info['href']];
            $data['info'] = $info;
            $modules = [];
            $menus = [];
            $this->load->model('campaign/campaign_module');
            $results = $this->model_campaign_campaign_module->getModules($info['id']);
            foreach ($results as $module) {
                if ($module['code']) {
                    $modules[] = ['code' => 'module/' . $module['code'], 'data' => array_merge($module, ['parent_info' => $info])];
                    if (isset($module['properties']['menu']) && $module['properties']['menu']) {
                        $menu_text = isset($module['menu_text']) ? $module['menu_text'] : '';
                        if (!$menu_text) $menu_text = isset($content['properties']['textMenu']) ? $module['properties']['textMenu'] : '';
                        $menus[] = ['id' => $module['id'], 'name' => isset($module['name']) ? $module['name'] : '', 'menu_name' => $menu_text];
                    }
                }
            }
            $data['modules'] = [];
            foreach ($modules as $module) {
                $module_data = $this->load->controller('module/' . $module['code'], $module['data']);
                if ($module_data) $data['modules'][] = $module_data;
            }
            $data['menu_list'] = $menus;

            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');
            $this->load->model('setting/setting');
            $data['logo'] = $this->config->get('config_logo');
            $data['lang'] = $this->config->get('config_language');
            $data['container'] = $this->model_setting_setting->getSettingValue('pg_header_cont');
            $data['global'] = $this->registry->get('global');
            $this->response->setOutput($this->load->view('campaign/campaign', $data));
        } else {
            $this->document->setTitle($this->language->get('text_error'));

            $data['heading_title'] = $this->language->get('text_error');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }
}
