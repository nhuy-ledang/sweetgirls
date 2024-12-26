<?php
class ControllerCheckoutFailure extends Controller {
    public function index() {
        $this->load->language('checkout/failure');

        $this->document->setTitle($this->language->get('heading_title'));

        $id = isset($this->request->get['id']) ? (int)$this->request->get['id'] : 0;
        $this->load->model('checkout/order');
        $data['info'] = $this->model_checkout_order->getOrder($id);

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('checkout/failure', $data));
    }
}
