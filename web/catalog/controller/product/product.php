<?php
class ControllerProductProduct extends Controller {
    public function index($data = []) {
        $this->load->language('product/category');
        $this->load->language('product/product');

        $this->load->model('product/product');
        $this->load->model('product/category');
        $this->load->model('product/manufacturer');

        $id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
        $info = $this->model_product_product->getProduct($id);
        if ($info) {
            $this->document->setTitle($info['meta_title'] ? $info['meta_title'] : $info['name']);
            $this->document->setDescription($info['meta_description']);
            $this->document->setKeywords($info['meta_keyword']);
            $this->document->addLink($info['href'], 'canonical');
            $this->session->data['redirect'] = $info['href'];
            if (isset($info['raw_url'])) $this->document->setImage($info['raw_url']);

            $data['info'] = $info;
            $data['id'] = $id;

            // Root Id
            if (isset($info['categories'])) {
                $paths = explode(',', $info['categories']);
                $root_id = (int)$paths[0];
                $data['root_info'] = $this->model_product_category->getCategory($root_id);
            }

            $data['category_info'] = $this->model_product_category->getCategory($info['category_id']);
            if ($info['manufacturer_id']) $data['manufacturer_info'] = $this->model_product_manufacturer->getManufacturer($info['manufacturer_id']);

            $data['breadcrumbs'] = [];
            $data['breadcrumbs'][] = ['text' => $this->language->get('text_home'), 'href' => '/'];
            if ($data['category_info']) {
                $data['breadcrumbs'][] = ['text' => $data['category_info']['name'], 'href' => $data['category_info']['href']];
            }
            $data['breadcrumbs'][] = ['text' => $info['name'], 'href' => ''];

            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');
            $data['global'] = $this->registry->get('global');

            // Get master id
            if ($info['master_id']) {
                $id = $info['master_id'];
                $data['master_info'] = $this->model_product_product->getProduct($id);
            }

            $data['product_options'] = [];
            $product_options = $this->model_product_product->getProductOptions($id);
            if ($product_options) {
                foreach ($product_options as $product_option) {
                    $product_option['products'] = array_values(array_column($product_option['products'], null, 'option_value_id'));
                    $data['unique_options'][] = $product_option;
                }
            }
            $data['product_options'] = $product_options;
            $data['product_images'] = $this->model_product_product->getProductImages($id);
            // var_dump($data['product_images']);exit();
            $data['product_relateds'] = $this->model_product_product->getProductRelated($id);

            // Review
            $this->load->model('product/review');
            $data['reviews'] = [];
            $product_child = $this->model_product_product->getIdsProductChild($id);
            $ids = $id . ($product_child ? ',' . implode(',', $product_child) : '');
            $reviews = $this->model_product_review->getReviews(['filter_product_id' => $ids]);
            if ($reviews) {
                foreach ($reviews as $review) {
                    $review['images'] = $this->model_product_review->getReviewImages($review['id']);
                    $data['reviews'][] = $review;
                }
                // Analysis
                $totalReviews = count($reviews);
                $data['review_analysis'] = [];
                for ($rating = 1; $rating <= 5; $rating++) {
                    $count = count(array_filter($reviews, function($review) use ($rating) {
                        return $review['rating'] == $rating;
                    }));
                    $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                    $data['review_analysis'][] = ['rating' => $rating, 'percent' => $percentage . '%', 'count' => $count];
                }
            }

            /*$data['infoData'] = [
                'id'         => $info['id'],
                'liked'      => $info['liked'],
                'totalLikes' => $info['totalLikes'],
                'href'       => $info['href'],
            ];*/
            $data['infoData'] = $info;

            $this->response->setOutput($this->load->view('product/product', $data));
        } else {
            if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
                $this->response->redirect($this->config->get('config_url') . $this->config->get('config_language'));
            }

            $this->document->setTitle($this->language->get('text_error'));
            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['header'] = $this->load->controller('common/header');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }

    public function viewed() {
        if (isset($this->request->get['product_id'])) {
            $product_id = (int)$this->request->get['product_id'];
        } else {
            $product_id = 0;
        }

        if ($product_id) {
            $this->load->model('product/product');
            $this->model_product_product->updateViewed($product_id);
        }
    }
}
