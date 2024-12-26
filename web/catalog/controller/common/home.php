<?php
class ControllerCommonHome extends Controller {
    public function index() {
        $this->document->setTitle($this->config->get('config_meta_title'));
        $this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->setKeywords($this->config->get('config_meta_keyword'));
        $this->document->addLink($this->config->get('config_url'), 'canonical');

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $data['global'] = $this->registry->get('global');

        $this->response->setOutput($this->load->view('common/home', $data));

    }

    public function landingpage() {
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
        $data['data'] = [[],[],[],[],[],[],];
        $this->response->setOutput($this->load->view('common/landingpage', $data));
    }
}