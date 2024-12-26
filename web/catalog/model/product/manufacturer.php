<?php
class ModelProductManufacturer extends Model {
    private $table = DB_PREFIX . 'pd__manufacturers';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['alias'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'sort_order' => 'integer',
    ];

    protected function getTransformer($row) {
        if (isset($row['image'])) {
            $row['raw_url'] = media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'));
            $row['thumb_url'] = media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'thumb');
        }
        if (isset($row['alias'])) {
            //$row['href'] = $row['alias'] ? $this->config->get('config_url') . $row['alias'] : $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $row['id']);
            $this->getHref($row, 'product/manufacturer','manufacturer_id=' . $row['id']);
        }
        if (isset($row['description'])) {
            $row['description'] = html_entity_decode($row['description'], ENT_QUOTES, 'UTF-8');
        }

        return $this->transform($row);
    }

    public function getManufacturer($id) {
        $query = $this->db->query("SELECT * FROM " . $this->table . " m WHERE m.id = '" . (int)$id . "'");

        if ($query->num_rows) {
            return $this->getTransformer($query->row);
        } else {
            return false;
        }
    }

    public function getManufacturers($data = []) {
        $data = true; // Chưa xóa được cache
        if ($data) {
            $sql = "SELECT * FROM " . $this->table . " m";

            $sort_data = ['name', 'sort_order'];

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY sort_order";
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

            $data = [];
            foreach ($query->rows as $row) {
                $data[] = $this->getTransformer($row);
            }
            return $data;
        } else {
            $manufacturer_data = $this->cache->get('manufacturer');

            if (!$manufacturer_data) {
                $query = $this->db->query("SELECT * FROM " . $this->table . " m ORDER BY sort_order ASC");

                $manufacturer_data = [];
                foreach ($query->rows as $row) {
                    $manufacturer_data[] = $this->getTransformer($row);
                }

                $this->cache->set('manufacturer', $manufacturer_data);
            }

            return $manufacturer_data;
        }
    }
}
