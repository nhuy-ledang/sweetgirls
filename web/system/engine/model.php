<?php
/**
 * @package		MotilaCore
 * @author		HuyD
 * @copyright	Copyright (c) 2018 - 2020
 * @link		https://motila.vn
 */

/**
* Model class
*/
abstract class Model {
	protected $registry;

    /**
     * The attributes that should be hidden for serialization.
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [];

	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

    /**
     * Transform
     * @param array $item
     * @return array
     */
    protected function transform($item = []) {
        $newItem = [];
        foreach ($item as $k => $v) {
            if (!in_array($k, $this->hidden)) {
                if (isset($this->casts[$k])) {
                    $newItem[$k] = cast_helper($v, $this->casts[$k]);
                } else {
                    $newItem[$k] = $v;
                }
            }
        }

        return $newItem;
    }

    /**
     * Transforms
     * @param array $items
     * @return array
     */
    protected function transforms($items = []) {
        $newItems = [];
        foreach ($items as $item) {
            $newItems[] = $this->transform($item);
        }

        return $newItems;
    }

    /**
     * @param $row
     * @param $route
     * @param string $args
     * @return string
     */
    protected function getHref(&$row, $route, $args = '') {
        $suffix = $this->config->get('config_suffix_url');
        if (!empty($row['alias']) && $suffix) {
            if (substr($row['alias'], -(strlen($suffix) + 1)) == ".$suffix") {
                $suffix = false;
            }
        }
        $prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        $row['href'] = $this->config->get('config_url') . $prefix . (!empty($row['alias']) ? ($row['alias'] . ($suffix ? ".$suffix" : '')) : ($route . ($args ? '?' : '') . $args));

        return $row['href'];
    }

    /**
     * @param string $q
     *
     * @return array
     */
    public function getWords($q = '') {
        $words = [];
        //$q = explode(' ', trim(preg_replace('/\s+/', ' ', $q)));
        $q = utf8_strtolower($q);
        $q = str_replace(',', ' ', $q);
        $q = explode(' ', $q);
        foreach ($q as $v) {
            $v = trim($v);
            if (empty($v)) continue;
            $words[] = $v;
        }

        return $words;
    }
}