<?php
class ControllerCommonCart extends Controller {
    public function index() {
        $data['quantity'] = $this->cart->countProducts();

        return $this->load->view('common/cart', $data);
    }

    public function info() {
        $this->response->setOutput($this->index());
    }
}
