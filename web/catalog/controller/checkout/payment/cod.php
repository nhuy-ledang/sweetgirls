<?php
class ControllerCheckoutPaymentCod extends Controller {
    /*public function index() {
        return $this->load->view('extension/payment/cod');
    }

    public function confirm() {
        $json = [];

        if ($this->session->data['payment_method']['code'] == 'cod') {
            $this->load->model('product/order');

            $this->model_product_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_cod_order_status_id'));

            $json['redirect'] = $this->url->link('checkout/success', 'language=' . $this->config->get('config_language'));
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }*/
}