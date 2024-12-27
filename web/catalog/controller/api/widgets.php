<?php
class ControllerApiWidgets extends Controller {
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
        $widgets = [];
        // Compatibility code for old extension folders
        $files = glob(DIR_APPLICATION . 'controller/module/widget/*.php');
        if ($files) {
            foreach ($files as $file) {
                $filename = basename($file, '.php');
                $this->load->language('module/widget/' . $filename, 'widget');
                $configs = $this->load->controller('module/widget/' . $filename . '/configs');
                $widgets[] = [
                    'id'      => $filename,
                    'name'    => $this->language->get('widget')->get('heading_title'),
                    'preview' => $this->config->get('config_url') . "assets/templates/widgets/{$filename}/0.jpg",
                    'configs' => $configs,
                ];
            }
        }
        $sort_order = [];
        foreach ($widgets as $key => $value) {
            $sort_order[$key] = $value['name'];
        }
        array_multisort($sort_order, SORT_ASC, $widgets);

        $json = ['data' => ['data' => $widgets], 'errors' => null];

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
