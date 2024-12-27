<?php
class ControllerStartupStartup extends Controller {
    private function getSetting($key, $value) {
        if ($key == 'config_logo') {
            $value = $value ? media_url_file(html_entity_decode($value, ENT_QUOTES, 'UTF-8')) : '/assets/logo/logo.svg';
        } else if ($key == 'config_icon') {
            $value = $value ? media_url_file(html_entity_decode($value, ENT_QUOTES, 'UTF-8')) : '/favicon.ico';
        } else if ($key == 'config_image') {
            $value = $value ? media_url_file(html_entity_decode($value, ENT_QUOTES, 'UTF-8')) : '';
        } else if ($key == 'config_bg_login') {
            $value = $value ? media_url_file(html_entity_decode($value, ENT_QUOTES, 'UTF-8')) : '';
        } else if ($key == 'config_icon_marker') {
            $value = $value ? media_url_file(html_entity_decode($value, ENT_QUOTES, 'UTF-8')) : '';
        } else if ($key == 'config_icon_vendor') {
            $value = $value ? media_url_file(html_entity_decode($value, ENT_QUOTES, 'UTF-8')) : '';
        } else if ($key == 'config_wheel') {
            $value = $value ? media_url_file(html_entity_decode($value, ENT_QUOTES, 'UTF-8')) : '';
        } else if ($key == 'config_wheel_bg') {
            $value = $value ? media_url_file(html_entity_decode($value, ENT_QUOTES, 'UTF-8')) : '';
        } else if ($key == 'config_suffix_url') {
            $value = $value ? strtolower(trim(trim(trim($value), '.'))) : false;
        } else {
            if (is_array($value)) {
                $keys = [];
                foreach (array_keys($value) as $k) $keys[] = (string)$k;
                if (in_array($this->config->get('config_language'), $keys) || in_array($this->config->get('language_code_default'), $keys)) {
                    $newVal = isset($value[$this->config->get('config_language')]) ? $value[$this->config->get('config_language')] : '';
                    if (!$newVal) $newVal = isset($value[$this->config->get('language_code_default')]) ? $value[$this->config->get('language_code_default')] : '';
                    if ($newVal && is_string($newVal)) $newVal = html_entity_decode($newVal, ENT_QUOTES, 'UTF-8');
                    return $newVal;
                }
            }
        }

        return $value;
    }

    private function getPgConfig(&$pgConfig) {
        if (!empty($pgConfig['title_font'])) $pgConfig['title_font'] = 'ff_' . $pgConfig['title_font'];
        if (!empty($pgConfig['title_sub_font'])) $pgConfig['title_sub_font'] = 'ff_' . $pgConfig['title_sub_font'];
        if (!empty($pgConfig['title_icon'])) $pgConfig['title_icon'] = $pgConfig['title_icon'] ? media_url_file(html_entity_decode($pgConfig['title_icon'], ENT_QUOTES, 'UTF-8')) : '';

        return $pgConfig;
    }

