<?php
class ControllerProductSearch extends Controller {
    public function index($data = []) {
        $this->load->language('product/category');
        $this->load->model('product/product');
        $this->load->model('product/category');
        $this->load->model('product/manufacturer');

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => 'Trang chủ', 'href' => '/'];

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }
        $data['page'] = $page;

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

        $limit = 40;

        $this->document->setTitle('Search');
        $this->document->setKeywords($q);
        $this->document->addLink($this->url->link('product/search'), 'canonical');
        $data['heading_title'] = 'Search';
        $data['breadcrumbs'][] = ['text' => 'Search', 'href' => ''];

        $filter_data = [
            'filter_name'         => $q,
            'filter_categories'   => $filter_c,
            'filter_manufacturer' => $filter_m,
            'filter_stock_status' => $filter_s,
            'filter_min'          => $filter_min,
            'filter_max'          => $filter_max,
            'filter_p'            => $filter_p,
            'sort'                => $data['sort'],
            'order'               => $data['order'],
            'start'               => ($page - 1) * $limit,
            'limit'               => $limit,
        ];

        $product_total = $this->model_product_product->getTotalProducts($filter_data);
        $data['products'] = $this->model_product_product->getProducts($filter_data);

        $url = '';

        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $product_total,
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link('product/search',  $url . '&page={page}')
        ]);
        $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total);

        // http://googlewebmastercentral.productspot.com/2011/09/pagination-with-relnext-and-relprev.html
        if ($page == 1) {
            $this->document->addLink($this->url->link('product/search'), 'canonical');
        } else {
            $this->document->addLink($this->url->link('product/search','&page=' . $page), 'canonical');
        }

        if ($page > 1) {
            $this->document->addLink($this->url->link('product/search', (($page - 2) ? '&page=' . ($page - 1) : '')), 'prev');
        }

        if ($limit && ceil($product_total / $limit) > $page) {
            $this->document->addLink($this->url->link('product/search','&page=' . ($page + 1)), 'next');
        }

        $data['limit'] = $limit;
        $data['current_url'] = 'product/search';

        // Categories
        $data['categories'] = [];
        $categories = $this->model_product_category->getCategories(['filter_parent' => 0]);
        foreach ($categories as $category) {
            $category['childs'] = $this->model_product_category->getCategories(['filter_parent' => $category['id']]);
            $category['count_item'] = $this->model_product_product->getTotalProducts(['filter_category' => $category['id'],]);
            $data['categories'][] = $category;
        }

        $data['manufacturers'] = $this->model_product_manufacturer->getManufacturers();

        $data['global'] = $this->registry->get('global');
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->load->model('setting/setting');
        $data['category_status'] = $this->model_setting_setting->getSettingValue('pd_category_status');
        $data['manufacturer_status'] = $this->model_setting_setting->getSettingValue('pd_manufacturer_status');

        $this->response->setOutput($this->load->view('product/category', $data));
    }
}
