<?php
class ControllerPagePage extends Controller {
    public function index() {
        $this->load->language('page/page');
        $this->load->model('page/page');
        $data = $this->registry->get('global');
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => '/'];
        $id = isset($this->request->get['page_id']) ? (int)$this->request->get['page_id'] : 0;
        if (!$id) {
            $info = $this->model_page_page->getPageHome();
        } else {
            $info = $this->model_page_page->getPage($id);
        }
        // var_dump($info);exit();
        if ($info) {
            $this->document->setTitle($info['meta_title'] ? $info['meta_title'] : $info['name']);
            $this->document->setDescription($info['meta_description']);
            $this->document->setKeywords($info['meta_keyword']);
            $this->document->addLink($info['home'] ? $data['home_url'] : $info['href'], 'canonical');
            if (isset($info['raw_url'])) $this->document->setImage($info['raw_url']);
            $data['breadcrumbs'][] = ['text' => $info['name'], 'href' => $info['href']];
            $data['info'] = $info;
            $modules = [];
            $menus = [];
            if ($info['style']) $modules[] = ['code' => 'page/' . $info['style'], 'data' => $data];
        
            
            $pgConfig = $this->registry->get('pgConfig');
            
            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');

            $this->load->model('product/product');
            $this->load->model('product/category');
            $data['products'] = [];
            $products = $this->model_product_product->getProducts([]);
            $data['categories'] = $this->model_product_category->getCategories([]);
            foreach ($products as $product) {
                $product['images'] = $this->model_product_product->getProductImages($product['id']);
                $data['products'][] = $product;
            }
            
            $this->response->setOutput($this->load->view('page/page', $data));
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