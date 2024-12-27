<?php
class ModelProductCategory extends Model {
    protected $table = DB_PREFIX . 'pd__categories';
    protected $casts = [
        'id' => 'integer',
    ];
    protected $hidden = ['alias', 'image', 'icon'];

    protected function getTransformer($row) {
        //$prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        //$row['href'] = $this->url->link('product/category', 'category_id=' . $row['id']);
        //$row['href'] = $this->config->get('config_url') . $prefix . (!empty($row['alias']) ? $row['alias'] : 'product/category?category_id=' . $row['id']);
        $this->getHref($row, 'product/category', 'category_id=' . $row['id']);
        if (isset($row['description'])) $row['description'] = html_entity_decode($row['description'], ENT_QUOTES, 'UTF-8');
        if (!empty($row['image'])) {
            $row['small_url'] = media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'small');
            $row['thumb_url'] = media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'thumb');
            $row['large_url'] = media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'large');
            $row['raw_url'] = media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'));
        }
        if (!empty($row['icon'])) $row['icon_url'] = media_url_file(html_entity_decode($row['icon'], ENT_QUOTES, 'UTF-8'));
        if (!empty($row['banner'])) $row['banner_url'] = media_url_file(html_entity_decode($row['banner'], ENT_QUOTES, 'UTF-8'));

        return $this->transform($row);
    }

    public function getCategory($id) {
        $sql = "select distinct c.*";
        $sql .= " from " . $this->table . " c";
    
        $sql .= " where c.id = '" . (int)$id . "' and c.status = '1'";

        $query = $this->db->query($sql);
        if ($query->num_rows) {
            return $this->getTransformer($query->row);
        } else {
            return false;
        }
    }

    public function getCategories($data = []) {
        $sql = "select c.*";
        $sql .= " from " . $this->table . " c";
       
        $implodes = [];
        if (isset($data['filter_parent']) && !is_null($data['filter_parent'])) {
            $implodes[] = "c.parent_id = '" . (int)$data['filter_parent'] . "'";
        }
        if (isset($data['filter_top']) && !is_null($data['filter_top'])) {
            $implodes[] = "c.top = '" . (int)$data['filter_top'] . "'";
        }
        if (isset($data['filter_bottom']) && !is_null($data['filter_bottom'])) {
            $implodes[] = "c.bottom = '" . (int)$data['filter_bottom'] . "'";
        }
        if (isset($data['filter_home']) && !is_null($data['filter_home'])) {
            $implodes[] = "c.home = '" . (int)$data['filter_home'] . "'";
        }
        if (isset($data['filter_show']) && !is_null($data['filter_show'])) {
            $implodes[] = "c.show = '" . (int)$data['filter_show'] . "'";
        }
        if (!empty($data['filter_in'])) {
            $implodes[] = "c.id in (" . implode(',', $data['filter_in']) . ")";
        }
        $implodes[] = "c.sort_order > '-1'";
        $implodes[] = "c.status = '1'";
        if (!empty($implodes)) $sql .= " where " . implode(' and ', $implodes);

        $sort_data = ['c.sort_order'];
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " order by " . $data['sort'];
        } else {
            $sql .= " order by c.sort_order";
        }
        if (isset($data['order']) && (strtolower($data['order']) == 'desc')) {
            $sql .= " desc";
        } else {
            $sql .= " asc";
        }

        $data = [];
        $query = $this->db->query($sql);
        foreach ($query->rows as $row) {
            $data[] = $this->getTransformer($row);
        }

        return $data;
    }

    public function getOptions($id) {
        $sql = "select o.* from `pd__options` o left join " . $this->table . " c on (FIND_IN_SET(o.id, c.options))";

        $sql .= " where c.id = '" . $id . "'";
        $sql .= " order by `o`.`sort_order` asc";
        $query = $this->db->query($sql);
        $data = [];
        foreach ($query->rows as $row) {
            if (isset($row['image'])) {
                $row = array_merge($row, [
                    'thumb_url' => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'thumb'),
                    'raw_url'   => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'))
                ]);
            }
            $data[] = $row;
        }
        return $data;
    }

    public function getCategoriesInIds($ids = []) {
        if (empty($ids)) return [];
        $sql = "select c.*";
        $sql .= " from " . $this->table . " c";
        
        $implodes = [];
        $implode[] = "c.id in (" . implode(', ', $ids) . ")";
        $implodes[] = "c.status = '1'";
        if (!empty($implodes)) $sql .= " where " . implode(' and ', $implodes);
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
