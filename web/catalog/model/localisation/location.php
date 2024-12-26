<?php

class ModelLocalisationLocation extends Model {
    private $table = DB_PREFIX . 'loc__locations';
    private $desc_table = DB_PREFIX . 'loc__location_desc';
    private $selectRaw = [
        '`l`.`id`',
        '`l`.`province_id`',
        '`l`.`district_id`',
        '`l`.`name`',
        '`l`.`address`',
        '`l`.`link`',
        '`l`.`comment`',
        '`l`.`email`',
        '`l`.`telephone`',
        '`l`.`geocode`',
        '`l`.`fax`',
        '`l`.`geocode`',
        '`l`.`image`',
        '`l`.`latitude`',
        '`l`.`longitude`',
        '`l`.`open`',
    ];

    protected function getTransformer($row) {
        $data = array_merge($row, [
            'address' => $this->trans->T($row['address']),
            'latitude' => (float)$row['latitude'],
            'longitude' => (float)$row['longitude'],
        ]);
        if (!empty($row['image'])) {
            $data = array_merge($row, [
                'small_url' => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'small'),
                'thumb_url' => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'thumb'),
                'raw_url'   => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8')),
            ]);
        }

        return $data;
    }

    public function getLocation($id) {
        $sql = "SELECT l.*";

        /*if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= ", ld.*";
        }*/

        $sql .= " FROM " . $this->table . " l";

        /*if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= " INNER JOIN " . $this->table . "_description ld ON (ld.id = l.id AND ld.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
        }*/

        $sql .= " WHERE l.id = '" . (int)$id . "'";

        $query = $this->db->query($sql);

        if ($query->num_rows) {
            return $this->getTransformer($query->row);
        } else {
            return false;
        }
    }

    public function getLocations($data = []) {

            $sql = "SELECT " . implode(', ', $this->selectRaw);

            if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
                $sql .= ", ld.*";
            }

            $sql .= " FROM " . $this->table . " l";

            if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
                $sql .= " INNER JOIN " . $this->desc_table . " ld ON (ld.id = l.id AND ld.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
            }

//            $sql .= " WHERE p.status = 1 AND p.parent_id = 0 AND p.sort_order > -1 ORDER BY p.sort_order, LCASE(p.name) ASC";
            $implodes = [];
            if (isset($data['filter_name']) && $data['filter_name'] !== '') {
                $implodes[] = "l.name like '%" . $data['filter_name'] . "%' or l.address like '%" . $data['filter_name'] . "%'";
            }
            if (!empty($implodes)) $sql .= " where " . implode(' and ', $implodes);
            $query = $this->db->query($sql);

            $data = [];
            foreach($query->rows as $row) {
                $data[] = $this->getTransformer($row);
            }

        return $data;
    }
    public function getAllLocation() {
        $sql = "SELECT l.province_id, l.district_id, p.name as province, d.name as district, p.latitude, p.longitude, d.location FROM " . $this->table . " l LEFT JOIN loc__provinces p ON(p.id = l.province_id) LEFT JOIN loc__districts d ON(d.id = l.district_id) WHERE l.province_id IS NOT NULL AND l.province_id <> 0 GROUP BY l.province_id, l.district_id ORDER BY province ASC";
        $query = $this->db->query($sql);

        return $query->rows;
    }
}