	public function index() {
        // Store
        $this->config->set('config_url', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? HTTPS_SERVER : HTTP_SERVER);

        /*// Language
        $_['language_directory']   = 'vi-vn';
        $_['language_autoload']    = ['vi-vn'];
        $_['language_list']        = [
            ['code' => 'en', 'name' => 'English', 'directory'=>'en-gb', 'locale' => 'en_US.UTF-8,en_US,en-gb,english'],
        ];
        $_['language_list_all']    = [
            ['code' => 'vi', 'name' => 'Tiếng Việt', 'directory' => 'vi-vn', 'locale' => 'vi_VN.UTF-8,vi_VN,vi-vn,vietnamese'],
            ['code' => 'en', 'name' => 'English', 'directory' => 'en-gb', 'locale' => 'en_US.UTF-8,en_US,en-gb,english'],
        ];
        $_['language_code_all']    = [
            'vi' => 'vi-vn',
            'en' => 'en-gb',
        ];
        $_['language_code_default']= 'vi';*/

        // Language
        /*$language_code = $this->config->get('language_code_default');
        if (isset($this->request->get['_route_'])) {
            $parts = explode('/', $this->request->get['_route_']);
            // Remove any empty arrays from trailing
            if (utf8_strlen(end($parts)) == 0) array_pop($parts);
            if (!empty($parts) && in_array($parts[0], array_keys($this->config->get('language_code_all')))) $language_code = $parts[0];
        } else {
            if (isset($this->request->get['lang']) && in_array($this->request->get['lang'], array_keys($this->config->get('language_code_all')))) $language_code = $this->request->get['lang'];
        }
        $language_directory = $this->config->get('language_code_all')[$language_code];
        $this->config->set('config_language', $language_code);
        $language = new Language($language_directory);
        $language->load($language_directory);
        $this->registry->set('language', $language);*/

        // Language
        // $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "core__languages` WHERE `status` = 1 ORDER BY `sort_order` ASC");
        // $language_list = $query->rows;
        // $this->config->set('core_language_list', $language_list);
        // $language_code = $this->config->get('language_code_default');
        // if (isset($this->request->get['_route_'])) {
        //     $parts = explode('/', $this->request->get['_route_']);
        //     // Remove any empty arrays from trailing
        //     if (utf8_strlen(end($parts)) == 0) array_pop($parts);
        //     if (!empty($parts) && in_array($parts[0], array_column($language_list,'code'))) $language_code = $parts[0];
        // } else {
        //     if (isset($this->request->get['lang']) && in_array($this->request->get['lang'], array_column($language_list,'code'))) $language_code = $this->request->get['lang'];
        // }
        // $language_key = array_search($language_code, array_column($this->config->get('language_list'), 'code'));
        // $language_directory = $language_list[array_search($this->config->get('config_language'), array_column($language_list, 'code'))]['filename'];
        // $this->config->set('config_language', $language_code);
        // $language = new Language($language_directory);
        // $language->load($language_directory);
        // $this->registry->set('language', $language);

        // Global variables
        $global = [];
        $pgConfig = [];

        // Settings
        $setting_data = false;//$this->cache->get('setting.all');
        if (!$setting_data) {
            $query = $this->db->query("select * from `" . DB_PREFIX . "setting` where `code` = 'config' or `code` = 'pg' order by `key` asc");
            $setting_data = $query->rows;
            //$this->cache->set('setting.all', $setting_data);
        }
        foreach ($setting_data as $result) {
            $value = !$result['serialized'] ? $result['value'] : json_decode($result['value'], true);
            if ($result['code'] == 'config') {
                $value = $this->getSetting($result['key'], $value);
                $this->config->set($result['key'], $value);
                $global[$result['key']] = $value;
            } else if ($result['code'] == 'pg') {
                $pgConfig[str_replace('pg_', '', $result['key'])] = $value;
            }
        }
        $pgConfig = $this->getPgConfig($pgConfig);
        $this->registry->set('pgConfig', $pgConfig);

        // Config Override
        $file = DIR_CONFIG . 'env/' . APP_ENV . '.php';
        if (file_exists($file)) {
            $_ = [];
            require($file);
            foreach ($_ as $key => $value) {
                $this->config->set($key, $value);
                $global[$key] = $value;
            }
        }

        // Is mobile
        $global['is_mobile'] = is_mobile();
        $this->config->set('is_mobile', $global['is_mobile']);

		// Set time zone
		if ($this->config->get('config_timezone')) {
			date_default_timezone_set($this->config->get('config_timezone'));
			// Sync PHP and DB time zones.
			$this->db->query("SET time_zone = '" . $this->db->escape(date('P')) . "'");
		}

		// Response output compression level
		if ($this->config->get('config_compression')) $this->response->setCompression($this->config->get('config_compression'));

        // Theme
        $this->config->set('template_cache', $this->config->get('developer_theme'));

		// Url
		$this->registry->set('url', new Url($this->config->get('config_url')));

        // Language
        // $this->config->set('config_language', $language_code);

		// Encryption
		$this->registry->set('encryption', new Encryption());

        // Shortcodes
        $this->registry->set('shortcodes', new Shortcodes($this->registry));

		// Translation
        $this->registry->set('trans', new Translation($this->registry, $this->config->get('config_language')));

        //<editor-fold desc="Start Cart">
        // User
        $this->registry->set('user', new User($this->registry));

        // Logged in users
        $this->config->set('config_user_group_id', 1);
        /*if ($this->user->isLogged()) {
            $this->config->set('config_user_group_id', $this->user->getGroupId());
        } else if (isset($this->session->data['guest']) && isset($this->session->data['guest']['user_group_id'])) {
            $this->config->set('config_user_group_id', $this->session->data['guest']['user_group_id']);
        } else {
            $this->config->set('config_user_group_id', 1);
        }*/

        // Currency
        $code = 'VND';

        $this->session->data['currency'] = $code;

        // Set a new currency cookie if the code does not match the current one
        if (!isset($this->request->cookie['currency']) || $this->request->cookie['currency'] != $code) {
            setcookie('currency', $code, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
        }

        $this->registry->set('currency', new Currency($this->registry));

        // Cart
        $this->registry->set('cart', new Cart\Cart($this->registry));
        //</editor-fold>

        // Breadcrumbs
        //$this->registry->set('breadcrumbs', new Breadcrumbs());

        // RichSnippets
        $this->registry->set('richSnippets', new RichSnippets($this->config));

        $latlng = $this->config->get('config_googlemap_latlng');
        if (empty($latlng)) $latlng = '10.808214762585886, 106.70713978256222';
        $data['latlng'] = [];
        foreach (explode(',', $latlng) as $item) {
            $data['latlng'][] = (float)trim($item);
        }

        // Set global
        $url_prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        $home_url = rtrim($this->config->get('config_url') . $url_prefix, '/');
        $this->registry->set('global', array_merge($global, [
            'url_prefix' => $url_prefix,
            'home_url'   => $home_url,
            'lang' => $this->config->get('config_language'),
            'latlng' => $data['latlng'],
            'actual_link' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
        ]));

        $initData = [
            'URL'                  => rtrim($this->config->get('config_url'), '/'),
            'APP_URL'              => $home_url,
            'API_URL'              => API_URL,
            'FACEBOOK_APP_ID'      => $this->config->get('config_facebook_app_id'),
            'FACEBOOK_APP_VERSION' => $this->config->get('config_facebook_version'),
            'GOOGLE_CLIENT_ID'     => $this->config->get('config_google_client_id'),
            'reCAPTCHA_SITE_KEY'   => reCAPTCHA_SITE_KEY,
            'urlPrefix'            => $url_prefix,
            //'currentUrl'           => "//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
            'loginUrl'             => "$home_url/login",
            'returnUrl'            => isset($this->session->data['redirect']) ? $this->session->data['redirect'] : $home_url,
            'locale'               => $this->config->get('config_language'),
            'login_popup'          => (int)$this->config->get('config_login_popup'),
            'login_first'          => (int)$this->config->get('config_login_first'),
        ];

        $this->registry->set('initData', $initData);
	}

    public function user() {
        //<editor-fold desc="User Logged">
        $logged = $this->user->isLogged();
        $data = $this->user->getData();
    
        $this->registry->set('userData', ['logged' => $logged, 'info' => $data]);
    }
}
