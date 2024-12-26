<?php
class ControllerApiContact extends Controller {
    public function index() {
        $json = array();
        $error = array();

        $request = json_decode(file_get_contents("php://input"));

        /*if (!(!empty($request->email) && filter_var($request->email, FILTER_VALIDATE_EMAIL))) {
            $error['email'] = 'Email';
        }*/

        if (!empty($error)) {
            $json['error'] = $error;
            $this->response->addHeader('HTTP/1.1 400 Bad Request');
        } else {
            $this->load->model('catalog/contact');
            $json['result'] = $this->model_catalog_contact->addContact(array(
                'name'    => isset($request->name) ? $request->name : '',
                'company' => isset($request->company) ? $request->company : '',
                'email'   => $request->email,
                'phone'   => isset($request->phone) ? $request->phone : '',
                'message' => isset($request->message) ? $request->message : '',
            ));
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
