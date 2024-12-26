<?php
class ControllerPageInformation extends Controller {
    public function index() {
        $this->load->language('page/information');

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => '/'
        ];

        if (isset($this->request->get['information_id'])) {
            $id = (int)$this->request->get['information_id'];
        } else {
            $id = 0;
        }

        $this->load->model('page/information');
        $info = $this->model_page_information->getInformation($id);
        if ($info) {
            $this->document->setTitle($info['meta_title'] ? $info['meta_title'] : $info['name']);
            $this->document->setDescription($info['meta_description']);
            $this->document->setKeywords($info['meta_keyword']);
            $this->document->addLink($info['href'], 'canonical');

            $data['breadcrumbs'][] = [
                'text' => $info['name'],
                'href' => $info['href']
            ];

            $data['info'] = $info;

            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('page/information', $data));
        } else {
            $this->document->setTitle($this->language->get('text_error'));

            $data['heading_title'] = $this->language->get('text_error');

            $data['text_error'] = $this->language->get('text_error');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }
}
