<?php
class ModelDesignSeoUrl extends Model {
    private $table = DB_PREFIX . 'seo__url';

    public function getSeoUrl($seo_url_id) {
        $query = $this->db->query("SELECT * FROM `" . $this->table . "` WHERE `seo_url_id` = '" . (int)$seo_url_id . "'");

        return $query->row;
    }

    public function getSeoUrls($data = []) {
        $sql = "SELECT * FROM `" . $this->table . "`";

        $implode = [];

        if (!empty($data['filter_keyword'])) {
            $implode[] = "`keyword` LIKE '" . $this->db->escape((string)$data['filter_keyword']) . "'";
        }

        if (!empty($data['filter_query'])) {
            $implode[] = "`query` LIKE '" . $this->db->escape((string)$data['filter_query']) . "'";
        }

        if (!empty($data['filter_language'])) {
            $implode[] = "`lang` = '" . (int)$data['filter_language'] . "'";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $sort_data = ['keyword', 'query'];

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY `sort_order`";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalSeoUrls($data = []) {
        $sql = "SELECT COUNT(*) AS total FROM `" . $this->table . "`";

        $implode = [];

        if (!empty($data['filter_keyword'])) {
            $implode[] = "`keyword` LIKE '" . $this->db->escape((string)$data['filter_keyword']) . "'";
        }

        if (!empty($data['filter_query'])) {
            $implode[] = "`query` LIKE '" . $this->db->escape((string)$data['filter_query']) . "'";
        }

        if (!empty($data['filter_language'])) {
            $implode[] = "`lang` = '" . (int)$data['filter_language'] . "'";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getSeoUrlsByKeyword($keyword) {
        $query = $this->db->query("SELECT * FROM `" . $this->table . "` WHERE `keyword` = '" . $this->db->escape($keyword) . "'");

        return $query->rows;
    }

    public function getSeoUrlsByQuery($query, $lang = 'vi') {
        $query = $this->db->query("SELECT * FROM `" . $this->table . "` WHERE `query` = '" . $this->db->escape($query) . "' AND `lang` = '" . $this->db->escape($lang) . "'");

        return $query->rows;
    }
}
