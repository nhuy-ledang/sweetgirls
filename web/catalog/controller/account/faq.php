<?php
class ControllerAccountFaq extends Controller {
    public function index() {
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        if (!$this->user->isLogged()) $this->response->redirect("/{$url_prefix}register");
        $this->load->language('account/faq');
        $this->load->model('catalog/faq');
        $this->load->model('catalog/faq_content');
        $this->document->setTitle($this->language->get('heading_title'));

        $data['faqs'] = $this->model_catalog_faq->getFaqs();
        /*$faqs = $this->model_catalog_faq->getFaqs();
        $data['faqs'] = [];
        foreach ($faqs as $faq) {
            $data['faqs'][] = array_merge($faq, [
                'table_of_contents' => $this->model_catalog_faq_content->getContents($faq['id']),
            ]);
        }*/

        $userData = $this->registry->get('userData');
        $data['userInfo'] = $userData['info'];
        $data['module_profile'] = $this->load->view('account/profile/profile', $data);
        $data['column_left'] = $this->load->controller('account/column_left');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('account/faq', $data));
    }
}
