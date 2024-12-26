<?php
class ModelSettingSetting extends Model {
    private $table = DB_PREFIX . 'setting';
    private $data = [
        ['keys' => []],
        ['codes' => []],
    ];

    private function getTransformer($key, $value) {
        if (is_array($value)) {
            $keys = [];
            foreach (array_keys($value) as $k) $keys[] = (string)$k;
            if (in_array($this->config->get('config_language'), $keys) || in_array($this->config->get('language_code_default'), $keys)) {
                $value = isset($value[$this->config->get('config_language')]) ? $value[$this->config->get('config_language')] : '';
                if (!$value) $value = isset($value[$this->config->get('language_code_default')]) ? $value[$this->config->get('language_code_default')] : '';
                return $value;
            }
        }/* else if (($key == 'pg_well_stats')) {
            $newVal = [];
            if (is_array($value)) foreach ($value as $item) {
                $d = isset($item[$this->config->get('config_language')]) ? $item[$this->config->get('config_language')] : [];
                if (!$d) $d = isset($item[$this->config->get('language_code_default')]) ? $item[$this->config->get('language_code_default')] : [];
                $newVal[] = [
                    'name'        => $d && isset($d['name']) ? $d['name'] : '',
                    'image_alt'   => $d && isset($d['image_alt']) ? $d['image_alt'] : '',
                    'description' => html_entity_decode($d && isset($d['description']) ? $d['description'] : ''),
                    'thumb_url'   => media_url_file(html_entity_decode(isset($item['image']) ? $item['image'] : '', ENT_QUOTES, 'UTF-8'))
                ];
            }
            return $newVal;
        }*/

        return $value;
    }

    public function getSetting($code) {
        $setting_data = isset($this->data['codes'][$code]) ? $this->data['codes'][$code] : [];
        if (!$setting_data) {
            $setting_data = [];
            $query = $this->db->query("select * from " . $this->table . " where `code` = '" . $this->db->escape($code) . "'");
            foreach ($query->rows as $result) {
                if (!$result['serialized']) {
                    $value = $result['value'];
                } else {
                    $value = json_decode($result['value'], true);
                }
                $value = $this->getTransformer($result['key'], $value);
                $setting_data[$result['key']] = $value;
                $this->data['keys'][$result['key']] = $value;
            }
            $this->data['codes'][$code] = $setting_data;
        }
        return $setting_data;
    }

    public function getSettingValue($key) {
        $value = isset($this->data['keys'][$key]) ? $this->data['keys'][$key] : null;
        if (!$value) {
            $query = $this->db->query("select value from " . $this->table . " where `key` = '" . $this->db->escape($key) . "'");
            if ($query->num_rows) {
                $value = $this->getTransformer($key, $query->row['value']);
                $this->data['keys'][$key] = $value;
            }
        }
        return $value;
    }

    /*public function getSettings($codes = []) {
        $setting_data = [];
        $query = $this->db->query("select * from " . $this->table . " where `code` in (" . $this->db->escape(implode(', ', $codes)) . ")");
        foreach ($query->rows as $result) {
            if (!$result['serialized']) {
                $value = $result['value'];
            } else {
                $value = json_decode($result['value'], true);
            }
            $setting_data[$result['key']] = $this->getTransformer($result['key'], $value);
        }
        return $setting_data;
    }*/
}
