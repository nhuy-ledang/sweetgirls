<?php
class ModelPagePage extends Model {
    protected $table = DB_PREFIX . 'pg__pages';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['image'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'             => 'integer',
        'parent_id'      => 'integer',
        'is_sub'         => 'boolean',
        'is_land'        => 'boolean',
        'home'           => 'boolean',
        'bottom'         => 'boolean',
        'table_contents' => 'json',
        'properties'     => 'json',
        'sort_order'     => 'integer',
        'status'         => 'boolean',
    ];

    protected function getTransformer($row) {
        if (!empty($row['d__name'])) $row['name'] = $row['d__name'];
        if (isset($row['d__name'])) unset($row['d__name']);
        if (!empty($row['d__description'])) $row['description'] = $row['d__description'];
        if (isset($row['d__description'])) unset($row['d__description']);
        if (!empty($row['d__alias'])) $row['alias'] = $row['d__alias'];
        if (isset($row['d__alias'])) unset($row['d__alias']);
        //$prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        //$row['href'] = $this->url->link('activity/info', 'activity_id=' . $row['id']);
        // $row['href'] = $this->config->get('config_url') . $prefix . (!empty($row['alias']) ? $row['alias'] : 'page/page?page_id=' . $row['id']);
        $this->getHref($row, 'page/page', 'page_id=' . $row['id']);
        if (!empty($row['description'])) $row['description'] = html_entity_decode($row['description'], ENT_QUOTES, 'UTF-8');
        if (!empty($row['image'])) {
            $row['small_url'] = media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'small');
            $row['thumb_url'] = media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'thumb');
            $row['large_url'] = media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'large');
            $row['raw_url'] = media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'));
        }
        if (!empty($row['icon'])) $row['icon_url'] = media_url_file(html_entity_decode($row['icon'], ENT_QUOTES, 'UTF-8'));
        if (!empty($row['banner'])) $row['banner_url'] = media_url_file(html_entity_decode($row['banner'], ENT_QUOTES, 'UTF-8'));
        $row = $this->transform($row);
        if (isset($row['table_contents']) && isset($row['table_contents'][$this->config->get('config_language')])) {
            $row['table_contents'] = $row['table_contents'][$this->config->get('config_language')];
        }

        return $row;
    }

    public function getPage($id) {
        $fields = ['p.*'];
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $fields[] = 'd.name as d__name';
            $fields[] = 'd.meta_title as d__meta_title';
            $fields[] = 'd.meta_description as d__meta_description';
            $fields[] = 'd.meta_keyword as d__meta_keyword';
            $fields[] = 'd.description as d__description';
            $fields[] = 'd.alias as d__alias';
        }
        $sql = "select " . implode(', ', $fields) . " from " . $this->table . " p";
    
        $sql .= " where p.id = '" . (int)$id . "'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {
            return $this->getTransformer($query->row);
        } else {
            return false;
        }
    }

    public function getPages($data = []) {
        $fields = ['p.*'];
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $fields[] = 'd.name as d__name';
            $fields[] = 'd.meta_title as d__meta_title';
            $fields[] = 'd.meta_description as d__meta_description';
            $fields[] = 'd.meta_keyword as d__meta_keyword';
            $fields[] = 'd.description as d__description';
            $fields[] = 'd.alias as d__alias';
        }
        $sql = "select " . implode(', ', $fields) . " from " . $this->table . " p";
        
        $implode = [];
        if (!empty($data['filter_name'])) {
            $implode[] = "p.name like '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        if (isset($data['filter_category']) && intval($data['filter_category'])) {
            $implode[] = "p.category_id = '" . intval($data['filter_category']) . "'";
        }
        $implode[] = "p.status = 1";
        if (!empty($implode)) $sql .= " where " . implode(' and ', $implode);
        $sql .= " group by p.id";
        $sort_data = ['p.id', 'p.name', 'p.sort_order', 'rand()'];
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " order by " . $data['sort'];
            if (isset($data['order']) && (strtolower($data['order']) == 'asc')) {
                $sql .= " asc";
            } else {
                $sql .= " desc";
            }
        } else {
            $sql .= " order by p.name asc";
        }
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) $data['start'] = 0;
            if ($data['limit'] < 1) $data['limit'] = 20;
            $sql .= " limit " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        $query = $this->db->query($sql);
        $rows = [];
        foreach ($query->rows as $row) {
            $rows[] = $this->getTransformer($row);
        }

        return $rows;
    }

    public function getTotalPages($data = []) {
        $sql = "select count(*) as total from " . $this->table . " p";
        $implode = [];
        if (!empty($data['filter_name'])) {
            $implode[] = "p.name like '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        if (isset($data['filter_category']) && intval($data['filter_category'])) {
            $implode[] = "p.category_id = '" . intval($data['filter_category']) . "'";
        }
        $implode[] = "p.status = 1";
        if (!empty($implode)) {
            $sql .= " where " . implode(' and ', $implode);
        }
        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getPageHome() {
        $fields = ['p.*'];
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $fields[] = 'd.name as d__name';
            $fields[] = 'd.meta_title as d__meta_title';
            $fields[] = 'd.meta_description as d__meta_description';
            $fields[] = 'd.meta_keyword as d__meta_keyword';
            $fields[] = 'd.description as d__description';
            $fields[] = 'd.alias as d__alias';
        }
        $sql = "select " . implode(', ', $fields) . " from " . $this->table . " p";
        
        $sql .= " where p.home = 1";
        $query = $this->db->query($sql);

        if ($query->num_rows) {
            return $this->getTransformer($query->row);
        } else {
            return false;
        }
    }

    public function getPagesInIds($ids = []) {
        if (empty($ids)) return [];
        $fields = ['p.*'];
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $fields[] = 'd.name as d__name';
            $fields[] = 'd.meta_title as d__meta_title';
            $fields[] = 'd.meta_description as d__meta_description';
            $fields[] = 'd.meta_keyword as d__meta_keyword';
            $fields[] = 'd.description as d__description';
            $fields[] = 'd.alias as d__alias';
        }
        $sql = "select " . implode(', ', $fields) . " from " . $this->table . " p";
       
        $implode = [];
        $implode[] = "p.id in (" . implode(', ', $ids) . ")";
        $implode[] = "p.status = 1";
        if (!empty($implode)) $sql .= " where " . implode(' and ', $implode);
        $sql .= " group by p.id";
        $query = $this->db->query($sql);
        $rows = [];
        foreach ($query->rows as $row) {
            $rows[$row['id']] = $this->getTransformer($row);
        }
        // Sort order
        $output = [];
        foreach ($ids as $id) {
            if (isset($rows[$id])) $output[] = $rows[$id];
        }
        return $output;
    }
}
