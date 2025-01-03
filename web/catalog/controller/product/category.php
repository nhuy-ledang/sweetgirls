<?php
class ControllerProductCategory extends Controller {
    public function index($data = []) {
        $this->load->language('product/category');
        $this->load->model('product/product');
        $this->load->model('product/category');
        $this->load->model('page/page');

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => '/'];

        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $data['page'] = $page;
        $limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : 40;
        $data['limit'] = $limit;
        $filter_sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : '';
        if ($filter_sort) {
            $arr_filter_sort = explode(',', $filter_sort);
            if (isset($arr_filter_sort[1])) {
                $sort = '';
            } else {
                $arr_sort = explode('-', $filter_sort);
                $sort = isset($arr_sort[0])?$arr_sort[0]:'';
                $order = isset($arr_sort[1])?$arr_sort[1]:'';
            }
        }
        $data['sort'] = isset($sort)?'p.'.$sort:'';
        $data['order'] = isset($order)?$order:'';

        $q = isset($this->request->get['q']) ? (string)$this->request->get['q'] : '';
        $data['q'] = $q;
        $filter_name = isset($this->request->get['q']) ? $this->request->get['q'] : '';
        $data['filter_name'] = $filter_name;
        $filter_c = isset($this->request->get['c']) ? $this->request->get['c'] : '';
        $data['filter_c'] = $filter_c;
        $filter_p = isset($this->request->get['p']) ? $this->request->get['p'] : '';
        $data['filter_p'] = $filter_p;
        $filter_s = isset($this->request->get['s']) ? (string)$this->request->get['s'] : '';
        $data['filter_s'] = $filter_s;
        $data['count_filter_s'] = !empty($filter_s) ? count(explode(',', $filter_s)) : 0;
        $filter_min = isset($this->request->get['min']) ? $this->request->get['min'] : '';
        $data['filter_min'] = $filter_min;
        $filter_max = isset($this->request->get['max']) ? $this->request->get['max'] : '';
        $data['filter_max'] = $filter_max;

        $id = isset($this->request->get['category_id']) ? (int)$this->request->get['category_id'] : '';
        $data['id'] = $id;
        $info = false;
        if ($id) {
            $info = $this->model_product_category->getCategory($id);
            if ($info) {
                $data['info'] = $info;
            }
        } else {
            $info = [
                'meta_title'       => 'Tất cả sản phẩm',
                'meta_description' => '',
                'meta_keyword'     => '',
                'href'             => '/product/category',
                'name'             => 'Tất cả sản phẩm',
            ];
        }

        return $this->info($info, $data);
    }

    public function info($info, $data) {
        $this->document->setTitle($info['meta_title'] ? $info['meta_title'] : $info['name']);
        $this->document->setDescription($info['meta_description']);
        $this->document->setKeywords($info['meta_keyword']);
        $this->document->addLink($info['href'], 'canonical');
        $data['heading_title'] = $info['name'];
        $data['breadcrumbs'][] = ['text' => $info['name'], 'href' => $info['href']];

        $data['info'] = $info;
        if (isset($info['parent_id'])) {
            $data['parent_info'] = $this->model_product_category->getCategory($info['parent_id']);
        }

        $data['products'] = [];
        $products = $this->model_product_product->getProducts([]);
        foreach ($products as $product) {
            $product['images'] = $this->model_product_product->getProductImages($product['id']);
            $data['products'][] = $product;
        }

        $data['current_url'] = isset($info['href']) ? $info['href'] : '';
        $data['filter_q'] = $data['q'];

        $data['global'] = $this->registry->get('global');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('product/category', $data));
    }
}
