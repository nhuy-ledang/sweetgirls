<?php

/**
 * Class Translation
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class Translation {
    // private $table = DB_PREFIX . 'core__translates';
    // private $data = array();
    // private $translates = array();
    // private $hidden = [];
    // private $lang_code = 'vi';

    // public function __construct($registry, $lang_code = 'vi') {
    //     $this->db = $registry->get('db');
    //     $this->language = $registry->get('language');
    //     $this->cache = $registry->get('cache');
    //     $this->lang_code = $lang_code;

    //     $cache_data = false;//$this->cache->get("trans.$lang_code");
    //     if (!$cache_data) {
    //         $query = $this->db->query("SELECT * FROM `" . $this->table . "` WHERE `lang` = '$lang_code' ORDER BY `key` ASC, `translate` ASC");
    //         $cache_data = $query->rows;
    //         $this->cache->set("trans.$lang_code", $cache_data);
    //     }

    //     $data = [];
    //     foreach ($cache_data as $row) {
    //         $k = $row['key'];
    //         $v = $row['value'];
    //         if (in_array($k, $this->hidden)) {
    //             continue;
    //         }

    //         if (!isset($data[$k])) {
    //             $data[$k] = array();
    //         }
    //         $translate = html_entity_decode($row['translate'], ENT_QUOTES, 'UTF-8');
    //         $data[$k][$v] = $translate;
    //         $this->translates[trim(utf8_strtolower($v))] = $translate;
    //     }

    //     foreach ($data as $k => $v) {
    //         if (count($v) == 1) {
    //             foreach ($v as $k2 => $v2) {
    //                 $data[$k] = $v2;
    //             }
    //         } else {
    //             $newV = [];
    //             foreach ($v as $k2 => $v2) {
    //                 $newV[] = $v2;
    //             }
    //             $data[$k] = $newV;
    //         }

    //         $this->language->set($k, $data[$k]);
    //     }

    //     $this->data = $data;
    // }

    // public function get($key_code) {
    //     if (isset($this->data[$key_code])) {
    //         return $this->data[$key_code];
    //     } else {
    //         return '';
    //     }
    // }

    // public function T($v) {
    //     if (isset($this->translates[trim(utf8_strtolower($v))])) {
    //         return $this->translates[trim(utf8_strtolower($v))];
    //     } else {
    //         return $v;
    //     }
    // }
}
