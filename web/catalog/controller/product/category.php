<?php
class ControllerProductCategory extends Controller {
    public function index($data = []) {
        $this->load->language('product/category');
        $this->load->model('product/product');
        $this->load->model('product/category');
        $this->load->model('product/manufacturer');
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
        $filter_m = isset($this->request->get['m']) ? $this->request->get['m'] : '';
        $data['filter_m'] = $filter_m;
        $data['count_filter_m'] = !empty($filter_m) ? count(explode(',', $filter_m)) : 0;
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
        $filter_data = [
            //'filter_category'     => $id,
            'filter_categories'   => $data['filter_c'] ? $data['filter_c'] : $data['id'],
            'filter_name'         => $data['q'],
            'filter_manufacturer' => $data['filter_m'],
            'filter_stock_status' => $data['filter_s'],
            'filter_min'          => $data['filter_min'],
            'filter_max'          => $data['filter_max'],
            'filter_p'            => $data['filter_p'],
            'sort'                => $data['sort'],
            'order'               => $data['order'],
            'start'               => ($data['page'] - 1) * $data['limit'],
            'limit'               => $data['limit'],
        ];

        $product_total = $this->model_product_product->getTotalProducts($filter_data);
        // $data['products'] = $this->model_product_product->getProducts($filter_data);

        $data['products'] = [];
        $products = $this->model_product_product->getProducts($filter_data);
        foreach ($products as $product) {
            $product['images'] = $this->model_product_product->getProductImages($product['id']);
            $data['products'][] = $product;
        }

        $url = '';
        if (isset($this->request->get['c'])) $url .= '&c=' . $this->request->get['c'];
        if (isset($this->request->get['m'])) $url .= '&m=' . $this->request->get['m'];
        if (isset($this->request->get['p'])) $url .= '&p=' . $this->request->get['p'];
        if (isset($this->request->get['q'])) $url .= '&q=' . $this->request->get['q'];

        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $product_total,
            'page'  => $data['page'],
            'limit' => $data['limit'],
            'url'   => $info['href'] . $url . '&page={page}',
        ]);
        $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($data['page'] - 1) * $data['limit']) + 1 : 0, ((($data['page'] - 1) * $data['limit']) > ($product_total - $data['limit'])) ? $product_total : ((($data['page'] - 1) * $data['limit']) + $data['limit']), $product_total);

        // http://googlewebmastercentral.productspot.com/2011/09/pagination-with-relnext-and-relprev.html
        if ($data['page'] == 1) {
            $this->document->addLink($info['href'], 'canonical');
        } else {
            $this->document->addLink($info['href'] . '&page=' . $data['page'], 'canonical');
        }

        if ($data['page'] > 1) {
            $this->document->addLink($info['href'] . (($data['page'] - 2) ? '&page=' . ($data['page'] - 1) : ''), 'prev');
        }

        if ($data['limit'] && ceil($product_total / $data['limit']) > $data['page']) {
            $this->document->addLink($info['href'] . '&page=' . ($data['page'] + 1), 'next');
        }

        // Categories
        $data['categories'] = [];
        $categories = $this->model_product_category->getCategories(['filter_parent' => 0]);
        foreach ($categories as $category) {
            $category['childs'] = [];
            $childs = $this->model_product_category->getCategories(['filter_parent' => $category['id']]);
            foreach ($childs as $child) {
                $child['count_item'] = $this->model_product_product->getTotalProducts(['filter_category' => $child['id'],]);
                $category['childs'][] = $child;
            }
            $category['count_item'] = $this->model_product_product->getTotalProducts(['filter_category' => $category['id'],]);
            $data['categories'][] = $category;
        }

        // Manufacturers
        $data['manufacturers'] = [];
        $manufacturers = $this->model_product_manufacturer->getManufacturers();
        foreach ($manufacturers as $manufacturer) {
            $manufacturer['count_item'] = $this->model_product_product->getTotalProducts(['filter_name' => $data['q'], 'filter_c' => $data['filter_c'], 'filter_manufacturer' => $manufacturer['id'], 'filter_p' => $data['filter_p']]);
            $data['manufacturers'][] = $manufacturer;
        }

        $data['current_url'] = isset($info['href']) ? $info['href'] : '';
        $data['filter_q'] = $data['q'];

        $data['global'] = $this->registry->get('global');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->load->model('setting/setting');
        $data['cart_status'] = $this->model_setting_setting->getSettingValue('pd_cart_status');
        $data['category_status'] = $this->model_setting_setting->getSettingValue('pd_category_status');
        $data['manufacturer_status'] = $this->model_setting_setting->getSettingValue('pd_manufacturer_status');
        $data['primary_color'] = $this->model_setting_setting->getSettingValue('pd_category_primary_color');
        $data['background_image'] = $this->model_setting_setting->getSettingValue('pd_category_bg_image') ? media_url_file(html_entity_decode($this->model_setting_setting->getSettingValue('pd_category_bg_image'), ENT_QUOTES, 'UTF-8')) : '';
        $data['frame'] = $this->model_setting_setting->getSettingValue('pd_frame');

        $this->response->setOutput($this->load->view('product/category', $data));
    }
}
