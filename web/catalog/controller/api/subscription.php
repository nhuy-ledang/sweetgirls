<?php

class ControllerApiSubscription extends Controller {
    public function index() {
        $json = array();

        $request = json_decode(file_get_contents("php://input"));

        if (!empty($request->email) && filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $this->load->model('catalog/subscription');
            $json['result'] = $this->model_catalog_subscription->addSubscription($request->email);
        } else {
            $json['result'] = false;
            $this->response->addHeader('HTTP/1.1 400 Bad Request');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
