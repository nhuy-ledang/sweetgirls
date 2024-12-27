<?php
class ControllerApiModules extends Controller {
    protected function cors() {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            // may also be using PUT, PATCH, HEAD etc
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) header("Access-Control-Allow-Headers: '{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}'");

            exit(0);
        }
    }

    public function index() {
        $this->cors();
        $modules = [];
        // Compatibility code for old extension folders
        $files = glob(DIR_APPLICATION . 'controller/module/module/*.php');
        if ($files) {
            foreach ($files as $file) {
                $extension = basename($file, '.php');
                $this->load->language('module/module/' . $extension, 'extension');
                $configs = $this->load->controller('module/module/' . $extension . '/configs');
                if (!($configs && isset($configs['delegated']) && $configs['delegated'] === true)) {
                    $modules[] = [
                        'id'      => $extension,
                        'name'    => $this->language->get('extension')->get('heading_title'),
                        'order'   => $this->language->get('extension')->get('order'),
                        'configs' => $configs,
                        'layouts' => $this->load->controller('module/module/' . $extension . '/layouts'),
                    ];
                }
            }
        }
        $sort_order = [];
        foreach ($modules as $key => $value) {
            $sort_order[$key] = $value['order'];
        }
        array_multisort($sort_order, SORT_ASC, $modules);

        $pages = [];
        // Compatibility code for old extension folders
        $files = glob(DIR_APPLICATION . 'controller/module/page/*.php');
        if ($files) {
            foreach ($files as $file) {
                $extension = basename($file, '.php');
                $this->load->language('module/page/' . $extension, 'extension');
                $pages[] = [
                    'id'      => $extension,
                    'name'    => $this->language->get('extension')->get('heading_title'),
                    'order'   => $this->language->get('extension')->get('order'),
                    'configs' => $this->load->controller('module/page/' . $extension . '/configs'),
                    'layouts' => $this->load->controller('module/page/' . $extension . '/layouts'),
                ];
            }
        }
        $sort_order = [];
        foreach ($pages as $key => $value) {
            $sort_order[$key] = $value['name'];
        }
        array_multisort($sort_order, SORT_ASC, $pages);

        $json = ['data' => ['data' => [
            'modules' => $modules,
            'pages'   => $pages,
            'headers' => $this->load->controller('module/common/header/themes'),
            'footers' => $this->load->controller('module/common/footer/themes'),
            'titles'  => $this->load->controller('module/common/title'),
            'fonts'   => $this->load->controller('module/common/title/font'),
            'buttons' => $this->load->controller('module/common/button'),
            'dots' => $this->load->controller('module/common/dot'),
            'arrows' => $this->load->controller('module/common/arrow'),
        ]], 'errors'    => null];

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
