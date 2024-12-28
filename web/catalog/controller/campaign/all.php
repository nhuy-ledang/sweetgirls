<?php
class ControllerCampaignAll extends Controller {
    public function index() {
        $this->load->language('campaign/category');
        $this->load->model('campaign/campaign');
        $canonical = $this->url->link('campaign/all');
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = ['text' => 'Trang chủ', 'href' => '/'];
        $data['breadcrumbs'][] = ['text' => $this->language->get('heading_title'), 'href' => $canonical];
        $this->document->setTitle($this->language->get('heading_title'));
        $data['heading_title'] = $this->language->get('heading_title');
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = 12;
        $filter_data = [
            //'sort'  => 'n.created_at',
            'order' => 'ASC',
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        ];
        $campaign_total = $this->model_campaign_campaign->getTotalCampaigns($filter_data);
        $data['pages'] = $this->model_campaign_campaign->getCampaigns($filter_data);
        $url = '';
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $campaign_total,
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link('campaign/all', $url . 'page={page}')
        ]);
        $data['results'] = sprintf($this->language->get('text_pagination'), ($campaign_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($campaign_total - $limit)) ? $campaign_total : ((($page - 1) * $limit) + $limit), $campaign_total, ceil($campaign_total / $limit));
        // http://googlewebmastercentral.campaignspot.com/2011/09/pagination-with-relnext-and-relprev.html
        if ($page == 1) {
            $this->document->addLink($canonical, 'canonical');
        } else {
            $this->document->addLink($this->url->link('campaign/all', 'page=' . $page), 'canonical');
        }
        if ($page > 1) {
            $this->document->addLink($this->url->link('campaign/all', (($page - 2) ? 'page=' . ($page - 1) : '')), 'prev');
        }
        if ($limit && ceil($campaign_total / $limit) > $page) {
            $this->document->addLink($this->url->link('campaign/all', 'page=' . ($page + 1)), 'next');
        }
        $data['limit'] = $limit;

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('campaign/category', $data));
    }
}
