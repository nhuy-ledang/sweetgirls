<?php
class ControllerStartupSeoUrl extends Controller {
    private $regex = [];

    public function index() {
        // Add rewrite to url class
        $this->url->addRewrite($this);
        $this->load->model('design/seo_url');
        $this->load->model('design/seo_regex');
        // Remove suffix
        $suffix = $this->config->get('config_suffix_url');
        if (isset($this->request->get['_route_']) && $suffix) {
            $this->request->get['_route_'] = str_replace(".$suffix", '', $this->request->get['_route_']);
        }
        // Redirect old urls
        /*if (isset($this->request->get['_route_'])) {
            $redirects = false; //$this->cache->get('redirect.all');
            if (!$redirects) {
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mkt__redirects`");
                $redirects = $query->rows;
                $this->cache->set('redirect.all', $redirects);
            }
            $obj = [];
            foreach ($redirects as $item) {
                $obj[ltrim($item['old_url'], '/')] = ltrim($item['new_url'], '/');
            }
            if (isset($obj[$this->request->get['_route_']])) {
                $new_url = $obj[$this->request->get['_route_']];
                $this->response->redirect($this->url->link("$new_url", '', true));
            }
        }*/
        // Load all regexes in the var so we are not accessing the db so much.
        $this->regex = $this->model_design_seo_regex->getSeoRegexes();
        if (isset($this->request->get['_route_'])) $this->request->get['_full_route_'] = $this->request->get['_route_'];
        // Set Language Default
        if (isset($this->request->get['_route_'])) {
            $parts = explode('/', $this->request->get['_route_']);
            // Remove any empty arrays from trailing
            if (utf8_strlen(end($parts)) == 0) array_pop($parts);
            // if (!empty($parts) && in_array($parts[0], array_column($this->config->get('core_language_list'),'code'))) {
            //     $this->request->get['lang'] = $parts[0];
            //     unset($parts[0]);
            //     if (empty($parts)) {
            //         unset($this->request->get['_route_']);
            //     } else {
            //         $this->request->get['_route_'] = implode('/', $parts);
            //     }
            // }
        }
        // Decode URL
        if (isset($this->request->get['_route_'])) {
            $parts = explode('/', $this->request->get['_route_']);
            // Access Direct
            if (count($parts) >= 2) {
                $fileExist = false;
                $file = DIR_APPLICATION . 'controller/' . implode('/', $parts) . '.php';
                if (is_file($file)) {
                    $fileExist = true;
                } else if (count($parts) >= 3) {
                    $newParts = $parts;
                    array_pop($newParts);
                    $file = DIR_APPLICATION . 'controller/' . implode('/', $newParts) . '.php';
                    if (is_file($file)) $fileExist = true;
                }
                if ($fileExist) {
                    $this->request->get['route'] = implode('/', $parts);
                    return;
                }
            }
            // remove any empty arrays from trailing
            if (utf8_strlen(end($parts)) == 0) array_pop($parts);
            foreach ($parts as $part) {
                $results = $this->model_design_seo_url->getSeoUrlsByKeyword($part);
                if ($results) {
                    foreach ($results as $result) {
                        $data = [];
                        // Push additional query string vars into GET data
                        parse_str($result['push'], $data);
                        foreach ($data as $key => $value) {
                            $this->request->get[$key] = $value;
                        }
                    }
                } else {
                    $this->request->get['route'] = 'error/not_found';
                    break;
                }
            }
        }
    }

    public function rewrite($link) {
        $url = '';
        $url_info = parse_url(str_replace('&amp;', '&', $link));
        parse_str($url_info['query'], $data);
        foreach ($this->regex as $result) {
            if (preg_match('/' . $result['regex'] . '/', $url_info['query'], $matches)) {
                array_shift($matches);
                foreach ($matches as $match) {
                    $results = $this->model_design_seo_url->getSeoUrlsByQuery($match, $this->config->get('config_language'));
                    if ($results) {
                        /*$languages = [];
                        foreach ($query->rows as $seo) {
                            if (!isset($languages[$seo['lang']])) $languages[$seo['lang']] = [];
                            $languages[$seo['lang']][$seo['query']] = $seo['keyword'];
                        }
                        var_dump($languages); exit();*/
                        foreach ($results as $seo) {
                            if (!empty($seo['keyword'])) $url .= '/' . $seo['keyword'];
                        }
                        parse_str($match, $remove);
                        // Remove all the matched url elements
                        foreach (array_keys($remove) as $key) {
                            if (isset($data[$key])) unset($data[$key]);
                        }
                    }
                }
            }
        }
        /*if (!$url) {
            foreach ($data as $key => $value) {
                if ($key == 'path') {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo__url WHERE `query` LIKE 'path=%_" . (int)$value . "' AND `lang` = '" . $this->db->escape($this->config->get('config_language')) . "'");
                    if ($query->num_rows && !empty($query->row['keyword'])) {
                        $url .= '/' . $query->row['keyword'];
                        unset($data[$key]);
                    }
                }
            }
        }*/
        if (!$url && isset($data['route'])) {
            $url .= '/' . $data['route'];
            unset($data['route']);
        }
        if ($url) {
            if ($this->config->get('config_language') != $this->config->get('language_code_default')) $url = '/' . $this->config->get('config_language') . $url;
            if (isset($data['lang'])) unset($data['lang']);
            if (isset($data['route'])) unset($data['route']);
            $query = '';
            if ($data) {
                foreach ($data as $key => $value) {
                    $query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode(is_array($value) ? http_build_query($value) : (string)$value);
                }
                if ($query) $query = '?' . str_replace('&', '&amp;', trim(str_replace('%2F', '/', $query), '&'));
            }
            $suffix = $this->config->get('config_suffix_url');
            if ($suffix) $suffix = '.' . $suffix;
            return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . ($url && $url != '/' ? "$suffix" : '') . $query;
        } else {
            if ($url_info['query'] == 'route=common/home') {
                if (($this->config->get('config_language') != $this->config->get('language_code_default'))) {
                    $link = $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . '/' . $this->config->get('config_language');
                } else {
                    $link = $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . '/';
                }
            }

            return $link;
        }
    }
}
