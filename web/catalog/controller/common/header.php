<?php
class ControllerCommonHeader extends Controller {
    private $version = '1.0.0';
    private $compile_version = '1689818851518';

    public function index($data = []) {
        $this->load->language('common/header');
        /*// Analytics
        $this->load->model('setting/extension');
        $data['analytics'] = [];
        $analytics = $this->model_setting_extension->getExtensions('analytics');
        foreach ($analytics as $analytic) {
            if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
                $data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
            }
        }*/
        $this->document->addLink($this->config->get('config_icon'), 'shortcut icon');
        $data['logo'] = $this->config->get('config_logo');
        $data['base'] = $this->config->get('config_url');
        $data['lang'] = $this->language->get('code');
        $data['direction'] = $this->language->get('direction');
        $data['name'] = $this->config->get('config_name');
        $data['facebook_id'] = $this->config->get('config_facebook_id');

        //<editor-fold desc="Meta Data">
        $data['title'] = $this->document->getTitle();
        $data['description'] = $this->document->getDescription();
        $data['keywords'] = $this->document->getKeywords();
        $data['links'] = $this->document->getLinks();
        $data['metaTags'] = $this->document->getMetaTags();
        $data['cssCodes'] = $this->document->getCssCodes();
        $image = $this->document->getImage();
        if ($image) {
            $data['image'] = $image;
        } elseif ($this->config->get('config_image')) {
            $data['image'] = $this->config->get('config_image');
        } else {
            $data['image'] = $data['logo'];
        }
        $data['image_alt'] = $this->document->getImageAlt();
        if (!$data['image_alt']) $data['image_alt'] = $data['name'];

        $data['richSnippets'] = $this->richSnippets->render();
        //</editor-fold>

        // Get style from theme
        /*if (!is_dir(DIR_TEMPLATE . $this->config->get('config_theme_directory') . '/assets/js/assets')) {

        }*/
        if (!is_dir(DIR_ROOT . 'assets/desktop/assets')) {
            $styles = [['rel' => 'stylesheet', 'media' => 'screen', 'href' => '/assets/desktop/dev/build/assets/app-' . $this->version . '.css?v=' . $this->compile_version]];
        } else {
            $styles = [['rel' => 'stylesheet', 'media' => 'screen', 'href' => '/assets/desktop/assets/app-' . $this->version . '.css?v=' . $this->compile_version]];
        }

        $data['styles'] = array_merge($styles, $this->document->getStyles(), [
            ['rel' => 'stylesheet', 'media' => 'screen', 'href' => '/styles.css'],
        ]);
        $data['scripts'] = array_merge([
            '/assets/js/common.js?v=' . $this->compile_version,
        ], $this->document->getScripts('header'));

        // For page specific css
        if (isset($this->request->get['route'])) {
            $route = $this->request->get['route'];
        } else {
            $route = $this->config->get('action_default');
        }
        $pid = '';
        if (isset($this->request->get['page_id'])) {
            $pid = '-' . $this->request->get['page_id'];
        } else if (isset($this->request->get['information_id'])) {
            $pid = '-' . $this->request->get['information_id'];
        } else if (isset($this->request->get['product_id'])) {
            $pid = '-' . $this->request->get['product_id'];
        } else if (isset($this->request->get['path'])) {
            $pid = '-' . $this->request->get['path'];
        } else if (isset($this->request->get['news_id'])) {
            $pid = '-' . $this->request->get['news_id'];
        } else if (isset($this->request->get['news_category_id'])) {
            $pid = '-' . $this->request->get['news_category_id'];
        }
        $part = str_replace('/', '-', $route);
        //$parts = explode('-', $part);
        //$partEnd = $parts[count($parts) - 1];
        $data['classes'] = /*'page-' . $partEnd . */'path--' . $part . ($pid ? (' id--' . $pid) : '');

        // Language
        if (isset($this->request->get['lang'])) {
            $language_code_default = $this->request->get['lang'];
        } else {
            $language_code_default = $this->config->get('language_code_default');
        }
        $current_link = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
        $language_list = $this->config->get('core_language_list');
        $data['languages'] = [];
        $data['current_language'] = [];
        if (strpos("$current_link/", "/$language_code_default/") !== false) {
            $format = 1;
        } else if (strpos($current_link, "lang=$language_code_default") !== false) {
            $format = 2;
        } else {
            $format = 0;
        }
        // foreach ($language_list as $item) {
        //     $code = $item['code'];
        //     if ($format == 1) {
        //         if ($code == $this->config->get('language_code_default')) {
        //             $href = rtrim(str_replace("/$language_code_default/", "/", "$current_link/"), '/');
        //         } else {
        //             $href = rtrim(str_replace("/$language_code_default/", "/$code/", "$current_link/"), '/');
        //         }
        //     } else if ($format == 2) {
        //         $href = str_replace("lang=$language_code_default", "lang=$code", $current_link);
        //     } else {
        //         if ($code == $this->config->get('language_code_default')) {
        //             $href = $this->config->get('config_url') . trim($this->request->server['REQUEST_URI'], '/');
        //         } else {
        //             $href = $this->config->get('config_url') . $code . rtrim($this->request->server['REQUEST_URI'], '/');
        //         }
        //     }
        //     $data['languages'][] = array_merge($item, ['href' => $href]);
        //     if ($code == $language_code_default) {
        //         $data['current_language'] = $item;
        //     }
        // }
        // $data['language_code_default'] = $language_code_default;

        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';

        // Menus
        // $this->load->model('page/menu');
        // $menus = $this->model_page_menu->getMenus();
        // foreach ($menus as $menu) {
        //     if ($menu['is_header']) {
        //         if ($menu['home']) {
        //             $menu['href'] = "/$url_prefix";
        //         }
        //         $data['menus'][] = $menu;
        //     }
        // }

        $this->load->model('setting/setting');
        $data['cart_status'] = $this->model_setting_setting->getSettingValue('pd_cart_status');

        // Global
        $data['global'] = $this->registry->get('global');
        if ($data['global']['is_mobile']) $data['classes'] .= " mobile";
        $data['initData'] = $this->registry->get('initData');
        $data['initData']['ROUTE'] = $route;
        $data['userData'] = $this->registry->get('userData');

        // Labels
        // $data['labels'] = ['lang' => $this->language->get('code')];
        // $directory = $language_list[array_search($this->config->get('config_language'), array_column($language_list, 'code'))]['filename'];
        // $file = DIR_LANGUAGE . $directory . '/js.php';
        // if (file_exists($file)) {
        //     $_ = [];
        //     require($file);
        //     foreach ($_ as $key => $value) {
        //         $data['labels'][$key] = $value;
        //     }
        //     /*$onepay_terms = $this->config->get('config_onepay_terms');
        //     if ($onepay_terms && is_array($onepay_terms) && $onepay_terms[$this->config->get('config_language')]) {
        //         $data['labels']['text_onepay_terms'] = $onepay_terms[$this->config->get('config_language')];
        //     }*/
        // }

        $data['compiled'] = $this->load->view('common/compiled');

        // Load header content
        $data['theme'] = '';
        if (!(!empty($data['cfg_full_body']) && $data['cfg_full_body'] == true)) {
            $data['theme'] = $this->load->controller('module/common/header', $data);
        }
        //var_dump($data['theme']); exit();

        return $this->load->view('common/header', $data);
    }
}
