<?php
class ControllerAccountLogout extends Controller {
    public function index() {
        if ($this->user->isLogged()) {
            $this->user->logout();
        }

        if (isset($this->request->get['returnUrl'])) {
            $returnUrl = urldecode($this->request->get['returnUrl']);
        } else {
            $returnUrl = '/';
        }

        $this->response->redirect($returnUrl);
    }
}
